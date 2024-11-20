<?php

namespace App\Livewire;

use App\Http\Controllers\ScheduleController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Schedule extends Component
{
    public $teachers = [], $groups = [], $weeksRange = [];
    public $currentSchedule = [];
    public $teacher = null, $week = null, $weekRange = null;

    public function render()
    {
        $scheduleController = new ScheduleController();

        if (Auth::user()->hasRole('Teacher'))
            $this->teachers = $scheduleController->getTeachersByLastName(Auth::user()->userData->last_name);

        $this->weeksRange = $scheduleController->getWeeksRange();
        $lastUpdateDate = $scheduleController->getlastUpdate();

        $this->currentSchedule = $scheduleController->getCurrentSchedule($this->week, $this->teacher);

        $now = (is_null($this->week)) ? Carbon::now() : Carbon::parse($this->week);
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');

        $day = $weekStartDate;
        $daysOfWeek = array();

        for ($i = 0; $i < 7; $i++) {
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

    public function changeTeacher(): void
    {
        $this->teacher = $this->teacher;
    }

    public function changeWeek(): void
    {
        $this->week = $this->splitWeek();
    }

    public function splitWeek(): string
    {
        $data = explode('-', $this->weekRange);
        return $data[0];
    }

    public function uploadSchedule()
    {
        $scheduleController = new ScheduleController();
        $scheduleController->uploadSchedule();

        $this->dispatch('show-toast', message: 'Данные расписания обновлены', type: 'success');
    }

    public function exportScheduleToIcs()
    {
        $scheduleController = new ScheduleController();
        $schedules = $scheduleController->getCurrentSchedule($this->week, $this->teacher);

        $content = 'BEGIN:VCALENDAR' . PHP_EOL;
        $content .= 'VERSION:2.0' . PHP_EOL;
        $content .= PHP_EOL;

        foreach ($schedules as $schedule) {
            foreach ($schedule['schedule']['data'] as $value) {
                $str = '';
                $times = explode('-', $value['time']);

                $content .= 'BEGIN:VEVENT' . PHP_EOL;
                $content .= 'SUMMARY:' . $value['discipline'] . ' (' . $value['lesson_type'] . ')' . PHP_EOL;
                $content .= 'DTSTART:' . Carbon::parse($schedule['date'])->format('Ymd') . 'T' . Carbon::parse($times[0])->format('His') . PHP_EOL;
                $content .= 'DTEND:' . Carbon::parse($schedule['date'])->format('Ymd') . 'T' . Carbon::parse($times[1])->format('His') . PHP_EOL;
                $content .= 'DTSTAMP:' . now()->format('Ymd') . 'T' . now()->format('His') . PHP_EOL;
                $content .= 'LOCATION:ауд. ' . $value['lesson_place'] . PHP_EOL;

                foreach ($value['data'] as $item) {
                    if (Auth::user()->hasRole('Student')) {
                        $str = $item['teacher'];
                    } else if (Auth::user()->hasRole('Teacher')) {
                        $str .= $item['group'] . ' ';
                    } else {
                        $str = '';
                    }
                }

                $content .= 'DESCRIPTION:' . $str . PHP_EOL;
                $content .= 'UID:' . uniqid('', true) . PHP_EOL;
                $content .= 'ORGANIZER:ИУБиП' . PHP_EOL;
                $content .= 'STATUS:CONFIRMED' . PHP_EOL;
                $content .= 'PRIORITY:0' . PHP_EOL;
                $content .= 'END:VEVENT' . PHP_EOL;
                $content .= PHP_EOL;
            }
        }

        $content .= 'END:VCALENDAR';

        $filename = time() . '.ics';
        $tempFilePath = 'public/temp/' . $filename;

        Storage::put($tempFilePath, $content);
        $fullTempFilePath = Storage::path($tempFilePath);

        return Response::download($fullTempFilePath, $filename)->deleteFileAfterSend(true);
    }
}
