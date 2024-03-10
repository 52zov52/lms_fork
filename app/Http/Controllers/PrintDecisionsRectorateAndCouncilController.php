<?php

namespace App\Http\Controllers;

use App\Models\DecisionsRectorateAndCouncil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintDecisionsRectorateAndCouncilController extends Controller
{
    public function printDecisions($category, $dateAndNumberDecision)
    {
        $data = DecisionsRectorateAndCouncil::select(
            'date_decision',
            'number_decision',
            'position',
            'task',
            'due_date',
            DB::raw("CONCAT(DATE_FORMAT(date_decision, '%d.%m.%Y'), ' №', number_decision) AS date_and_number_decision")
        )
            ->having('date_and_number_decision', '=', base64_decode($dateAndNumberDecision))
            ->where('category', '=', $category)->get();

        $path  = Storage::disk('public')->path('template/decisions_rectorate_and_council/template.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('date_decision', Carbon::parse($data[0]->date_decision)->format('d.m.Y'));
        $templateProcessor->setValue('number_decision', $data[0]->number_decision);

        $tableFontBoldStyle = array('bold' => true, 'name' => 'Times New Roman');
        $tableFontStyle = array('name' => 'Times New Roman');

        $table = new Table(array('borderSize' => 12, 'borderColor' => 'black', 'unit' => TblWidth::TWIP));
        $table->addRow();
        $table->addCell(3500)->addText('Должность', $tableFontBoldStyle);
        $table->addCell(3500)->addText('Решение', $tableFontBoldStyle);
        $table->addCell(3500)->addText('Cрок исполнения', $tableFontBoldStyle);
        foreach ($data as $key => $value) {
            $table->addRow();
            $table->addCell(3500)->addText($value->position, $tableFontStyle);
            $table->addCell(3500)->addText($value->task, $tableFontStyle);
            $table->addCell(3500)->addText($value->due_date, $tableFontStyle);
        }

        $templateProcessor->setComplexBlock('table', $table);

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
    }
}
