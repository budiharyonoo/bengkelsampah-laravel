<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/events",
     *     summary="Dapatkan daftar event aktif",
     *     description="Mengembalikan daftar event aktif dengan paginasi, diurutkan dari yang terbaru dibuat",
     *     operationId="getEvents",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman untuk paginasi",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
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
     *                     property="events",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Bersih-bersih Pantai"),
     *                         @OA\Property(property="description", type="string", example="Kegiatan membersihkan pantai dari sampah plastik..."),
     *                         @OA\Property(property="cover", type="string", example="https://example.com/cover.jpg"),
     *                         @OA\Property(property="start_datetime", type="string", format="date-time"),
     *                         @OA\Property(property="end_datetime", type="string", format="date-time"),
     *                         @OA\Property(property="location", type="string", example="Pantai Indah"),
     *                         @OA\Property(property="max_participants", type="integer", example=100),
     *                         @OA\Property(property="status", type="string", example="active"),
     *                         @OA\Property(property="participants_count", type="integer", example=25),
     *                         @OA\Property(property="has_result", type="boolean", example=false),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="admin_name", type="string", example="Admin Bengkel Sampah")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
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
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Event::withCount('participants')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc');

        $events = $query->paginate(10);

        // Transform the events
        $transformedEvents = collect($events->items())->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'cover' => $event->cover,
                'start_datetime' => $event->start_datetime,
                'end_datetime' => $event->end_datetime,
                'location' => $event->location,
                'url' => $event->url,
                'max_participants' => $event->max_participants,
                'status' => $event->status,
                'participants_count' => $event->participants_count,
                'has_result' => $event->hasResult(),
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
                'admin_name' => $event->admin_name,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'events' => $transformedEvents,
                'pagination' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                    'has_more_pages' => $events->hasMorePages(),
                ],
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/events/{id}",
     *     summary="Dapatkan detail event",
     *     description="Mengembalikan informasi detail tentang event tertentu termasuk daftar peserta dan status join user",
     *     operationId="getEventDetails",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID event",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
     *                     property="event",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Bersih-bersih Pantai"),
     *                     @OA\Property(property="description", type="string", example="Kegiatan membersihkan pantai dari sampah plastik..."),
     *                     @OA\Property(property="cover", type="string", example="https://example.com/cover.jpg"),
     *                     @OA\Property(property="start_datetime", type="string", format="date-time"),
     *                     @OA\Property(property="end_datetime", type="string", format="date-time"),
     *                     @OA\Property(property="location", type="string", example="Pantai Indah"),
     *                     @OA\Property(property="max_participants", type="integer", example=100),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="result_description", type="string", example="Kegiatan berhasil dilaksanakan..."),
     *                     @OA\Property(property="saved_waste_amount", type="number", example=150.5),
     *                     @OA\Property(property="actual_participants", type="integer", example=25),
     *                     @OA\Property(property="result_photos", type="array", @OA\Items(type="string")),
     *                     @OA\Property(property="result_submitted_at", type="string", format="date-time"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="user_has_joined", type="boolean", example=true),
     *                     @OA\Property(property="admin_name", type="string", example="Admin Bengkel Sampah"),
     *                     @OA\Property(property="result_submitted_by_name", type="string", example="Admin Bengkel Sampah"),
     *                     @OA\Property(
     *                         property="participants",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="user_name", type="string", example="John Doe"),
     *                             @OA\Property(property="user_identifier", type="string", example="081234567890"),
     *                             @OA\Property(property="join_datetime", type="string", format="date-time")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Event tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Event tidak ditemukan")
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
     *     )
     * )
     */
    public function show($id)
    {
        $event = Event::with(['participants'])
            ->withCount('participants')
            ->find($id);

        if (! $event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        // Debug: Log participants data
        \Log::info('Event participants count: '.$event->participants->count());
        foreach ($event->participants as $participant) {
            \Log::info('Participant: '.$participant->user_name.' | '.$participant->user_identifier);
        }

        // Check if authenticated user has joined this event
        $user = auth()->user();
        $userHasJoined = false;

        if ($user) {
            $userHasJoined = $event->participants()
                ->where('user_identifier', $user->identifier)
                ->exists();
        }

        // Add user join status to event data
        $event->user_has_joined = $userHasJoined;

        return response()->json([
            'status' => 'success',
            'data' => [
                'event' => $event,
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/events/{id}/toggle-join",
     *     summary="Join atau unjoin event",
     *     description="Toggle status join user ke event. Jika sudah join akan unjoin, jika belum join akan join",
     *     operationId="toggleEventJoin",
     *     tags={"Events"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID event",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Operasi berhasil",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Berhasil join event"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="action", type="string", example="joined", description="joined atau unjoined"),
     *                 @OA\Property(property="user_has_joined", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Event tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Event tidak ditemukan")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Event tidak aktif atau sudah penuh",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Event tidak aktif atau sudah penuh")
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
     *     )
     * )
     */
    public function toggleJoin($id)
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event tidak ditemukan',
            ], 404);
        }

        $user = auth()->user();

        // Check if user is already joined
        $existingParticipant = $event->participants()
            ->where('user_identifier', $user->identifier)
            ->first();

        if ($existingParticipant) {
            // User is already joined, so unjoin
            $existingParticipant->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil unjoin event',
                'data' => [
                    'action' => 'unjoined',
                    'user_has_joined' => false,
                ],
            ]);
        } else {
            // User is not joined, so join
            // Check if event is active
            if ($event->status !== 'active') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event tidak aktif',
                ], 400);
            }

            // Check if event is full
            if ($event->max_participants && $event->participants()->count() >= $event->max_participants) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Event sudah penuh',
                ], 400);
            }

            // Join the event
            $event->participants()->create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_identifier' => $user->identifier,
                'join_datetime' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil join event',
                'data' => [
                    'action' => 'joined',
                    'user_has_joined' => true,
                ],
            ]);
        }
    }
}
