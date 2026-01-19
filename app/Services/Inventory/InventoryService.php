<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\Setoran;
use App\Models\WasteInventory;
use App\Models\WasteTracking;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service for managing waste inventory with FIFO tracking.
 */
class InventoryService
{
    /**
     * Add stock to inventory when setoran is completed.
     */
    public function addFromSetoran(Setoran $setoran): void
    {
        DB::transaction(function () use ($setoran) {
            $items = is_array($setoran->items_json)
                ? $setoran->items_json
                : json_decode($setoran->items_json, true);

            if (empty($items)) {
                return;
            }

            foreach ($items as $item) {
                // Skip deleted items
                if (isset($item['status']) && $item['status'] === 'dihapus') {
                    continue;
                }

                $quantity = $item['aktual_berat'] ?? $item['estimasi_berat'] ?? 0;
                if ($quantity <= 0) {
                    continue;
                }

                $hargaPerSatuan = $item['harga_per_satuan'] ?? 0;
                $totalHarga = $quantity * $hargaPerSatuan;

                // Create tracking record
                WasteTracking::create([
                    'setoran_id' => $setoran->id,
                    'bank_sampah_id' => $setoran->bank_sampah_id,
                    'sampah_id' => $item['sampah_id'],
                    'quantity' => $quantity,
                    'unit' => $item['sampah_satuan'] ?? 'kg',
                    'harga_beli' => $totalHarga,
                    'status' => WasteTracking::STATUS_STORED,
                ]);

                // Update or create inventory
                WasteInventory::updateOrCreate(
                    [
                        'bank_sampah_id' => $setoran->bank_sampah_id,
                        'sampah_id' => $item['sampah_id'],
                    ],
                    ['unit' => $item['sampah_satuan'] ?? 'kg']
                )->addStock($quantity);
            }
        });
    }

    /**
     * Reduce stock using FIFO method.
     * Returns the tracking records that were consumed with their actual quantities.
     *
     * @param  string  $newStatus  'sold' or 'processed'
     * @return array{trackings: Collection, total_harga_beli: float}
     *
     * @throws \Exception if insufficient stock
     */
    public function reduceStock(
        $bankSampahId,
        $sampahId,
        $quantity,
        $wasteTransactionId,
        string $newStatus = WasteTracking::STATUS_SOLD
    ): array {
        $consumedTrackings = collect();
        $totalHargaBeli = 0;
        $remainingQuantity = $quantity;

        DB::transaction(function () use (
            $bankSampahId,
            $sampahId,
            &$remainingQuantity,
            $wasteTransactionId,
            $newStatus,
            &$consumedTrackings,
            &$totalHargaBeli,
            $quantity,
        ) {
            // Get stored items in FIFO order (oldest first)
            $storedItems = WasteTracking::where('bank_sampah_id', $bankSampahId)
                ->where('sampah_id', $sampahId)
                ->where('status', WasteTracking::STATUS_STORED)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            $totalAvailable = $storedItems->sum('quantity');

            if ($totalAvailable < $remainingQuantity) {
                throw new \Exception(
                    "Stok tidak mencukupi. Tersedia: {$totalAvailable}, diminta: {$remainingQuantity}"
                );
            }

            foreach ($storedItems as $tracking) {
                if ($remainingQuantity <= 0) {
                    break;
                }

                $consumeQuantity = min($tracking->quantity, $remainingQuantity);

                if ($consumeQuantity >= $tracking->quantity) {
                    // Consume entire tracking record
                    $consumedHargaBeli = $tracking->harga_beli;

                    if ($newStatus === WasteTracking::STATUS_SOLD) {
                        $tracking->markAsSold($wasteTransactionId);
                    } else {
                        $tracking->markAsProcessed($wasteTransactionId);
                    }

                    $consumedTrackings->push([
                        'tracking_id' => $tracking->id,
                        'quantity' => $tracking->quantity,
                        'harga_beli' => $consumedHargaBeli,
                    ]);

                    $totalHargaBeli += $consumedHargaBeli;
                    $remainingQuantity -= $tracking->quantity;
                } else {
                    // Partial consume - split the tracking record
                    $ratio = $consumeQuantity / $tracking->quantity;
                    $consumedHargaBeli = $tracking->harga_beli * $ratio;

                    // Update existing record with remaining
                    $tracking->update([
                        'quantity' => $tracking->quantity - $consumeQuantity,
                        'harga_beli' => $tracking->harga_beli - $consumedHargaBeli,
                    ]);

                    // Create new record for consumed portion
                    $newTracking = WasteTracking::create([
                        'setoran_id' => $tracking->setoran_id,
                        'bank_sampah_id' => $tracking->bank_sampah_id,
                        'sampah_id' => $tracking->sampah_id,
                        'quantity' => $consumeQuantity,
                        'unit' => $tracking->unit,
                        'harga_beli' => $consumedHargaBeli,
                        'status' => $newStatus,
                        'sold_at' => $newStatus === WasteTracking::STATUS_SOLD ? now() : null,
                        'processed_at' => $newStatus === WasteTracking::STATUS_PROCESSED ? now() : null,
                        'waste_transaction_id' => $wasteTransactionId,
                    ]);

                    $consumedTrackings->push([
                        'tracking_id' => $newTracking->id,
                        'quantity' => $consumeQuantity,
                        'harga_beli' => $consumedHargaBeli,
                    ]);

                    $totalHargaBeli += $consumedHargaBeli;
                    $remainingQuantity = 0;
                }
            }

            // Update inventory
            $inventory = WasteInventory::where('bank_sampah_id', $bankSampahId)
                ->where('sampah_id', $sampahId)
                ->first();

            if ($inventory) {
                $inventory->reduceStock((float) $quantity);
            }
        });

        return [
            'trackings' => $consumedTrackings,
            'total_harga_beli' => $totalHargaBeli,
        ];
    }

    /**
     * Get available stock for a bank sampah.
     */
    public function getAvailableStock(int $bankSampahId, ?int $sampahId = null): Collection
    {
        $query = WasteInventory::where('bank_sampah_id', $bankSampahId)
            ->where('quantity', '>', 0)
            ->with('sampah');

        if ($sampahId !== null) {
            $query->where('sampah_id', $sampahId);
        }

        return $query->get();
    }

    /**
     * Check if sufficient stock is available.
     */
    public function hasAvailableStock(int $bankSampahId, $sampahId, $quantity): bool
    {
        $inventory = WasteInventory::where('bank_sampah_id', $bankSampahId)
            ->where('sampah_id', $sampahId)
            ->first();

        return $inventory && $inventory->hasStock($quantity);
    }

    /**
     * Get inventory summary for a bank sampah.
     *
     * @return array{total_stored_kg: float, total_stored_value: float, item_count: int}
     */
    public function getInventorySummary(int $bankSampahId): array
    {
        $inventory = WasteInventory::where('bank_sampah_id', $bankSampahId)
            ->where('quantity', '>', 0)
            ->get();

        $totalStoredKg = $inventory->where('unit', 'kg')->sum('quantity');
        $totalStoredUnit = $inventory->where('unit', '!=', 'kg')->sum('quantity');

        // Get total value from tracking records
        $totalValue = WasteTracking::where('bank_sampah_id', $bankSampahId)
            ->where('status', WasteTracking::STATUS_STORED)
            ->sum('harga_beli');

        return [
            'total_stored_kg' => (float) $totalStoredKg,
            'total_stored_unit' => (float) $totalStoredUnit,
            'total_stored_value' => (float) $totalValue,
            'item_count' => $inventory->count(),
        ];
    }

    /**
     * Get tracking summary by status.
     *
     * @return array{stored: float, sold: float, processed: float}
     */
    public function getTrackingSummary(?int $bankSampahId = null): array
    {
        $query = WasteTracking::query();

        if ($bankSampahId !== null) {
            $query->where('bank_sampah_id', $bankSampahId);
        }

        $stored = (clone $query)->where('status', WasteTracking::STATUS_STORED)->sum('quantity');
        $sold = (clone $query)->where('status', WasteTracking::STATUS_SOLD)->sum('quantity');
        $processed = (clone $query)->where('status', WasteTracking::STATUS_PROCESSED)->sum('quantity');

        return [
            'stored' => (float) $stored,
            'sold' => (float) $sold,
            'processed' => (float) $processed,
        ];
    }
}
