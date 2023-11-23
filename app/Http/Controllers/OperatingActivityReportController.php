<?php

namespace App\Http\Controllers;

use App\Models\OperatingActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OperatingActivityReportController extends Controller
{
    public function generateOperatingActivityReport($week, $fio)
    {
        $user = Auth::user()->userData->last_name . ' ' . Auth::user()->userData->first_name . ' ' . Auth::user()->userData->middle_name;
        $fio = ($fio != 0) ? $fio : '';

        $operatingActivity = OperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id')
            ->select(
                'operating_activities.id',
                'operating_activities.activity_direction',
                'operating_activities.task',
                'operating_activities.description',
                'operating_activities.status',
                'operating_activities.file_path',
                'operating_activities.user_id',
                'operating_activities.created_at',
                DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name) AS fio')
            );

        if (!empty($week) && $week != 0) {
            $now = Carbon::parse($week);
            $weekStartDate = $now->startOfWeek()->format('Y-m-d');
            $weekEndDate = $now->endOfWeek()->format('Y-m-d');

            $operatingActivity
                ->where('operating_activities.created_at', '>=', Carbon::parse($weekStartDate)->format('Y-m-d'))
                ->where('operating_activities.created_at', '<=', Carbon::parse($weekEndDate)->format('Y-m-d'));
        }

        $operatingActivity = $operatingActivity
            ->where(DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name)'), 'like', '%' . $fio . '%')
            ->orderBy('fio')
            ->get();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $increment = 1;
        $offset = 1;

        $sheet->setCellValue('A' . $offset, 'ФИО: ' . $user);
        $sheet->mergeCells('A' . $offset . ':G' . $offset);

        $offset = $offset + 2;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'Направление деятельности');
        $sheet->setCellValue('C' . $offset, 'Исполнитель');
        $sheet->setCellValue('D' . $offset, 'Задачи');
        $sheet->setCellValue('E' . $offset, 'Статус');
        $sheet->setCellValue('F' . $offset, 'Период');
        $sheet->setCellValue('G' . $offset, 'Комментарий');

        $sheet->getStyle('A' . $offset . ':G' . $offset)->getFont()->setBold(true);

        $offset++;

        foreach ($operatingActivity as $value) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $value->activity_direction);
            $sheet->setCellValue('C' . $offset, $value->fio);
            $sheet->setCellValue('D' . $offset, $value->task);

            if ($value->status == 1) {
                $status = 'В работе';
            } else if ($value->status == 2) {
                $status = 'На утверждение';
            } else {
                $status = 'Выполнено';
            }

            $sheet->setCellValue('E' . $offset, $status);

            $now = Carbon::parse($value->created_at);
            $startWeek = $now->startOfWeek()->format('d.m.Y');
            $endWeek = $now->endOfWeek()->format('d.m.Y');

            $sheet->setCellValue('F' . $offset, 'с '.$startWeek.' по '.$endWeek);
            $sheet->setCellValue('G' . $offset, $value->description);

            $increment++;
            $offset++;
        }

        $sheet->getStyle('B3:G' . $offset)->getAlignment()->setWrapText(true);

        //Стиль для рамки
        $sheet->getStyle('A3:G' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Выравнивание текста в ячейке
        $sheet->getStyle('A3:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('B3:G' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(45);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(45);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }
}
