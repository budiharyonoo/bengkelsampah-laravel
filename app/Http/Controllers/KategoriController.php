<?php

namespace App\Http\Controllers;

use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KategoriController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $kategori = KategoriArtikel::create([
                'nama' => $request->nama,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan',
                'category' => $kategori,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating category: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kategori: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = KategoriArtikel::withCount('artikels')->find($id);

            if (! $kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            // Check if category has articles
            if ($kategori->artikels_count > 0) {
                return response()->json([
                    'success' => false,
                    'has_articles' => true,
                    'message' => "Kategori '{$kategori->nama}' masih digunakan oleh {$kategori->artikels_count} artikel. Jika dihapus, semua artikel yang menggunakan kategori ini akan ikut terhapus.",
                    'article_count' => $kategori->artikels_count,
                    'category_name' => $kategori->nama,
                ], 200); // Changed from 409 to 200 for better frontend handling
            }

            // If no articles, proceed with deletion
            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting category: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: '.$e->getMessage(),
            ], 500);
        }
    }

    public function forceDestroy($id)
    {
        try {
            // Use database transaction for data consistency
            return DB::transaction(function () use ($id) {
                $kategori = KategoriArtikel::with('artikels')->find($id);

                if (! $kategori) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kategori tidak ditemukan',
                    ], 404);
                }

                // Count articles before deletion
                $deletedArticlesCount = $kategori->artikels()->count();
                $categoryName = $kategori->nama;

                // Delete all articles that use this category first
                if ($deletedArticlesCount > 0) {
                    $kategori->artikels()->delete();
                }

                // Delete the category
                $kategori->delete();

                // Log the successful deletion
                Log::info("Category '{$categoryName}' and {$deletedArticlesCount} associated articles deleted successfully");

                return response()->json([
                    'success' => true,
                    'message' => "Kategori '{$categoryName}' dan {$deletedArticlesCount} artikel yang menggunakannya berhasil dihapus",
                    'deleted_articles_count' => $deletedArticlesCount,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Error force deleting category: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: '.$e->getMessage(),
            ], 500);
        }
    }
}
