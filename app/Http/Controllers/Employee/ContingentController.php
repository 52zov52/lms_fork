<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\AcademicYearController;
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
            ->where('study_threads.year', '=', AcademicYearController::getYear())
            ->select('groups.id')
            ->distinct()
            ->get()->toArray());
    }

    public function getFullContingent($groupId, $gender = [0, 1]): object
    {
        return GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->leftJoin('address_user', 'users_data.user_id', '=', 'address_user.user_id')
            ->leftJoin('addresses', 'address_user.address_id', '=', 'addresses.id')
            ->leftJoin('countries', 'addresses.country_id', '=', 'countries.id')
            ->leftJoin('regions', 'addresses.region_id', '=', 'regions.id')
            ->leftJoin('cities', 'addresses.city_id', '=', 'cities.id')
            ->leftJoin('city_types', 'cities.city_type_id', '=', 'city_types.id')
            ->leftJoin('districts', 'addresses.district_id', '=', 'districts.id')
            ->leftJoin('user_education_backgrounds', 'user_education_backgrounds.user_id', '=', 'users_data.user_id')
            ->leftJoin('education_organizations', 'user_education_backgrounds.education_organization_id', '=', 'education_organizations.id')
            ->leftJoin('documents', 'user_education_backgrounds.document_id', '=', 'documents.id')
            ->leftJoin('type_educations', 'user_education_backgrounds.type_education_id', '=', 'type_educations.id')
            ->leftJoin('military_registration_students', 'military_registration_students.user_id', '=', 'users_data.user_id')
            ->where('group_student.group_id', '=', $groupId)
            ->whereIn('users_data.sex', $gender)
            ->where(function ($query) {
                $query->where('address_user.address_type', '=', 0)
                    ->orWhere(function ($query) {
                        $query->whereNull('address_user.address_type')
                            ->where('address_user.address_type', '=', 2);
                    });
            })
            ->select(
                'group_student.user_id',
                'group_student.id',
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
                'city_types.name as city_type_name',
                'cities.city_name',
                'districts.district_name',
                'addresses.street',
                'addresses.building',
                'addresses.flat',
                'addresses.phone',
                DB::raw("(SELECT group_name FROM groups WHERE id = group_student.group_id ORDER BY id DESC LIMIT 1) as group_name"),
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = group_student.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name"),
                'type_educations.type_name',
                'education_organizations.name',
                'documents.document_type',
                'user_education_backgrounds.document_series',
                'user_education_backgrounds.document_number',
                'user_education_backgrounds.document_issue_date',
                'user_education_backgrounds.end_year',
                'military_registration_students.reserve_category',
                'military_registration_students.military_rank',
                'military_registration_students.military_occupation_profile',
                'military_registration_students.full_military_specialty_code',
                'military_registration_students.fitness_category',
                'military_registration_students.military_commissariat_name',
                'military_registration_students.military_registration_type_general',
                'military_registration_students.military_registration_type_special',
                'military_registration_students.military_document',
                'military_registration_students.document_series as military_document_series',
                'military_registration_students.document_number as military_document_number',
                'military_registration_students.issued_by as military_issued_by',
                'military_registration_students.issue_date as military_issue_date'
            )
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }

    public function getContingent($groupId, $gender = [0, 1]): object
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
            ->whereIn('users_data.sex', $gender)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }

    public function getContingentWithMilitaryStatus($groupId, $gender = [0, 1]): object
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
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name"),
                DB::raw("(SELECT user_id FROM military_registration_students WHERE user_id = group_student.user_id LIMIT 1) as military_status")
            )
            ->where('group_student.group_id', '=', $groupId)
            ->whereIn('users_data.sex', $gender)
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
                DB::raw("(SELECT states.name FROM student_state INNER JOIN states ON states.id = student_state.state_id WHERE student_state.user_id = group_student.user_id ORDER BY student_state.id DESC LIMIT 1) as state_name"),
                DB::raw("(SELECT user_id FROM military_registration_students WHERE user_id = group_student.user_id LIMIT 1) as military_status")
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

    public function getGroupIdsByTeachingForm($teachingForm) : array
    {
        return $this->extractGroupIds(StudyThread::join('study_thread_subgroups', 'study_threads.id', '=', 'study_thread_subgroups.study_thread_id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
            ->where('study_threads.year', '=', AcademicYearController::getYear())
            ->where('work_study_plans.teaching_form_id', '=', $teachingForm)
            ->select('groups.id')
            ->distinct()
            ->get()->toArray());
    }
}
