<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $user_name
 * @property string $user_identifier
 * @property int $redeem_item_id
 * @property string $redeem_item_name
 * @property string|null $redeem_item_description
 * @property float $point_used
 * @property string $status
 * @property int|null $status_changed_by
 * @property string|null $status_changed_by_name
 * @property \Illuminate\Support\Carbon|null $status_changed_at
 * @property string|null $reward_proof_url
 * @property array|null $info_json
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read RedeemItem $redeemItem
 * @property-read Admin|null $statusChangedBy
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserRedeem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRedeem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRedeem query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRedeem status(string $status)
 * @method static \Illuminate\Database\Eloquent\Builder|UserRedeem waiting()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRedeem approved()
 *
 * @mixin \Eloquent
 */
class UserRedeem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_identifier',
        'redeem_item_id',
        'redeem_item_name',
        'redeem_item_description',
        'point_used',
        'status',
        'status_changed_by',
        'status_changed_by_name',
        'status_changed_at',
        'reward_proof_url',
        'info_json',
        'notes',
    ];

    protected $casts = [
        'point_used' => 'decimal:2',
        'status_changed_at' => 'datetime',
        'info_json' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who made the redemption request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the redeem item.
     */
    public function redeemItem(): BelongsTo
    {
        return $this->belongsTo(RedeemItem::class);
    }

    /**
     * Get the admin who changed the status.
     */
    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'status_changed_by');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter waiting redemptions.
     */
    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope to filter approved redemptions.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Check if redemption is waiting.
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Check if redemption is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if redemption is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if redemption is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
