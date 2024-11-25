<?php

namespace App\Classes\Utils;

use DateTime;

class DateUtil
{
    private static function getTodayIfNull(string $date): string
    {
        return $date ? $date : date('Y-m-d');
    }

    public static function getTodayIfAnyNull(string $date1, string $date2): array
    {
        $newStartDate = self::getTodayIfNull($date1);
        $newEndDate = self::getTodayIfNull($date2);

        return ['date1' => $newStartDate, 'date2' => $newEndDate];
    }
}
