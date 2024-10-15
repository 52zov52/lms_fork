<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\GroupStudent;
use App\Models\StudyThread;
use Illuminate\Support\Facades\DB;

class ContingentController extends Controller
{
    public function getGroupIds(): array
    {
        return $this->extractGroupIds(StudyThread::join('study_thread_subgroups', 'study_threads.id', '=', 'study_thread_subgroups.study_thread_id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->where('study_threads.year', '=', 2024)
            ->select('groups.id')
            ->distinct()
            ->get()->toArray());
    }

    public function getContingent($groupId): object
    {
        return GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->select(
                'group_student.user_id',
                'group_student.id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = group_student.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name")
            )
            ->where('group_student.group_id', '=', $groupId)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }

    private function extractGroupIds($listGroupId): array
    {
        return array_map(function ($item) {
            return $item['id'];
        }, $listGroupId);
    }
}
