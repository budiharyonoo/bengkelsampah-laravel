<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property mixed $password
 * @property-read \App\Models\BankSampah|null $bankSampah
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventResult> $eventResults
 * @property-read int|null $event_results_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Admin query()
 *
 * @mixin \Eloquent
 */
class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'id_bank_sampah',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the bank sampah associated with this admin.
     */
    public function bankSampah()
    {
        return $this->belongsTo(BankSampah::class, 'id_bank_sampah');
    }

    /**
     * Get the events created by the admin.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the event results created by the admin.
     */
    public function eventResults()
    {
        return $this->hasMany(EventResult::class, 'created_by');
    }
}
