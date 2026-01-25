<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Models\XpResetHistory;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XPResetController extends Controller
{
    public function __construct(private FirebaseService $firebaseService) {}

    /**
     * Show XP reset page with top 3 users preview
     */
    public function index()
    {
        $topUsers = $this->getTop3Users();

        return view('admin.xp-reset.index', compact('topUsers'));
    }

    /**
     * Execute XP reset with transaction safety
     */
    public function executeReset(Request $request)
    {
        try {
            $admin = Auth::guard('admin')->user();

            if (! $admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            // Get top 3 users before reset
            $topUsers = $this->getTop3Users();

            if ($topUsers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data user untuk di-reset',
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Send FCM notifications to both topics
                $notificationTitle = 'XP anda di Reset telah untuk periode ini';
                $notificationBody = $this->buildNotificationMessage($topUsers);

                // Send to broadcast topic
                $broadcastResult = $this->firebaseService->sendToTopic(
                    'broadcast',
                    $notificationTitle,
                    $notificationBody,
                    [
                        'type' => 'xp_reset',
                        'top1_name' => $topUsers->get(0)?->name ?? '',
                        'top2_name' => $topUsers->get(1)?->name ?? '',
                        'top3_name' => $topUsers->get(2)?->name ?? '',
                    ]
                );

                // Send to broadcast-gocap topic
                $gocapResult = $this->firebaseService->sendToTopic(
                    'broadcast-gocap',
                    $notificationTitle,
                    $notificationBody,
                    [
                        'type' => 'xp_reset',
                        'top1_name' => $topUsers->get(0)?->name ?? '',
                        'top2_name' => $topUsers->get(1)?->name ?? '',
                        'top3_name' => $topUsers->get(2)?->name ?? '',
                    ]
                );

                Log::info('XP Reset FCM notifications sent', [
                    'broadcast_result' => $broadcastResult,
                    'gocap_result' => $gocapResult,
                ]);

                // Reset all user XP to 0
                User::query()->update(['xp' => 0]);

                // Save reset history
                XpResetHistory::create([
                    'reset_at' => now(),
                    'admin_id' => $admin->id,
                    'admin_name' => $admin->name,
                    'top1_user_id' => $topUsers->get(0)?->id,
                    'top1_name' => $topUsers->get(0)?->name,
                    'top1_xp' => $topUsers->get(0)?->xp,
                    'top2_user_id' => $topUsers->get(1)?->id,
                    'top2_name' => $topUsers->get(1)?->name,
                    'top2_xp' => $topUsers->get(1)?->xp,
                    'top3_user_id' => $topUsers->get(2)?->id,
                    'top3_name' => $topUsers->get(2)?->name,
                    'top3_xp' => $topUsers->get(2)?->xp,
                ]);

                Notification::create([
                    'title' => $notificationTitle,
                    'body' => $notificationBody,
                    'is_read' => 1
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'XP berhasil di-reset untuk semua user. Notifikasi telah dikirim.',
                    'fcm_broadcast' => $broadcastResult['success'] ?? false,
                    'fcm_gocap' => $gocapResult['success'] ?? false,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('XP Reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset XP: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show XP reset history datatable
     */
    public function history(Request $request)
    {
        $query = XpResetHistory::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admin_name', 'like', "%{$search}%")
                    ->orWhere('top1_name', 'like', "%{$search}%")
                    ->orWhere('top2_name', 'like', "%{$search}%")
                    ->orWhere('top3_name', 'like', "%{$search}%");
            });
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'reset_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $history = $query->paginate(10);

        return view('admin.xp-reset.history', compact('history'));
    }

    /**
     * Get top 3 users by XP
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTop3Users()
    {
        return User::query()
            ->orderBy('xp', 'desc')
            ->limit(3)
            ->get(['id', 'name', 'xp']);
    }

    /**
     * Build notification message with top 3 users
     */
    private function buildNotificationMessage($topUsers): string
    {
        $top1 = $topUsers->get(0);
        $top2 = $topUsers->get(1);
        $top3 = $topUsers->get(2);

        $message = 'Selamat kepada ';

        if ($top1) {
            $message .= "{$top1->name} sebagai User Top 1 XP";
        }

        if ($top2) {
            $message .= ", kepada {$top2->name} sebagai User Top 2 XP";
        }

        if ($top3) {
            $message .= ", kepada {$top3->name} sebagai User Top 3 XP";
        }

        $message .= ' pada periode ini';

        return $message;
    }
}
