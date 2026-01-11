<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Dapatkan data profil pengguna",
     *     description="Dapatkan data profil pengguna termasuk nama, identifier, level, dan poin",
     *     operationId="getProfileData",
     *     tags={"Profil"},
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
     *                 @OA\Property(property="nama", type="string", example="John Doe"),
     *                 @OA\Property(property="identifier", type="string", example="john@example.com"),
     *                 @OA\Property(property="level", type="string", example="Pemula"),
     *                 @OA\Property(property="poin", type="integer", example=100)
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'nama' => $user->name,
                'identifier' => $user->identifier,
                'level' => $currentLevel ? $currentLevel->nama : null,
                'poin' => $user->poin,
            ],
        ]);
    }
}
