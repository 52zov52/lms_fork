<?php

namespace App\Livewire;

use App\Models\CourseTeacher;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AvailableCourses extends Component
{
    public $groupId = 0;
    public bool $isAlertVisible = false;

    public function mount($groupId)
    {
        $this->groupId = $groupId;
    }

    public function render()
    {
        $listOfDisciplines = $this->getDisciplines($this->groupId);

        return view('livewire.available-courses', ['listOfDisciplines' => $listOfDisciplines]);
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function getDisciplines($groupId)
    {
        $group = Group::select('course', 'study_plan_id')->where('id', $groupId)->first();
        $month = Carbon::now()->format('m');

        $semester = 0;

        switch ($group->course) {
            case 1:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 1 : 2;
                break;
            case 2:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 3 : 4;
                break;
            case 3:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 5 : 6;
                break;
            case 4:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 7 : 8;
                break;
            case 5:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 9 : 10;
                break;
            default:
                break;
        }

        return DB::table('study_plan_courses')
            ->join('study_plan_courses_in_semesters', 'study_plan_courses.id', '=', 'study_plan_courses_in_semesters.study_plan_course_id')
            ->join('study_plan_course_control_types_in_semesters', 'study_plan_courses_in_semesters.id', '=', 'study_plan_course_control_types_in_semesters.study_plan_courses_in_semester_id')
            ->join('control_types', 'study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('study_plan_courses.study_plan_id', '=', $group->study_plan_id)
            ->where('study_plan_courses_in_semesters.semester_number', $semester)
            ->select(
                'study_plan_courses_in_semesters.id',
                'disciplines.name',
                'control_types.name as control_name',
                'study_plan_courses_in_semesters.lecture_hours',
                'study_plan_courses_in_semesters.practical_hours',
                'study_plan_courses_in_semesters.laboratory_hours',
                'study_plan_courses_in_semesters.semester_number'
            )
            ->get()->toArray();
    }

    public function getAcademicYear($currentMonth)
    {
        $month = (int)$currentMonth;
        $result = "";

        if ($month >= 9 && $month <= 12) {
            $result = Carbon::now()->format('Y') . '/' . Carbon::now()->addYear()->format('Y');
        } elseif ($month >= 1 && $month < 7) {
            $result = Carbon::now()->subYear()->format('Y') . '/' . Carbon::now()->format('Y');
        } else {
            $result = "Каникулы";
        }

        return $result;
    }

    public function subscribeToCourse($courseId)
    {
        $academicYear = $this->getAcademicYear(Carbon::now()->format('m'));

        $findCourse = CourseTeacher::where('course_id', '=', $courseId)
            ->where('group_id', '=', $this->groupId)
            ->where('teacher_id', '=', Auth::user()->id)
            ->where('academic_year', '=', $academicYear)
            ->count();

        if ($findCourse == 0) {
            CourseTeacher::create([
                'course_id' => $courseId,
                'group_id' => $this->groupId,
                'teacher_id' => Auth::user()->id,
                'academic_year' => $academicYear
            ]);
        }

        $this->isAlertVisible = true;

        session()->flash('message-success', 'Дисциплина успешно добавлена в список дисциплин эл. журнала на текущий уч. год');
    }
}
