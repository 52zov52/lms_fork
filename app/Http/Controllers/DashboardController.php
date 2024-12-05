<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public $additionalInfoAboutUser = '';

    public function index()
    {
        if (Auth::user()->hasRole('Student')) {
            $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')
                ->orderBy('group_student.id', 'desc')
                ->first();

            $this->additionalInfoAboutUser = $groupStudent->group_name;
        }

        $fio = Auth::user()->userData->last_name;

        $scheduleEventCount = Schedule::where('teacher', 'like', '%' . $fio . '%')
            ->where('date', '>=', now()->format('Y-m-d'))
            ->count();

        $schedulesEvents = Schedule::where('teacher', 'like', '%' . $fio . '%')
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('time', '>=', now()->format('H:i'))
            ->first();

        $eventCount = $scheduleEventCount;
        $event = 'Занятие по дис.: ' . $schedulesEvents->discipline . ' время: ' . $schedulesEvents->time;

        return view('dashboard', [
            'additionalInfoAboutUser' => $this->additionalInfoAboutUser,
            'eventCount' => $eventCount,
            'event' => $event
        ]);
    }
}
