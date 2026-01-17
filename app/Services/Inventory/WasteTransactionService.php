<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Admin;
use App\Models\WasteTracking;
use App\Models\WasteTransaction;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing waste sales and processing transactions.
 */
class WasteTransactionService
{
    public function __construct(
        private InventoryService $inventoryService
    ) {}

    /**
     * Create a sale transaction.
     *
     * @param  array{
     *   bank_sampah_id: int,
     *   offtaker_id: int,
     *   items: array<array{sampah_id: int, quantity: float, harga_jual: float}>,
     *   tanggal_transaksi: string,
     *   struk?: string|null,
     *   notes?: string|null
     * }  $data
     *
     * @throws \Exception if insufficient stock
     */
    public function createSale(array $data, Admin $admin): WasteTransaction
    {
        return DB::transaction(function () use ($data, $admin) {
            // First validate all stock availability
            $this->validateStockAvailability($data['bank_sampah_id'], $data['items']);

            // Create transaction first to get ID
            $transaction = WasteTransaction::create([
                'bank_sampah_id' => $data['bank_sampah_id'],
                'offtaker_id' => $data['offtaker_id'],
                'type' => WasteTransaction::TYPE_SALE,
                'items_json' => [],
                'total_quantity' => 0,
                'total_value' => 0,
                'harga_beli_total' => 0,
                'struk' => $data['struk'] ?? null,
                'notes' => $data['notes'] ?? null,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'tanggal_transaksi' => $data['tanggal_transaksi'],
            ]);

            $itemsJson = [];
            $totalQuantity = 0;
            $totalValue = 0;
            $totalHargaBeli = 0;

            foreach ($data['items'] as $item) {
                // Reduce stock using FIFO
                $result = $this->inventoryService->reduceStock(
                    $data['bank_sampah_id'],
                    $item['sampah_id'],
                    $item['quantity'],
                    $transaction->id,
                    WasteTracking::STATUS_SOLD
                );

                $itemTotal = $item['quantity'] * $item['harga_jual'];

                $itemsJson[] = [
                    'sampah_id' => $item['sampah_id'],
                    'quantity' => $item['quantity'],
                    'harga_jual' => $item['harga_jual'],
                    'total' => $itemTotal,
                    'harga_beli' => $result['total_harga_beli'],
                    'profit' => $itemTotal - $result['total_harga_beli'],
                ];

                $totalQuantity += $item['quantity'];
                $totalValue += $itemTotal;
                $totalHargaBeli += $result['total_harga_beli'];
            }

            // Update transaction with calculated values
            $transaction->update([
                'items_json' => $itemsJson,
                'total_quantity' => $totalQuantity,
                'total_value' => $totalValue,
                'harga_beli_total' => $totalHargaBeli,
            ]);

            return $transaction->fresh();
        });
    }

    /**
     * Create a processing transaction.
     *
     * @param  array{
     *   bank_sampah_id: int,
     *   offtaker_id: int,
     *   items: array<array{sampah_id: int, quantity: float}>,
     *   metode_pengolahan: string,
     *   hasil_pengolahan?: string|null,
     *   tanggal_transaksi: string,
     *   notes?: string|null
     * }  $data
     *
     * @throws \Exception if insufficient stock
     */
    public function createProcessing(array $data, Admin $admin): WasteTransaction
    {
        return DB::transaction(function () use ($data, $admin) {
            // First validate all stock availability
            $this->validateStockAvailability($data['bank_sampah_id'], $data['items']);

            // Create transaction first to get ID
            $transaction = WasteTransaction::create([
                'bank_sampah_id' => $data['bank_sampah_id'],
                'offtaker_id' => $data['offtaker_id'],
                'type' => WasteTransaction::TYPE_PROCESSING,
                'items_json' => [],
                'total_quantity' => 0,
                'total_value' => 0, // Processing doesn't have sale value
                'harga_beli_total' => 0,
                'metode_pengolahan' => $data['metode_pengolahan'],
                'hasil_pengolahan' => $data['hasil_pengolahan'] ?? null,
                'notes' => $data['notes'] ?? null,
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'tanggal_transaksi' => $data['tanggal_transaksi'],
            ]);

            $itemsJson = [];
            $totalQuantity = 0;
            $totalHargaBeli = 0;

            foreach ($data['items'] as $item) {
                // Reduce stock using FIFO
                $result = $this->inventoryService->reduceStock(
                    $data['bank_sampah_id'],
                    $item['sampah_id'],
                    $item['quantity'],
                    $transaction->id,
                    WasteTracking::STATUS_PROCESSED
                );

                $itemsJson[] = [
                    'sampah_id' => $item['sampah_id'],
                    'quantity' => $item['quantity'],
                    'harga_beli' => $result['total_harga_beli'],
                ];

                $totalQuantity += $item['quantity'];
                $totalHargaBeli += $result['total_harga_beli'];
            }

            // Update transaction with calculated values
            $transaction->update([
                'items_json' => $itemsJson,
                'total_quantity' => $totalQuantity,
                'harga_beli_total' => $totalHargaBeli,
            ]);

            return $transaction->fresh();
        });
    }

    /**
     * Validate stock availability for all items.
     *
     * @param  array<array{sampah_id: int, quantity: float}>  $items
     *
     * @throws \Exception if any item has insufficient stock
     */
    private function validateStockAvailability(int $bankSampahId, array $items): void
    {
        foreach ($items as $item) {
            if (! $this->inventoryService->hasAvailableStock(
                $bankSampahId,
                (int) $item['sampah_id'],
                $item['quantity']
            )) {
                throw new \Exception(
                    "Stok tidak mencukupi untuk sampah ID {$item['sampah_id']}"
                );
            }
        }
    }

    /**
     * Calculate profit for a transaction.
     */
    public function calculateProfit(WasteTransaction $transaction): float
    {
        return $transaction->getProfit();
    }

    /**
     * Get transaction summary by type.
     *
     * @return array{sales_count: int, sales_value: float, processing_count: int, processing_quantity: float}
     */
    public function getTransactionSummary(?int $bankSampahId = null): array
    {
        $query = WasteTransaction::query();

        if ($bankSampahId !== null) {
            $query->where('bank_sampah_id', $bankSampahId);
        }

        $sales = (clone $query)->sales()->selectRaw('COUNT(*) as count, COALESCE(SUM(total_value), 0) as value')->first();
        $processing = (clone $query)->processing()->selectRaw('COUNT(*) as count, COALESCE(SUM(total_quantity), 0) as quantity')->first();

        return [
            'sales_count' => (int) ($sales->count ?? 0),
            'sales_value' => (float) ($sales->value ?? 0),
            'processing_count' => (int) ($processing->count ?? 0),
            'processing_quantity' => (float) ($processing->quantity ?? 0),
        ];
    }
}
