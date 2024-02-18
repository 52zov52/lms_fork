<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use App\Models\GroupTeacher;
use App\Models\Schedule;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyGroups extends Component
{
    public function render()
    {
        $countGroup = GroupTeacher::where('teacher_id', Auth::user()->id)->count();

        if ($countGroup == 0) {
            $findGroups = $this->searchGroups();
            $academicYear = $this->getAcademicYear(Carbon::now()->format('m'));

            foreach ($findGroups as $key => $value) {
                GroupTeacher::create([
                    'group_id' => $value,
                    'teacher_id' => Auth::user()->id,
                    'academic_year' => $academicYear
                ]);
            }
        }

        $groups = GroupTeacher::where('teacher_id', Auth::user()->id)
            ->join('groups', 'group_teachers.group_id', '=', 'groups.id')
            ->select('groups.id', 'groups.group_name')
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

    public function searchGroups()
    {
        $contingentController = new ContingentController();

        $userData = UserData::select('last_name', 'first_name', 'middle_name')->where('user_id', '=', Auth::user()->id)->first();
        $fio = $userData->last_name . ' ' . mb_substr($userData->first_name, 0, 1) . '.' . mb_substr($userData->middle_name, 0, 1) . '.';

        $listOfGroupName = Schedule::select('group')
            ->where('teacher', 'like', '%' . $fio . '%')
            ->distinct()
            ->get()->toArray();

        $groupsName = array();

        foreach ($listOfGroupName as $key => $value) {
            array_push($groupsName, str_replace('-', '', $value['group']));
        }

        $listOfGroupId = Group::select('id')
            ->whereIn(DB::raw("REPLACE(group_name, '-', '')"), $groupsName)
            ->get()->toArray();

        $groupsId = array();

        foreach ($listOfGroupId as $key => $value) {
            array_push($groupsId, str_replace('-', '', $value['id']));
        }

        return array_intersect($contingentController->groupId, $groupsId);
    }

    public function showTeacherCourse($groupId)
    {
        return redirect()->route('teacher.courses', ['groupId' => $groupId]);
    }

    public function getAvailableCourses($groupId)
    {
        return redirect()->route('available.courses', ['groupId' => $groupId]);
    }
}
