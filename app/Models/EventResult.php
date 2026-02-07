<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Admin|null $admin
 * @property-read \App\Models\Event|null $event
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult query()
 * @property int $id
 * @property int $event_id
 * @property string $activity_summary
 * @property array|null $photos
 * @property string $waste_saved_kg
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereActivitySummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult wherePhotos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventResult whereWasteSavedKg($value)
 * @mixin \Eloquent
 */
class EventResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'activity_summary',
        'photos',
        'waste_saved_kg',
        'created_by',
    ];

    protected $casts = [
        'photos' => 'array',
        'waste_saved_kg' => 'decimal:2',
    ];

    /**
     * Get the event that this result belongs to.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the admin that created this result.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
