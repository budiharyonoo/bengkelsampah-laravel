<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read mixed $categories
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Price> $prices
 * @property-read int|null $prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah query()
 * @property int $id
 * @property string $nama
 * @property string|null $deskripsi
 * @property string|null $gambar
 * @property string $satuan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereGambar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereSatuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sampah whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sampah extends Model
{
    use HasFactory;

    protected $table = 'sampah';

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'satuan',
    ];

    /**
     * Get the prices for this sampah
     */
    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Get the categories that contain this sampah
     */
    public function getCategoriesAttribute()
    {
        return Category::whereJsonContains('sampah', $this->id)->get();
    }
}
