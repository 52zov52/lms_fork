<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ApplicantAverageScore;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PrintApplicantAverageScoreController extends Controller
{
    public function print()
    {
        $data = ApplicantAverageScore::join('admission_campaign_directions', 'applicant_average_scores.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('applicant_average_scores.admission_campaign_id', '=', 1)
            ->select(
                'applicant_average_scores.last_name',
                'applicant_average_scores.first_name',
                'applicant_average_scores.middle_name',
                'applicant_average_scores.phone',
                'applicant_average_scores.threes_count',
                'applicant_average_scores.fours_count',
                'applicant_average_scores.fives_count',
                'applicant_average_scores.average_score',
                'applicant_average_scores.completed_classes_count',
                DB::raw("CONCAT(admission_campaign_directions.speciality_code, ' ', admission_campaign_directions.speciality_name) AS direction")
            )
            ->orderBy('applicant_average_scores.average_score', 'desc')
            ->get();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;
        $iteration = 1;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'ФИО');
        $sheet->setCellValue('C' . $offset, 'Телефон');
        $sheet->setCellValue('D' . $offset, 'Кол-во 3');
        $sheet->setCellValue('E' . $offset, 'Кол-во 4');
        $sheet->setCellValue('F' . $offset, 'Кол-во 5');
        $sheet->setCellValue('G' . $offset, 'Средний балл');
        $sheet->setCellValue('H' . $offset, 'Образование');
        $sheet->setCellValue('I' . $offset, 'Направление подготовки');

        $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

        $offset++;

        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $offset, $iteration);
            $sheet->setCellValue('B' . $offset, $value->last_name . ' ' . $value->first_name . ' ' . $value->middle_name);
            $sheet->setCellValue('C' . $offset, $value->phone);
            $sheet->setCellValue('D' . $offset, $value->threes_count);
            $sheet->setCellValue('E' . $offset, $value->fours_count);
            $sheet->setCellValue('F' . $offset, $value->fives_count);
            $sheet->setCellValue('G' . $offset, $value->average_score);
            $sheet->setCellValue('H' . $offset, $value->completed_classes_count . ' кл.');
            $sheet->setCellValue('I' . $offset, $value->direction);

            $offset++;
            $iteration++;
        }

        $sheet->getStyle('A1:I' . $offset)->getAlignment()->setWrapText(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('B1:C' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        $sheet->getStyle('D1:H' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('I1:I' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:I' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(40);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }
}
