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