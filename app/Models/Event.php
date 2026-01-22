<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int|null $participants_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventParticipant> $participants
 * @property-read \App\Models\EventResult|null $result
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $cover
 * @property \Illuminate\Support\Carbon $start_datetime
 * @property \Illuminate\Support\Carbon $end_datetime
 * @property string $location
 * @property string|null $url
 * @property int|null $max_participants
 * @property string $status
 * @property int|null $admin_id
 * @property string|null $admin_name
 * @property string|null $result_description
 * @property string|null $saved_waste_amount
 * @property int|null $actual_participants
 * @property \Illuminate\Support\Carbon|null $result_submitted_at
 * @property string|null $result_submitted_by_name
 * @property array|null $result_photos
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereActualParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereAdminName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEndDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereMaxParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereResultDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereResultPhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereResultSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereResultSubmittedByName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSavedWasteAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartDatetime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUrl($value)
 * @mixin \Eloquent
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'cover',
        'start_datetime',
        'end_datetime',
        'location',
        'url',
        'max_participants',
        'status',
        'admin_name',
        'result_description',
        'saved_waste_amount',
        'result_photos',
        'actual_participants',
        'result_submitted_at',
        'result_submitted_by_name',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'result_submitted_at' => 'datetime',
        'result_photos' => 'array',
        'saved_waste_amount' => 'decimal:2',
    ];

    /**
     * Get the participants for the event.
     */
    public function participants()
    {
        return $this->hasMany(EventParticipant::class);
    }

    /**
     * Get the result for the event.
     */
    public function result()
    {
        return $this->hasOne(EventResult::class);
    }

    /**
     * Boot method to handle cascade deletes
     */
    protected static function boot()
    {
        parent::boot();

        // When an event is deleted, delete all its participants
        static::deleting(function ($event) {
            $event->participants()->delete();
        });
    }

    /**
     * Check if event is expired based on end_datetime.
     */
    public function isExpired()
    {
        return now()->isAfter($this->end_datetime);
    }

    /**
     * Check if event is active and not expired.
     */
    public function isActive()
    {
        return $this->status === 'active' && ! $this->isExpired();
    }

    /**
     * Check if event has result submitted.
     */
    public function hasResult()
    {
        return ! is_null($this->result_submitted_at);
    }

    /**
     * Check if event can have result submitted (must be completed).
     */
    public function canSubmitResult()
    {
        return $this->status === 'completed';
    }

    /**
     * Get the number of participants.
     */
    public function getParticipantsCountAttribute()
    {
        return $this->participants()->count();
    }
}
