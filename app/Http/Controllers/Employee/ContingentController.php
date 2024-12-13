<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\GroupStudent;
use App\Models\StudentTicketNumber;
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
                DB::raw("(SELECT group_name FROM groups WHERE id = group_student.group_id ORDER BY id DESC LIMIT 1) as group_name"),
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = group_student.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name")
            )
            ->where('group_student.group_id', '=', $groupId)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }

    public function searchContingentByTicketNumber($ticketNumber): object
    {
        return StudentTicketNumber::join('users_data', 'student_ticket_numbers.user_id', '=', 'users_data.user_id')
            ->join('group_student', 'student_ticket_numbers.user_id', '=', 'group_student.user_id')
            ->select(
                'student_ticket_numbers.user_id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'student_ticket_numbers.ticket_number',
                DB::raw("(SELECT group_name FROM groups WHERE id = group_student.group_id ORDER BY id DESC LIMIT 1) as group_name"),
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name")
            )
            ->where('student_ticket_numbers.ticket_number', '=', $ticketNumber)
            ->get();
    }

    public function getForeignStudents(): object
    {
        return GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->join('countries', 'users_data.citizenship', '=', 'countries.id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
            ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
            ->whereIn('group_student.group_id', $this->getGroupIds())
            ->whereNotIn('countries.id', [4, 155])
            ->select(
                'users_data.id',
                'countries.country_name',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'users_data.birthday',
                'users_data.passport_series',
                'users_data.passport_number',
                'users_data.passport_issue_date',
                'users_data.passport_issuing_authority',
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = group_student.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                'groups.group_name',
                'groups.course',
                'teaching_forms.teaching_form_name',
                DB::raw("CONCAT(specialities.specialty_code, ' ', specialities.speciality_name) as sp"),
            )
            ->orderBy('groups.course')
            ->get();
    }

    private function extractGroupIds($listGroupId): array
    {
        return array_map(function ($item) {
            return $item['id'];
        }, $listGroupId);
    }
}
