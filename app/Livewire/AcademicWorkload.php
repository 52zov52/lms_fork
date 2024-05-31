<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\ActiveUser;
use App\Models\StudyThreadGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

        $data = $this->sanitizeData(StudyThreadGroup::join('work_study_plan_courses_in_semesters', 'study_thread_groups.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_courses_in_semesters.id', '=', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->join('groups', 'study_thread_groups.group_id', '=', 'groups.id')
            ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
            ->whereIn('study_thread_groups.user_id', $this->listUserId)
            ->whereIn('study_thread_groups.group_id', $contingentController->groupId)
            ->select(
                'disciplines.name as discipline_name',
                'groups.group_name',
                'groups.course',
                'control_types.name as control_name',
                'work_study_plans.teaching_form_id',
                'work_study_plan_courses_in_semesters.lecture_hours',
                'work_study_plan_courses_in_semesters.practical_hours',
                'work_study_plan_courses_in_semesters.laboratory_hours',
                'work_study_plan_courses_in_semesters.group_consultation_hours',
                'work_study_plan_courses_in_semesters.semester_number'
            )
            ->distinct()->orderBy('disciplines.name', 'asc')->get()->toArray());

        return view('livewire.academic-workload', ['academicYear' => $this->getAcademicYear(), 'data' => $data]);
    }

    private function getSemesters($teachingForm, $course): array
    {
        $currentSemesters = array();

        switch ($course) {
            case 1:
                if ($teachingForm == 1) {
                    $currentSemesters = [1, 2];
                }

                if ($teachingForm == 2) {
                    $currentSemesters = [1, 2, 3];
                }
                break;
            case 2:
                if ($teachingForm == 1) {
                    $currentSemesters = [3, 4];
                }

                if ($teachingForm == 2) {
                    $currentSemesters = [3, 4, 5];
                }
                break;
            case 3:
                if ($teachingForm == 1) {
                    $currentSemesters = [5, 6];
                }

                if ($teachingForm == 2) {
                    $currentSemesters = [6, 7, 8];
                }
                break;
            case 4:
                if ($teachingForm == 1) {
                    $currentSemesters = [7, 8];
                }

                if ($teachingForm == 2) {
                    $currentSemesters = [9, 10, 11];
                }
                break;
            default:
                break;
        }

        return $currentSemesters;
    }

    private function sanitizeData($data)
    {
        $sanitizedData = array();
        foreach ($data as $key => $value) {
            if (in_array($value['semester_number'], $this->getSemesters($value['teaching_form_id'], $value['course']))) {
                array_push($sanitizedData, $value);
            }
        }

        return $this->divideDataBySemiannual($sanitizedData);
    }

    private function divideDataBySemiannual($sanitizedData)
    {
        $oddSemesters = [];
        $evenSemesters = [];

        foreach ($sanitizedData as $value) {
            if ($value['semester_number'] % 2 != 0) {
                $oddSemesters[] = $value;
            } else {
                $evenSemesters[] = $value;
            }
        }

        $result = array();

        $result['first_half_year'] = $oddSemesters;
        $result['second_half_year'] = $evenSemesters;

        return $result;
    }

    /*
    private function dataTransform($data)
    {
        $oddSemesters = [];
        $evenSemesters = [];

        foreach ($data as $value) {
            if ($value->semester_number % 2 != 0) {
                $oddSemesters[] = $value;
            } else {
                $evenSemesters[] = $value;
            }
        }

        $transformedData['first_half_year'] = $this->collectData($oddSemesters);
        $transformedData['second_half_year'] = $this->collectData($evenSemesters);

        return $transformedData;
    }

    private function collectData($data)
    {
        return collect($data)->groupBy('name')->map(function ($items, $key) {
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
    }
    */
    private function getAcademicYear()
    {
        $month = (int)Carbon::now()->format('m');
        $result = "";

        if ($month >= 9 && $month <= 12) {
            $result = Carbon::now()->format('Y') . '/' . Carbon::now()->addYear()->format('Y');
        } else {
            $result = Carbon::now()->subYear()->format('Y') . '/' . Carbon::now()->format('Y');
        }

        return $result;
    }
}
