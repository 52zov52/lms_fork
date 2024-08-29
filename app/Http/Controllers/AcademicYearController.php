<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class AcademicYearController extends Controller
{
    public static function getAcademicYear() : string
    {
        $month = (int)Carbon::now()->format('m');
        $result = "";

        if ($month >= 9 && $month <= 12) {
            $result = Carbon::now()->format('Y') . '/' . Carbon::now()->addYear()->format('Y');
        } else {
            $result = Carbon::now()->subYear()->format('Y') . '/' . Carbon::now()->format('Y');
        }

        return $result;
    }
}
