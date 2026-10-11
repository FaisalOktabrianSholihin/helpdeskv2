<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class IndoDate
{
    private const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    /** Format meniru nowStr() di assets/js/app.js: "11 Feb 2025 09:23". */
    public static function format(?Carbon $date): ?string
    {
        if (! $date) {
            return null;
        }

        return sprintf(
            '%02d %s %d %02d:%02d',
            $date->day,
            self::BULAN[$date->month - 1],
            $date->year,
            $date->hour,
            $date->minute
        );
    }
}
