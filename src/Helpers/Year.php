<?php


namespace App\Helpers;


use Carbon\Carbon;

class Year
{
    public static function getYears($limit = 10) {
        $years = [];
        $thisYear = Carbon::today()->format('Y');
        for($i = $thisYear; $i >= $thisYear - $limit; $i --) {
            $years[$i] = $i;
        }
        return $years;
    }
}