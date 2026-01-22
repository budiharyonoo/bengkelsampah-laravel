<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artikel> $artikels
 * @property-read int|null $artikels_count
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel query()
 * @property int $id
 * @property string $nama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel whereNama($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class KategoriArtikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
    ];

    public function artikels()
    {
        return $this->hasMany(Artikel::class, 'kategori_id');
    }
}
