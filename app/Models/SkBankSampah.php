<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SK Bank Sampah Model
 *
 * Represents the official decree (Surat Keputusan) for a Bank Sampah.
 * All data is stored independently in this table (not referenced from bank_sampah).
 *
 * @property int $id
 * @property int $bank_sampah_id
 * @property string $nama_bank_sampah
 * @property string $alamat_bank_sampah
 * @property string $nomor_surat
 * @property \Illuminate\Support\Carbon $tanggal_ditetapkan
 * @property \Illuminate\Support\Carbon $tanggal_rapat_musyawarah
 * @property string $nama_desa_kelurahan
 * @property string $kecamatan
 * @property string $kabupaten
 * @property string $provinsi
 * @property string|null $kode_pos
 * @property int $masa_bakti_mulai
 * @property int $masa_bakti_selesai
 * @property string $nama_kepala_desa
 * @property string|null $pengurus_image_path
 * @property string|null $struktur_organisasi_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read BankSampah $bankSampah
 */
class SkBankSampah extends Model
{
    use HasFactory;

    protected $table = 'sk_bank_sampah';

    protected $fillable = [
        'bank_sampah_id',
        'nama_bank_sampah',
        'alamat_bank_sampah',
        'nomor_surat',
        'tanggal_ditetapkan',
        'tanggal_rapat_musyawarah',
        'nama_desa_kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'masa_bakti_mulai',
        'masa_bakti_selesai',
        'nama_kepala_desa',
        'pengurus_image_path',
        'struktur_organisasi_path',
    ];

    protected $casts = [
        'tanggal_ditetapkan' => 'date',
        'tanggal_rapat_musyawarah' => 'date',
        'masa_bakti_mulai' => 'integer',
        'masa_bakti_selesai' => 'integer',
    ];

    /**
     * Get the bank sampah that owns this SK.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class, 'bank_sampah_id');
    }

    /**
     * Get formatted masa bakti string.
     */
    public function getMasaBaktiAttribute(): string
    {
        return $this->masa_bakti_mulai . ' - ' . $this->masa_bakti_selesai;
    }

    /**
     * Get full address string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [
            $this->nama_desa_kelurahan,
            $this->kecamatan,
            $this->kabupaten,
            $this->provinsi,
        ];

        if ($this->kode_pos) {
            $parts[] = $this->kode_pos;
        }

        return implode(', ', $parts);
    }
}
