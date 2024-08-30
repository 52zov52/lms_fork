<?php

namespace App\Livewire;

use App\Models\StudentDocumentIssuance;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StudentDocumentIssuances extends Component
{
    public $documentNumber,
        $lastName,
        $firstName,
        $middleName,
        $lastNameDative,
        $firstNameDative,
        $middleNameDative,
        $sex,
        $course,
        $teachingFormName,
        $specialityName,
        $specialtyCode,
        $orders,
        $trainingPeriod;

    public $showModal = false;

    public function render()
    {
        $studentDocumentIssuances = StudentDocumentIssuance::join('student_document_requests', 'student_document_issuances.student_document_request_id', '=', 'student_document_requests.id')
            ->join('users_data', 'student_document_requests.requester_id', '=', 'users_data.user_id')
            ->join('group_student', 'student_document_requests.requester_id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->select(
                'student_document_issuances.id',
                'student_document_issuances.student_document_request_id',
                'student_document_requests.document_type',
                'student_document_requests.status',
                'student_document_requests.requester_id',
                'student_document_requests.academic_year',
                'student_document_requests.half_year',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'groups.group_name'
            )
            ->paginate(50);

        return view('livewire.student-document-issuances', ['studentDocumentIssuances' => $studentDocumentIssuances]);
    }

    public function callMethod($methodname)
    {
        $this->{$methodname}();
    }

    public function edit($requesterId)
    {
        $userData = UserData::join('group_student', 'users_data.user_id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
            ->join('specialities',  'work_study_plans.speciality_id', '=', 'specialities.id')
            ->where('users_data.user_id', '=', $requesterId)
            ->select(
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'users_data.last_name_dative',
                'users_data.first_name_dative',
                'users_data.middle_name_dative',
                'users_data.sex',
                'groups.group_name',
                'groups.course',
                'groups.year_admission',
                'groups.year_completion',
                'teaching_forms.teaching_form_name',
                'specialities.specialty_code',
                'specialities.speciality_name',
                'specialities.level_education_id',
            )
            ->first();

        $this->showModal = true;

        $this->trainingPeriod = 'с ' . Carbon::parse($userData->year_admission)->format('d.m.Y') . 'г. по ' . Carbon::parse($userData->year_completion)->format('d.m.Y') . 'г.';

        dd($this->trainingPeriod);
    }
}
