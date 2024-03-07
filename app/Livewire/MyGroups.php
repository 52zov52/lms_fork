<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\ActiveUser;
use App\Models\CourseTeacher;
use App\Models\GroupTeacher;
use App\Models\StudyThreadGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyGroups extends Component
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
        $academicYear = $this->getAcademicYear(Carbon::now()->format('m'));
        $countGroup = GroupTeacher::where('teacher_id', Auth::user()->id)->count();

        if ($countGroup == 0) {
            $findGroups = $this->searchGroups($this->listUserId);

            foreach ($findGroups as $group) {
                GroupTeacher::create([
                    'group_id' => $group,
                    'teacher_id' => Auth::user()->id,
                    'academic_year' => $academicYear
                ]);

                $availableCourses = $this->getAvailableCourses($this->listUserId, $group);
                foreach ($availableCourses as $course) {
                    CourseTeacher::create([
                        'course_id' => $course->work_study_plan_courses_in_semester_id,
                        'group_id' => $course->group_id,
                        'teacher_id' => Auth::user()->id,
                        'academic_year' => $academicYear
                    ]);
                }
            }
        }

        $groups = GroupTeacher::where('group_teacher.teacher_id', Auth::user()->id)
            ->join('groups', 'group_teacher.group_id', '=', 'groups.id')
            ->leftJoin('course_teacher', 'groups.id', '=', 'course_teacher.group_id')
            ->select(
                'groups.id',
                'groups.group_name',
                DB::raw('COUNT(course_teacher.group_id) as count_courses')
            )
            ->groupBy('groups.id')
            ->get();

        return view('livewire.my-groups', ['groups' => $groups]);
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

    public function searchGroups($listUserId)
    {
        $contingentController = new ContingentController();

        $studyThreadGroup = StudyThreadGroup::whereIn('user_id', $listUserId)->select('group_id')->distinct()->get()->toArray();

        $groupsId = array();

        foreach ($studyThreadGroup as $key => $value) {
            array_push($groupsId, $value['group_id']);
        }

        return array_intersect($contingentController->groupId, $groupsId);
    }

    public function getAvailableCourses($listUserId, $groupId)
    {
        return StudyThreadGroup::whereIn('user_id', $listUserId)
            ->where('group_id', '=', $groupId)
            ->select('group_id', 'work_study_plan_courses_in_semester_id')
            ->distinct()->get();
    }

    public function showTeacherCourse($groupId)
    {
        return redirect()->route('teacher.courses', ['groupId' => $groupId]);
    }
}
