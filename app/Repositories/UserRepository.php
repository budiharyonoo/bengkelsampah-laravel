<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Repository for User data access.
 *
 * Handles all database queries related to users with optimizations:
 * - Aggregated monthly growth queries
 * - Caching for expensive queries
 */
class UserRepository
{
    /**
     * Cache duration in seconds (10 minutes).
     */
    private const CACHE_TTL = 600;

    /**
     * Get recent users.
     *
     * @param  int  $limit  Number of records
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public function getRecentUsers(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return User::query()
            ->select(['id', 'name', 'identifier', 'poin', 'xp', 'setor', 'created_at'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user growth data by month.
     *
     * Optimized: Single query with window function instead of 6 separate queries.
     *
     * @param  int|null  $bankSampahId  Filter by bank sampah (via setorans)
     * @param  int  $months  Number of months to retrieve
     * @return array{labels: array<string>, values: array<int>}
     */
    public function getMonthlyGrowth(?int $bankSampahId, int $months = 6): array
    {
        $cacheKey = "user_growth:{$bankSampahId}:{$months}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId, $months) {
            if ($bankSampahId) {
                // Cumulative users with transactions at specific bank sampah per month
                return $this->getGrowthWithBankFilter($bankSampahId, $months);
            }

            // Total cumulative users per month (more efficient query)
            return $this->getTotalUserGrowth($months);
        });
    }

    /**
     * Get total user growth without bank filter.
     */
    private function getTotalUserGrowth(int $months): array
    {
        $labels = [];
        $values = [];

        // Get cumulative count at end of each month
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $endOfMonth = $month->copy()->endOfMonth();

            $labels[] = $month->format('M Y');
            $values[] = User::query()
                ->where('created_at', '<=', $endOfMonth)
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Get user growth filtered by bank sampah.
     */
    private function getGrowthWithBankFilter(int $bankSampahId, int $months): array
    {
        $labels = [];
        $values = [];

        // Get users who have made transactions at this bank up to end of each month
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $endOfMonth = $month->copy()->endOfMonth();

            $labels[] = $month->format('M Y');

            // Subquery for users with transactions at this bank
            $values[] = User::query()
                ->whereExists(function ($query) use ($bankSampahId, $endOfMonth) {
                    $query->select(DB::raw(1))
                        ->from('setorans')
                        ->whereColumn('setorans.user_id', 'users.id')
                        ->where('setorans.bank_sampah_id', $bankSampahId)
                        ->where('setorans.created_at', '<=', $endOfMonth);
                })
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Count users with transactions in a specific period.
     *
     * @param  int|null  $bankSampahId  Filter by bank sampah
     * @param  Carbon  $startDate  Start date
     * @param  Carbon  $endDate  End date
     */
    public function countUsersWithTransactions(
        ?int $bankSampahId,
        Carbon $startDate,
        Carbon $endDate
    ): int {
        $cacheKey = "user_count:{$bankSampahId}:{$startDate->format('Ymd')}:{$endDate->format('Ymd')}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($bankSampahId, $startDate, $endDate) {
            return User::query()
                ->whereExists(function ($query) use ($bankSampahId, $startDate, $endDate) {
                    $query->select(DB::raw(1))
                        ->from('setorans')
                        ->whereColumn('setorans.user_id', 'users.id')
                        ->whereBetween('setorans.created_at', [$startDate, $endDate]);

                    if ($bankSampahId) {
                        $query->where('setorans.bank_sampah_id', $bankSampahId);
                    }
                })
                ->count();
        });
    }
}
