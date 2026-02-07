<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/artikels",
     *     summary="Dapatkan daftar artikel",
     *     description="Mengembalikan daftar artikel dengan paginasi, diurutkan dari yang terbaru",
     *     operationId="getArticles",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Nomor halaman untuk paginasi",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="bank_sampah_id",
     *         in="query",
     *         description="Filter by bank sampah ID (optional). Returns bank-specific articles only.",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
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
     *                 @OA\Property(
     *                     property="artikels",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Tips Memilah Sampah"),
     *                         @OA\Property(property="content", type="string", example="Konten artikel..."),
     *                         @OA\Property(property="cover", type="string", example="https://example.com/image.jpg"),
     *                         @OA\Property(property="kategori_id", type="integer", example=1),
     *                         @OA\Property(property="creator", type="string", example="Admin"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(
     *                             property="kategori",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="nama", type="string", example="Tips & Trik")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=50),
     *                     @OA\Property(property="has_more_pages", type="boolean", example=true)
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
        $query = Artikel::with('kategori');

        // Apply bank sampah filter if provided
        if ($request->filled('bank_sampah_id')) {
            $bankSampahId = (int) $request->bank_sampah_id;
            $query->where('bank_sampah_id', $bankSampahId)
                ->orWhere('bank_sampah_id', null);
        } else {
            $query->whereNull('bank_sampah_id');
        }

        $query->orderBy('created_at', 'desc');

        $artikels = $query->paginate(10);

        // Transform the articles to limit content
        $transformedArticles = collect($artikels->items())->map(function ($artikel) {
            return [
                'id' => $artikel->id,
                'title' => $artikel->title,
                'content' => Str::limit(strip_tags($artikel->content), 70),
                'cover' => $artikel->cover,
                'kategori_id' => $artikel->kategori_id,
                'creator' => $artikel->creator,
                'created_at' => $artikel->created_at,
                'updated_at' => $artikel->updated_at,
                'kategori' => $artikel->kategori,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'artikels' => $transformedArticles,
                'pagination' => [
                    'current_page' => $artikels->currentPage(),
                    'last_page' => $artikels->lastPage(),
                    'per_page' => $artikels->perPage(),
                    'total' => $artikels->total(),
                    'has_more_pages' => $artikels->hasMorePages(),
                ],
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/artikels/{id}",
     *     summary="Dapatkan detail artikel",
     *     description="Mengembalikan informasi detail tentang artikel tertentu",
     *     operationId="getArticleDetails",
     *     tags={"Articles"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID artikel",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
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
     *                 @OA\Property(
     *                     property="artikel",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Tips Memilah Sampah"),
     *                     @OA\Property(property="content", type="string", example="Konten lengkap artikel..."),
     *                     @OA\Property(property="cover", type="string", example="https://example.com/image.jpg"),
     *                     @OA\Property(property="kategori_id", type="integer", example=1),
     *                     @OA\Property(property="creator", type="string", example="Admin"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(
     *                         property="kategori",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="nama", type="string", example="Tips & Trik")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Artikel tidak ditemukan",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Artikel tidak ditemukan")
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
        $artikel = Artikel::with('kategori')->find($id);

        if (! $artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'artikel' => $artikel,
            ],
        ]);
    }
}
