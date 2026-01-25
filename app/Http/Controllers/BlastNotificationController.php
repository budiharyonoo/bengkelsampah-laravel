<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BlastNotification\StoreBlastNotificationRequest;
use App\Models\BlastNotification;
use App\Models\Notification;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BlastNotificationController extends Controller
{
    public function __construct(private FirebaseService $firebaseService) {}

    /**
     * Display a listing of blast notifications.
     */
    public function index(Request $request): View|JsonResponse
    {
        $admin = Auth::guard('admin')->user();
        $query = BlastNotification::query()->orderBy('created_at', 'desc');

        // Apply role-based topic filter
        // Admin users only see "broadcast" topic
        if ($admin->role === 'admin') {
            $query->where('topic', 'broadcast');
        }
        // Cabang users with bank_sampah_id = 13 only see "broadcast-gocap" topic
        elseif ($admin->role === 'cabang' && $admin->id_bank_sampah == 13) {
            $query->where('topic', 'broadcast-gocap');
        }
        // Other users should not see any data (shouldn't reach here due to menu access control)
        else {
            $query->whereRaw('1 = 0'); // Return empty result
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%")
                    ->orWhere('sent_by_name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        // Apply topic filter (user can still filter within their allowed topic)
        if ($request->filled('topic')) {
            $topic = $request->input('topic');
            $query->where('topic', $topic);
        }

        $notifications = $query->paginate(10);

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
            ]);
        }

        return view('dashboard-blast-notification', compact('notifications'));
    }

    /**
     * Show the form for creating a new blast notification.
     */
    public function create(): View
    {
        $admin = Auth::guard('admin')->user();

        // Determine topic based on business rules
        $topic = $this->determineTopic($admin);
        $topicLabel = $this->getTopicLabel($topic);

        return view('dashboard-blast-notification-create', compact('topic', 'topicLabel'));
    }

    /**
     * Send blast notification to FCM topic and log the result.
     */
    public function store(StoreBlastNotificationRequest $request): JsonResponse
    {
        $admin = Auth::guard('admin')->user();

        // Determine topic based on business rules
        $topic = $this->determineTopic($admin);

        if (! $topic) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengirim blast notification',
            ], 403);
        }

        $validated = $request->validated();

        // Send notification via FCM
        $fcmResult = $this->firebaseService->sendToTopic(
            $topic,
            $validated['title'],
            $validated['body'],
            [
                'type' => 'blast_notification',
                'sent_at' => now()->toIso8601String(),
            ]
        );

        // Prepare log data
        $logData = [
            'title' => $validated['title'],
            'body' => $validated['body'],
            'topic' => $topic,
            'sent_by' => $admin->id,
            'sent_by_name' => $admin->name,
            'sent_by_role' => $admin->role,
            'status' => $fcmResult['success'] ? 'success' : 'failed',
            'error_message' => $fcmResult['success'] ? null : ($fcmResult['error'] ?? 'Unknown error'),
            'fcm_response' => $fcmResult,
        ];

        // Log to database
        try {
            DB::beginTransaction();

            BlastNotification::create($logData);

            Notification::create([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'is_read' => 1
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to log blast notification', [
                'error' => $e->getMessage(),
                'log_data' => $logData,
            ]);
        }

        if ($fcmResult['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Blast notification berhasil dikirim ke topic: ' . $topic,
                'data' => [
                    'topic' => $topic,
                    'fcm_name' => $fcmResult['name'] ?? null,
                ],
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim blast notification: ' . ($fcmResult['error'] ?? 'Unknown error'),
                'error' => $fcmResult['error'] ?? 'Unknown error',
            ], 500);
        }
    }

    /**
     * Determine the FCM topic based on admin role and bank_sampah_id.
     */
    private function determineTopic($admin): ?string
    {
        // Rule 1: admin role → broadcast topic
        if ($admin->role === 'admin') {
            return 'broadcast';
        }

        // Rule 2: cabang role AND bank_sampah_id = 13 → broadcast-gocap topic
        if ($admin->role === 'cabang' && $admin->id_bank_sampah == 13) {
            return 'broadcast-gocap';
        }

        // No other roles or configurations are supported
        return null;
    }

    /**
     * Get human-readable topic label.
     */
    private function getTopicLabel(string $topic): string
    {
        return match ($topic) {
            'broadcast' => 'Broadcast (Semua User)',
            'broadcast-gocap' => 'Broadcast GoCap',
            default => $topic,
        };
    }
}
