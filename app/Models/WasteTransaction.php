<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $kode_transaksi
 * @property int $bank_sampah_id
 * @property int $offtaker_id
 * @property string $type
 * @property array $items_json
 * @property float $total_quantity
 * @property float $total_value
 * @property float $harga_beli_total
 * @property string|null $struk
 * @property string|null $metode_pengolahan
 * @property string|null $hasil_pengolahan
 * @property string|null $notes
 * @property int $admin_id
 * @property string $admin_name
 * @property \Carbon\Carbon $tanggal_transaksi
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\BankSampah $bankSampah
 * @property-read \App\Models\Offtaker $offtaker
 * @property-read \App\Models\Admin $admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WasteTracking> $trackings
 * @property-read string $type_text
 * @property-read int|null $trackings_count
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction processing()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction sales()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction salesAndProcessing()
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereBankSampahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereHargaBeliTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereHasilPengolahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereItemsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereKodeTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereMetodePengolahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereOfftakerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereStruk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereTanggalTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereTotalQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WasteTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WasteTransaction extends Model
{
    use HasFactory;

    protected $table = 'waste_transactions';

    public const TYPE_SALE = 'sale';

    public const TYPE_PROCESSING = 'processing';

    protected $fillable = [
        'bank_sampah_id',
        'offtaker_id',
        'type',
        'items_json',
        'total_quantity',
        'total_value',
        'harga_beli_total',
        'struk',
        'metode_pengolahan',
        'hasil_pengolahan',
        'notes',
        'admin_id',
        'admin_name',
        'tanggal_transaksi',
    ];

    protected $casts = [
        'items_json' => 'array',
        'total_quantity' => 'decimal:2',
        'total_value' => 'decimal:2',
        'harga_beli_total' => 'decimal:2',
        'tanggal_transaksi' => 'date',
    ];

    /**
     * Generate the next kode_transaksi
     */
    public static function generateKode(): string
    {
        $prefix = 'TRX-'.date('Ymd').'-';
        $lastTransaction = self::where('kode_transaksi', 'like', $prefix.'%')
            ->orderBy('kode_transaksi', 'desc')
            ->first();

        if (! $lastTransaction) {
            return $prefix.'001';
        }

        $lastNumber = (int) substr($lastTransaction->kode_transaksi, -3);

        return $prefix.str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->kode_transaksi)) {
                $transaction->kode_transaksi = self::generateKode();
            }
        });
    }

    /**
     * Get the bank sampah for this transaction.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class);
    }

    /**
     * Get the offtaker for this transaction.
     */
    public function offtaker(): BelongsTo
    {
        return $this->belongsTo(Offtaker::class);
    }

    /**
     * Get the admin who created this transaction.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the waste tracking records for this transaction.
     */
    public function trackings(): HasMany
    {
        return $this->hasMany(WasteTracking::class);
    }

    /**
     * Scope to filter sales only.
     */
    public function scopeSales($query)
    {
        return $query->where('type', self::TYPE_SALE);
    }

    /**
     * Scope to filter processing only.
     */
    public function scopeProcessing($query)
    {
        return $query->where('type', self::TYPE_PROCESSING);
    }

    /**
     * Scope to filter sales and processing combined.
     */
    public function scopeSalesAndProcessing($query)
    {
        return $query->whereIn('type', [self::TYPE_SALE, self::TYPE_PROCESSING]);
    }

    /**
     * Get the type text attribute.
     */
    public function getTypeTextAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_SALE => 'Penjualan',
            self::TYPE_PROCESSING => 'Pengolahan',
            default => $this->type,
        };
    }

    /**
     * Calculate profit for this transaction.
     */
    public function getProfit(): float
    {
        return $this->total_value - $this->harga_beli_total;
    }

    /**
     * Get profit margin percentage.
     */
    public function getProfitMargin(): float
    {
        if ($this->harga_beli_total == 0) {
            return 0;
        }

        return (($this->total_value - $this->harga_beli_total) / $this->harga_beli_total) * 100;
    }

    /**
     * Check if this is a sale transaction.
     */
    public function isSale(): bool
    {
        return $this->type === self::TYPE_SALE;
    }

    /**
     * Check if this is a processing transaction.
     */
    public function isProcessing(): bool
    {
        return $this->type === self::TYPE_PROCESSING;
    }
}
