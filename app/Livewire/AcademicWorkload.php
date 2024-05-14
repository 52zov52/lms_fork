<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\ActiveUser;
use App\Models\StudyThreadGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AcademicWorkload extends Component
{
    protected $listUserId = array();

    public function mount()
    {
        $activeUser = ActiveUser::where('user_id', Auth::user()->id)->select('list_user_id')->first();

        if (!is_null($activeUser)) {
            $this->listUserId = json_decode($activeUser->list_user_id)->id;
        } else {
            $this->listUserId = array(Auth::user()->id);
        }
    }

    public function render()
    {
        $contingentController = new ContingentController();

        $data = $this->dataTransform(StudyThreadGroup::join('work_study_plan_courses_in_semesters', 'study_thread_groups.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_courses_in_semesters.id', '=', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->join('groups', 'study_thread_groups.group_id', '=', 'groups.id')
            ->whereIn('study_thread_groups.user_id', $this->listUserId)
            ->whereIn('study_thread_groups.group_id', $contingentController->groupId)
            ->select(
                'disciplines.name',
                'groups.group_name',
                'control_types.name as control_name',
                'work_study_plan_courses_in_semesters.lecture_hours',
                'work_study_plan_courses_in_semesters.practical_hours',
                'work_study_plan_courses_in_semesters.laboratory_hours',
                'work_study_plan_courses_in_semesters.semester_number'
            )
            ->distinct()->orderBy('disciplines.name', 'asc')->get());

        return view('livewire.academic-workload', ['data' => $data]);
    }

    private function dataTransform($data)
    {
        $transformedData = collect($data)->groupBy('name')->map(function ($items, $key) {
            $groupNames = $items->pluck('group_name')->unique()->implode(', ');            
            $semesterNumbers = $items->pluck('semester_number')->unique()->sort();                       
            $lectureHours = $items->pluck('lecture_hours')->first();           
            $practicalHours = $items->pluck('practical_hours')->first();           
            $laboratoryHours = $items->pluck('laboratory_hours')->first();            
            $controlName = $items->pluck('control_name')->first();
                    
            return [            
                'discipline_name' => $key,            
                'group_names' => $groupNames,            
                'control_name' => $controlName,            
                'lecture_hours' => $lectureHours,            
                'practical_hours' => $practicalHours,            
                'laboratory_hours' => $laboratoryHours,
                'semester_numbers' => $semesterNumbers->implode(', ')
            ];
        })->values();

        return $transformedData;
    }
}
