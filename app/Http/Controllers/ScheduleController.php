<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function getWeeksRange($weeks): array
    {
        $uniqueWeeks = array_unique(array_column($weeks, 'week'));
        $weeksRange = array();

        foreach ($uniqueWeeks as $week) {
            $dateRange = $this->getDataByWeek($weeks, $week);

            $dates = array_column($dateRange, 'date');
            $min = min($dates);
            $max = max($dates);

            array_push($weeksRange, ['date' => Carbon::parse($min)->format('d.m.Y') . '-' . Carbon::parse($max)->format('d.m.Y')]);
        }

        return $weeksRange;
    }

    private function getDataByWeek($data, $week): array
    {
        $dateRange = array();

        foreach ($data as $value) {
            if ($value['week'] == $week) {
                array_push($dateRange, [
                    'week' => $value['week'],
                    'date' => $value['date']
                ]);
            }
        }

        return $dateRange;
    }
}
