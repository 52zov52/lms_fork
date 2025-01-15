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
    public $rectorateDecisionEventCount = 0;
    public $upbringingEventCount = 0;

    public $schedulesEvents = null;
    public $rectorateDecisionEvents = null;
    public $upbringingEvents = null;

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

            $firstSchedulesEvent = $querySchedulesEvents->orderBy('date', 'asc')->first();

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
            $fio = Auth::user()->userData->last_name . ' ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';
            $alternative_fio = Auth::user()->userData->last_name . '1 ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';

            $this->scheduleEventCount = Schedule::where(function ($query) use ($fio, $alternative_fio) {
                $query->where('teacher', $fio)
                    ->orWhere('teacher', $alternative_fio);
            })->where('date', '>=', $currentDate)
                ->select(DB::raw('COUNT(DISTINCT date, time) as count'))
                ->first()->count;

            $this->rectorateDecisionEventCount = RectorateAndCouncilDecision::where('category', '=', 1)
                ->where('executor_id', Auth::user()->id)
                ->where('due_date', '>=', Carbon::now()->format('Y-m-d'))
                ->select('task', 'date')
                ->count();

            $this->upbringingEventCount = UpbringingEvent::where('user_id', Auth::user()->id)
                ->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->select('name', 'date')
                ->count();

            $querySchedulesEvents = Schedule::where(function ($query) use ($fio, $alternative_fio) {
                $query->where('teacher', 'like', $fio)
                    ->orWhere('teacher', 'like', $alternative_fio);
            })->where('date', '>=', $currentDate);

            $firstSchedulesEvent = $querySchedulesEvents->orderBy('date', 'asc')->first();

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

            $this->rectorateDecisionEvents = RectorateAndCouncilDecision::where('category', '=', 1)
                ->where('executor_id', '=', Auth::user()->id)
                ->where('due_date', '>=', Carbon::now()->format('Y-m-d'))
                ->select('task', 'due_date')
                ->orderBy('due_date', 'asc')
                ->first();

            $this->upbringingEvents = UpbringingEvent::where('user_id', '=', Auth::user()->id)
                ->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->select('name', 'date')
                ->orderBy('date', 'asc')
                ->first();
        }

        $eventCount = ($this->scheduleEventCount + $this->rectorateDecisionEventCount + $this->upbringingEventCount);
        if (!is_null($this->schedulesEvents)) {
            $event = 'Расписание: ' . $this->schedulesEvents->discipline . ' дата: ' . Carbon::parse($this->schedulesEvents->date)->format('d.m.Y') . ' время: ' . $this->schedulesEvents->time;
        } else {
            if (!is_null($this->rectorateDecisionEvents) && !is_null($this->upbringingEvents)) {
                $rectorateDueDate = Carbon::parse($this->rectorateDecisionEvents->due_date);
                $upbringingDate = Carbon::parse($this->upbringingEvents->date);

                if ($rectorateDueDate->lte($upbringingDate)) {
                    $event = 'Решение ректората: ' . $this->rectorateDecisionEvents->task . ' дата: ' . $rectorateDueDate->format('d.m.Y');
                } else {
                    $event = 'Воспитательная работа: ' . $this->upbringingEvents->name . ' дата: ' . $upbringingDate->format('d.m.Y');
                }
            } else if (!is_null($this->rectorateDecisionEvents)) {
                $event = 'Решение ректората: ' . $this->rectorateDecisionEvents->task . ' дата: ' . Carbon::parse($this->rectorateDecisionEvents->due_date)->format('d.m.Y');
            } else if (!is_null($this->upbringingEvents)) {
                $event = 'Воспитательная работа: ' . $this->upbringingEvents->name . ' дата: ' . Carbon::parse($this->upbringingEvents->date)->format('d.m.Y');
            } else {
                $event = '-';
            }
        }

        return view('dashboard', [
            'additionalInfoAboutUser' => $this->additionalInfoAboutUser,
            'eventCount' => $eventCount,
            'event' => $event
        ]);
    }
}
