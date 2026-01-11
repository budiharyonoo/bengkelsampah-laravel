<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artikel> $artikels
 * @property-read int|null $artikels_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KategoriArtikel query()
 *
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
