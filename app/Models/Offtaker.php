<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $kode_offtaker
 * @property string $nama
 * @property string $tipe
 * @property string $nama_pic
 * @property string $kontak_pic
 * @property string|null $alamat
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WasteTransaction> $wasteTransactions
 * @property-read string $tipe_text
 * @property-read int|null $waste_transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker active()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker buyers()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker processors()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker query()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereKodeOfftaker($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereKontakPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereNamaPic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Offtaker withoutTrashed()
 * @mixin \Eloquent
 */
class Offtaker extends Model
{
    use HasFactory, SoftDeletes;

    public const TIPE_BUYER = 'buyer';

    public const TIPE_PROCESSOR = 'processor';

    public const TIPE_BOTH = 'both';

    protected $fillable = [
        'nama',
        'tipe',
        'nama_pic',
        'kontak_pic',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Generate the next kode_offtaker
     */
    public static function generateKode(): string
    {
        $existingKodes = self::withTrashed()->pluck('kode_offtaker')->toArray();

        if (empty($existingKodes)) {
            return 'OF-001';
        }

        $existingNumbers = [];
        foreach ($existingKodes as $kode) {
            $number = (int) substr($kode, 3);
            $existingNumbers[] = $number;
        }

        sort($existingNumbers);

        $nextNumber = 1;
        foreach ($existingNumbers as $number) {
            if ($number === $nextNumber) {
                $nextNumber++;
            } else {
                break;
            }
        }

        return 'OF-'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($offtaker) {
            if (empty($offtaker->kode_offtaker)) {
                $offtaker->kode_offtaker = self::generateKode();
            }
        });
    }

    /**
     * Get the waste transactions for this offtaker.
     */
    public function wasteTransactions(): HasMany
    {
        return $this->hasMany(WasteTransaction::class);
    }

    /**
     * Scope to filter active offtakers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter buyers only.
     */
    public function scopeBuyers($query)
    {
        return $query->whereIn('tipe', [self::TIPE_BUYER, self::TIPE_BOTH]);
    }

    /**
     * Scope to filter processors only.
     */
    public function scopeProcessors($query)
    {
        return $query->whereIn('tipe', [self::TIPE_PROCESSOR, self::TIPE_BOTH]);
    }

    /**
     * Get the tipe text attribute.
     */
    public function getTipeTextAttribute(): string
    {
        return match ($this->tipe) {
            self::TIPE_BUYER => 'Pembeli',
            self::TIPE_PROCESSOR => 'Pengolah',
            self::TIPE_BOTH => 'Pembeli & Pengolah',
            default => $this->tipe,
        };
    }
}
