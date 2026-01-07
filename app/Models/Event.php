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
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Check if event has result submitted.
     */
    public function hasResult()
    {
        return !is_null($this->result_submitted_at);
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
