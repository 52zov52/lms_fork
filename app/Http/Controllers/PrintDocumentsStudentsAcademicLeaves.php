<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Speciality;
use App\Models\StudentsAcademicLeave;
use App\Models\StudyPlan;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintDocumentsStudentsAcademicLeaves extends Controller
{
    public function spravka301($id)
    {
        $studentsAcademicLeave = StudentsAcademicLeave::select('user_id', 'group_id')->where('id', '=', $id)->first();

        $group = Group::select('group_name', 'course', 'study_plan_id')
            ->where('id', '=', $studentsAcademicLeave->group_id)->first();

        $studyPlan = StudyPlan::select('teaching_form_id', 'speciality_id')
            ->where('id', '=', $group->study_plan_id)->first();

        switch ($studyPlan->teaching_form_id) {
            case 1:
                $teachingForm = 'очной';
                break;
            case 2:
                $teachingForm = 'заочной';
                break;
            default:
                $teachingForm = 'очно-заочной';
                break;
        }

        $speciality = Speciality::select('speciality_name', 'specialty_code', 'level_education_id')->where('id', '=', $studyPlan->speciality_id)->first();

        $userData = UserData::where('user_id', '=', $studentsAcademicLeave->user_id)->first();

        //$path  = Storage::disk('public')->path(($speciality->level_education_id == 2) ? 'template/academic_leaves/application_spo(301).docx' : 'template/academic_leaves/application_vo(301).docx');
        //$path  = Storage::disk('public')->path('template/academic_leaves/personal_file_verification_sheet.docx');
        $path  = Storage::disk('public')->path(($speciality->level_education_id == 2) ? 'template/academic_leaves/aa-spo(aa01).docx' : 'template/academic_leaves/aa-vo(aa01).docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        /*
        $templateProcessor->setValue('course', $group->course);
        $templateProcessor->setValue('group_name', $group->group_name);
        $templateProcessor->setValue('specialty_code', $speciality->specialty_code);
        $templateProcessor->setValue('speciality_name', $speciality->speciality_name);
        $templateProcessor->setValue('teaching_form', $teachingForm);
        $templateProcessor->setValue('last_name', $userData->last_name_genitive);
        $templateProcessor->setValue('first_name', $userData->first_name_genitive);
        $templateProcessor->setValue('middle_name', $userData->middle_name_genitive);
        */
        /*
        $templateProcessor->setValue('last_name', $userData->last_name);
        $templateProcessor->setValue('first_name', $userData->first_name);
        $templateProcessor->setValue('middle_name', $userData->middle_name);
        $templateProcessor->setValue('specialty_code', $speciality->specialty_code);
        $templateProcessor->setValue('speciality_name', $speciality->speciality_name);
        $templateProcessor->setValue('group_name', $group->group_name);
        */
        $templateProcessor->setValue('last_name_dative', $userData->last_name_genitive);
        $templateProcessor->setValue('first_name_dative', $userData->first_name_genitive);
        $templateProcessor->setValue('middle_name_dative', $userData->middle_name_genitive);
        $templateProcessor->setValue('last_name', $userData->last_name);
        $templateProcessor->setValue('first_name', $userData->first_name);
        $templateProcessor->setValue('middle_name', $userData->middle_name);
        $templateProcessor->setValue('birthday', $userData->birthday);
        $templateProcessor->setValue('passport', $userData->passport_series.''.$userData->passport_number.''.$userData->passport_issuing_authority.''.$userData->passport_issue_date);

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
    }
}
