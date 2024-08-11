<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\ActiveUser;
use App\Models\StudyThreadGroup;
use Carbon\Carbon;
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
        $currentYear = now()->year;
        $currentMonth = now()->month;

        if ($currentMonth >= 9 && $currentMonth <= 12) {
            $currentYear = $currentYear;
        } else {
            $currentYear = now()->subYear()->year;
        }

        $data = $this->transformer(DB::table('study_thread_subgroup_user')
            ->join('study_thread_subgroups', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('study_thread_normatives', 'study_threads.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->select(
                'study_thread_subgroup_user.user_id',
                'study_thread_subgroups.subgroup_name',
                'group_study_thread_subgroup.student_count',
                'groups.group_name',
                'study_threads.name as discipline',
                'study_thread_normatives.name as type_work',
                'study_threads.year',
                'study_threads.semester'
            )
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->where('study_threads.year', '=', $currentYear)
            ->orderBy('study_threads.semester')
            ->orderBy('groups.group_name')
            ->distinct()->get()->toArray());

        /*
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
        */
        return view('livewire.academic-workload', ['academicYear' => $this->getAcademicYear(), 'data' => $data]);
    }

    /*
    'discipline' => 
        ['type_work' => ['subgroup_name' => ['group_name', 'student_count']]]
        ['type_work' => ['subgroup_name' => ['group_name', 'student_count']]]
        ...
    */

    private function transformer($data): array
    {
        $divideData = $this->divideDataBySemiannual($data);
        $results = [];

        foreach ($divideData as $key => $value) {
            $unique_discipline = array_unique(array_column($value, 'discipline'));

            $disciplineData = [];
            foreach ($unique_discipline as $discipline) {
                $filtredDisciplines = array_filter($value, function ($item) use ($discipline) {
                    return $item->discipline == $discipline;
                });

                $unique_type_work = array_unique(array_column($filtredDisciplines, 'type_work'));

                $typeWorkData = [];
                foreach ($unique_type_work as $type_work) {
                    $filtredTypeWorks = array_filter($filtredDisciplines, function ($item) use ($type_work) {
                        return $item->type_work == $type_work;
                    });

                    $unique_subgroup_name = array_unique(array_column($filtredTypeWorks, 'subgroup_name'));

                    $subgroupData = [];
                    foreach ($unique_subgroup_name as $subgroup_name) {
                        $filtredSubgroups = array_filter($filtredTypeWorks, function ($item) use ($subgroup_name) {
                            return $item->subgroup_name == $subgroup_name;
                        });

                        $groupData = array_map(function ($item) {
                            return [
                                "student_count" => $item->student_count,
                                "group_name" => $item->group_name
                            ];
                        }, $filtredSubgroups);

                        $subgroupData[$subgroup_name] = $groupData;
                    }

                    $typeWorkData[$type_work] = $subgroupData;
                }

                $disciplineData[$discipline] = $typeWorkData;
            }

            $results[$key] = $disciplineData;
        }
        
        return $results;
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

    private function divideDataBySemiannual($data)
    {
        $oddSemesters = [];
        $evenSemesters = [];

        foreach ($data as $value) {
            if ($value->semester % 2 != 0) {
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
