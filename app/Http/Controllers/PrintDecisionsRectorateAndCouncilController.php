<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintDecisionsRectorateAndCouncilController extends Controller
{
    public function printDecisions()
    {
        $data = array(1,2,3,4,5,6,7,8,9);
        $path  = Storage::disk('public')->path('template/decisions_rectorate_and_council/template.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('date_decision', 2);
        $templateProcessor->setValue('number_decision', 1);

        $tableFontBoldStyle = array('bold' => true, 'name' => 'Times New Roman');
        $tableFontStyle = array('name' => 'Times New Roman');
        
        $table = new Table(array('borderSize' => 12, 'borderColor' => 'black', 'unit' => TblWidth::TWIP));
        $table->addRow();
        $table->addCell(3500)->addText('Должность', $tableFontBoldStyle);
        $table->addCell(3500)->addText('Решение', $tableFontBoldStyle);
        $table->addCell(3500)->addText('Cрок исполнения', $tableFontBoldStyle);
        foreach ($data as $detail) {
            $table->addRow();
            $table->addCell(3500)->addText($detail, $tableFontStyle);
            $table->addCell(3500)->addText(1, $tableFontStyle);
            $table->addCell(3500)->addText(1, $tableFontStyle);
        }

        $templateProcessor->setComplexBlock('table', $table);

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
    }
}
