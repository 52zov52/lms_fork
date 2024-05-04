<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\CourseTeacher;
use App\Models\EJournal as ModelsEJournal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EJournal extends Component
{
    public $courseTeacherId = 0;
    public $status = array();

    public $listfOfLessonType = [
        1 => 'Лекции',
        2 => 'Лабораторные',
        3 => 'Практические'
    ];

    public $listOfLessonTime = [
        '08:20-09:50',
        '10:00-11:30',
        '11:40-13:10',
        '13:30-15:00',
        '15:10-16:40',
        '17:00-18:30',
        '18:40-20:10',
        '20:20-21:50'
    ];

    public $listOfStatuses = [
        1 => '.',
        2 => 'Н',
        3 => 'НУ',
        4 => '2',
        5 => '3',
        6 => '4',
        7 => '5'
    ];

    public $lesson_date = '', $lesson_time = '', $lesson_type = 1, $lesson_theme = 'Тема';

    public $isCreatingAndEditingModalFormVisible = false;
    public $isEdit = false;

    public function mount($courseTeacherId)
    {
        $this->courseTeacherId = $courseTeacherId;
    }

    public function render()
    {
        $currentData = Carbon::now()->format('d.m.Y');
        $currentTime = $this->getLessonTime(Carbon::now()->format('H:i'));

        $courseTeacher = CourseTeacher::where('id', $this->courseTeacherId)->select('group_id')->first();

        $contingent = $this->getStudents($courseTeacher->group_id);

        $journal = ModelsEJournal::where('course_teacher_id', $this->courseTeacherId)->orderBy('lesson_date', 'asc')->get();
        $infoAboutGroupAndCourse = CourseTeacher::where('course_teacher.id', '=', $this->courseTeacherId)
            ->join('groups', 'course_teacher.group_id', '=', 'groups.id')
            ->join('work_study_plan_courses_in_semesters', 'course_teacher.course_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->select('groups.group_name', 'disciplines.name as discipline_name')
            ->first();

        foreach ($journal as $key => $value) {
            $this->status[$value->id] = $value->status;
        }

        $dates = ModelsEJournal::where('course_teacher_id', $this->courseTeacherId)
            ->select('lesson_date', 'lesson_time', 'lesson_type', 'is_signature')
            ->distinct()
            ->orderBy('lesson_date', 'asc')
            ->get();

        return view('livewire.e-journal', [
            'contingent' => $contingent,
            'journal' => $journal,
            'infoAboutGroupAndCourse' => $infoAboutGroupAndCourse,
            'dates' => $dates,
            'currentData' => $currentData,
            'currentTime' => $currentTime,
            'listfOfLessonType' => $this->listfOfLessonType,
            'listOfLessonTime' => $this->listOfLessonTime,
            'listOfStatuses' => $this->listOfStatuses
        ]);
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
    }

    public function creatingAndEditingModalFormVisible($isEdit): void
    {
        $this->isEdit = $isEdit;

        $this->lesson_date = Carbon::now()->format('d.m.Y');
        $this->lesson_time = $this->getLessonTime(Carbon::now()->format('H:i'));
        $this->lesson_type = 1;

        $this->lesson_theme = 'Тема ' . ($this->getCountLessons($this->lesson_type) + 1);

        $this->isCreatingAndEditingModalFormVisible = true;
    }

    public function store()
    {
        $courseTeacher = CourseTeacher::where('id', $this->courseTeacherId)->select('group_id')->first();
        $contingent = $this->getStudents($courseTeacher->group_id);

        foreach ($contingent as $key => $value) {
            ModelsEJournal::create([
                'student_id' => $value->user_id,
                'status' => 2,
                'lesson_date' => Carbon::parse($this->lesson_date)->format('Y-m-d'),
                'lesson_time' => $this->lesson_time,
                'lesson_type' => $this->lesson_type,
                'lesson_theme' => $this->lesson_theme,
                'course_teacher_id' => $this->courseTeacherId,
                'who_checked' => Auth::user()->id,
                'checked_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }

        $this->isCreatingAndEditingModalFormVisible = false;

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function destroy($param)
    {
        $eJournal = ModelsEJournal::where('lesson_date', '=', Carbon::parse($param['lesson_date'])->format('Y-m-d'))
            ->where('lesson_time', '=', $param['lesson_time'])
            ->where('lesson_type', '=', (int)$param['lesson_type'])
            ->where('course_teacher_id', '=', $this->courseTeacherId)
            ->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    public function setStatus($id, $studentId)
    {
        $eJournal = ModelsEJournal::where('id', '=', $id)
            ->where('student_id', '=', $studentId)->update([
                'status' => $this->status[$id],
                'who_checked' => Auth::user()->id,
                'checked_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }

    public function setSignature($param)
    {
        $eJournal = ModelsEJournal::where('lesson_date', '=', Carbon::parse($param['lesson_date'])->format('Y-m-d'))
            ->where('lesson_time', '=', $param['lesson_time'])
            ->where('lesson_type', '=', (int)$param['lesson_type'])
            ->where('course_teacher_id', '=', $this->courseTeacherId)
            ->update([
                'is_signature' => true,
                'who_signed' => Auth::user()->id,
                'signatured_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

        session()->flash('type', 'success');
        session()->flash('message', 'Журнал успешно подписан!');
    }

    public function setTypelesson()
    {
        $this->lesson_type = $this->lesson_type;
        $this->lesson_theme = 'Тема ' . ($this->getCountLessons($this->lesson_type) + 1);
    }

    protected function getCountLessons($lessonType)
    {
        return ModelsEJournal::where('course_teacher_id', $this->courseTeacherId)
            ->select('lesson_date', 'lesson_time', 'lesson_type')
            ->where('lesson_type', '=', $lessonType)
            ->distinct()
            ->get()->count();
    }

    public function getStudents($groupId)
    {
        $contingent = new ContingentController();
        return $contingent->getContingent($groupId);
    }

    public function getLessonTime($currentTime)
    {
        $lessonTime = "";

        if (strtotime($currentTime) >= strtotime('08:20') && strtotime($currentTime) <= strtotime('09:50')) {
            $lessonTime = "08:20-09:50";
        } elseif (strtotime($currentTime) > strtotime('09:50') && strtotime($currentTime) <= strtotime('11:30')) {
            $lessonTime = "10:00-11:30";
        } elseif (strtotime($currentTime) > strtotime('11:30') && strtotime($currentTime) <= strtotime('13:10')) {
            $lessonTime = "11:40-13:10";
        } elseif (strtotime($currentTime) > strtotime('13:10') && strtotime($currentTime) <= strtotime('15:00')) {
            $lessonTime = "13:30-15:00";
        } elseif (strtotime($currentTime) > strtotime('15:00') && strtotime($currentTime) <= strtotime('16:40')) {
            $lessonTime = "15:10-16:40";
        } elseif (strtotime($currentTime) > strtotime('16:40') && strtotime($currentTime) <= strtotime('18:30')) {
            $lessonTime = "17:00-18:30";
        } elseif (strtotime($currentTime) > strtotime('18:30') && strtotime($currentTime) <= strtotime('20:10')) {
            $lessonTime = "18:40-20:10";
        } elseif (strtotime($currentTime) > strtotime('20:10') && strtotime($currentTime) <= strtotime('21:50')) {
            $lessonTime = "20:20-21:50";
        } else {
            $lessonTime = "";
        }

        return $lessonTime;
    }
}
