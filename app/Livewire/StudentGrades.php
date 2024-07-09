<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\LevelEducation;
use App\Models\Speciality;
use App\Models\WorkStudyPlan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StudentGrades extends Component
{
    public $level_education_id = 0;
    public $speciality_id = 0;
    public $group_id = 0;
    public $list_wsp_id = [];

    public function render()
    {
        $listLevelEducations = LevelEducation::select('id', 'level_name')->get();
        $listSpecialities = Speciality::where('level_education_id', '=', $this->level_education_id)
            ->select('id', 'specialty_code', 'speciality_name')->get();

        $this->list_wsp_id = $this->dataTransformToWSP(WorkStudyPlan::where('speciality_id', '=', $this->speciality_id)
            ->select('id')
            ->get());

        $listGroups = Group::whereIn('work_study_plan_id', $this->list_wsp_id)->select('id', 'group_name')->get();

        $studentGrades = GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->join('student_grades', 'group_student.user_id', '=', 'student_grades.user_id')
            ->where('group_student.group_id', '=', $this->group_id)
            ->select(DB::raw("CONCAT(users_data.last_name) as fio"), 'student_grades.grade')
            ->get();

        return view('livewire.student-grades', [
            'listLevelEducations' => $listLevelEducations,
            'listSpecialities' => $listSpecialities,
            'listGroups' => $listGroups,
            'studentGrades' => $studentGrades
        ]);
    }

    public function setLevelEducationId()
    {
        $this->level_education_id = $this->level_education_id;
    }

    public function setSpecialityId()
    {
        $this->speciality_id = $this->speciality_id;
    }

    public function setGroupId()
    {
        $this->group_id = $this->group_id;
    }

    private function dataTransformToWSP($data)
    {
        $result = array();

        foreach ($data as $key => $value) {
            array_push($result, $value->id);
        }

        return $result;
    }
}
