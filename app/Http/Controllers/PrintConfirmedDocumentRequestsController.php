<?php

namespace App\Http\Controllers;

use App\Models\StudentDocumentIssuance;
use App\Models\StudentDocumentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintConfirmedDocumentRequestsController extends Controller
{
    public function print(Request $request): void
    {
        $studentDocumentRequest = StudentDocumentRequest::where('id', '=', $request->id)->first();
        $studentDocumentIssuance = StudentDocumentIssuance::where('student_document_request_id', '=', $request->id)->first();

        if ($studentDocumentRequest->document_type == 1) {
            $path = Storage::disk('public')->path('template/certificates/tmp_about_study.docx');
        } else if ($studentDocumentRequest->document_type == 2) {
            $path = Storage::disk('public')->path('template/certificates/tmp_for_transport_cards.docx');
        } else {
            $path = Storage::disk('public')->path('template/spravka.docx');
        }

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('date', Carbon::parse($studentDocumentRequest->created_at)->format('d.m.Y'));
        $templateProcessor->setValue('number', $studentDocumentIssuance->document_number . '-эл.');
        $templateProcessor->setValue('last_name_dative', $studentDocumentIssuance->last_name_dative);
        $templateProcessor->setValue('first_name_dative', $studentDocumentIssuance->first_name_dative);
        $templateProcessor->setValue('middle_name_dative', $studentDocumentIssuance->middle_name_dative);
        $templateProcessor->setValue('course', $studentDocumentIssuance->course);
        $templateProcessor->setValue('teaching_form', $this->transformTeachingForm($studentDocumentIssuance->teaching_form_name));

        if ($studentDocumentRequest->document_type == 1 || $studentDocumentRequest->document_type == 2) {
            $templateProcessor->setValue('sex', ($studentDocumentIssuance->sex) ? 'она' : 'он');
            $templateProcessor->setValue('sp', $studentDocumentIssuance->specialty_code . ' - ' . $studentDocumentIssuance->speciality_name);
            $templateProcessor->setValue('orders', $studentDocumentIssuance->orders);
            $templateProcessor->setValue('training_period',  $studentDocumentIssuance->training_period);
            $templateProcessor->setValue('is_budget', (!$studentDocumentIssuance->is_budget) ? 'Университет не предоставляет стипендии и иные меры материальной поддержки.' : '');
        }

        if ($studentDocumentRequest->document_type == 3) {
            $templateProcessor->setValue('last_name', $studentDocumentIssuance->last_name);
            $templateProcessor->setValue('first_name', $studentDocumentIssuance->first_name);
            $templateProcessor->setValue('middle_name', $studentDocumentIssuance->middle_name);
            $templateProcessor->setValue('specialty_code', $studentDocumentIssuance->specialty_code);
            $templateProcessor->setValue('speciality_name', $studentDocumentIssuance->speciality_name);
            $templateProcessor->setValue('check_in_date', Carbon::parse($studentDocumentIssuance->check_in_date)->format('d.m.Y'));
            $templateProcessor->setValue('check_out_date', Carbon::parse($studentDocumentIssuance->check_out_date)->format('d.m.Y'));
            $templateProcessor->setValue('check_in_duration', $studentDocumentIssuance->check_in_duration);
            $templateProcessor->setValue('employment_place', $studentDocumentIssuance->employment_place);
        }

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
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
