<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Speciality;
use App\Models\StudentsAcademicLeave;
use App\Models\StudyPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use PhpOffice\PhpWord\TemplateProcessor;
use PHPUnit\Framework\Constraint\IsEmpty;

class Contingent extends Component
{
    public int $groupId = 0;
    public $contingent = null;

    public function render()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $groups = Group::select('id', 'group_name')->whereIn('id', $groupId)->orderBy('group_name', 'asc')->get();
        $studentsAcademicLeaves = StudentsAcademicLeave::select(
            'students_academic_leaves.id',
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name',
            'groups.group_name',
            'students_academic_leaves.academic_leave_end_date',
            'students_academic_leaves.description'
        )
            ->join('users_data', 'students_academic_leaves.user_id', '=', 'users_data.user_id')
            ->join('groups', 'students_academic_leaves.group_id', '=', 'groups.id')
            ->where('is_academic_leave', '=', 1)
            ->orderBy('students_academic_leaves.academic_leave_end_date')
            ->get();

        return view('livewire.contingent', [
            'groups' => $groups,
            'contingent' => $this->contingent,
            'studentsAcademicLeaves' => $studentsAcademicLeaves
        ]);
    }

    public function getContingent(): void
    {
        $contingents = new ContingentController();
        $this->contingent = $contingents->getContingent($this->groupId);
    }

    public function spravka301($id)
    {
        return redirect()->route('spravka301', ['id' => $id]);
    }
}
