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

class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Autentikasi"},
     *     summary="Daftar pengguna baru",
     *     description="Endpoint untuk mendaftarkan pengguna baru dengan verifikasi OTP",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"fullname","identifier","password","confirm_password","otp"},
     *
     *             @OA\Property(property="fullname", type="string", example="John Doe"),
     *             @OA\Property(property="identifier", type="string", example="john@example.com", description="Email atau nomor telepon Indonesia"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", minLength=8),
     *             @OA\Property(property="confirm_password", type="string", format="password", example="password123"),
     *             @OA\Property(property="otp", type="string", example="123456", description="Kode OTP yang dikirim ke email/WhatsApp")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Registrasi berhasil",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registrasi berhasil"),
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
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validasi gagal",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validasi gagal"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="identifier",
     *                     type="array",
     *
     *                     @OA\Items(type="string", example="Identifier harus berupa email atau nomor telepon Indonesia yang valid.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Kesalahan server",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal membuat token autentikasi.")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'identifier' => [
                'required',
                'string',
                'unique:users,identifier',
                function ($attribute, $value, $fail) {
                    // Check if it's a valid email
                    $isEmail = filter_var($value, FILTER_VALIDATE_EMAIL);

                    // Check if it's a valid phone number (Indonesian format)
                    $isPhone = preg_match('/^(\+62|62|0)8[1-9][0-9]{6,9}$/', $value);

                    if (! $isEmail && ! $isPhone) {
                        $fail('Identifier harus berupa email atau nomor telepon Indonesia yang valid.');
                    }
                },
            ],
            'password' => 'required|string|min:8',
            'confirm_password' => 'required|same:password',
            'user_type' => ['required', 'int', 'min:-1'],
            // 'otp' => 'required|string|size:6'
        ]);

        if ($validator->fails()) {
            return R::error('Validasi gagal', 422, $validator->errors());
        }

        // Verify OTP
        // $otp = Otp::where('identifier', $request->identifier)
        //           ->where('type', 'register')
        //           ->where('code', $request->otp)
        //           ->first();

        // if (!$otp) {
        //     return R::error('Kode OTP tidak valid.', 422);
        // }

        // // Check if OTP is expired
        // if (Carbon::parse($otp->expires_at)->isPast()) {
        //     return R::error('Kode OTP telah kedaluwarsa. Silakan minta kode baru.', 422);
        // }

        // Create user
        $user = User::create([
            'name' => $request->fullname,
            'identifier' => $request->identifier,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        // Delete used OTP
        // $otp->delete();

        // Generate token with 1 year expiration
        $token = $user->createToken('auth_token', ['*'], now()->addYear())->plainTextToken;

        return R::success('Registrasi berhasil', [
            'user' => $user,
            'token' => $token,
            'token_expires_at' => now()->addYear()->toDateTimeString(),
        ], 201);
    }
}
