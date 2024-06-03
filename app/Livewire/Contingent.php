<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Contingent extends Component
{
    public int $groupId = 0;
    public $contingent = null;

    public function render()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $groups = Group::select('id', 'group_name')->whereIn('id', $groupId)->orderBy('group_name', 'asc')->get();

        $userInfo = DB::table('users_data')
            ->leftJoin('users_addresses', 'users_data.user_id', '=', 'users_addresses.user_id')
            ->leftJoin('countries', 'users_addresses.country_id', '=', 'countries.id')
            ->leftJoin('regions', 'users_addresses.region_id', '=', 'regions.id')
            ->leftJoin('cities', 'users_addresses.city_id', '=', 'cities.id')
            ->leftJoin('districts', 'users_addresses.district_id', '=', 'districts.id')
            ->where('users_data.user_id', '=', 26421)
            ->select(
                'users_data.last_name',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'users_data.birthday',
                'users_data.passport_series',
                'users_data.passport_number',
                'users_data.passport_issue_date',
                'users_data.passport_issuing_authority',
                'countries.country_name',
                'regions.region_name',
                'cities.city_name',
                'districts.district_name'
            )
            ->get();

        dd($userInfo);die();


        return view('livewire.contingent', [
            'groups' => $groups,
            'contingent' => $this->contingent
        ]);
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
    }

    public function getContingent(): void
    {
        $contingents = new ContingentController();
        $this->contingent = $contingents->getContingent($this->groupId);
    }

    public function generateListOfStudentsInGroupReport()
    {
        if ($this->groupId != 0) {
            return redirect()->route('generate.list.of.student.in.group.report', ['groupId' => $this->groupId]);
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }
}
