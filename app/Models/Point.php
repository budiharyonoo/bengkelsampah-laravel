<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Setoran|null $setoran
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Point newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point query()
 *
 * @mixin \Eloquent
 */
class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_identifier',
        'type',
        'tanggal',
        'jumlah_point',
        'xp',
        'setoran_id',
        'keterangan',
        'bukti_redeem',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_point' => 'decimal:2',
        'xp' => 'decimal:2',
    ];

    // Type constants
    const TYPE_SETOR = 'setor';

    const TYPE_REDEEM = 'redeem';

    /**
     * Get the user that owns the point
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the setoran associated with this point (if type is 'setor')
     */
    public function setoran()
    {
        return $this->belongsTo(Setoran::class);
    }

    /**
     * Check if point is from deposit
     */
    public function isFromDeposit()
    {
        return $this->type === self::TYPE_SETOR;
    }

    /**
     * Check if point is from redemption
     */
    public function isFromRedemption()
    {
        return $this->type === self::TYPE_REDEEM;
    }
}
