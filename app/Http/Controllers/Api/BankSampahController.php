<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankSampah;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class BankSampahController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/bank-sampah",
     *     summary="Dapatkan daftar bank sampah",
     *     description="Dapatkan daftar semua bank sampah yang tersedia",
     *     operationId="getBankSampahList",
     *     tags={"Bank Sampah"},
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
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="kode_bank_sampah", type="string", example="BS-001"),
     *                     @OA\Property(property="nama_bank_sampah", type="string", example="Bank Sampah Hijau"),
     *                     @OA\Property(property="alamat_bank_sampah", type="string", example="Jl. Merdeka No. 123"),
     *                     @OA\Property(property="nama_penanggung_jawab", type="string", example="John Doe"),
     *                     @OA\Property(property="kontak_penanggung_jawab", type="string", example="081234567890")
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
        // Get all bank sampah ordered by kode
        $bankSampah = BankSampah::orderBy('kode_bank_sampah', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $bankSampah,
        ]);
    }
}
