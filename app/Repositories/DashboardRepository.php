<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Artikel;
use App\Models\BankSampah;
use App\Models\DeleteAccountRequest;
use App\Models\Event;
use App\Models\Setoran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Repository for dashboard-related data aggregation.
 *
 * Handles queries for events, articles, bank sampah, and other
 * dashboard components with caching and optimization.
 */
class DashboardRepository
{
    /**
     * Cache duration in seconds (10 minutes).
     */
    private const CACHE_TTL = 600;

    /**
     * Get all bank sampah ordered by name.
     *
     * @return Collection<int, BankSampah>
     */
    public function getAllBankSampah(): Collection
    {
        return Cache::remember('bank_sampah:all', self::CACHE_TTL, function () {
            return BankSampah::query()
                ->select(['id', 'kode_bank_sampah', 'nama_bank_sampah', 'tipe_layanan'])
                ->orderBy('nama_bank_sampah')
                ->get();
        });
    }

    /**
     * Get recent setorans with user and bank sampah relations.
     *
     * @param int $limit Number of records
     * @return Collection<int, Setoran>
     */
    public function getRecentSetorans(int $limit = 10): Collection
    {
        return Setoran::query()
            ->select([
                'id',
                'user_id',
                'user_name',
                'bank_sampah_id',
                'bank_sampah_name',
                'status',
                'tipe_setor',
                'aktual_total',
                'created_at',
            ])
            ->with([
                'user:id,name,identifier',
                'bankSampah:id,nama_bank_sampah,kode_bank_sampah',
            ])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent events with participant count.
     *
     * @param int $limit Number of records
     * @return Collection<int, Event>
     */
    public function getRecentEvents(int $limit = 10): Collection
    {
        return Event::query()
            ->select([
                'id',
                'title',
                'status',
                'start_datetime',
                'end_datetime',
                'location',
                'max_participants',
                'actual_participants',
                'created_at',
            ])
            ->withCount('participants')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get event statistics.
     *
     * Optimized: Single query with conditional aggregation.
     *
     * @return array{total_events: int, active_events: int, completed_events: int}
     */
    public function getEventStats(): array
    {
        return Cache::remember('event_stats', self::CACHE_TTL, function () {
            $result = Event::query()
                ->select([
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active"),
                    DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                ])
                ->first();

            return [
                'total_events' => (int) $result->total,
                'active_events' => (int) $result->active,
                'completed_events' => (int) $result->completed,
            ];
        });
    }

    /**
     * Get recent articles.
     *
     * @param int $limit Number of records
     * @return Collection<int, Artikel>
     */
    public function getRecentArticles(int $limit = 10): Collection
    {
        return Artikel::query()
            ->select(['id', 'title', 'cover', 'creator', 'kategori_id', 'created_at'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent delete account requests.
     *
     * @param int $limit Number of records
     * @return Collection<int, DeleteAccountRequest>
     */
    public function getDeleteRequests(int $limit = 10): Collection
    {
        return DeleteAccountRequest::query()
            ->select(['id', 'email', 'phone', 'full_name', 'reason', 'status', 'created_at'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
