<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Point;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class PointController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/point",
     *     summary="Dapatkan data poin pengguna",
     *     description="Dapatkan data XP pengguna, level saat ini, daftar level, dan poin",
     *     operationId="getPointData",
     *     tags={"Poin"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Halaman untuk history poin (opsional)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=1)
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
     *                 @OA\Property(property="xp", type="integer", example=100),
     *                 @OA\Property(
     *                     property="current_level",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="Pemula"),
     *                     @OA\Property(property="xp", type="integer", example=0)
     *                 ),
     *                 @OA\Property(
     *                     property="levels",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nama", type="string", example="Pemula"),
     *                         @OA\Property(property="xp", type="integer", example=0)
     *                     )
     *                 ),
     *                 @OA\Property(property="poin", type="integer", example=100),
     *                 @OA\Property(
     *                     property="history",
     *                     type="object",
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="type", type="string", example="setor"),
     *                             @OA\Property(property="tanggal", type="string", format="date", example="2024-01-15"),
     *                             @OA\Property(property="jumlah_point", type="integer", example=100),
     *                             @OA\Property(property="xp", type="integer", example=1),
     *                             @OA\Property(property="keterangan", type="string", example="Setoran sampah"),
     *                             @OA\Property(property="setoran_id", type="integer", example=1)
     *                         )
     *                     ),
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="next_page_url", type="string", nullable=true),
     *                     @OA\Property(property="prev_page_url", type="string", nullable=true)
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

        // Get user's current level
        $currentLevel = Level::where('xp', '<=', $user->xp)
            ->orderBy('xp', 'desc')
            ->first();

        // Get all levels
        $levels = Level::orderBy('xp', 'asc')->get();

        // Get point history with pagination (latest first)
        $page = $request->get('page', 1);
        $perPage = 10;

        $pointHistory = Point::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'data' => [
                'xp' => (int) $user->xp,
                'current_level' => $currentLevel ? [
                    'id' => $currentLevel->id,
                    'nama' => $currentLevel->nama,
                    'xp' => (int) $currentLevel->xp,
                ] : null,
                'levels' => $levels->map(function ($level) {
                    return [
                        'id' => $level->id,
                        'nama' => $level->nama,
                        'xp' => (int) $level->xp,
                    ];
                }),
                'poin' => $user->poin,
                'history' => [
                    'data' => $pointHistory->items(),
                    'current_page' => $pointHistory->currentPage(),
                    'last_page' => $pointHistory->lastPage(),
                    'per_page' => $pointHistory->perPage(),
                    'total' => $pointHistory->total(),
                    'next_page_url' => $pointHistory->nextPageUrl(),
                    'prev_page_url' => $pointHistory->previousPageUrl(),
                ],
            ],
        ]);
    }
}
