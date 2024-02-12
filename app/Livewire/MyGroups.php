<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MyGroups extends Component
{
    public function render()
    {
        return view('livewire.my-groups');
    }

    public function searchGroups()
    {
        $сontingentController = new ContingentController();

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

        $tmp = array_intersect($сontingentController->groupId, $groupsId);

        dd($tmp);
        die();
    }
}
