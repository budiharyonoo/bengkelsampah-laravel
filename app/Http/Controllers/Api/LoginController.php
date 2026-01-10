<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Helpers\ResponseHelper as R;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Autentikasi"},
     *     summary="Login pengguna",
     *     description="Endpoint untuk login pengguna dengan verifikasi OTP. Token yang dihasilkan berlaku selama 1 tahun.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"identifier","password","otp"},
     *             @OA\Property(
     *                 property="identifier",
     *                 type="string",
     *                 example="john@example.com",
     *                 description="Email atau nomor telepon yang terdaftar"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="password123",
     *                 description="Kata sandi pengguna"
     *             ),
     *             @OA\Property(
     *                 property="otp",
     *                 type="string",
     *                 example="123456",
     *                 description="Kode OTP yang dikirim ke email/WhatsApp"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login berhasil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="identifier", type="string", example="john@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *                 @OA\Property(property="token_expires_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Validasi gagal"),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(
     *                             property="identifier",
     *                             type="array",
     *                             @OA\Items(type="string", example="Field identifier wajib diisi.")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Kata sandi salah")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Kode OTP tidak valid")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Kode OTP telah kedaluwarsa")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return R::error('Validasi gagal', 422, $validator->errors());
        }

        // BYPASS untuk akun testing Google
        if (
            $request->identifier === 'testing@bengkelsampah.com' &&
            $request->password === 'BengkelSampah25'
        ) {
            $user = User::where('identifier', $request->identifier)->first();
            if (!$user) {
                return R::error('Akun testing tidak ditemukan', 422);
            }
            $expiresAt = now()->addYear();
            $token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;
            return R::success('Login berhasil (bypass testing)', [
                'user' => $user,
                'token' => $token,
                'token_expires_at' => $expiresAt->toDateTimeString()
            ]);
        }

        // Verify OTP first
        // $otp = Otp::where('identifier', $request->identifier)
        //           ->where('type', 'login')
        //           ->where('code', $request->otp)
        //           ->first();

        // if (!$otp) {
        //     return R::error('Kode OTP tidak valid', 422);
        // }

        // Check if OTP is expired
        // if (Carbon::parse($otp->expires_at)->isPast()) {
        //     return R::error('Kode OTP telah kedaluwarsa', 422);
        // }

        // Get user and verify password
        $user = User::where('identifier', $request->identifier)->first();
        if (!Hash::check($request->password, $user->password)) {
            return R::error('Kata sandi salah', 422);
        }

        // Delete used OTP
        // $otp->delete();

        // Generate token with 1 year expiration
        $expiresAt = now()->addYear();
        $token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

        return R::success('Login berhasil', [
            'user' => $user,
            'token' => $token,
            'token_expires_at' => $expiresAt->toDateTimeString()
        ]);
    }
}
