<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use App\Helpers\ResponseHelper as R;

class OtpController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/send-otp",
     *     tags={"Autentikasi"},
     *     summary="Kirim OTP ke email atau WhatsApp",
     *     description="Endpoint untuk mengirim kode OTP ke email atau nomor WhatsApp pengguna. OTP berlaku selama 5 menit dan maksimal 10 permintaan per jam. Untuk tipe register, identifier harus belum terdaftar. Untuk tipe login dan forgot, identifier harus sudah terdaftar.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"identifier","type"},
     *             @OA\Property(
     *                 property="identifier",
     *                 type="string",
     *                 example="john@example.com",
     *                 description="Email atau nomor telepon Indonesia"
     *             ),
     *             @OA\Property(
     *                 property="type",
     *                 type="string",
     *                 enum={"register","login","forgot","change"},
     *                 example="register",
     *                 description="Tipe OTP: register untuk pendaftaran, login untuk login, forgot untuk lupa kata sandi, change untuk perubahan identifier"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP berhasil dikirim",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP berhasil dikirim"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="expires_at", type="string", format="date-time", example="2024-03-20T10:30:00Z")
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
     *                         ),
     *                         @OA\Property(
     *                             property="type",
     *                             type="array",
     *                             @OA\Items(type="string", example="Field tipe wajib diisi.")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Identifier sudah terdaftar.")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="status", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Identifier tidak terdaftar.")
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=429,
     *         description="Terlalu banyak permintaan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Maksimal 10 OTP per jam. Silakan coba nanti.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Kesalahan server",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Gagal mengirim OTP")
     *         )
     *     )
     * )
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return R::error('Validasi gagal', 422, $validator->errors());
        }

        $identifier = $request->identifier;
        $type = $request->type;

        // Cek apakah user sudah terdaftar untuk tipe register
        if ($type === 'register' || $type === 'change') {
            $userExists = User::where('identifier', $identifier)->exists();
            if ($userExists) {
                return R::error('Identifier sudah terdaftar', 422);
            }
        }
        // Cek apakah user terdaftar untuk tipe login dan forgot
        else {
            $userExists = User::where('identifier', $identifier)->exists();
            if (!$userExists) {
                return R::error('Identifier tidak terdaftar', 422);
            }
        }

        $existing = Otp::where('identifier', $identifier)->first();

        // Jika belum ada OTP, buat baru
        if (!$existing) {
            return $this->createOtp($identifier, $type, 1);
        }

        // Cek apakah masih dalam 1 jam
        $created = Carbon::parse($existing->created_at);

        if ($created->diffInHours(now()) < 1) {
            if ($existing->count >= 10) {
                return R::error('Maksimal 10 OTP per jam. Silakan coba nanti', 429);
            }

            // Tambah count + update OTP
            $newCode = rand(100000, 999999);
            $existing->update([
                'code' => $newCode,
                'type' => $type,
                'expires_at' => now()->addMinutes(5),
                'count' => $existing->count + 1,
                'created_at' => now(), // optional: reset timestamp
            ]);
        } else {
            // Reset count karena sudah lewat 1 jam
            $newCode = rand(100000, 999999);
            $existing->update([
                'code' => $newCode,
                'type' => $type,
                'expires_at' => now()->addMinutes(5),
                'count' => 1,
                'created_at' => now(),
            ]);
        }

        // Kirim OTP
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $this->sendEmail($identifier, $existing->code);
        } else {
            $this->sendWhatsApp($identifier, $existing->code);
        }

        return R::success('OTP berhasil dikirim', [
            'expires_at' => $existing->expires_at
        ]);
    }

    private function createOtp($identifier, $type, $count)
    {
        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        Otp::create([
            'identifier' => $identifier,
            'type' => $type,
            'code' => $code,
            'created_at' => now(),
            'expires_at' => $expiresAt,
            'count' => $count,
        ]);

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $this->sendEmail($identifier, $code);
        } else {
            $this->sendWhatsApp($identifier, $code);
        }

        return R::success('OTP berhasil dikirim', [
            'expires_at' => $expiresAt
        ]);
    }

    private function sendEmail($email, $code)
    {
        $message = <<<TEXT
            Halo,

            Berikut adalah kode OTP Anda untuk verifikasi di Bengkel Sampah:

            Kode OTP: $code

            Kode ini berlaku selama 5 menit. Jangan bagikan kepada siapa pun demi keamanan akun Anda.

            Terima kasih,
            Tim Bengkel Sampah
            TEXT;

        \Mail::raw($message, function ($message) use ($email) {
            $message->to($email)
                    ->subject('Kode OTP Bengkel Sampah');
        });
    }

    private function sendWhatsApp($phone, $code)
    {
        $userkey = env('ZENZIVA_USERKEY');
        $passkey = env('ZENZIVA_PASSKEY');
        $brand   = env('ZENZIVA_BRAND');

        $url = 'https://console.zenziva.net/waofficial/api/sendWAOfficial/';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, [
            'userkey' => $userkey,
            'passkey' => $passkey,
            'to'      => $phone,
            'brand'   => $brand,
            'otp'     => $code,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);

        // Cek status
        if (!isset($result['status']) || $result['status'] != '1') {
            \Log::error("Gagal kirim OTP via WA ke $phone", [
                'response' => $result,
                'raw' => $response,
            ]);
        }
    }

    /**
     * Get Zenziva balance for OTP account
     *
     * @return array|null
     */
    public function getZenzivaOtpBalance()
    {
        $userkey = env('ZENZIVA_USERKEY');
        $passkey = env('ZENZIVA_PASSKEY');
        $url = 'https://console.zenziva.net/api/balance/?userkey=' . $userkey . '&passkey=' . $passkey;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response, true);
        return $result;
    }
}
