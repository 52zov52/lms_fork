<?php

namespace App\Livewire\Teacher;

use App\Models\ActiveUser;
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

        $groupIds = DB::table('study_thread_subgroup_user')
            ->join('study_thread_subgroups', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->where('study_threads.year', $currentYear)
            ->distinct()
            ->select('group_study_thread_subgroup.group_id')
            ->get();
        
        // Преобразование результата в массив, если необходимо
        $groupIdsArray = $groupIds->pluck('group_id')->toArray();

        $data = $this->transformer(DB::table('study_thread_subgroup_user')
            ->join('study_thread_subgroups', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('study_thread_normatives', 'study_threads.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->join('work_study_plan_courses_in_semesters', 'group_study_thread_subgroup.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
            ->select(
                'specialities.level_education_id',
                'study_thread_subgroups.subgroup_name',
                'group_study_thread_subgroup.student_count',
                'groups.group_name',
                'study_threads.name as discipline',
                'study_thread_normatives.name as type_work',
                'study_thread_normatives.sort_order',
                'study_threads.year',
                'study_threads.semester',
                DB::raw('CASE
                    WHEN study_threads.study_thread_normative_id IN (1, 2, 26, 34, 35) THEN work_study_plan_courses_in_semesters.control_hours
                    WHEN study_thread_normatives.hour_weight = 1 AND study_threads.study_thread_normative_id = 17 THEN work_study_plan_courses_in_semesters.lecture_hours
                    WHEN study_thread_normatives.hour_weight = 1 AND study_threads.study_thread_normative_id = 18 THEN work_study_plan_courses_in_semesters.practical_hours
                    WHEN study_thread_normatives.hour_weight = 1 AND study_threads.study_thread_normative_id = 19 THEN work_study_plan_courses_in_semesters.laboratory_hours
                    WHEN study_threads.study_thread_normative_id = 28 THEN work_study_plan_courses_in_semesters.individual_consultation_hours
                    WHEN study_threads.study_thread_normative_id IN (29, 30) THEN work_study_plan_courses_in_semesters.course_work_hours
                    WHEN study_threads.study_thread_normative_id = 32 THEN work_study_plan_courses_in_semesters.group_consultation_hours
                    WHEN study_threads.study_thread_normative_id = 36 THEN work_study_plan_courses_in_semesters.group_consultation_hours
                    WHEN study_threads.calculation_type <> 1 AND study_threads.study_thread_normative_id IN (1, 4, 21) THEN 0
                    WHEN study_thread_normatives.hour_weight = 8 AND study_threads.calculation_type <> 4 THEN study_thread_normatives.day_weight
                    ELSE study_thread_normatives.person_weight * group_study_thread_subgroup.student_count
                END as hours')
            )
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->where('study_threads.year', '=', $currentYear)
            ->whereIn('group_study_thread_subgroup.group_id', $groupIdsArray)
            ->orderBy('study_threads.semester')
            ->orderBy('groups.group_name')
            ->distinct()->get()->toArray());

        return view('livewire.teacher.academic-workload', ['academicYear' => $this->getAcademicYear(), 'data' => $data]);
    }

    private function transformer($data): array
    {
        $dividedDataByLevelEducation = $this->divideDataByLevelEducation($data);
        $dividedDataBySemiannual = $this->divideDataBySemiannual($dividedDataByLevelEducation);
        $results = [];

        foreach ($dividedDataBySemiannual as $levelEducationKey => $levelEducationValue) {
            $halfYearsData = [];
            foreach ($levelEducationValue as $halfYearKey => $halfYearValue) {
                $totalHours = 0;

                $unique_discipline = array_unique(array_column($halfYearValue, 'discipline'));

                $disciplineData = [];
                foreach ($unique_discipline as $discipline) {
                    $disciplineHours = 0;

                    $filtredDisciplines = array_filter($halfYearValue, function ($item) use ($discipline) {
                        return $item->discipline == $discipline;
                    });

                    usort($filtredDisciplines, function ($a, $b) {
                        return $a->sort_order <=> $b->sort_order;
                    });

                    $unique_semester_arrival = array_unique(array_column($filtredDisciplines, 'semester'));

                    $semesterArrivalData = [];
                    foreach ($unique_semester_arrival as $semester_arrival) {
                        $filtredDisciplinesBySemester = array_filter($filtredDisciplines, function ($item) use ($semester_arrival) {
                            return $item->semester == $semester_arrival;
                        });

                        $unique_type_work = array_unique(array_column($filtredDisciplinesBySemester, 'type_work'));

                        $typeWorkData = [];
                        foreach ($unique_type_work as $type_work) {
                            $filtredTypeWorks = array_filter($filtredDisciplinesBySemester, function ($item) use ($type_work) {
                                return $item->type_work == $type_work;
                            });

                            $unique_subgroup_name = array_unique(array_column($filtredTypeWorks, 'subgroup_name'));

                            $subgroupData = [];
                            foreach ($unique_subgroup_name as $subgroup_name) {
                                $subgroupHours = 0;

                                $filtredSubgroups = array_filter($filtredTypeWorks, function ($item) use ($subgroup_name) {
                                    return $item->subgroup_name == $subgroup_name;
                                });

                                $groupData = array_map(function ($item) {
                                    return [
                                        "student_count" => $item->student_count,
                                        "group_name" => $item->group_name
                                    ];
                                }, $filtredSubgroups);

                                $hours = array_values(array_unique(array_map(function ($item) {
                                    return $item->hours;
                                }, $filtredSubgroups)));

                                if ($type_work == 'Проверка письменной работы' || $type_work == 'Курсовая работа') {
                                    $subgroupHours = array_sum($hours);
                                } else {
                                    $subgroupHours = $hours[0];
                                }

                                $disciplineHours += $subgroupHours;
                                $totalHours += $subgroupHours;
                                $subgroupData[$subgroup_name] = ['subgroupHours' => $subgroupHours, 'groupData' => $groupData];
                            }
                            ksort($subgroupData);
                            $typeWorkData[$type_work] = $subgroupData;
                        }

                        $semesterArrivalData[$semester_arrival] = ['typeWorkData' => $typeWorkData];
                    }
                    $disciplineData[$discipline] = ['disciplineHours' => $disciplineHours, 'semesterArrivalData' => $semesterArrivalData];
                }
                ksort($disciplineData);
                $halfYearsData[$halfYearKey] = ['totalHours' => $totalHours, 'disciplineData' => $disciplineData];
            }

            $results[$levelEducationKey] = $halfYearsData;
        }

        return $results;
    }

    private function transformer_($data): array
    {
        $dividedDataByLevelEducation = $this->divideDataByLevelEducation($data);
        $dividedDataBySemiannual = $this->divideDataBySemiannual($dividedDataByLevelEducation);
        $results = [];

        foreach ($dividedDataBySemiannual as $levelEducationKey => $levelEducationValue) {
            $halfYearsData = [];
            foreach ($levelEducationValue as $halfYearKey => $halfYearValue) {
                $totalHours = 0;

                $unique_discipline = array_unique(array_column($halfYearValue, 'discipline'));

                $disciplineData = [];
                foreach ($unique_discipline as $discipline) {
                    $disciplineHours = 0;

                    $filtredDisciplines = array_filter($halfYearValue, function ($item) use ($discipline) {
                        return $item->discipline == $discipline;
                    });

                    usort($filtredDisciplines, function ($a, $b) {
                        return $a->sort_order <=> $b->sort_order;
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
                            $subgroupHours = 0;

                            $filtredSubgroups = array_filter($filtredTypeWorks, function ($item) use ($subgroup_name) {
                                return $item->subgroup_name == $subgroup_name;
                            });

                            $groupData = array_map(function ($item) {
                                return [
                                    "student_count" => $item->student_count,
                                    "group_name" => $item->group_name
                                ];
                            }, $filtredSubgroups);

                            $hours = array_values(array_unique(array_map(function ($item) {
                                return $item->hours;
                            }, $filtredSubgroups)));

                            if ($type_work == 'Проверка письменной работы' || $type_work == 'Курсовая работа') {
                                $subgroupHours = array_sum($hours);
                            } else {
                                $subgroupHours = $hours[0];
                            }

                            $disciplineHours += $subgroupHours;
                            $totalHours += $subgroupHours;
                            $subgroupData[$subgroup_name] = ['subgroupHours' => $subgroupHours, 'groupData' => $groupData];
                        }
                        ksort($subgroupData);
                        $typeWorkData[$type_work] = $subgroupData;
                    }
                    $disciplineData[$discipline] = ['disciplineHours' => $disciplineHours, 'typeWorkData' => $typeWorkData];
                }
                ksort($disciplineData);
                $halfYearsData[$halfYearKey] = ['totalHours' => $totalHours, 'disciplineData' => $disciplineData];
            }

            $results[$levelEducationKey] = $halfYearsData;
        }

        return $results;
    }

    private function divideDataByLevelEducation($data)
    {
        $spo = [];
        $vo = [];

        foreach ($data as $key => $value) {
            if ($value->level_education_id == 2) {
                $spo[] = $value;
            } else {
                $vo[] = $value;
            }
        }

        $result = array();

        if (count($spo) != 0) {
            $result['spo'] = $spo;
        }

        if (count($vo) != 0) {
            $result['vo'] = $vo;
        }

        return $result;
    }

    private function divideDataBySemiannual($data)
    {
        $result = [];

        foreach ($data as $key => $values) {
            $halfYears = [];
            $firstHalfYear = [];
            $secondHalfYear = [];

            foreach ($values as $items) {
                if ($items->semester == 1) {
                    $firstHalfYear[] = $items;
                } else {
                    $secondHalfYear[] = $items;
                }
            }

            $halfYears['first_half_year'] = $firstHalfYear;
            $halfYears['second_half_year'] = $secondHalfYear;

            $result[$key] = $halfYears;
        }

        return $result;
    }

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
