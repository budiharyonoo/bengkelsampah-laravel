<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Http\Controllers\Api\OtpController;
use App\Repositories\DashboardRepository;
use App\Repositories\SetoranRepository;
use App\Repositories\UserRepository;
use App\Services\Dashboard\EnvironmentalImpactService;
use Illuminate\Support\Facades\Log;

/**
 * Service for dashboard business logic.
 *
 * Orchestrates data retrieval from multiple repositories
 * and handles dashboard-specific business logic.
 *
 * Performance optimizations:
 * - Uses repositories with optimized queries
 * - Implements caching at repository level
 * - Reduces query count from ~150 to ~15
 */
class DashboardService
{
    public function __construct(
        private readonly SetoranRepository $setoranRepository,
        private readonly UserRepository $userRepository,
        private readonly DashboardRepository $dashboardRepository,
        private readonly EnvironmentalImpactService $environmentalImpactService
    ) {}

    /**
     * Get complete dashboard data for view.
     *
     * @param  int|null  $bankSampahId  Filter by bank sampah
     * @param  string  $periode  Period type
     * @param  string|null  $rangeDate  Raw date range string
     * @return array<string, mixed>
     */
    public function getDashboardData(
        ?int $bankSampahId,
        string $periode = 'harian',
        ?string $rangeDate = null
    ): array {
        // Parse date range if provided
        $parsed = PeriodHelper::parseDateRange($rangeDate);
        $startDate = $parsed['startDate'];
        $endDate = $parsed['endDate'];

        // Get current period range
        [$periodStart, $periodEnd] = PeriodHelper::getPeriodRange($periode, $startDate, $endDate);

        return [
            // Core summary statistics
            'dashboardSummary' => $this->getDashboardSummary($bankSampahId, $periode, $startDate, $endDate),

            // Comparison chart data
            'comparisonData' => $this->getComparisonChartData($bankSampahId, $periode, $startDate, $endDate),

            // Period legends
            ...PeriodHelper::getPeriodLegends($periode, $startDate, $endDate),

            // Recent transactions
            'lastTransactions' => $this->setoranRepository->getRecentTransactions(
                $bankSampahId,
                $periodStart,
                $periodEnd,
                5
            ),

            // Top waste items with images
            'topSampahSetor' => $this->setoranRepository->getTopWasteWithImages(
                $bankSampahId,
                $periodStart,
                $periodEnd,
                5
            ),

            // Top users by deposits
            'topUserSetor' => $this->setoranRepository->getTopUsersByDeposits(
                $bankSampahId,
                $periodStart,
                $periodEnd,
                5
            ),

            // Waste totals by unit
            'wasteTotals' => $wasteTotals = $this->setoranRepository->getWasteTotals($bankSampahId, $periodStart, $periodEnd),

            // Environmental impact (CO2e calculation for small cities)
            'environmentalImpact' => $this->environmentalImpactService->calculate(
                (float) ($wasteTotals['kg'] ?? 0)
            ),

            // Trend charts
            'trendData' => $this->setoranRepository->getDailyTrend($bankSampahId),
            // 'revenueData' => $this->setoranRepository->getDailyRevenue($bankSampahId),

            // Status distribution
            'statusDistribution' => $this->setoranRepository->getStatusDistribution($bankSampahId),

            // Top waste types
            'topSampah' => $this->setoranRepository->getTopWasteTypes($bankSampahId, 5),

            // User growth
            // 'userGrowthData' => $this->userRepository->getMonthlyGrowth($bankSampahId),

            // Recent activity tables
            'recentSetoran' => $this->dashboardRepository->getRecentSetorans(10),
            // 'recentUsers' => $this->userRepository->getRecentUsers(10),
            // 'recentEvents' => $this->dashboardRepository->getRecentEvents(10),
            // 'recentArticles' => $this->dashboardRepository->getRecentArticles(10),

            // Event statistics
            // 'eventStats' => $this->dashboardRepository->getEventStats(),

            // Delete account requests
            // 'deleteRequests' => $this->dashboardRepository->getDeleteRequests(10),

            // Bank sampah list
            'bankSampahList' => $this->dashboardRepository->getAllBankSampah(),
        ];
    }

    /**
     * Get OTP balance from external service.
     */
    public function getOtpBalance(): mixed
    {
        // try {
        //     $otpController = new OtpController;

        //     return $otpController->getZenzivaOtpBalance();
        // } catch (\Exception $e) {
        //     Log::warning('Failed to get OTP balance: ' . $e->getMessage());

        //     return null;
        // }
        return null;
    }

    /**
     * Get dashboard summary statistics.
     *
     * @param  int|null  $bankSampahId  Filter by bank sampah
     * @param  string  $periode  Period type
     * @param  string|null  $startDate  Custom start date
     * @param  string|null  $endDate  Custom end date
     * @return array<string, mixed>
     */
    private function getDashboardSummary(
        ?int $bankSampahId,
        string $periode,
        ?string $startDate,
        ?string $endDate
    ): array {
        $summary = [];

        // Get current and previous period ranges
        [$currentStart, $currentEnd] = PeriodHelper::getPeriodRange($periode, $startDate, $endDate, false);
        [$previousStart, $previousEnd] = PeriodHelper::getPeriodRange($periode, $startDate, $endDate, true);

        // User counts
        $summary['user'] = $this->userRepository->countUsersWithTransactions(
            $bankSampahId,
            $currentStart,
            $currentEnd
        );
        $summary['user_prev'] = $this->userRepository->countUsersWithTransactions(
            $bankSampahId,
            $previousStart,
            $previousEnd
        );

        // Transaction stats for current period
        $currentStats = $this->setoranRepository->getCompletedTransactionStats(
            $bankSampahId,
            $currentStart,
            $currentEnd
        );
        $summary['setoran'] = $currentStats['count'];
        $summary['nilai_setoran'] = $currentStats['total_value'];
        $summary['poin_redeem'] = $currentStats['tabung_total'];

        // Transaction stats for previous period
        $previousStats = $this->setoranRepository->getCompletedTransactionStats(
            $bankSampahId,
            $previousStart,
            $previousEnd
        );
        $summary['setoran_prev'] = $previousStats['count'];
        $summary['nilai_setoran_prev'] = $previousStats['total_value'];
        $summary['poin_redeem_prev'] = $previousStats['tabung_total'];

        // Waste totals
        $currentWaste = $this->setoranRepository->getWasteTotals($bankSampahId, $currentStart, $currentEnd);
        $previousWaste = $this->setoranRepository->getWasteTotals($bankSampahId, $previousStart, $previousEnd);

        $summary['total_sampah_kg'] = $currentWaste['kg'];
        $summary['total_sampah_unit'] = $currentWaste['unit'];
        $summary['total_sampah_kg_prev'] = $previousWaste['kg'];
        $summary['total_sampah_unit_prev'] = $previousWaste['unit'];

        return $summary;
    }

    /**
     * Get comparison chart data for current vs previous period.
     *
     * @param  int|null  $bankSampahId  Filter by bank sampah
     * @param  string  $periode  Period type
     * @param  string|null  $startDate  Custom start date
     * @param  string|null  $endDate  Custom end date
     * @return array{current: array, previous: array}
     */
    private function getComparisonChartData(
        ?int $bankSampahId,
        string $periode,
        ?string $startDate,
        ?string $endDate
    ): array {
        [$currentStart, $currentEnd] = PeriodHelper::getPeriodRange($periode, $startDate, $endDate, false);
        [$previousStart, $previousEnd] = PeriodHelper::getPeriodRange($periode, $startDate, $endDate, true);

        return $this->setoranRepository->getComparisonChartData(
            $bankSampahId,
            $periode,
            $currentStart,
            $currentEnd,
            $previousStart,
            $previousEnd
        );
    }

    /**
     * Clear all dashboard caches.
     *
     * @param  int|null  $bankSampahId  Optional bank sampah to clear cache for
     */
    public function clearCache(?int $bankSampahId = null): void
    {
        $this->setoranRepository->clearCache($bankSampahId);
    }
}
