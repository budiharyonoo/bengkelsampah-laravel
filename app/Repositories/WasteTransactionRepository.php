<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\WasteTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Repository for WasteTransaction data access.
 *
 * Handles all database queries related to waste transactions with optimizations:
 * - Caching for expensive queries
 * - Aggregation at database level
 * - Eager loading for relationships
 */
class WasteTransactionRepository
{
    /**
     * Cache duration in seconds (10 minutes).
     */
    private const CACHE_TTL = 600;

    /**
     * Build cache key for consistent caching.
     */
    private function buildCacheKey(string $prefix, ?int $bankSampahId, Carbon $startDate, Carbon $endDate): string
    {
        return "waste_transaction:{$prefix}:{$bankSampahId}:{$startDate->format('Y-m-d')}:{$endDate->format('Y-m-d')}";
    }

    /**
     * Get sales metrics for dashboard.
     *
     * @return array{count: int, total_value: float, total_quantity: float, total_profit: float}
     */
    public function getSalesMetrics(?int $bankSampahId, Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = $this->buildCacheKey('sales_metrics', $bankSampahId, $startDate, $endDate);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId, $startDate, $endDate) {
            $result = WasteTransaction::query()
                ->sales()
                ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw('
                    COUNT(*) as count,
                    COALESCE(SUM(total_value), 0) as total_value,
                    COALESCE(SUM(total_quantity), 0) as total_quantity,
                    COALESCE(SUM(total_value - harga_beli_total), 0) as total_profit
                ')
                ->first();

            return [
                'count' => (int) ($result->count ?? 0),
                'total_value' => (float) ($result->total_value ?? 0),
                'total_quantity' => (float) ($result->total_quantity ?? 0),
                'total_profit' => (float) ($result->total_profit ?? 0),
            ];
        });
    }

    /**
     * Get purchase metrics (total cost of buying waste from users).
     *
     * @return array{total_pembelian: float}
     */
    public function getPurchaseMetrics(?int $bankSampahId, Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = $this->buildCacheKey('purchase_metrics', $bankSampahId, $startDate, $endDate);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId, $startDate, $endDate) {
            $result = WasteTransaction::query()
                ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw('COALESCE(SUM(harga_beli_total), 0) as total_pembelian')
                ->first();

            return [
                'total_pembelian' => (float) ($result->total_pembelian ?? 0),
            ];
        });
    }

    /**
     * Get profit metrics for dashboard.
     *
     * @return array{total_penjualan: float, total_pembelian: float, laba_kotor: float}
     */
    public function getProfitMetrics(?int $bankSampahId, Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = $this->buildCacheKey('profit_metrics', $bankSampahId, $startDate, $endDate);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId, $startDate, $endDate) {
            $result = WasteTransaction::query()
                ->sales()
                ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw('
                    COALESCE(SUM(total_value), 0) as total_penjualan,
                    COALESCE(SUM(harga_beli_total), 0) as total_pembelian
                ')
                ->first();

            $totalPenjualan = (float) ($result->total_penjualan ?? 0);
            $totalPembelian = (float) ($result->total_pembelian ?? 0);

            return [
                'total_penjualan' => $totalPenjualan,
                'total_pembelian' => $totalPembelian,
                'laba_kotor' => $totalPenjualan - $totalPembelian,
            ];
        });
    }

    /**
     * Get processing metrics for dashboard.
     *
     * @return array{count: int, total_quantity: float, total_cost: float}
     */
    public function getProcessingMetrics(?int $bankSampahId, Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = $this->buildCacheKey('processing_metrics', $bankSampahId, $startDate, $endDate);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId, $startDate, $endDate) {
            $result = WasteTransaction::query()
                ->processing()
                ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->selectRaw('
                    COUNT(*) as count,
                    COALESCE(SUM(total_quantity), 0) as total_quantity,
                    COALESCE(SUM(harga_beli_total), 0) as total_cost
                ')
                ->first();

            return [
                'count' => (int) ($result->count ?? 0),
                'total_quantity' => (float) ($result->total_quantity ?? 0),
                'total_cost' => (float) ($result->total_cost ?? 0),
            ];
        });
    }

    /**
     * Get sales grouped by offtaker.
     */
    public function getSalesByOfftaker(?int $bankSampahId, Carbon $startDate, Carbon $endDate): Collection
    {
        return WasteTransaction::query()
            ->sales()
            ->with('offtaker:id,kode_offtaker,nama')
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->select([
                'offtaker_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_quantity) as total_quantity'),
                DB::raw('SUM(total_value) as total_value'),
                DB::raw('SUM(total_value - harga_beli_total) as total_profit'),
            ])
            ->groupBy('offtaker_id')
            ->orderByDesc('total_value')
            ->get();
    }

    /**
     * Get sales grouped by waste type.
     */
    public function getSalesByWasteType(?int $bankSampahId, Carbon $startDate, Carbon $endDate): Collection
    {
        // This requires parsing items_json, so we'll do it in PHP
        $transactions = WasteTransaction::query()
            ->sales()
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->get(['items_json']);

        $wasteTypeTotals = [];

        foreach ($transactions as $transaction) {
            foreach ($transaction->items_json as $item) {
                $sampahId = $item['sampah_id'];

                if (! isset($wasteTypeTotals[$sampahId])) {
                    $wasteTypeTotals[$sampahId] = [
                        'sampah_id' => $sampahId,
                        'quantity' => 0,
                        'value' => 0,
                        'profit' => 0,
                    ];
                }

                $wasteTypeTotals[$sampahId]['quantity'] += $item['quantity'] ?? 0;
                $wasteTypeTotals[$sampahId]['value'] += $item['total'] ?? 0;
                $wasteTypeTotals[$sampahId]['profit'] += $item['profit'] ?? 0;
            }
        }

        return collect($wasteTypeTotals)->sortByDesc('value')->values();
    }

    /**
     * Get processing grouped by method.
     */
    public function getProcessingByMethod(?int $bankSampahId, Carbon $startDate, Carbon $endDate): Collection
    {
        return WasteTransaction::query()
            ->processing()
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->select([
                'metode_pengolahan',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_quantity) as total_quantity'),
            ])
            ->groupBy('metode_pengolahan')
            ->orderByDesc('total_quantity')
            ->get();
    }

    /**
     * Get recent transactions with optional date filtering.
     */
    public function getRecentTransactions(
        ?int $bankSampahId,
        ?Carbon $startDate = null,
        ?Carbon $endDate = null,
        int $limit = 10
    ): Collection {
        return WasteTransaction::query()
            ->with(['bankSampah:id,nama_bank_sampah', 'offtaker:id,nama,kode_offtaker'])
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->when($startDate && $endDate, fn ($q) => $q->whereBetween('tanggal_transaksi', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get daily trend for transactions.
     *
     * @return array{labels: array, sales: array, processing: array}
     */
    public function getDailyTrend(?int $bankSampahId, Carbon $startDate, Carbon $endDate): array
    {
        $transactions = WasteTransaction::query()
            ->when($bankSampahId, fn ($q) => $q->where('bank_sampah_id', $bankSampahId))
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->select([
                'tanggal_transaksi',
                'type',
                DB::raw('SUM(total_value) as value'),
                DB::raw('SUM(total_quantity) as quantity'),
            ])
            ->groupBy('tanggal_transaksi', 'type')
            ->orderBy('tanggal_transaksi')
            ->get();

        $labels = [];
        $sales = [];
        $processing = [];

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');

            $daySales = $transactions->first(fn ($t) => $t->tanggal_transaksi->format('Y-m-d') === $dateStr && $t->type === 'sale');
            $dayProcessing = $transactions->first(fn ($t) => $t->tanggal_transaksi->format('Y-m-d') === $dateStr && $t->type === 'processing');

            $sales[] = (float) ($daySales?->value ?? 0);
            $processing[] = (float) ($dayProcessing?->quantity ?? 0);
        }

        return [
            'labels' => $labels,
            'sales' => $sales,
            'processing' => $processing,
        ];
    }
}
