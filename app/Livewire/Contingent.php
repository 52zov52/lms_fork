<?php

namespace App\Livewire;

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
        $groupId = [
            2406, 2409, 2397, 2410, 2393, 2414, 2415, 2416, 2404, 2417, 2407, 2411, 2412, 2413, 2361, 2402, 2362, 2405, 2392, 2352, 2353,
            2408, 2426, 2424, 2425, 2401, 2368, 2350, 2351, 2365, 2403, 2355, 2356, 2357, 2358, 2345, 2298, 2348, 2282, 2283, 2346, 2292,
            2349, 2301, 2302, 2347, 2288, 2289, 2290, 2291, 2303, 2304, 2385, 2337, 2422, 2339, 2308, 2398, 2222, 2340, 2395, 2341, 2306,
            2399, 2218, 2342, 2281, 2420, 2220, 2221, 2421, 2396, 2343, 2310, 2400, 2423, 2216, 2344, 2329, 2338, 2309, 2245, 2257,
            2369
        ];

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
        $this->contingent = GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->join('student_ticket_numbers', 'group_student.user_id', '=', 'student_ticket_numbers.user_id')
            ->select(
                'group_student.id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'student_ticket_numbers.ticket_number',
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id) as state_name")
            )
            ->where('group_student.group_id', '=', $this->groupId)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }

    public function spravka301($id)
    {
        return redirect()->route('spravka301', ['id' => $id]);
    }
}
