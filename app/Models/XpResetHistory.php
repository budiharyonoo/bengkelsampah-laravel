<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
