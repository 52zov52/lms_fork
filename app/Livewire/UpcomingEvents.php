<?php

namespace App\Livewire;

use App\Models\GroupStudent;
use App\Models\RectorateAndCouncilDecision;
use App\Models\Schedule;
use App\Models\UpbringingEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpcomingEvents extends Component
{
    public $schedulesEvents = null;
    public $rectorateDecisionEvents = null;
    public $upbringingEvents = null;

    public function render()
    {
        $fio = Auth::user()->userData->last_name . ' ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';
        $alternative_fio = Auth::user()->userData->last_name . '1 ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';

        if (Auth::user()->hasRole('Student')) {
            $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')
                ->orderBy('group_student.id', 'desc')
                ->first();

            $this->schedulesEvents = Schedule::where('group', 'like', '%' . $groupStudent->group_name . '%')
                ->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
                ->get();
        } else {
            $this->schedulesEvents = Schedule::where(function ($query) use ($fio, $alternative_fio) {
                $query->where('teacher', $fio)
                    ->orWhere('teacher', $alternative_fio);
            })->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
                ->get();

            $this->rectorateDecisionEvents = RectorateAndCouncilDecision::where('category', '=', 1)
                ->where('executor_id', Auth::user()->id)
                ->where('due_date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('due_date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
                ->get();

            $this->upbringingEvents = UpbringingEvent::where('user_id', Auth::user()->id)
                ->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
                ->get();
        }

        return view('livewire.upcoming-events', [
            'schedulesEvents' => $this->schedulesEvents,
            'rectorateDecisionEvents' => $this->rectorateDecisionEvents,
            'upbringingEvents' => $this->upbringingEvents
        ]);
    }
}
