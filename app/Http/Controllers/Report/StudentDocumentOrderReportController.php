<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\StudentDocumentOrderIssuance;
use App\Models\StudentDocumentOrderRequest;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class StudentDocumentOrderReportController extends Controller
{
    public function downloadStudentDocument($studentDocumentOrderRequestId)
    {
        $spo = '';

        $studentDocumentOrderRequest = StudentDocumentOrderRequest::where('id', '=', $studentDocumentOrderRequestId)->first();
        $studentDocumentOrderIssuance = StudentDocumentOrderIssuance::where('student_document_order_request_id', '=', $studentDocumentOrderRequestId)->first();

        if ($studentDocumentOrderIssuance->level_education_name == 'Среднее профессиональное') {
            $spo = 'Колледжа рационального обучения ЧОУ ВО ЮУ (ИУБиП)';
        }

        $confirmiter = UserData::select('last_name', 'first_name', 'middle_name')
            ->where('user_id', '=', $studentDocumentOrderIssuance->confirmiter_id)->first();

        if ($studentDocumentOrderRequest->document_type == 1) {
            $path = Storage::disk('public')->path('template/certificates/tmp_about_study.docx');
        } else if ($studentDocumentOrderRequest->document_type == 2) {
            $path = Storage::disk('public')->path('template/certificates/tmp_for_transport_cards.docx');
        } else if ($studentDocumentOrderRequest->document_type == 3) {
            $path = Storage::disk('public')->path('template/spravka.docx');
        } else {
            $path = Storage::disk('public')->path('template/certificates/tmp_characteristic.docx');
        }

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('date', Carbon::parse($studentDocumentOrderRequest->created_at)->format('d.m.Y'));
        $templateProcessor->setValue('number', $studentDocumentOrderIssuance->document_number . '-эл.');

        if ($studentDocumentOrderRequest->document_type == 1 || $studentDocumentOrderRequest->document_type == 2 || $studentDocumentOrderRequest->document_type == 3) {
            $templateProcessor->setValue('last_name_dative', $studentDocumentOrderIssuance->last_name_dative);
            $templateProcessor->setValue('first_name_dative', $studentDocumentOrderIssuance->first_name_dative);
            $templateProcessor->setValue('middle_name_dative', $studentDocumentOrderIssuance->middle_name_dative);
        }

        if ($studentDocumentOrderRequest->document_type == 4) {
            $templateProcessor->setValue('last_name', $studentDocumentOrderIssuance->last_name);
            $templateProcessor->setValue('first_name', $studentDocumentOrderIssuance->first_name);
            $templateProcessor->setValue('middle_name', $studentDocumentOrderIssuance->middle_name);
            $templateProcessor->setValue('last_name_genitive', $studentDocumentOrderIssuance->last_name_genitive);
            $templateProcessor->setValue('first_name_genitive', $studentDocumentOrderIssuance->first_name_genitive);
            $templateProcessor->setValue('middle_name_genitive', $studentDocumentOrderIssuance->middle_name_genitive);
            $templateProcessor->setValue('birthday', Carbon::parse($studentDocumentOrderIssuance->birthday)->format('d.m.Y'));
            $templateProcessor->setValue('spo', $spo);
            $templateProcessor->setValue('sp', $studentDocumentOrderIssuance->specialty_code . ' - ' . $studentDocumentOrderIssuance->speciality_name);
            $templateProcessor->setValue('orders', $studentDocumentOrderIssuance->orders);
            $templateProcessor->setValue('training_period',  $studentDocumentOrderIssuance->training_period);
            $templateProcessor->setValue('signer_position', $studentDocumentOrderIssuance->signer_position);
            $templateProcessor->setValue('signer_fio', $studentDocumentOrderIssuance->signer_fio);
            $templateProcessor->setValue('confirmiter',  $confirmiter->last_name . ' ' . mb_substr($confirmiter->first_name, 0, 1) . '.' . mb_substr($confirmiter->middle_name, 0, 1) . '.');
        }

        $templateProcessor->setValue('course', $studentDocumentOrderIssuance->course);
        $templateProcessor->setValue('teaching_form', $this->transformTeachingForm($studentDocumentOrderIssuance->teaching_form_name));

        //Справка об обучении
        if ($studentDocumentOrderRequest->document_type == 1 || $studentDocumentOrderRequest->document_type == 2) {
            $templateProcessor->setValue('spo', $spo);
            $templateProcessor->setValue('sex', ($studentDocumentOrderIssuance->sex) ? 'она' : 'он');
            $templateProcessor->setValue('sp', $studentDocumentOrderIssuance->specialty_code . ' - ' . $studentDocumentOrderIssuance->speciality_name);
            $templateProcessor->setValue('orders', $studentDocumentOrderIssuance->orders);
            $templateProcessor->setValue('training_period',  $studentDocumentOrderIssuance->training_period);
            $templateProcessor->setValue('is_budget', (!$studentDocumentOrderIssuance->is_budget) ? 'Университет не предоставляет стипендии и иные меры материальной поддержки.' : '');
            $templateProcessor->setValue('confirmiter',  $confirmiter->last_name . ' ' . mb_substr($confirmiter->first_name, 0, 1) . '.' . mb_substr($confirmiter->middle_name, 0, 1) . '.');
            $templateProcessor->setValue('signer_position', $studentDocumentOrderIssuance->signer_position);
            $templateProcessor->setValue('signer_fio', $studentDocumentOrderIssuance->signer_fio);
        }

        //Справка-вызов
        if ($studentDocumentOrderRequest->document_type == 3) {
            $templateProcessor->setValue('last_name', $studentDocumentOrderIssuance->last_name);
            $templateProcessor->setValue('first_name', $studentDocumentOrderIssuance->first_name);
            $templateProcessor->setValue('middle_name', $studentDocumentOrderIssuance->middle_name);
            $templateProcessor->setValue('specialty_code', $studentDocumentOrderIssuance->specialty_code);
            $templateProcessor->setValue('speciality_name', $studentDocumentOrderIssuance->speciality_name);
            $templateProcessor->setValue('check_in_date', Carbon::parse($studentDocumentOrderIssuance->check_in_date)->format('d.m.Y'));
            $templateProcessor->setValue('check_out_date', Carbon::parse($studentDocumentOrderIssuance->check_out_date)->format('d.m.Y'));
            $templateProcessor->setValue('check_in_duration', $studentDocumentOrderIssuance->check_in_duration);
            $templateProcessor->setValue('employment_place', $studentDocumentOrderIssuance->employment_place);
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $templateProcessor->saveAs($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    private function transformTeachingForm($teachingForm): string
    {
        $result = '';

        switch ($teachingForm) {
            case 'Очная':
                $result = 'очной';
                break;
            case 'Заочная':
                $result = 'заочной';
                break;
            case 'Заочная форма (ускоренное обучение)':
                $result = 'заочной (ускоренное)';
                break;
            case 'Очно-заочная':
                $result = 'очно-заочной';
                break;
            case 'Очная (ускоренное обучение)':
                $result = 'очной (ускоренное)';
                break;
            default:
                $result = '';
                break;
        }

        return $result;
    }
}
