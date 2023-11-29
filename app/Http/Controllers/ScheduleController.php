<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use XBase\TableReader;

class ScheduleController extends Controller
{
    public $daysOfWeek = [
        ['en' => 'Mon', 'ru' => 'Понедельник'],
        ['en' => 'Tue', 'ru' => 'Вторник'],
        ['en' => 'Wed', 'ru' => 'Среда'],
        ['en' => 'Thu', 'ru' => 'Четверг'],
        ['en' => 'Fri', 'ru' => 'Пятница'],
        ['en' => 'Sat', 'ru' => 'Суббота'],
        ['en' => 'Sun', 'ru' => 'Воскресенье'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachers(): object
    {
        return Schedule::select('teacher')->distinct()->orderBy('teacher', 'ASC')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersByLastName($lastName): object
    {
        return Schedule::select('teacher')
            ->where('teacher', 'like', '%' . $lastName . '%')
            ->distinct()
            ->orderBy('teacher', 'ASC')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeacherChair($fio): string
    {
        return Schedule::select('chair')->where('teacher', '=', $fio)->first()->chair;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGroups(): object
    {
        return Schedule::select('group')->distinct()->orderBy('group', 'ASC')->get();
    }

    public function getlastUpdate(): string
    {
        $schedule = Schedule::select('created_at')->latest('created_at')->first();

        return (is_null($schedule)) ? '' : Carbon::parse($schedule->created_at)->format('d.m.Y H:i:s');
    }

    public function uploadSchedule(): void
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

        Schedule::truncate();

        while ($record = $table->nextRecord()) {
            $classTime = array_search($record->get('LES'), array_column($schedule, 'number'));
            $groups = explode(',', $record->get('GROUP'));
            foreach ($groups as $group) {
                Schedule::create([
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

        asort($weeksRange);

        return $weeksRange;
    }

    public function getCurrentSchedule($date, $teacher): array
    {
        $currentSchedule = array();
        $schedule = array();

        $now = (is_null($date)) ? Carbon::now() : Carbon::parse($date);
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        if (Auth::user()->hasRole('Student')) {
            $groupName = explode('-', GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')->first()->group_name);

            if (count($groupName) == 2) {
                $group = $groupName[0] . $groupName[1];
            } else {
                $group = $groupName[0];
            }

            $schedule = Schedule::where('date', '>=', $weekStartDate)
                ->where('date', '<=', $weekEndDate)
                ->where('group', 'like', '%' . $group . '%')
                ->orderBy('date', 'ASC')
                ->orderBy('lesson_number', 'ASC')
                ->get()->toArray();
        } else if (Auth::user()->hasRole('Teacher')) {

            if (is_null($teacher)) {
                $fio = Auth::user()->userData->last_name . ' ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';
            } else {
                $fio = $teacher;
            }

            $schedule = Schedule::where('date', '>=', $weekStartDate)
                ->where('date', '<=', $weekEndDate)
                ->where('teacher', '=', $fio)
                ->orderBy('date', 'ASC')
                ->orderBy('lesson_number', 'ASC')
                ->get()->toArray();
        } else {
        }

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

    public function getScheduleByTeacher($fio): array
    {
        $teacherSchedule = array();

        $schedule = Schedule::where('teacher', '=', $fio)
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

            array_push($teacherSchedule, [
                'day' => Carbon::parse($dayOfLesson)->shortDayName,
                'date' => $dayOfLesson,
                'schedule' => array('data' => $data)
            ]);

            unset($data);
        }

        return $teacherSchedule;
    }

    public function getScheduleByGroup($group): array
    {
        $groupSchedule = array();

        $schedule = Schedule::where('group', '=', $group)
            ->orderBy('date', 'ASC')
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();

        $weeksOfLesson = array_unique(array_column($schedule, 'week'));

        foreach ($weeksOfLesson as $weekOfLesson) {
            $data = array();

            $daysOfWeek = $this->getScheduleByWeekAndGroup($weekOfLesson, $group);

            foreach ($daysOfWeek as $dayOfWeek) {
                $scheduleByDayAndGroup = $this->getScheduleByDayAndGroup($dayOfWeek->date, $group);
                $lessonNumbers = array_unique(array_column($scheduleByDayAndGroup, 'lesson_number'));

                $lessons = array();

                foreach ($lessonNumbers as $lessonNumber) {
                    $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayAndGroup, $lessonNumber);

                    $times = array_unique(array_column($scheduleByLessonNumber, 'time'));
                    $disciplines = array_unique(array_column($scheduleByLessonNumber, 'discipline'));
                    $teachers = array_unique(array_column($scheduleByLessonNumber, 'teacher'));
                    $lessonsPlace = array_unique(array_column($scheduleByLessonNumber, 'lesson_place'));
                    $lessonsType = array_unique(array_column($scheduleByLessonNumber, 'lesson_type'));

                    array_push($lessons, [
                        'lesson_number' => $lessonNumber,
                        'time' => $times[0],
                        'discipline' => $disciplines[0],
                        'teacher' => $teachers[0],
                        'lesson_place' => $lessonsPlace[0],
                        'lesson_type' => $lessonsType[0],
                    ]);
                }

                array_push($data, [
                    'day' => Carbon::parse($dayOfWeek->date)->shortDayName,
                    'date' => Carbon::parse($dayOfWeek->date)->format('d/m'),
                    'lessons' => $lessons
                ]);
            }

            array_push($groupSchedule, [
                'week' => $weekOfLesson,
                'data' => $data
            ]);

            unset($data);
        }

        return $groupSchedule;
    }

    public function getScheduleByWeek($week): array
    {
        $weekSchedule = array();

        $weekRange = explode('-', $week);

        $schedule = Schedule::where('date', '>=', Carbon::parse($weekRange[0])->format('Y-m-d'))
            ->where('date', '<=', Carbon::parse($weekRange[1])->format('Y-m-d'))
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

            array_push($weekSchedule, [
                'day' => Carbon::parse($dayOfLesson)->shortDayName,
                'date' => $dayOfLesson,
                'schedule' => array('data' => $data)
            ]);

            unset($data);
        }

        return $weekSchedule;
    }

    public function getScheduleByDayOfLesson($schedule, $dayOfLesson): array
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

    public function getScheduleByWeekAndGroup($week, $group): object
    {
        return Schedule::select('date')
            ->where('week', '=', $week)
            ->where('group', '=', $group)
            ->orderBy('date', 'ASC')
            ->distinct()
            ->get();
    }

    public function getScheduleByDayAndGroup($day, $group): array
    {
        return Schedule::where('date', '=', $day)
            ->where('group', '=', $group)
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();
    }
}
