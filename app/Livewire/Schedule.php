<?php

namespace App\Livewire;

use App\Http\Controllers\ScheduleController;
use App\Models\Schedule as ModelsSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use XBase\TableReader;

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
        $schedule = [
            ['number' => 1, 'time' => '08:20-09:50'],
            ['number' => 2, 'time' => '10:00-11:30'],
            ['number' => 3, 'time' => '11:40-13:10'],
            ['number' => 4, 'time' => '13:30-15:00'],
            ['number' => 5, 'time' => '15:10-16:40'],
            ['number' => 6, 'time' => '17:00-18:30'],
            ['number' => 7, 'time' => '18:40-20:10'],
            ['number' => 8, 'time' => '20:20-21:50']
        ];

        $contents = file_get_contents('https://iubip.ru/upload/iblock/ab7/vr7vuzn1u41ly0mmcs6tah16c2ytu59k.DBF');
        Storage::disk('public')->put('schedule/RASP.DBF', $contents);

        $path  = Storage::disk('public')->path('schedule/RASP.DBF');;

        $table = new TableReader($path, [
            'encoding' => 'cp1251'
        ]);

        ModelsSchedule::truncate();

        while ($record = $table->nextRecord()) {
            $classTime = array_search($record->get('LES'), array_column($schedule, 'number'));
            $groups = explode(',', $record->get('GROUP'));
            foreach ($groups as $group) {
                ModelsSchedule::create([
                    'group' => $group,
                    'course' => $record->get('COURSE'),
                    'lesson_number' => $record->get('LES'),
                    'day' => $record->get('DAY'),
                    'week' => $record->get('WEEK'),
                    'date' => Carbon::parse($record->get('DATE'))->format('Y-m-d'),
                    'time' => $schedule[$classTime]['time'],
                    'lesson_place' => $record->get('AUD'),
                    'discipline' => $record->get('SUBJECT'),
                    'lesson_type' => $record->get('SUBJ_TYPE'),
                    'teacher' => $record->get('NAME'),
                    'chair' => $record->get('CAFEDRA')
                ]);
            }
        }

        session()->flash('message-success', 'Данные расписания обновлены!');
    }
}
