<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $limit = $request->get('limit', 20);
            $offset = $request->get('offset', 0);

            $notifications = $this->notificationService->getUserNotifications(
                $user->id,
                $limit,
                $offset
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => $notifications,
                ],
                'message' => 'Notifications retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            $notificationId = $request->notification_id;

            $result = $this->notificationService->markAsRead($notificationId, $user->id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to mark notification as read',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            $result = $this->notificationService->markAllAsRead($user->id);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'notification_id' => 'required|integer|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            $notificationId = $request->get('notification_id');

            $result = $this->notificationService->deleteNotification($notificationId, $user->id);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification deleted successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete notification',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update FCM token
     */
    public function updateFcmToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = Auth::user();
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM token: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test notification
     */
    public function testNotification(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user->fcm_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'User does not have FCM token',
                ], 400);
            }

            $result = $this->notificationService->sendGeneralNotification(
                $user->id,
                'Test Notification',
                'This is a test notification from Laravel API',
                ['test' => 'data']
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully',
                    'fcm_token' => $user->fcm_token,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test notification',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: '.$e->getMessage(),
            ], 500);
        }
    }
}
