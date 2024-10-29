<?php

namespace App\Livewire\Student;

use App\Models\EJournal;
use App\Models\GroupStudent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyAttendance extends Component
{
    public function render()
    {
        $groupStudent = GroupStudent::select('id', 'group_id')
            ->where('user_id', '=', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->first();

        $eJournals = $this->getAttendanceBySemester(EJournal::select(
            'e_journals.id',
            'work_study_plan_courses_in_semesters.semester_number',
            'disciplines.name as discipline_name',
            'e_journals.status',
            DB::raw("DATE_FORMAT(e_journals.lesson_date, '%d.%m.%Y') as lesson_date"),
            'e_journals.lesson_time',
            'e_journals.lesson_type',
        )
            ->join('work_study_plan_courses_in_semesters', 'e_journals.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('group_id', '=', $groupStudent->group_id)
            ->where('student_id', '=', Auth::user()->id)
            ->orderBy('e_journals.lesson_date', 'asc')
            ->orderBy('e_journals.lesson_time', 'asc')
            ->get()->toArray());

        return view('livewire.student.my-attendance', [
            'eJournals' => $eJournals
        ]);
    }

    private function getAttendanceBySemester($eJournals)
    {
        $semesterData = [];
        $results = [];
        $semesters = array_unique(array_column($eJournals, 'semester_number'));

        foreach ($semesters as $key => $value) {
            $semesterData[$value] = array_filter($eJournals, function ($item) use ($value) {
                return $item['semester_number'] == $value;
            });
        }

        foreach ($semesterData as $semester => $data) {
            foreach ($data as $key => $value) {
                $tmp[$value['discipline_name']] = array_filter($data, function ($item) use ($value) {
                    return $item['discipline_name'] == $value['discipline_name'];
                });
            }
            $results[$semester] = $tmp;
        }

        return $results;
    }
}
