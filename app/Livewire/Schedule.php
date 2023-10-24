<?php

namespace App\Livewire;

use App\Http\Controllers\ScheduleController;
use App\Models\GroupStudent;
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

    public function render()
    {
        $this->teachers = ModelsSchedule::select('teacher')->distinct()->orderBy('teacher', 'ASC')->get();

        $weeks = ModelsSchedule::select('week', 'date')->distinct()->orderBy('week', 'ASC')->orderBy('date', 'ASC')->get()->toArray();
        $scheduleController = new ScheduleController();
        $this->weeksRange = $scheduleController->getWeeksRange($weeks);

        $this->groups = ModelsSchedule::select('group')->distinct()->orderBy('group', 'ASC')->get();

        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        $schedule = ModelsSchedule::where('date', '>=', $weekStartDate)
            ->where('date', '<=', $weekEndDate)
            ->where('teacher', 'like', '%Мизюков%')
            ->orderBy('date', 'ASC')
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();

        $this->currentSchedule = array();
        $daysOfLesson = array_unique(array_column($schedule, 'date'));

        foreach ($daysOfLesson as $dayOfLesson) {
            $data = array();

            $scheduleByDayOfLesson = $this->getScheduleByDayOfLesson($schedule, $dayOfLesson);
            $lessonNumbers = array_unique(array_column($scheduleByDayOfLesson, 'lesson_number'));

            foreach ($lessonNumbers as $lessonNumber) {
                $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber);
                dd($scheduleByLessonNumber);die();
            }

            

            unset($data);
        }

        if (Auth::user()->hasRole('Student')) {
            $groupName = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')->first();

            //$teacherName = mb_substr(Auth::user()->userData->last_name, 0, -1);
        } else if (Auth::user()->hasRole('Teacher')) {
        } else {
        }

        return view('livewire.schedule', [
            'teachers' => $this->teachers,
            'weeksRange' => $this->weeksRange,
            'groups' => $this->groups,
            'daysOfWeek' => $scheduleController->daysOfWeek,
            'currentSchedule' => $this->currentSchedule
        ]);
    }

    public function getScheduleByDayOfLesson($schedule, $dayOfLesson) : array
    {
        $scheduleByDayOfLesson = array();
        foreach ($schedule as $value) {
            if ($value['date'] == $dayOfLesson) {
                array_push($scheduleByDayOfLesson, [
                    'group' => $value['group'],
                    'course' => $value['course'],
                    'lesson_number' => $value['lesson_number'],
                    'date' => $value['date'],
                    'time' => $value['time'],
                    'lesson_place' => $value['lesson_place'],
                    'discipline' => $value['discipline'],
                    'lesson_type' => $value['lesson_type']
                ]);
            }
        }

        return $scheduleByDayOfLesson;
    }

    public function getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber)
    {
        $tmp = array();
        foreach ($scheduleByDayOfLesson as $value) {
            if ($value['lesson_number'] == $lessonNumber) {
                array_push($tmp, [
                    'group' => $value['group'],
                    'course' => $value['course'],
                    'lesson_number' => $value['lesson_number'],
                    'date' => $value['date'],
                    'time' => $value['time'],
                    'lesson_place' => $value['lesson_place'],
                    'discipline' => $value['discipline'],
                    'lesson_type' => $value['lesson_type']
                ]);
            }
        }

        return $tmp;
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
