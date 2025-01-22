<?php

namespace App\Http\Controllers;

use App\Models\DecisionsRectorateAndCouncil;
use App\Models\OperatingActivity;
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
            $table->addCell(3500)->addText(Carbon::parse($value->due_date)->format('m.d.Y'), $tableFontStyle);
        }

        $templateProcessor->setComplexBlock('table', $table);

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
    }

    public function downloadReport($rectorateDecisions)
    {
        $path  = Storage::disk('public')->path('template/decisions_rectorate_and_council/report.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $tableFontBoldStyle = array('bold' => true, 'name' => 'Times New Roman');
        $tableFontStyle = array('name' => 'Times New Roman');

        $table = new Table(array('borderSize' => 12, 'borderColor' => 'black', 'unit' => TblWidth::TWIP));
        $table->addRow();

        $table->addCell(3500)->addText('Должность', $tableFontBoldStyle);
        $table->addCell(3500)->addText('Решение', $tableFontBoldStyle);
        $table->addCell(1000)->addText('Cрок исполнения', $tableFontBoldStyle);
        $table->addCell(6500)->addText('Описание выполненной работы', $tableFontBoldStyle);

        foreach ($rectorateDecisions as $value) {
            $report = "";

            $table->addRow();
            $table->addCell(3500)->addText($value->position, $tableFontStyle);
            $table->addCell(3500)->addText($value->task, $tableFontStyle);
            $table->addCell(1000)->addText(Carbon::parse($value->due_date)->format('m.d.Y'), $tableFontStyle);

            $operatingActivityIds = json_decode($value->operating_activity_id, true);

            if (!is_null($operatingActivityIds)) {
                if (count($operatingActivityIds) != 0) {
                    foreach ($operatingActivityIds as $id) {
                        $operatingActivity = OperatingActivity::where('id', $id)->first();
                        $report .= $operatingActivity->description . " ";
                    }
                } else {
                    $report .= 'Отсутствует';
                }
            } else {
                $report .= 'Отсутствует';
            }

            $table->addCell(6500)->addText($report, $tableFontStyle);
        }

        $templateProcessor->setComplexBlock('table', $table);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $templateProcessor->saveAs($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
