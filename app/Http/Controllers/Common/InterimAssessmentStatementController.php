<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Controller;
use App\Models\EJournal;
use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InterimAssessmentStatementController extends Controller
{
    public function index($wspCoursesInSemesterId, $groupId)
    {
        $wsp = DB::table('work_study_plan_courses_in_semesters')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
            ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
            ->join('level_educations', 'specialities.level_education_id', '=', 'level_educations.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_courses_in_semesters.id', '=', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->where('work_study_plan_courses_in_semesters.id', '=', $wspCoursesInSemesterId)
            ->select(
                'work_study_plan_courses_in_semesters.semester_number',
                'work_study_plan_course_control_types_in_semesters.id as wsp_course_control_types_in_semesters_id',
                'teaching_forms.teaching_form_name',
                'level_educations.level_name',
                'specialities.speciality_name',
                'specialities.specialty_code',
                'disciplines.name as discipline_name',
                'control_types.name as control_name'
            )
            ->first();

        $bulletin = DB::table('bulletins')->where('group_id', '=', $groupId)
            ->where('work_study_plan_course_control_types_in_semesters_id', '=', $wsp->wsp_course_control_types_in_semesters_id)
            ->select('bulletin_number')
            ->first();

        $bulletinNumber = $bulletin->bulletin_number ?? '';

        $group = Group::where('id', '=', $groupId)->select('id', 'group_name')->first();

        $data = EJournal::join('users_data', 'e_journals.student_id', '=', 'users_data.user_id')
            ->whereIn('e_journals.lesson_type', [6, 7, 8])
            ->where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $wspCoursesInSemesterId)
            ->where('group_id', '=', $groupId)
            ->select(
                'e_journals.id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = e_journals.student_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                'e_journals.status',
                'e_journals.lesson_date'
            )
            ->get();

        $examDate = array_unique(array_column($data->toArray(), 'lesson_date'));

        $teacherFullname = auth()->user()->userData->last_name . ' ' . auth()->user()->userData->first_name . ' ' . auth()->user()->userData->middle_name;
        $teacherShortname = auth()->user()->userData->last_name . ' ' . mb_substr(auth()->user()->userData->first_name, 0, 1) . '. ' . mb_substr(auth()->user()->userData->middle_name, 0, 1) . '.';

        $filename = time() . '.pdf';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $template = ($wsp->level_name == 'Среднее профессиональное') ? 'reports.pdf.interim-assessment-statement-spo' : 'reports.pdf.interim-assessment-statement-vo';

        $pdf = PDF::loadView($template, [
            'bulletinNumber' => $bulletinNumber,
            'studyYear' => AcademicYearController::getAcademicYear(),
            'wsp' => $wsp,
            'group' => $group,
            'examDate' => Carbon::parse($examDate[0])->format('d.m.Y'),
            'data' => $data,
            'teacherFullname' => $teacherFullname,
            'teacherShortname' => $teacherShortname
        ]);

        $pdf->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
