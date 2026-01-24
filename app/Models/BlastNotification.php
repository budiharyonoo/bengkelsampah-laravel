<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $title
 * @property string $body
 * @property string $topic
 * @property int $sent_by
 * @property string $sent_by_name
 * @property string $sent_by_role
 * @property string $status
 * @property string|null $error_message
 * @property array|null $fcm_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereFcmResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereSentBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereSentByName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereSentByRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereTopic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BlastNotification whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class BlastNotification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'topic',
        'sent_by',
        'sent_by_name',
        'sent_by_role',
        'status',
        'error_message',
        'fcm_response',
    ];

    protected $casts = [
        'fcm_response' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the admin who sent this blast notification
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'sent_by');
    }
}
