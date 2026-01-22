<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $points_required
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem wherePointsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RedeemItem extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive()
    {
        return $this->where('is_active', '=', true);
    }
}
