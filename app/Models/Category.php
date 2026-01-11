<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read mixed $sampah_count
 * @property-read mixed $sampah_items
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 *
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'sampah',
    ];

    protected $casts = [
        'sampah' => 'array',
    ];

    protected $attributes = [
        'sampah' => '[]',
    ];

    /**
     * Get the sampah items for this category
     */
    public function getSampahItemsAttribute()
    {
        if (empty($this->sampah)) {
            return collect();
        }

        return Sampah::whereIn('id', $this->sampah)->get();
    }

    /**
     * Get sampah count
     */
    public function getSampahCountAttribute()
    {
        return count($this->sampah ?? []);
    }
}
