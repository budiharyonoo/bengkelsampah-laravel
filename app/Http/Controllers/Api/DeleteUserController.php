<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper as R;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class DeleteUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/delete-account",
     *     tags={"Profil"},
     *     summary="Hapus akun pengguna",
     *     description="Endpoint untuk menghapus akun pengguna dan semua data terkait.",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Akun berhasil dihapus",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Akun berhasil dihapus")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Tidak terautentikasi",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Tidak terautentikasi. Token tidak diberikan.")
     *         )
     *     )
     * )
     */
    public function delete(Request $request)
    {
        // Get token from Authorization header
        $token = $request->bearerToken();
        if (! $token) {
            return R::error('Tidak terautentikasi. Token tidak diberikan.', 401);
        }

        // Get user from token
        $accessToken = PersonalAccessToken::findToken($token);
        if (! $accessToken) {
            return R::error('Tidak terautentikasi. Token tidak valid.', 401);
        }

        $user = User::find($accessToken->tokenable_id);
        if (! $user) {
            return R::error('Pengguna tidak ditemukan.', 404);
        }

        DB::beginTransaction();
        try {
            // Delete all user's addresses
            $user->addresses()->delete();

            // Delete all user's OTPs
            Otp::where('identifier', $user->identifier)->delete();

            // Delete all user's tokens
            $user->tokens()->delete();

            // Delete the user
            $user->delete();

            DB::commit();

            return R::success('Akun berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();

            return R::error('Gagal menghapus akun', 500);
        }
    }
}
