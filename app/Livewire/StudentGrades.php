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
    public $bulletins = null;

    public $level_education_id = 0;
    public $speciality_id = 0;
    public $group_id = 0;
    public $list_wsp_id = [];

    public $bulletin_number = null;

    public function render()
    {
        $listLevelEducations = LevelEducation::select('id', 'level_name')->get();
        $listSpecialities = Speciality::where('level_education_id', '=', $this->level_education_id)
            ->select('id', 'specialty_code', 'speciality_name')->get();

        $this->list_wsp_id = $this->dataTransformToWSP(WorkStudyPlan::where('speciality_id', '=', $this->speciality_id)
            ->select('id')
            ->get());

        $listGroups = Group::whereIn('work_study_plan_id', $this->list_wsp_id)->select('id', 'group_name')->orderBy('group_name')->get();

        $this->bulletins = $this->getBulletins();

        return view('livewire.student-grades', [
            'listLevelEducations' => $listLevelEducations,
            'listSpecialities' => $listSpecialities,
            'listGroups' => $listGroups,
            'bulletins' => $this->bulletins
        ]);
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
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
        $this->bulletin_number = null;
    }

    public function searchBulletinByNumber()
    {
        if (is_null($this->bulletin_number)) {
            session()->flash('type', 'error');
            session()->flash('message', 'Для поиска необходимо ввести номер ведомости!');
        } else {
            $this->level_education_id = 0;
            $this->speciality_id = 0;
            $this->group_id = 0;
        }
    }

    private function getBulletins()
    {
        if (is_null($this->bulletin_number)) {
            return Bulletin::join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
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
        } else {
            return Bulletin::join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
                ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
                ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
                ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
                ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
                ->where('bulletins.bulletin_number', '=', $this->bulletin_number)
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
        }
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
