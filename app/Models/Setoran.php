<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Address|null $address
 * @property-read \App\Models\BankSampah|null $bankSampah
 * @property-read mixed $catatan
 * @property-read mixed $foto
 * @property-read mixed $jadwal
 * @property-read mixed $status_text
 * @property-read mixed $tipe_setor_text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Point> $points
 * @property-read int|null $points_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran query()
 * @property int $id
 * @property int $user_id
 * @property string $user_name
 * @property string $user_identifier
 * @property int $bank_sampah_id
 * @property string $bank_sampah_name
 * @property string $bank_sampah_code
 * @property string $bank_sampah_address
 * @property string $bank_sampah_phone
 * @property int $address_id
 * @property string $address_name
 * @property string $address_phone
 * @property string $address_full_address
 * @property bool $address_is_default
 * @property string $tipe_setor
 * @property string $status
 * @property array $items_json
 * @property string $estimasi_total
 * @property string|null $aktual_total
 * @property \Illuminate\Support\Carbon|null $tanggal_penjemputan
 * @property \Illuminate\Support\Carbon|null $waktu_penjemputan
 * @property string|null $petugas_nama
 * @property string|null $petugas_contact
 * @property string|null $foto_sampah
 * @property string|null $alasan_pembatalan
 * @property \Illuminate\Support\Carbon|null $tanggal_selesai
 * @property string $tipe_layanan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAddressFullAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAddressIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAddressName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAddressPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAktualTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereAlasanPembatalan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereBankSampahAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereBankSampahCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereBankSampahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereBankSampahName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereBankSampahPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereEstimasiTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereFotoSampah($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereItemsJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran wherePetugasContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran wherePetugasNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereTanggalPenjemputan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereTanggalSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereTipeLayanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereTipeSetor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereUserIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setoran whereWaktuPenjemputan($value)
 * @mixin \Eloquent
 */
class Setoran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_identifier',
        'bank_sampah_id',
        'bank_sampah_name',
        'bank_sampah_code',
        'bank_sampah_address',
        'bank_sampah_phone',
        'address_id',
        'address_name',
        'address_phone',
        'address_full_address',
        'address_is_default',
        'tipe_setor',
        'status',
        'items_json',
        'estimasi_total',
        'aktual_total',
        'tanggal_penjemputan',
        'waktu_penjemputan',
        'petugas_nama',
        'petugas_contact',
        'foto_sampah',
        'notes',
        'alasan_pembatalan',
        'perubahan_data',
        'tanggal_selesai',
        'tipe_layanan',
    ];

    protected $casts = [
        'items_json' => 'array',
        'estimasi_total' => 'decimal:2',
        'aktual_total' => 'decimal:2',
        'tanggal_penjemputan' => 'date',
        'waktu_penjemputan' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'address_is_default' => 'boolean',
    ];

    // Status constants
    const STATUS_DIKONFIRMASI = 'dikonfirmasi';

    const STATUS_DIPROSES = 'diproses';

    const STATUS_DIJEMPUT = 'dijemput';

    const STATUS_SELESAI = 'selesai';

    const STATUS_BATAL = 'batal';

    const STATUS_BERHASIL = 'berhasil';

    // Tipe setor constants
    const TIPE_JUAL = 'jual';

    const TIPE_SEDEKAH = 'sedekah';

    const TIPE_TABUNG = 'tabung';

    // Tipe layanan constants
    const LAYANAN_JEMPUT = 'jemput';

    const LAYANAN_TEMPAT = 'tempat';

    const LAYANAN_KEDUANYA = 'keduanya';

    /**
     * Get the user that owns the setoran
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the bank sampah for this setoran
     */
    public function bankSampah()
    {
        return $this->belongsTo(BankSampah::class);
    }

    /**
     * Get the address for this setoran
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the points associated with this setoran
     */
    public function points()
    {
        return $this->hasMany(Point::class);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_DIKONFIRMASI:
                return 'Dikonfirmasi';
            case self::STATUS_DIPROSES:
                return 'Diproses';
            case self::STATUS_DIJEMPUT:
                return 'Dijemput';
            case self::STATUS_SELESAI:
                return 'Selesai';
            case self::STATUS_BATAL:
                return 'Batal';
            default:
                return ucfirst($this->status);
        }
    }

    /**
     * Get tipe setor text
     */
    public function getTipeSetorTextAttribute()
    {
        switch ($this->tipe_setor) {
            case self::TIPE_JUAL:
                return 'Jual';
            case self::TIPE_SEDEKAH:
                return 'Sedekah';
            case self::TIPE_TABUNG:
                return 'Tabung';
            default:
                return ucfirst($this->tipe_setor);
        }
    }

    /**
     * Get foto as string
     */
    public function getFotoAttribute()
    {
        return $this->foto_sampah;
    }

    /**
     * Get catatan
     */
    public function getCatatanAttribute()
    {
        return $this->notes;
    }

    /**
     * Get jadwal
     */
    public function getJadwalAttribute()
    {
        if ($this->tanggal_penjemputan && $this->waktu_penjemputan) {
            return $this->tanggal_penjemputan->format('d/m/Y').' '.$this->waktu_penjemputan->format('H:i');
        }

        return null;
    }

    /**
     * Check if setoran is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_SELESAI;
    }

    /**
     * Check if setoran is cancelled
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_BATAL;
    }

    /**
     * Check if setoran can earn points
     */
    public function canEarnPoints()
    {
        return $this->tipe_setor === self::TIPE_TABUNG;
    }
}
