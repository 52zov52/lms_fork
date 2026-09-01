<?php

namespace App\Http\Controllers;

use App\Models\OperatingActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintDecisionsRectorateAndCouncilController extends Controller
{
    public function downloadReport($decisions, $category)
    {
        $path  = Storage::disk('public')->path('template/decisions_rectorate_and_council/template.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $tableFontBoldStyle = array('bold' => true, 'name' => 'Times New Roman');
        $tableFontStyle = array('name' => 'Times New Roman');

        $templateProcessor->setValue('category', ($category == 1) ? 'РЕКТОРАТА' : 'УЧЕНОГО СОВЕТА');

        $table = new Table(array('borderSize' => 12, 'borderColor' => 'black', 'unit' => TblWidth::TWIP));
        $table->addRow();

        $table->addCell(3500)->addText('Должность', $tableFontBoldStyle);
        $table->addCell(3500)->addText('Решение', $tableFontBoldStyle);
        $table->addCell(1000)->addText('Cрок исполнения', $tableFontBoldStyle);
        $table->addCell(6500)->addText('Описание выполненной работы', $tableFontBoldStyle);

        foreach ($decisions as $value) {
            $report = "";

            $table->addRow();
            $table->addCell(3500)->addText($value->position, $tableFontStyle);
            $table->addCell(3500)->addText($value->task, $tableFontStyle);
            $table->addCell(1000)->addText(Carbon::parse($value->due_date)->format('d.m.Y'), $tableFontStyle);

            $operatingActivityIds = json_decode($value->operating_activity_id, true);

            if (!is_null($operatingActivityIds)) {
                if (count($operatingActivityIds) != 0) {
                    foreach ($operatingActivityIds as $id) {
                        $operatingActivity = OperatingActivity::where('id', $id)->first();
                        $report .= ($operatingActivity->description ?? '') . " ";
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
