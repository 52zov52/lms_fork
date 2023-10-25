<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public $daysOfWeek = [
        ['en' => 'Mon', 'ru' => 'Понедельник'],
        ['en' => 'Tue', 'ru' => 'Вторник'],
        ['en' => 'Wen', 'ru' => 'Среда'],
        ['en' => 'Thu', 'ru' => 'Четверг'],
        ['en' => 'Fri', 'ru' => 'Пятница'],
        ['en' => 'Sat', 'ru' => 'Суббота'],
        ['en' => 'Sun', 'ru' => 'Воскресенье'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachers() : object
    {
        return Schedule::select('teacher')->distinct()->orderBy('teacher', 'ASC')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGroups() : object
    {
        return Schedule::select('group')->distinct()->orderBy('group', 'ASC')->get();
    }

    public function getWeeksRange(): array
    {
        $weeks = Schedule::select('week', 'date')->distinct()->orderBy('week', 'ASC')->orderBy('date', 'ASC')->get()->toArray();

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

    public function getCurrentSchedule($date) : array
    {
        $currentSchedule = array();

        $now = (is_null($date)) ? Carbon::now() : Carbon::parse($date);
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        $schedule = Schedule::where('date', '>=', $weekStartDate)
            ->where('date', '<=', $weekEndDate)
            ->where('teacher', 'like', '%Мартынов%')
            //->where('group', 'like', '%К3И1(9)%')
            ->orderBy('date', 'ASC')
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();

        $daysOfLesson = array_unique(array_column($schedule, 'date'));

        foreach ($daysOfLesson as $dayOfLesson) {
            $data = array();

            $scheduleByDayOfLesson = $this->getScheduleByDayOfLesson($schedule, $dayOfLesson);
            $lessonNumbers = array_unique(array_column($scheduleByDayOfLesson, 'lesson_number'));

            foreach ($lessonNumbers as $lessonNumber) {
                $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber);
                
                $times = array_unique(array_column($scheduleByLessonNumber, 'time'));
                $disciplines = array_unique(array_column($scheduleByLessonNumber, 'discipline'));
                $lessonsPlace = array_unique(array_column($scheduleByLessonNumber, 'lesson_place'));
                $lessonsType = array_unique(array_column($scheduleByLessonNumber, 'lesson_type'));

                array_push($data, [
                    'lesson_number' => $lessonNumber,
                    'time' => $times[0],
                    'discipline' => $disciplines[0],
                    'lesson_place' => $lessonsPlace[0],
                    'lesson_type' => $lessonsType[0],
                    'data' => $scheduleByLessonNumber
                ]);
            }

            array_push($currentSchedule, [
                'day' => Carbon::parse($dayOfLesson)->shortEnglishDayOfWeek,
                'date' => $dayOfLesson,
                'schedule' => array('data' => $data)
            ]);

            unset($data);
        }

        return $currentSchedule;
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
                    'teacher' => $value['teacher'],
                    'lesson_type' => $value['lesson_type']
                ]);
            }
        }

        return $scheduleByDayOfLesson;
    }

    public function getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber)
    {
        $scheduleByLessonNumber = array();
        foreach ($scheduleByDayOfLesson as $value) {
            if ($value['lesson_number'] == $lessonNumber) {
                array_push($scheduleByLessonNumber, [
                    'group' => $value['group'],
                    'course' => $value['course'],
                    'lesson_number' => $value['lesson_number'],
                    'date' => $value['date'],
                    'time' => $value['time'],
                    'lesson_place' => $value['lesson_place'],
                    'discipline' => $value['discipline'],
                    'teacher' => $value['teacher'],
                    'lesson_type' => $value['lesson_type']
                ]);
            }
        }

        return $scheduleByLessonNumber;
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
