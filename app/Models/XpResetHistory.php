<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon $reset_at
 * @property int $admin_id
 * @property string $admin_name
 * @property int|null $top1_user_id
 * @property string|null $top1_name
 * @property string|null $top1_xp
 * @property int|null $top2_user_id
 * @property string|null $top2_name
 * @property string|null $top2_xp
 * @property int|null $top3_user_id
 * @property string|null $top3_name
 * @property string|null $top3_xp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereResetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop1Name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop1UserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop1Xp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop2Name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop2UserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop2Xp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop3Name($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop3UserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereTop3Xp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|XpResetHistory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class XpResetHistory extends Model
{
    use HasFactory;

    protected $table = 'xp_reset_history';

    protected $fillable = [
        'reset_at',
        'admin_id',
        'admin_name',
        'top1_user_id',
        'top1_name',
        'top1_xp',
        'top2_user_id',
        'top2_name',
        'top2_xp',
        'top3_user_id',
        'top3_name',
        'top3_xp',
    ];

    protected $casts = [
        'reset_at' => 'datetime',
        'top1_xp' => 'decimal:2',
        'top2_xp' => 'decimal:2',
        'top3_xp' => 'decimal:2',
    ];
}
