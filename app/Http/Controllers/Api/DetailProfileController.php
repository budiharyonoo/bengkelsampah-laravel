<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class DetailProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/detail-profile",
     *     summary="Dapatkan data profil detail pengguna",
     *     description="Dapatkan nama pengguna dan daftar alamat",
     *     operationId="getDetailProfileData",
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
     *                 @OA\Property(
     *                     property="addresses",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nama", type="string", example="John Doe"),
     *                         @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *                         @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *                         @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *                         @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *                         @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *                         @OA\Property(property="kode_pos", type="string", example="12120"),
     *                         @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *                         @OA\Property(property="is_default", type="boolean", example=true)
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

        $user = User::with('addresses')->find($accessToken->tokenable_id);
        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'nama' => $user->name,
                'addresses' => $user->addresses,
            ],
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/edit-profile",
     *     summary="Perbarui profil pengguna",
     *     description="Perbarui nama, identifier, dan kata sandi pengguna. Jika memperbarui identifier, verifikasi OTP diperlukan.",
     *     operationId="updateProfile",
     *     tags={"Profil"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="identifier", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword123"),
     *             @OA\Property(property="otp", type="string", example="123456", description="Diperlukan jika memperbarui identifier")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profil berhasil diperbarui",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Profil berhasil diperbarui")
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
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data yang diberikan tidak valid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="identifier", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="otp", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Kode OTP tidak valid",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Kode OTP tidak valid.")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
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

        // Validate request
        $rules = [
            'name' => 'sometimes|string|max:255',
            'identifier' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('users', 'identifier')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:6',
            'otp' => 'required_if:identifier,!=,null|string|size:6',
        ];

        $validator = validator($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If updating identifier, verify OTP
        if ($request->has('identifier')) {
            // Check if OTP exists
            $otp = Otp::where('identifier', $request->identifier)
                ->where('code', $request->otp)
                ->where('type', 'change')
                ->first();

            if (! $otp) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode OTP tidak valid.',
                ], 400);
            }

            // Check if OTP is expired
            if (Carbon::parse($otp->expires_at)->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.',
                ], 400);
            }
        }

        // Update user data
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('identifier')) {
            $user->identifier = $request->identifier;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
        ]);
    }
}
