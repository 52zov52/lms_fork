<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class AcademicYearController extends Controller
{
    public static function getAcademicYear() : string
    {
        $month = (int)Carbon::now()->format('m');

        if ($month >= 9 && $month <= 12) {
            return Carbon::now()->format('Y') . '/' . Carbon::now()->addYear()->format('Y');
        } else {
            return Carbon::now()->subYear()->format('Y') . '/' . Carbon::now()->format('Y');
        }
    }

    public static function getHalfYear() : int
    {
        $month = (int)Carbon::now()->format('m');

        if ($month >= 2 && $month <= 8) {
            return 2;
        } else {
            return 1;
        }
    }
}
