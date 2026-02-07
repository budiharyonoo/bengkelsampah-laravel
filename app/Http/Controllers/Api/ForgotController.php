<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper as R;
use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/forgot",
     *     tags={"Autentikasi"},
     *     summary="Reset kata sandi pengguna",
     *     description="Endpoint untuk reset kata sandi pengguna dengan verifikasi OTP. Setelah berhasil, pengguna harus login ulang.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"identifier","new_password","new_password_confirmation","otp"},
     *
     *             @OA\Property(
     *                 property="identifier",
     *                 type="string",
     *                 example="john@example.com",
     *                 description="Email atau nomor telepon yang terdaftar"
     *             ),
     *             @OA\Property(
     *                 property="new_password",
     *                 type="string",
     *                 format="password",
     *                 example="newpassword123",
     *                 description="Kata sandi baru"
     *             ),
     *             @OA\Property(
     *                 property="new_password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 example="newpassword123",
     *                 description="Konfirmasi kata sandi baru"
     *             ),
     *             @OA\Property(
     *                 property="otp",
     *                 type="string",
     *                 example="123456",
     *                 description="Kode OTP yang dikirim ke email/WhatsApp"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Kata sandi berhasil direset",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Kata sandi berhasil direset. Silakan login dengan kata sandi baru Anda.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *
     *         @OA\JsonContent(
     *             oneOf={
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Validasi gagal"),
     *                     @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         @OA\Property(
     *                             property="identifier",
     *                             type="array",
     *
     *                             @OA\Items(type="string", example="Field identifier wajib diisi.")
     *                         )
     *                     )
     *                 ),
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Kode OTP tidak valid")
     *                 ),
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Kode OTP telah kedaluwarsa")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return R::error('Validasi gagal', 422, $validator->errors());
        }

        // Verify OTP first
        $otp = Otp::where('identifier', $request->identifier)
            ->where('type', 'forgot')
            ->where('code', $request->otp)
            ->first();

        if (! $otp) {
            return R::error('Kode OTP tidak valid', 422);
        }

        // Check if OTP is expired
        if (Carbon::parse($otp->expires_at)->isPast()) {
            return R::error('Kode OTP telah kedaluwarsa', 422);
        }

        // Get user and update password
        $user = User::where('identifier', $request->identifier)->first();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Delete used OTP
        $otp->delete();

        return R::success('Kata sandi berhasil direset. Silakan login dengan kata sandi baru Anda.');
    }
}
