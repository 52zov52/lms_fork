<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\StudentSptConsentStatus;
use App\Models\UserData;
use App\Models\WorkStudyPlan;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SptConsentStatusController extends Controller
{
    public static function getSignature($userId): string
    {
        $userData = UserData::select(DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"))
            ->where('user_id', '=', $userId)->first();

        return hash('sha256', $userData->fio . now()->format('Y-m-d'));
    }

    public function downloadInformedConsent($id)
    {
        $studentSptConsentStatus = StudentSptConsentStatus::select('user_id', 'updated_at', 'signature')
            ->where('id', '=', $id)
            ->first();

        $userId = $studentSptConsentStatus->user_id;

        $userData = UserData::select(
            DB::raw("CONCAT(last_name, ' ', first_name, ' ', middle_name) as fio"),
            DB::raw("CONCAT(last_name_genitive, ' ', first_name_genitive, ' ', middle_name_genitive) as fio_genitive"),
            'birthday'
        )
            ->first();

        $group = Group::select('course', 'work_study_plan_id')
            ->where('id', '=', function ($query) use ($userId) {
                $query->select('group_id')
                    ->from('group_student')
                    ->where('user_id', '=', $userId)
                    ->orderBy('id', 'desc')
                    ->limit(1);
            })->first();

        $workStudyPlan = WorkStudyPlan::select(
            'specialities.specialty_code',
            'specialities.speciality_name',
            'teaching_forms.teaching_form_name'
        )
            ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
            ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
            ->where('work_study_plans.id', '=', $group->work_study_plan_id)->first();

        $filename = time() . '.pdf';
        $pdf = PDF::loadView('reports.pdf.informed-consent', [
            'fio' => $userData->fio,
            'fioGenitive' => $userData->fio_genitive,
            'birthday' => Carbon::parse($userData->birthday)->format('Y'),
            'course' => $group->course,
            'speciality' => $workStudyPlan->specialty_code . ' ' . $workStudyPlan->speciality_name,
            'teachingForm' => $this->getTeachingForm($workStudyPlan->teaching_form_name),
            'updatedAt' => $studentSptConsentStatus->updated_at
        ]);

        return $pdf->download($filename);
    }

    private function getTeachingForm($teachingForm): string
    {
        $name = '';

        switch ($teachingForm) {
            case 'Очная':
                $name = 'очной';
                break;
            case 'Заочная':
                $name = 'заочной';
                break;
            case 'Заочная форма (ускоренное обучение)':
                $name = 'заочной (ускоренное обучение)';
                break;
            case 'Воскресная':
                $name = 'воскресной';
                break;
            case 'Соискательство':
                $name = 'соискательство';
                break;
            case 'Очно-заочная':
                $name = 'очно-заочной';
                break;
            case 'Очная (ускоренное обучение)':
                $name = 'очной (ускоренное обучение)';
                break;
            default:
                $name = '';
                break;
        }

        return $name;
    }
}
