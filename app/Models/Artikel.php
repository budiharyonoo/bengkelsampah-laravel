<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\KategoriArtikel|null $kategori
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel query()
 * @mixin \Eloquent
 */
class Artikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'cover',
        'kategori_id',
        'creator',
        'created_at',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id');
    }
}
