<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function formatIndonesian($date, $format = 'd F Y H:i')
    {
        if (! $date) {
            return '-';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        $months = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        $formatted = $carbon->format($format);

        foreach ($months as $english => $indonesian) {
            $formatted = str_replace($english, $indonesian, $formatted);
        }

        return $formatted;
    }

    public static function formatIndonesianShort($date, $format = 'd M Y H:i')
    {
        if (! $date) {
            return '-';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        $months = [
            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'Mar' => 'Mar',
            'Apr' => 'Apr',
            'May' => 'Mei',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Ags',
            'Sep' => 'Sep',
            'Oct' => 'Okt',
            'Nov' => 'Nov',
            'Dec' => 'Des',
        ];

        $formatted = $carbon->format($format);

        foreach ($months as $english => $indonesian) {
            $formatted = str_replace($english, $indonesian, $formatted);
        }

        return $formatted;
    }
}
