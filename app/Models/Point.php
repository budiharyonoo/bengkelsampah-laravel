<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Setoran|null $setoran
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Point newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Point query()
 * @property int $id
 * @property int $user_id
 * @property string $user_name
 * @property string $user_identifier
 * @property string $type
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $jumlah_point
 * @property string $xp
 * @property int|null $setoran_id
 * @property string|null $keterangan
 * @property string|null $bukti_redeem
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereBuktiRedeem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereJumlahPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereSetoranId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereUserIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Point whereXp($value)
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
