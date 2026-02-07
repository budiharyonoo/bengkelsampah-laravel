<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\WasteInventory;
use App\Models\WasteTracking;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Repository for WasteInventory data access.
 *
 * Handles all database queries related to inventory with optimizations:
 * - Caching for expensive queries
 * - Aggregation at database level
 * - Eager loading for relationships
 */
class InventoryRepository
{
    /**
     * Cache duration in seconds (10 minutes).
     */
    private const CACHE_TTL = 600;

    /**
     * Get inventory by bank sampah.
     */
    public function getInventoryByBankSampah(int $bankSampahId): Collection
    {
        return WasteInventory::query()
            ->with('sampah:id,nama,satuan,gambar')
            ->where('bank_sampah_id', $bankSampahId)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'desc')
            ->get();
    }

    /**
     * Get all inventory grouped by bank sampah.
     */
    public function getAllInventory(): Collection
    {
        return WasteInventory::query()
            ->with(['bankSampah:id,kode_bank_sampah,nama_bank_sampah', 'sampah:id,nama,satuan'])
            ->where('quantity', '>', 0)
            ->orderBy('bank_sampah_id')
            ->get()
            ->groupBy('bank_sampah_id');
    }

    /**
     * Get inventory summary (total stored/sold/processed).
     *
     * @return array{
     *   stored_kg: float,
     *   stored_value: float,
     *   sold_kg: float,
     *   sold_value: float,
     *   processed_kg: float,
     *   processed_value: float
     * }
     */
    public function getInventorySummary(?int $bankSampahId = null): array
    {
        $cacheKey = "inventory:summary:{$bankSampahId}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId) {
            $query = WasteTracking::query()
                ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId));

            $stored = (clone $query)->stored()->selectRaw('
                COALESCE(SUM(quantity), 0) as quantity,
                COALESCE(SUM(harga_beli), 0) as value
            ')->first();

            $sold = (clone $query)->sold()->selectRaw('
                COALESCE(SUM(quantity), 0) as quantity,
                COALESCE(SUM(harga_beli), 0) as value
            ')->first();

            $processed = (clone $query)->processed()->selectRaw('
                COALESCE(SUM(quantity), 0) as quantity,
                COALESCE(SUM(harga_beli), 0) as value
            ')->first();

            return [
                'stored_kg' => (float) ($stored->quantity ?? 0),
                'stored_value' => (float) ($stored->value ?? 0),
                'sold_kg' => (float) ($sold->quantity ?? 0),
                'sold_value' => (float) ($sold->value ?? 0),
                'processed_kg' => (float) ($processed->quantity ?? 0),
                'processed_value' => (float) ($processed->value ?? 0),
            ];
        });
    }

    /**
     * Get low stock alerts.
     */
    public function getLowStockAlerts(float $threshold = 10.0): Collection
    {
        return WasteInventory::query()
            ->with(['bankSampah:id,nama_bank_sampah', 'sampah:id,nama'])
            ->where('quantity', '>', 0)
            ->where('quantity', '<', $threshold)
            ->orderBy('quantity')
            ->get();
    }

    /**
     * Get inventory trends over time.
     *
     * @return array{labels: array, stored: array, sold: array, processed: array}
     */
    public function getInventoryTrend(?int $bankSampahId, Carbon $startDate, Carbon $endDate): array
    {
        $tracking = WasteTracking::query()
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('SUM(quantity) as quantity'),
            ])
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $labels = [];
        $stored = [];
        $sold = [];
        $processed = [];

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $dayStored = $tracking->first(fn ($t) => $t->date === $dateStr && $t->status === 'stored');
            $daySold = $tracking->first(fn ($t) => $t->date === $dateStr && $t->status === 'sold');
            $dayProcessed = $tracking->first(fn ($t) => $t->date === $dateStr && $t->status === 'processed');

            $stored[] = (float) ($dayStored?->quantity ?? 0);
            $sold[] = (float) ($daySold?->quantity ?? 0);
            $processed[] = (float) ($dayProcessed?->quantity ?? 0);
        }

        return [
            'labels' => $labels,
            'stored' => $stored,
            'sold' => $sold,
            'processed' => $processed,
        ];
    }

    /**
     * Get top waste types by inventory.
     */
    public function getTopWasteTypes(?int $bankSampahId = null, int $limit = 5): Collection
    {
        return WasteInventory::query()
            ->with('sampah:id,nama,gambar')
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->where('quantity', '>', 0)
            ->orderByDesc('quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get inventory grouped by category for a bank sampah.
     *
     * @return \Illuminate\Support\Collection Collection of objects with category info and items
     */
    public function getInventoryGroupedByCategory(?int $bankSampahId = null): Collection
    {
        $query = WasteInventory::query()
            ->with(['sampah:id,nama,satuan,gambar', 'bankSampah:id,kode_bank_sampah,nama_bank_sampah'])
            ->where('quantity', '>', 0);

        if ($bankSampahId) {
            $query->where('bank_sampah_id', $bankSampahId);
        }

        $inventoryItems = $query->get();

        // Get all categories
        $categories = Category::all();

        // Group items by category
        $grouped = collect();

        foreach ($categories as $category) {
            // Get inventory items that belong to this category
            $categoryItems = $inventoryItems->filter(function ($item) use ($category) {
                return in_array($item->sampah_id, $category->sampah ?? []);
            });

            if ($categoryItems->isNotEmpty()) {
                $grouped->push((object) [
                    'category' => $category,
                    'items' => $categoryItems,
                    'total_quantity' => $categoryItems->sum('quantity'),
                    'item_count' => $categoryItems->count(),
                ]);
            }
        }

        // Handle items without category
        $itemsWithoutCategory = $inventoryItems->filter(function ($item) use ($categories) {
            foreach ($categories as $category) {
                if (in_array($item->sampah_id, $category->sampah ?? [])) {
                    return false;
                }
            }

            return true;
        });

        if ($itemsWithoutCategory->isNotEmpty()) {
            $grouped->push((object) [
                'category' => (object) ['id' => null, 'nama' => 'Lainnya'],
                'items' => $itemsWithoutCategory,
                'total_quantity' => $itemsWithoutCategory->sum('quantity'),
                'item_count' => $itemsWithoutCategory->count(),
            ]);
        }

        return $grouped;
    }

    /**
     * Get all inventory grouped by bank sampah, then by category.
     *
     * @return \Illuminate\Support\Collection Collection grouped by bank_sampah_id
     */
    public function getAllInventoryGroupedByCategory(): Collection
    {
        $inventoryItems = WasteInventory::query()
            ->with(['bankSampah:id,kode_bank_sampah,nama_bank_sampah', 'sampah:id,nama,satuan,gambar'])
            ->where('quantity', '>', 0)
            ->orderBy('bank_sampah_id')
            ->get();

        $categories = Category::all();
        $bankSampahIds = $inventoryItems->pluck('bank_sampah_id')->unique();

        $result = collect();

        foreach ($bankSampahIds as $bsId) {
            $bankInventory = $inventoryItems->where('bank_sampah_id', $bsId);
            $categoryGrouped = collect();

            foreach ($categories as $category) {
                $categoryItems = $bankInventory->filter(function ($item) use ($category) {
                    return in_array($item->sampah_id, $category->sampah ?? []);
                });

                if ($categoryItems->isNotEmpty()) {
                    $categoryGrouped->push((object) [
                        'category' => $category,
                        'items' => $categoryItems,
                        'total_quantity' => $categoryItems->sum('quantity'),
                        'item_count' => $categoryItems->count(),
                    ]);
                }
            }

            // Handle items without category
            $itemsWithoutCategory = $bankInventory->filter(function ($item) use ($categories) {
                foreach ($categories as $category) {
                    if (in_array($item->sampah_id, $category->sampah ?? [])) {
                        return false;
                    }
                }

                return true;
            });

            if ($itemsWithoutCategory->isNotEmpty()) {
                $categoryGrouped->push((object) [
                    'category' => (object) ['id' => null, 'nama' => 'Lainnya'],
                    'items' => $itemsWithoutCategory,
                    'total_quantity' => $itemsWithoutCategory->sum('quantity'),
                    'item_count' => $itemsWithoutCategory->count(),
                ]);
            }

            $result->put($bsId, $categoryGrouped);
        }

        return $result;
    }

    /**
     * Get inventory value by waste type.
     */
    public function getInventoryValueByType(?int $bankSampahId = null): Collection
    {
        $query = WasteTracking::query()
            ->stored()
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId));

        return $query
            ->select([
                'sampah_id',
                DB::raw('SUM(quantity) as quantity'),
                DB::raw('SUM(harga_beli) as value'),
            ])
            ->groupBy('sampah_id')
            ->orderByDesc('value')
            ->get();
    }
}
