<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkDeleteSetoranRequest;
use App\Repositories\SetoranRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * API controller for setoran (transaction) operations in the admin dashboard.
 */
class SetoranApiController extends Controller
{
    public function __construct(private SetoranRepository $setoranRepository)
    {
    }

    /**
     * Bulk delete multiple setoran records.
     */
    public function bulkDestroy(BulkDeleteSetoranRequest $request): JsonResponse
    {
        try {
            $ids = $request->validated('ids');
            $deleted = $this->setoranRepository->bulkDelete($ids);

            return response()->json([
                'success' => true,
                'message' => "{$deleted} setoran berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk delete setoran failed', [
                'ids' => $request->validated('ids'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus setoran.',
            ], 500);
        }
    }
}
