<?php

namespace App\Livewire;

use App\Http\Controllers\ScheduleController;
use App\View\Components\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Schedule extends Component
{
    public $teachers = [], $groups = [], $weeksRange = [];
    public $currentSchedule = [];
    public $teacher = null, $week = null, $weekRange = null;

    public function render()
    {
        $scheduleController = new ScheduleController();
        
        if(Auth::user()->hasRole('Teacher'))
            $this->teachers = $scheduleController->getTeachersByLastName(Auth::user()->userData->last_name);
        
        $this->weeksRange = $scheduleController->getWeeksRange();
        $lastUpdateDate = $scheduleController->getlastUpdate();

        $this->currentSchedule = $scheduleController->getCurrentSchedule($this->week, $this->teacher);

        $now = (is_null($this->week)) ? Carbon::now() : Carbon::parse($this->week);
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');

        $day = $weekStartDate;
        $daysOfWeek = array();

        for ($i=0; $i < 7; $i++) {
            array_push($daysOfWeek, [
                'en' => $scheduleController->daysOfWeek[$i]['en'],
                'ru' => $scheduleController->daysOfWeek[$i]['ru'],
                'date' => Carbon::parse($day)->format('d.m.Y')
            ]);

            $day = Carbon::parse($day)->addDay()->format('Y-m-d');
        }

        return view('livewire.schedule', [
            'teachers' => $this->teachers,
            'weeksRange' => $this->weeksRange,
            'daysOfWeek' => $daysOfWeek,
            'lastUpdateDate' => $lastUpdateDate,
            'currentSchedule' => $this->currentSchedule
        ]);
    }

    public function changeTeacher() : void
    {
        $this->teacher = $this->teacher;
    }

    public function changeWeekRange() : void
    {
        $this->week = $this->splitWeekRange();
    }

    public function splitWeekRange() : string
    {
        $data = explode('-', $this->weekRange);
        return $data[0];
    }

    public function uploadSchedule()
    {
        $scheduleController = new ScheduleController();
        $scheduleController->uploadSchedule();
        
        session()->flash('message-success', 'Данные расписания обновлены!');
    }
}
