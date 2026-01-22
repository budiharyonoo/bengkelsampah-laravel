<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read mixed $reason_text
 * @property-read mixed $status_badge_class
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest query()
 * @property int $id
 * @property string $email
 * @property string $phone
 * @property string $full_name
 * @property string $reason
 * @property string|null $explanation
 * @property string $ip_address
 * @property string $user_agent
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereExplanation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeleteAccountRequest whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class DeleteAccountRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'phone',
        'full_name',
        'reason',
        'explanation',
        'ip_address',
        'user_agent',
        'status',
        'admin_notes',
        'verified_at',
        'completed_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the reason text
     */
    public function getReasonTextAttribute()
    {
        $reasons = [
            'privacy' => 'Khawatir tentang privasi data',
            'no_longer_use' => 'Tidak lagi menggunakan aplikasi',
            'duplicate_account' => 'Memiliki akun ganda',
            'technical_issues' => 'Masalah teknis yang tidak teratasi',
            'service_quality' => 'Tidak puas dengan kualitas layanan',
            'other' => 'Lainnya',
        ];

        return $reasons[$this->reason] ?? $this->reason;
    }

    /**
     * Get the status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'badge-warning',
            'verified' => 'badge-info',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }
}
