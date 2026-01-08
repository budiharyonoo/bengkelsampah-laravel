<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use Carbon\Carbon;

/**
 * Helper class for period calculations.
 *
 * Centralizes all period-related logic to eliminate code duplication.
 * Handles daily, weekly, monthly, six-monthly, yearly, and custom range periods.
 */
final class PeriodHelper
{
    public const PERIOD_HARIAN = 'harian';

    public const PERIOD_MINGGUAN = 'mingguan';

    public const PERIOD_BULANAN = 'bulanan';

    public const PERIOD_ENAM_BULANAN = 'enam_bulanan';

    public const PERIOD_TAHUNAN = 'tahunan';

    public const PERIOD_RANGE = 'range';

    /**
     * Parse date range from filter string.
     *
     * Supports formats: "YYYY-MM-DD to YYYY-MM-DD" or "YYYY-MM-DD - YYYY-MM-DD"
     *
     * @param string|null $rangeDate Raw date range string
     * @return array{startDate: string|null, endDate: string|null}
     */
    public static function parseDateRange(?string $rangeDate): array
    {
        if (! $rangeDate) {
            return ['startDate' => null, 'endDate' => null];
        }

        // Try different separators
        $separator = str_contains($rangeDate, ' to ') ? ' to ' : ' - ';
        $dates = explode($separator, $rangeDate);

        if (count($dates) !== 2) {
            return ['startDate' => null, 'endDate' => null];
        }

        try {
            return [
                'startDate' => Carbon::parse(trim($dates[0]))->format('Y-m-d'),
                'endDate' => Carbon::parse(trim($dates[1]))->format('Y-m-d'),
            ];
        } catch (\Exception) {
            return ['startDate' => null, 'endDate' => null];
        }
    }

    /**
     * Get period date range for current or previous period.
     *
     * @param string $periode Period type (harian, mingguan, bulanan, etc.)
     * @param string|null $startDate Custom start date
     * @param string|null $endDate Custom end date (for range)
     * @param bool $previous Whether to get previous period
     * @return array{0: Carbon, 1: Carbon} [start, end]
     */
    public static function getPeriodRange(
        string $periode,
        ?string $startDate = null,
        ?string $endDate = null,
        bool $previous = false
    ): array {
        $now = now();

        // Handle custom range
        if ($periode === self::PERIOD_RANGE && $startDate && $endDate) {
            return self::getCustomRange($startDate, $endDate, $previous);
        }

        $baseDate = $startDate ? Carbon::parse($startDate) : $now->copy();

        return match ($periode) {
            self::PERIOD_HARIAN => self::getDailyRange($baseDate, $previous),
            self::PERIOD_MINGGUAN => self::getWeeklyRange($baseDate, $previous),
            self::PERIOD_BULANAN => self::getMonthlyRange($baseDate, $previous),
            self::PERIOD_ENAM_BULANAN => self::getSixMonthRange($baseDate, $previous),
            self::PERIOD_TAHUNAN => self::getYearlyRange($baseDate, $previous),
            default => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /**
     * Get legend labels for current and previous periods.
     *
     * @param string $periode Period type
     * @param string|null $startDate Custom start date
     * @param string|null $endDate Custom end date
     * @return array{current: string, previous: string}
     */
    public static function getPeriodLegends(
        string $periode,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $date = $startDate ? Carbon::parse($startDate) : now();

        return match ($periode) {
            self::PERIOD_HARIAN => [
                'current' => $date->translatedFormat('d F Y'),
                'previous' => $date->copy()->subDay()->translatedFormat('d F Y'),
            ],
            self::PERIOD_MINGGUAN => [
                'current' => $date->copy()->startOfWeek()->translatedFormat('d F Y') . ' - ' .
                    $date->copy()->endOfWeek()->translatedFormat('d F Y'),
                'previous' => $date->copy()->subWeek()->startOfWeek()->translatedFormat('d F Y') . ' - ' .
                    $date->copy()->subWeek()->endOfWeek()->translatedFormat('d F Y'),
            ],
            self::PERIOD_BULANAN => [
                'current' => $date->translatedFormat('F Y'),
                'previous' => $date->copy()->subMonth()->translatedFormat('F Y'),
            ],
            self::PERIOD_ENAM_BULANAN => [
                'current' => $date->copy()->subMonths(5)->translatedFormat('F') . ' - ' .
                    $date->translatedFormat('F Y'),
                'previous' => $date->copy()->subMonths(11)->translatedFormat('F') . ' - ' .
                    $date->copy()->subMonths(6)->translatedFormat('F Y'),
            ],
            self::PERIOD_TAHUNAN => [
                'current' => $date->translatedFormat('Y'),
                'previous' => $date->copy()->subYear()->translatedFormat('Y'),
            ],
            self::PERIOD_RANGE => self::getRangeLegends($startDate, $endDate),
            default => ['current' => '', 'previous' => ''],
        };
    }

    /**
     * Get custom range dates.
     */
    private static function getCustomRange(string $startDate, string $endDate, bool $previous): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($previous) {
            $days = $start->diffInDays($end);
            $end = $start->copy()->subDay();
            $start = $end->copy()->subDays($days);
        }

        return [$start->startOfDay(), $end->endOfDay()];
    }

    /**
     * Get daily range.
     */
    private static function getDailyRange(Carbon $baseDate, bool $previous): array
    {
        $date = $previous ? $baseDate->copy()->subDay() : $baseDate->copy();

        return [$date->startOfDay(), $date->copy()->endOfDay()];
    }

    /**
     * Get weekly range.
     */
    private static function getWeeklyRange(Carbon $baseDate, bool $previous): array
    {
        $date = $previous ? $baseDate->copy()->subWeek() : $baseDate->copy();

        return [$date->startOfWeek(), $date->copy()->endOfWeek()];
    }

    /**
     * Get monthly range.
     */
    private static function getMonthlyRange(Carbon $baseDate, bool $previous): array
    {
        $date = $previous ? $baseDate->copy()->subMonth() : $baseDate->copy();

        return [$date->startOfMonth(), $date->copy()->endOfMonth()];
    }

    /**
     * Get six-month range.
     */
    private static function getSixMonthRange(Carbon $baseDate, bool $previous): array
    {
        if ($previous) {
            return [
                $baseDate->copy()->subMonths(12)->startOfMonth(),
                $baseDate->copy()->subMonths(6)->endOfMonth(),
            ];
        }

        return [
            $baseDate->copy()->subMonths(6)->startOfMonth(),
            $baseDate->copy()->endOfMonth(),
        ];
    }

    /**
     * Get yearly range.
     */
    private static function getYearlyRange(Carbon $baseDate, bool $previous): array
    {
        $date = $previous ? $baseDate->copy()->subYear() : $baseDate->copy();

        return [$date->startOfYear(), $date->copy()->endOfYear()];
    }

    /**
     * Get range period legends.
     */
    private static function getRangeLegends(?string $startDate, ?string $endDate): array
    {
        if (! $startDate || ! $endDate) {
            return ['current' => '', 'previous' => ''];
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end);

        $prevEnd = $start->copy()->subDay();
        $prevStart = $prevEnd->copy()->subDays($days);

        return [
            'current' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
            'previous' => $prevStart->translatedFormat('d M Y') . ' - ' . $prevEnd->translatedFormat('d M Y'),
        ];
    }
}
