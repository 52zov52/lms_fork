<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Arabic;

class MyGroups extends Component
{
    public function render()
    {
        $groups = DB::table('tmp_group_teacher')->where('teacher_id', Auth::user()->id)->get();

        if (count($groups) == 0) {
        }

        return view('livewire.my-groups', ['groups' => $this->searchGroups()]);
    }

    public function getDisciplines($groupId)
    {
        $group = Group::select('course', 'study_plan_id')->where('id', $groupId)->first();
        $month = Carbon::now()->format('m');

        $semester = 0;

        switch ($group->course) {
            case 1:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 1 : 2;
                break;
            case 2:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 3 : 4;
                break;
            case 3:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 5 : 6;
                break;
            case 4:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 7 : 8;
                break;
            case 5:
                $semester = ((int)$month >= 9 && (int)$month <= 12) ? 9 : 10;
                break;
            default:
                break;
        }

        $res = DB::table('study_plan_courses')
            ->join('study_plan_courses_in_semesters', 'study_plan_courses.id', '=', 'study_plan_courses_in_semesters.study_plan_course_id')
            ->join('disciplines', 'study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('study_plan_courses.study_plan_id', '=', $group->study_plan_id)
            ->where('study_plan_courses_in_semesters.semester_number', $semester)
            ->select('study_plan_courses_in_semesters.id', 'disciplines.name', 'study_plan_courses_in_semesters.semester_number')
            ->get()->toArray();

        dd($res);
        die();
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
            $result = "каникулы";
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
}
