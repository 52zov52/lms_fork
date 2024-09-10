<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class AcademicYearController extends Controller
{
    //Получение текущего учебного года в виде строки вида 0000/0000
    public static function getAcademicYear(): string
    {
        $month = (int)Carbon::now()->format('m');

        if ($month >= 9 && $month <= 12) {
            return Carbon::now()->format('Y') . '/' . Carbon::now()->addYear()->format('Y');
        } else {
            return Carbon::now()->subYear()->format('Y') . '/' . Carbon::now()->format('Y');
        }
    }

    //Получение текущего полугодия
    public static function getHalfYear(): int
    {
        $month = (int)Carbon::now()->format('m');

        if ($month >= 2 && $month <= 8) {
            return 2;
        } else {
            return 1;
        }
    }

    //Получение текущего года
    public static function getYear(): int
    {
        $currentYear = now()->year;
        $currentMonth = now()->month;

        if ($currentMonth >= 9 && $currentMonth <= 12) {
            return $currentYear = $currentYear;
        } else {
            return $currentYear = now()->subYear()->year;
        }
    }
}
