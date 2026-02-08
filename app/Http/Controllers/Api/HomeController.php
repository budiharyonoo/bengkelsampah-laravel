<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use App\Models\Artikel;
use App\Models\Level;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class HomeController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @OA\Get(
     *     path="/api/home",
     *     summary="Dapatkan data beranda pengguna",
     *     description="Dapatkan data profil pengguna, statistik, level saat ini, artikel terbaru, dan jumlah notifikasi",
     *     operationId="getHomeData",
     *     tags={"Beranda"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Operasi berhasil",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="poin", type="integer", example=100),
     *                     @OA\Property(property="setor", type="integer", example=5),
     *                     @OA\Property(property="sampah", type="integer", example=10),
     *                     @OA\Property(property="xp", type="integer", example=50),
     *                     @OA\Property(property="level", type="string", example="Pemula"),
     *                     @OA\Property(property="next_level_xp", type="integer", example=100),
     *                     @OA\Property(property="unread_notifications", type="integer", example=3)
     *                 ),
     *                 @OA\Property(
     *                     property="articles",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Tips Memilah Sampah"),
     *                         @OA\Property(property="content", type="string", example="Konten artikel..."),
     *                         @OA\Property(property="cover", type="string", example="https://example.com/image.jpg"),
     *                         @OA\Property(property="kategori_id", type="integer", example=1),
     *                         @OA\Property(property="creator", type="string", example="Admin"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(
     *                             property="kategori",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="nama", type="string", example="Tips & Trik")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Tidak terautentikasi",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Tidak terautentikasi. Token tidak diberikan.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Pengguna tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Pengguna tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        // Get token from Authorization header
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak terautentikasi. Token tidak diberikan.',
            ], 401);
        }

        // Get user from token
        $accessToken = PersonalAccessToken::findToken($token);
        if (! $accessToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak terautentikasi. Token tidak valid.',
            ], 401);
        }

        $user = User::find($accessToken->tokenable_id);
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        // Get next level
        $nextLevel = Level::where('xp', '>', $user->xp)
            ->orderBy('xp', 'asc')
            ->first();

        // Get latest 5 articles with kategori relationship
        $articles = Artikel::with('kategori')->latest()->take(5)->get();

        // Get 3 latest active events
        $events = \App\Models\Event::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'cover' => $event->cover,
                    'start_datetime' => $event->start_datetime,
                    'end_datetime' => $event->end_datetime,
                    'location' => $event->location,
                    'max_participants' => $event->max_participants,
                    'status' => $event->status,
                    'participants_count' => $event->participants()->count(),
                    'has_result' => method_exists($event, 'hasResult') ? $event->hasResult() : false,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                    'admin_name' => $event->admin_name ?? null,
                ];
            });

        // Get unread notification count
        $unreadCount = $this->notificationService->getUnreadCount($user->id);

        // Get app version info for Android
        $bankSampahId = $request->get('bank_sampah_id');
        $appVersion = AppVersion::getLatestVersion('android', $bankSampahId);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'name' => $user->name,
                    'poin' => $user->poin,
                    'setor' => $user->setor,
                    'sampah' => $user->sampah,
                    'xp' => (int) $user->xp,
                    'level' => $nextLevel ? $nextLevel->nama : 'Pemula',
                    'next_level_xp' => $nextLevel ? (int) $nextLevel->xp : null,
                    'unread_notifications' => $unreadCount,
                ],
                'articles' => $articles,
                'events' => $events ?? [],
                'app_version' => $appVersion ? [
                    'version' => $appVersion->version,
                    'version_code' => $appVersion->version_code,
                    'is_required' => $appVersion->is_required,
                    'update_message' => $appVersion->update_message,
                    'store_url' => $appVersion->store_url,
                ] : null,
            ],
        ]);
    }
}
