<?php

namespace App\Livewire;

use App\Models\Bulletin;
use App\Models\Group;
use App\Models\LevelEducation;
use App\Models\Speciality;
use App\Models\WorkStudyPlan;
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

        $bulletins = Bulletin::join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('bulletins.group_id', '=', $this->group_id)
            ->select(
                'bulletins.id',
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                'bulletins.bulletin_number',
                'bulletins.opening_date',
                'bulletins.closing_date'
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->get();

        return view('livewire.student-grades', [
            'listLevelEducations' => $listLevelEducations,
            'listSpecialities' => $listSpecialities,
            'listGroups' => $listGroups,
            'bulletins' => $bulletins
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
