<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use App\Models\RectorateAndCouncilDecision;
use App\Models\Schedule;
use App\Models\UpbringingEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public $additionalInfoAboutUser = '';
    public $scheduleEventCount = 0;
    public $schedulesEvents = null;

    public function index()
    {
        $currentDate = now()->format('Y-m-d');
        $currentTime = now()->format('H:i');

        if (Auth::user()->hasRole('Student')) {
            $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')
                ->orderBy('group_student.id', 'desc')
                ->first();

            $this->additionalInfoAboutUser = $groupStudent->group_name;

            $this->scheduleEventCount = Schedule::where('group', 'like', '%' . $groupStudent->group_name . '%')
                ->where('date', '>=', $currentDate)
                ->select(DB::raw('COUNT(DISTINCT date, time) as count'))
                ->first()->count;

            $querySchedulesEvents = Schedule::where('group', 'like', '%' . $groupStudent->group_name . '%')
                ->where('date', '>=', $currentDate);

            $firstSchedulesEvent = $querySchedulesEvents->first();

            if ($firstSchedulesEvent) {
                $querySchedulesEvents->when($currentDate === $querySchedulesEvents->first()->date, function ($querySchedulesEvents) use ($currentTime) {
                    $querySchedulesEvents->where(function ($querySchedulesEvents) use ($currentTime) {
                        $querySchedulesEvents->whereRaw('SUBSTRING_INDEX(time, "-", 1) >= ?', [$currentTime]);
                    });
                });

                $this->schedulesEvents = $querySchedulesEvents->first();
            } else {
                $this->schedulesEvents = null;
            }
        } else {

            /*
            $rectorateDecisionEvent = RectorateAndCouncilDecision::where('category', '=', 1),
                ->where('executor_id', '=', Auth::user()->id)
                ->where('due_date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('due_date', '<=', Carbon::now()->addDays(2)->format('Y-m-d'))
                ->orderBy('due_date')
                ->get();

            $upbringingEvent = UpbringingEvent::where('user_id', '=', Auth::user()->id)
                ->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('date', '<=', Carbon::now()->addDays(2)->format('Y-m-d'))
                ->orderBy('date')
                ->get();
            */

            $fio = Auth::user()->userData->last_name . ' ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';
            $alternative_fio = Auth::user()->userData->last_name . '1 ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';

            $this->scheduleEventCount = Schedule::where(function ($query) use ($fio, $alternative_fio) {
                $query->where('teacher', $fio)
                    ->orWhere('teacher', $alternative_fio);
            })->where('date', '>=', $currentDate)
                ->select(DB::raw('COUNT(DISTINCT date, time) as count'))
                ->first()->count;

            $querySchedulesEvents = Schedule::where(function ($query) use ($fio, $alternative_fio) {
                $query->where('teacher', $fio)
                    ->orWhere('teacher', $alternative_fio);
            })->where('date', '>=', $currentDate);

            $firstSchedulesEvent = $querySchedulesEvents->first();

            if ($firstSchedulesEvent) {
                $querySchedulesEvents->when($currentDate === $firstSchedulesEvent->date, function ($querySchedulesEvents) use ($currentTime) {
                    $querySchedulesEvents->where(function ($querySchedulesEvents) use ($currentTime) {
                        $querySchedulesEvents->whereRaw('SUBSTRING_INDEX(time, "-", 1) >= ?', [$currentTime]);
                    });
                });

                $this->schedulesEvents = $querySchedulesEvents->first();
            } else {
                $this->schedulesEvents = null;
            }
        }

        $eventCount = $this->scheduleEventCount;
        if (!is_null($this->schedulesEvents)) {
            $event = 'Расписание: ' . $this->schedulesEvents->discipline . ' дата: ' . Carbon::parse($this->schedulesEvents->date)->format('d.m.Y') . ' время: ' . $this->schedulesEvents->time;
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
