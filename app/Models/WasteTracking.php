<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $setoran_id
 * @property int $bank_sampah_id
 * @property int $sampah_id
 * @property float $quantity
 * @property string $unit
 * @property float $harga_beli
 * @property string $status
 * @property \Carbon\Carbon|null $sold_at
 * @property \Carbon\Carbon|null $processed_at
 * @property int|null $waste_transaction_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Setoran $setoran
 * @property-read \App\Models\BankSampah $bankSampah
 * @property-read \App\Models\Sampah $sampah
 * @property-read \App\Models\WasteTransaction|null $wasteTransaction
 */
class WasteTracking extends Model
{
    use HasFactory;

    protected $table = 'waste_tracking';

    public const STATUS_STORED = 'stored';

    public const STATUS_SOLD = 'sold';

    public const STATUS_PROCESSED = 'processed';

    protected $fillable = [
        'setoran_id',
        'bank_sampah_id',
        'sampah_id',
        'quantity',
        'unit',
        'harga_beli',
        'status',
        'sold_at',
        'processed_at',
        'waste_transaction_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'sold_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the setoran that this tracking belongs to.
     */
    public function setoran(): BelongsTo
    {
        return $this->belongsTo(Setoran::class);
    }

    /**
     * Get the bank sampah for this tracking.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class);
    }

    /**
     * Get the sampah type for this tracking.
     */
    public function sampah(): BelongsTo
    {
        return $this->belongsTo(Sampah::class);
    }

    /**
     * Get the waste transaction if sold or processed.
     */
    public function wasteTransaction(): BelongsTo
    {
        return $this->belongsTo(WasteTransaction::class);
    }

    /**
     * Scope to filter stored items only.
     */
    public function scopeStored($query)
    {
        return $query->where('status', self::STATUS_STORED);
    }

    /**
     * Scope to filter sold items only.
     */
    public function scopeSold($query)
    {
        return $query->where('status', self::STATUS_SOLD);
    }

    /**
     * Scope to filter processed items only.
     */
    public function scopeProcessed($query)
    {
        return $query->where('status', self::STATUS_PROCESSED);
    }

    /**
     * Get the status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_STORED => 'Tersimpan',
            self::STATUS_SOLD => 'Terjual',
            self::STATUS_PROCESSED => 'Diolah',
            default => $this->status,
        };
    }

    /**
     * Mark as sold.
     */
    public function markAsSold(int $wasteTransactionId): self
    {
        $this->update([
            'status' => self::STATUS_SOLD,
            'sold_at' => now(),
            'waste_transaction_id' => $wasteTransactionId,
        ]);

        return $this;
    }

    /**
     * Mark as processed.
     */
    public function markAsProcessed(int $wasteTransactionId): self
    {
        $this->update([
            'status' => self::STATUS_PROCESSED,
            'processed_at' => now(),
            'waste_transaction_id' => $wasteTransactionId,
        ]);

        return $this;
    }
}
