<?php

namespace App\Services;

use App\Models\Notification as NotificationModel;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to user and save to database
     */
    public function sendToUser($userId, $title, $body, $type = 'general', $data = [], $image = null)
    {
        try {
            $user = User::find($userId);
            if (! $user) {
                Log::error('User not found for notification', ['user_id' => $userId]);

                return false;
            }

            // Save notification to database
            $notification = NotificationModel::create([
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'image' => $image,
                'data' => $data,
                'type' => $type,
                'is_read' => false,
            ]);

            // Send push notification if user has FCM token
            if ($user->fcm_token) {
                $firebaseData = array_merge($data, [
                    'notification_id' => $notification->id,
                    'type' => $type,
                ]);

                $result = $this->firebaseService->sendToUser(
                    $user->fcm_token,
                    $title,
                    $body,
                    $firebaseData
                );

                if (! $result['success']) {
                    Log::warning('Firebase notification failed for user', [
                        'user_id' => $userId,
                        'error' => $result['error'] ?? 'Unknown error',
                    ]);
                }
            }

            Log::info('Notification sent to user', [
                'user_id' => $userId,
                'notification_id' => $notification->id,
                'type' => $type,
                'has_fcm_token' => ! empty($user->fcm_token),
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification to user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send redeem notification
     */
    public function sendRedeemNotification($userId, $jumlahPoint, $alasanRedeem)
    {
        $title = 'Redeem Poin Berhasil';
        $body = "Redeem poin sebesar {$jumlahPoint} poin telah diproses. Alasan: {$alasanRedeem}";

        return $this->sendToUser(
            $userId,
            $title,
            $body,
            'redeem',
            [
                'jumlah_point' => $jumlahPoint,
                'alasan_redeem' => $alasanRedeem,
            ]
        );
    }

    /**
     * Send setoran completed notification
     */
    public function sendSetoranCompletedNotification($userId, $setoranId, $totalPoint, $totalXp)
    {
        $title = 'Setoran Selesai';
        $body = "Setoran sampah Anda telah selesai diproses. Anda mendapatkan {$totalPoint} poin dan {$totalXp} XP.";

        return $this->sendToUser(
            $userId,
            $title,
            $body,
            'setoran',
            [
                'setoran_id' => $setoranId,
                'total_point' => $totalPoint,
                'total_xp' => $totalXp,
            ]
        );
    }

    /**
     * Send event notification
     */
    public function sendEventNotification($userId, $eventTitle, $eventDescription)
    {
        $title = 'Event Baru';
        $body = "Event baru: {$eventTitle} - {$eventDescription}";

        return $this->sendToUser(
            $userId,
            $title,
            $body,
            'event',
            [
                'event_title' => $eventTitle,
                'event_description' => $eventDescription,
            ]
        );
    }

    /**
     * Send general notification
     */
    public function sendGeneralNotification($userId, $title, $body, $data = [])
    {
        return $this->sendToUser($userId, $title, $body, 'general', $data);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultipleUsers($userIds, $title, $body, $type = 'general', $data = [])
    {
        $users = User::whereIn('id', $userIds)->get();
        $fcmTokens = $users->pluck('fcm_token')->filter()->toArray();

        // Save notifications to database
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'type' => $type,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        NotificationModel::insert($notifications);

        // Send push notifications
        if (! empty($fcmTokens)) {
            $firebaseData = array_merge($data, [
                'type' => $type,
            ]);

            $result = $this->firebaseService->sendToMultipleUsers(
                $fcmTokens,
                $title,
                $body,
                $firebaseData
            );

            Log::info('Batch notification sent', [
                'user_count' => count($userIds),
                'fcm_token_count' => count($fcmTokens),
                'success_count' => $result['success_count'] ?? 0,
                'failure_count' => $result['failure_count'] ?? 0,
            ]);
        }

        return true;
    }

    /**
     * Send notification to all users
     */
    public function sendToAllUsers($title, $body, $type = 'general', $data = [])
    {
        $userIds = User::pluck('id')->toArray();

        return $this->sendToMultipleUsers($userIds, $title, $body, $type, $data);
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 20, $offset = 0)
    {
        $notifications = NotificationModel::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        // Transform data to match Flutter model expectations
        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->body, // Map body to message
                'data' => $notification->data,
                'is_read' => $notification->is_read,
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount($userId)
    {
        return NotificationModel::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return NotificationModel::where('id', $notificationId)
            ->where('user_id', $userId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        return NotificationModel::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId, $userId)
    {
        return NotificationModel::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete();
    }
}
