<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class AddressController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/addresses",
     *     summary="Tambah alamat baru",
     *     description="Tambah alamat baru untuk pengguna yang terautentikasi",
     *     operationId="addAddress",
     *     tags={"Alamat"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"nama", "nomor_handphone", "label_alamat", "provinsi", "kota_kabupaten", "kecamatan", "kode_pos", "is_default"},
     *
     *             @OA\Property(property="nama", type="string", example="John Doe"),
     *             @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *             @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *             @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *             @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *             @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *             @OA\Property(property="kode_pos", type="string", example="12120"),
     *             @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *             @OA\Property(property="is_default", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Alamat berhasil dibuat",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Alamat berhasil dibuat"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nama", type="string", example="John Doe"),
     *                 @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *                 @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *                 @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *                 @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *                 @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *                 @OA\Property(property="kode_pos", type="string", example="12120"),
     *                 @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *                 @OA\Property(property="is_default", type="boolean", example=true)
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
     *                 @OA\Property(property="nama", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="nomor_handphone", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="label_alamat", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="provinsi", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="kota_kabupaten", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="kecamatan", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="kode_pos", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="is_default", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
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
        $validator = validator($request->all(), [
            'nama' => 'required|string|max:255',
            'nomor_handphone' => 'required|string|max:20',
            'label_alamat' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kota_kabupaten' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
            'detail_lain' => 'nullable|string',
            'is_default' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // If new address is default, unset other default addresses
            if ($request->is_default) {
                $user->addresses()->update(['is_default' => false]);
            }

            // Create new address
            $address = $user->addresses()->create($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Alamat berhasil dibuat',
                'data' => $address,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat alamat.',
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/addresses/{id}",
     *     summary="Perbarui alamat",
     *     description="Perbarui alamat yang sudah ada untuk pengguna yang terautentikasi",
     *     operationId="updateAddress",
     *     tags={"Alamat"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Alamat",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="nama", type="string", example="John Doe"),
     *             @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *             @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *             @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *             @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *             @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *             @OA\Property(property="kode_pos", type="string", example="12120"),
     *             @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *             @OA\Property(property="is_default", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Alamat berhasil diperbarui",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Alamat berhasil diperbarui"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nama", type="string", example="John Doe"),
     *                 @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *                 @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *                 @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *                 @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *                 @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *                 @OA\Property(property="kode_pos", type="string", example="12120"),
     *                 @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *                 @OA\Property(property="is_default", type="boolean", example=true)
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
     *         description="Alamat tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Alamat tidak ditemukan.")
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
     *                 @OA\Property(property="nama", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="nomor_handphone", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="label_alamat", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="provinsi", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="kota_kabupaten", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="kecamatan", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="kode_pos", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="is_default", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
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

        // Find address
        $address = $user->addresses()->find($id);
        if (! $address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat tidak ditemukan.',
            ], 404);
        }

        // Validate request
        $validator = validator($request->all(), [
            'nama' => 'sometimes|string|max:255',
            'nomor_handphone' => 'sometimes|string|max:20',
            'label_alamat' => 'sometimes|string|max:100',
            'provinsi' => 'sometimes|string|max:100',
            'kota_kabupaten' => 'sometimes|string|max:100',
            'kecamatan' => 'sometimes|string|max:100',
            'kode_pos' => 'sometimes|string|max:10',
            'detail_lain' => 'nullable|string',
            'is_default' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // If address is being set as default, unset other default addresses
            if ($request->has('is_default') && $request->is_default) {
                $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
            }

            // Update address
            $address->update($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Alamat berhasil diperbarui',
                'data' => $address,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui alamat.',
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/addresses/{id}",
     *     summary="Hapus alamat",
     *     description="Hapus alamat yang sudah ada untuk pengguna yang terautentikasi",
     *     operationId="deleteAddress",
     *     tags={"Alamat"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Alamat",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Alamat berhasil dihapus",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Alamat berhasil dihapus")
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
     *         description="Alamat tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Alamat tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
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

        // Find address
        $address = $user->addresses()->find($id);
        if (! $address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat tidak ditemukan.',
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Delete address
            $address->delete();

            // If deleted address was default, set another address as default
            if ($address->is_default) {
                $newDefault = $user->addresses()->first();
                if ($newDefault) {
                    $newDefault->update(['is_default' => true]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Alamat berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus alamat.',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     summary="Dapatkan semua alamat",
     *     description="Dapatkan semua alamat untuk pengguna yang terautentikasi",
     *     operationId="getAllAddresses",
     *     tags={"Alamat"},
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
     *                     @OA\Property(property="nama", type="string", example="John Doe"),
     *                     @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *                     @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *                     @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *                     @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *                     @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *                     @OA\Property(property="kode_pos", type="string", example="12120"),
     *                     @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *                     @OA\Property(property="is_default", type="boolean", example=true)
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

        // Get all addresses
        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/addresses/{id}",
     *     summary="Dapatkan detail alamat",
     *     description="Dapatkan detail alamat tertentu untuk pengguna yang terautentikasi",
     *     operationId="getAddressDetail",
     *     tags={"Alamat"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID Alamat",
     *
     *         @OA\Schema(type="integer")
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
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nama", type="string", example="John Doe"),
     *                 @OA\Property(property="nomor_handphone", type="string", example="081234567890"),
     *                 @OA\Property(property="label_alamat", type="string", example="Rumah"),
     *                 @OA\Property(property="provinsi", type="string", example="DKI Jakarta"),
     *                 @OA\Property(property="kota_kabupaten", type="string", example="Jakarta Selatan"),
     *                 @OA\Property(property="kecamatan", type="string", example="Kebayoran Baru"),
     *                 @OA\Property(property="kode_pos", type="string", example="12120"),
     *                 @OA\Property(property="detail_lain", type="string", example="Jl. Kebayoran Baru No. 123"),
     *                 @OA\Property(property="is_default", type="boolean", example=true)
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
     *         description="Alamat tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Alamat tidak ditemukan.")
     *         )
     *     )
     * )
     */
    public function show(Request $request, $id)
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

        // Find address
        $address = $user->addresses()->find($id);
        if (! $address) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alamat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $address,
        ]);
    }
}
