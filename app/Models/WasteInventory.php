<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $bank_sampah_id
 * @property int $sampah_id
 * @property float $quantity
 * @property string $unit
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\BankSampah $bankSampah
 * @property-read \App\Models\Sampah $sampah
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory query()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereBankSampahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereSampahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteInventory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WasteInventory extends Model
{
    use HasFactory;

    protected $table = 'waste_inventory';

    protected $fillable = [
        'bank_sampah_id',
        'sampah_id',
        'quantity',
        'unit',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    /**
     * Get the bank sampah that owns this inventory.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class);
    }

    /**
     * Get the sampah type for this inventory.
     */
    public function sampah(): BelongsTo
    {
        return $this->belongsTo(Sampah::class);
    }

    /**
     * Add stock to inventory.
     */
    public function addStock(float $amount): self
    {
        $this->increment('quantity', $amount);

        return $this;
    }

    /**
     * Reduce stock from inventory.
     *
     * @throws \Exception if insufficient stock
     */
    public function reduceStock(float $amount): self
    {
        if ($this->quantity < $amount) {
            throw new \Exception("Stok tidak mencukupi. Tersedia: {$this->quantity}, diminta: {$amount}");
        }

        $this->decrement('quantity', $amount);

        return $this;
    }

    /**
     * Check if sufficient stock is available.
     */
    public function hasStock($amount): bool
    {
        return $this->quantity >= (float) $amount;
    }
}
