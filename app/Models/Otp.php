<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp query()
 * @mixin \Eloquent
 */
class Otp extends Model
{
    protected $fillable = [
        'identifier', 'code', 'type', 'created_at', 'expires_at', 'count'
    ];
}
