<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Admin|null $admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Setoran> $setorans
 * @property-read int|null $setorans_count
 * @method static \Illuminate\Database\Eloquent\Builder|BankSampah newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSampah newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankSampah query()
 * @mixin \Eloquent
 */
class BankSampah extends Model
{
    use HasFactory;

    protected $table = 'bank_sampah';

    protected $fillable = [
        'kode_bank_sampah',
        'nama_bank_sampah',
        'alamat_bank_sampah',
        'nama_penanggung_jawab',
        'kontak_penanggung_jawab',
        'foto',
        'gmaps_link',
        'tipe_layanan',
    ];

    /**
     * Generate the next kode_bank_sampah
     */
    public static function generateKode()
    {
        // Get all existing kode numbers
        $existingKodes = self::pluck('kode_bank_sampah')->toArray();

        if (empty($existingKodes)) {
            return 'BS-001';
        }

        // Extract numbers from existing kodes and sort them
        $existingNumbers = [];
        foreach ($existingKodes as $kode) {
            $number = (int) substr($kode, 3);
            $existingNumbers[] = $number;
        }

        sort($existingNumbers);

        // Find the first missing number starting from 1
        $nextNumber = 1;
        foreach ($existingNumbers as $number) {
            if ($number === $nextNumber) {
                $nextNumber++;
            } else {
                break; // Found a gap, use this number
            }
        }

        return 'BS-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Boot method to automatically generate kode_bank_sampah
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bankSampah) {
            if (empty($bankSampah->kode_bank_sampah)) {
                $bankSampah->kode_bank_sampah = self::generateKode();
            }
        });
    }

    /**
     * Get the admin associated with this bank sampah.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'id_bank_sampah');
    }

    /**
     * Get the setorans associated with this bank sampah.
     */
    public function setorans()
    {
        return $this->hasMany(Setoran::class, 'bank_sampah_id');
    }
}
