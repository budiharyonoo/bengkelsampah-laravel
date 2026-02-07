<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $points_required
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem wherePointsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RedeemItem whereUpdatedAt($value)
 * @property int|null $is_active
 * @method static Builder|RedeemItem active()
 * @method static Builder|RedeemItem whereIsActive($value)
 * @mixin \Eloquent
 */
class RedeemItem extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'points_required' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', '=', true);
    }
}
