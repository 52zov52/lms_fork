<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\UpbringingEvent;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UvrController extends Controller
{
    public function downloadReport()
    {
        $upbringingEvents = UpbringingEvent::leftJoin('users_data', 'upbringing_events.user_id', '=', 'users_data.user_id')
            ->join('upbringing_event_reports', 'upbringing_events.id', '=', 'upbringing_event_reports.upbringing_event_id')
            ->select(
                'upbringing_events.id',
                'upbringing_events.name',
                'upbringing_events.description',
                DB::raw("DATE_FORMAT(upbringing_events.date, '%d.%m.%Y') as date"),
                'upbringing_events.academic_year',
                'upbringing_events.half_year',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'upbringing_event_reports.link_to_news',
                'upbringing_event_reports.file_path'
            )
            ->orderBy('upbringing_events.date', 'asc')
            ->get();


        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $sheet->setCellValue('A' . $offset, 'Наименование мероприятия');
        $sheet->setCellValue('B' . $offset, 'Описание');
        $sheet->setCellValue('C' . $offset, 'Ответственное лицо');
        $sheet->setCellValue('D' . $offset, 'Дата проведения');
        $sheet->setCellValue('E' . $offset, 'Учебный год');
        $sheet->setCellValue('F' . $offset, 'Полугодие');
        $sheet->setCellValue('G' . $offset, 'Ссылка на отчет в сети интернет');
        $sheet->setCellValue('H' . $offset, 'Наличие прикрепленных файлов');

        $sheet->getStyle('A' . $offset . ':H' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('A' . $offset . ':H' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $offset++;

        foreach ($upbringingEvents as $key => $value) {
            $sheet->setCellValue('A' . $offset, $value->name);
            $sheet->setCellValue('B' . $offset, $value->description);
            $sheet->setCellValue('C' . $offset, $value->fio);
            $sheet->setCellValue('D' . $offset, $value->date);
            $sheet->setCellValue('E' . $offset, $value->academic_year);
            $sheet->setCellValue('F' . $offset, $value->half_year);
            $sheet->setCellValue('G' . $offset, $value->link_to_news ?? 'Отсутствует');
            $sheet->setCellValue('H' . $offset, (is_null($value->file_path)) ? 'Отсутствуют' : 'Прикреплены к отчету');

            $offset++;
        }

        $sheet->getStyle('A1:H' . $offset)->getAlignment()->setWrapText(true);

        $sheet->getStyle('A2:D' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        $sheet->getStyle('D2:H' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:H' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(75);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
