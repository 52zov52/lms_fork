<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public $additionalInfoAboutUser = '';
    public $scheduleEventCount = 0;
    public $schedulesEvents = null;

    public function index()
    {
        if (Auth::user()->hasRole('Student')) {
            $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')
                ->orderBy('group_student.id', 'desc')
                ->first();

            $this->additionalInfoAboutUser = $groupStudent->group_name;

            $this->scheduleEventCount = Schedule::where('group', 'like', '%' . $groupStudent->group_name . '%')
                ->where('date', '>=', now()->format('Y-m-d'))
                ->count();

            $this->schedulesEvents = Schedule::where('group', 'like', '%' . $groupStudent->group_name . '%')
                ->where('date', '>=', now()->format('Y-m-d'))
                ->where('time', '>=', now()->format('H:i'))
                ->first();
        } else {
            if(Auth::user()->id != 2190) {
                $fio = Auth::user()->userData->last_name;

                $this->scheduleEventCount = Schedule::where('teacher', 'like', '%' . $fio . '%')
                    ->where('date', '>=', now()->format('Y-m-d'))
                    ->count();
    
                $this->schedulesEvents = Schedule::where('teacher', 'like', '%' . $fio . '%')
                    ->where('date', '>=', now()->format('Y-m-d'))
                    ->where('time', '>=', now()->format('H:i'))
                    ->first();
            }
        }

        $eventCount = $this->scheduleEventCount;
        if (!is_null($this->schedulesEvents)) {
            $event = 'Занятие по дис.: ' . $this->schedulesEvents->discipline . ' время: ' . $this->schedulesEvents->time;
        } else {
            $event = '-';
        }

        return view('dashboard', [
            'additionalInfoAboutUser' => $this->additionalInfoAboutUser,
            'eventCount' => $eventCount,
            'event' => $event
        ]);
    }
}
