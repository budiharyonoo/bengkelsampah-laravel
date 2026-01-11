<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Price;
use App\Models\Sampah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Katalog",
 *     description="API endpoints untuk katalog sampah"
 * )
 */
class KatalogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/katalog",
     *     summary="Get katalog sampah dengan kategori dan pencarian",
     *     description="Mendapatkan daftar kategori dan sampah berdasarkan kategori dan pencarian. Jika category kosong, akan menampilkan data dari kategori pertama.",
     *     tags={"Katalog"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="ID kategori yang dipilih (opsional, jika kosong akan menggunakan kategori pertama)",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Kata kunci pencarian (opsional)",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan data katalog",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Katalog berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nama", type="string", example="Sampah Organik"),
     *                         @OA\Property(property="sampah_count", type="integer", example=5)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="sampah",
     *                     type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nama", type="string", example="Botol Plastik"),
     *                         @OA\Property(property="deskripsi", type="string", example="Deskripsi sampah"),
     *                         @OA\Property(property="satuan", type="string", example="kg"),
     *                         @OA\Property(property="gambar", type="string", example="sampah/image.jpg")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="selected_category",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="Sampah Organik")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Parameter tidak valid",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Parameter tidak valid")
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
     *         description="Kategori tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Kategori tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Check if user is authenticated
            $user = auth()->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak terautentikasi. Token tidak diberikan.',
                ], 401);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'category' => 'nullable|integer|exists:categories,id',
                'search' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parameter tidak valid',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Ambil semua kategori dengan sampah count
            $categories = Category::select('id', 'nama')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'nama' => $category->nama,
                        'sampah_count' => $category->sampah_count,
                    ];
                });

            // Tentukan kategori yang akan digunakan
            $selectedCategory = null;

            if ($request->filled('category')) {
                // Jika category dikirim, gunakan category tersebut
                $selectedCategory = Category::find($request->category);

                if (! $selectedCategory) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Kategori tidak ditemukan',
                    ], 404);
                }
            } else {
                // Jika category kosong, gunakan kategori pertama
                $selectedCategory = Category::first();

                if (! $selectedCategory) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tidak ada kategori yang tersedia',
                    ], 404);
                }
            }

            // Ambil sampah dari kategori yang dipilih
            $sampahQuery = Sampah::whereIn('id', $selectedCategory->sampah ?? []);

            // Jika ada search, filter berdasarkan nama sampah
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $sampahQuery->where('nama', 'like', '%'.$searchTerm.'%');
            }

            // Ambil data sampah dengan field yang diperlukan
            $sampah = $sampahQuery->select('id', 'nama', 'deskripsi', 'satuan', 'gambar')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'deskripsi' => $item->deskripsi,
                        'satuan' => strtoupper($item->satuan),
                        'gambar' => $item->gambar ? $item->gambar : null,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Katalog berhasil diambil',
                'data' => [
                    'categories' => $categories,
                    'sampah' => $sampah,
                    'selected_category' => [
                        'id' => $selectedCategory->id,
                        'nama' => $selectedCategory->nama,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in KatalogController@index: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/katalog/{id}",
     *     summary="Get detail sampah dengan harga di berbagai cabang",
     *     description="Mendapatkan detail lengkap sampah beserta daftar harga di berbagai cabang bank sampah",
     *     tags={"Katalog"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID sampah",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail sampah",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Detail sampah berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="sampah",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nama", type="string", example="Botol Plastik"),
     *                     @OA\Property(property="deskripsi", type="string", example="Deskripsi sampah"),
     *                     @OA\Property(property="satuan", type="string", example="kg"),
     *                     @OA\Property(property="gambar", type="string", example="sampah/image.jpg"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(
     *                     property="prices",
     *                     type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="bank_sampah_id", type="integer", example=1),
     *                         @OA\Property(property="bank_sampah_nama", type="string", example="Bank Sampah Indah"),
     *                         @OA\Property(property="bank_sampah_foto", type="string", nullable=true, example="https://example.com/bank_sampah.jpg"),
     *                         @OA\Property(property="bank_sampah_tipe_layanan", type="string", example="keduanya", description="jemput, tempat, atau keduanya"),
     *                         @OA\Property(property="harga", type="number", example=5000),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Sampah tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Sampah tidak ditemukan")
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
    public function show($id)
    {
        try {
            // Check if user is authenticated
            $user = auth()->user();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak terautentikasi. Token tidak diberikan.',
                ], 401);
            }

            // Validasi ID sampah
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|integer|exists:sampah,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID sampah tidak valid',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Ambil detail sampah
            $sampah = Sampah::find($id);

            if (! $sampah) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sampah tidak ditemukan',
                ], 404);
            }

            // Ambil semua harga sampah di berbagai cabang
            $prices = Price::where('sampah_id', $id)
                ->with('bankSampah:id,nama_bank_sampah,foto,tipe_layanan')
                ->get()
                ->map(function ($price) {
                    return [
                        'id' => $price->id,
                        'bank_sampah_id' => $price->bank_sampah_id,
                        'bank_sampah_nama' => $price->bankSampah ? $price->bankSampah->nama_bank_sampah : 'Bank Sampah Tidak Ditemukan',
                        'bank_sampah_foto' => $price->bankSampah ? $price->bankSampah->foto : null,
                        'bank_sampah_tipe_layanan' => $price->bankSampah ? $price->bankSampah->tipe_layanan : null,
                        'harga' => $price->harga,
                        'created_at' => $price->created_at,
                        'updated_at' => $price->updated_at,
                    ];
                });

            // Format data sampah
            $sampahData = [
                'id' => $sampah->id,
                'nama' => $sampah->nama,
                'deskripsi' => $sampah->deskripsi,
                'satuan' => strtoupper($sampah->satuan),
                'gambar' => $sampah->gambar ? $sampah->gambar : null,
                'created_at' => $sampah->created_at,
                'updated_at' => $sampah->updated_at,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Detail sampah berhasil diambil',
                'data' => [
                    'sampah' => $sampahData,
                    'prices' => $prices,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in KatalogController@show: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server',
            ], 500);
        }
    }
}
