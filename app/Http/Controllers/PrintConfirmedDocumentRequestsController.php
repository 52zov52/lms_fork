<?php

namespace App\Http\Controllers;

use App\Models\ConfirmedDocumentRequest;
use App\Models\DocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintConfirmedDocumentRequestsController extends Controller
{
    public function printDocument(Request $request) : void
    {
        $documentOrderRequest = DocumentOrderRequest::where('id', '=', $request->id)->first();
        $confirmedDocumentRequest = ConfirmedDocumentRequest::where('document_order_request_id', '=', $request->id)->first();

        $path  = Storage::disk('public')->path(($documentOrderRequest->document_type == 1) ? 'template/certificates/tmp_about_study.docx' : 'template/certificates/tmp_for_transport_cards.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('date', Carbon::parse($documentOrderRequest->issue_date)->format('d.m.Y'));
        $templateProcessor->setValue('number', $documentOrderRequest->document_number);
        $templateProcessor->setValue('last_name', $confirmedDocumentRequest->last_name);
        $templateProcessor->setValue('first_name', $confirmedDocumentRequest->first_name);
        $templateProcessor->setValue('middle_name', $confirmedDocumentRequest->middle_name);
        $templateProcessor->setValue('sex', ($confirmedDocumentRequest->sex) ? 'она' : 'он');
        $templateProcessor->setValue('course', $confirmedDocumentRequest->course);
        $templateProcessor->setValue('teaching_form', $confirmedDocumentRequest->teaching_form_name);
        $templateProcessor->setValue('sp', $confirmedDocumentRequest->specialty_code . ' - ' . $confirmedDocumentRequest->speciality_name);
        $templateProcessor->setValue('orders', $confirmedDocumentRequest->orders);
        $templateProcessor->setValue('training_period',  $confirmedDocumentRequest->training_period);

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
    }
}
