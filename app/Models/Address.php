<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Address query()
 * @property int $id
 * @property int $user_id
 * @property string $nama
 * @property string $nomor_handphone
 * @property string $label_alamat
 * @property string $provinsi
 * @property string $kota_kabupaten
 * @property string $kecamatan
 * @property string $kode_pos
 * @property string|null $detail_lain
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereDetailLain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereKecamatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereKodePos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereKotaKabupaten($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereLabelAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereNomorHandphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereProvinsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Address whereUserId($value)
 * @mixin \Eloquent
 */
class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama',
        'nomor_handphone',
        'label_alamat',
        'provinsi',
        'kota_kabupaten',
        'kecamatan',
        'kode_pos',
        'detail_lain',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
