<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\KategoriArtikel|null $kategori
 * @property-read \App\Models\BankSampah|null $bankSampah
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel query()
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $cover
 * @property int $kategori_id
 * @property int|null $bank_sampah_id
 * @property string $creator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereKategoriId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereBankSampahId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Artikel whereUpdatedAt($value)
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
        'bank_sampah_id',
        'creator',
        'created_at',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id');
    }

    public function bankSampah()
    {
        return $this->belongsTo(BankSampah::class, 'bank_sampah_id');
    }
}
