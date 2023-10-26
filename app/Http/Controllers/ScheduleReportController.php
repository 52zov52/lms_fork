<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ScheduleReportController extends Controller
{
    public function generateTeacherReport(Request $request)
    {
        $scheduleController = new ScheduleController();
        $teacherSchedule = $scheduleController->getScheduleByTeacher($request->fio);

        $chair = $scheduleController->getTeacherChair($request->fio);

        $groups = "";
        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $increment = 1;
        $offset = 1;

        $sheet->setCellValue('A' . $offset, 'Преподаватель: ' . $request->fio);
        $sheet->mergeCells('A' . $offset . ':H' . $offset);

        $offset = $offset + 2;

        $sheet->setCellValue('A' . $offset, 'Кафедра: ' . $chair);
        $sheet->mergeCells('A' . $offset . ':H' . $offset);

        $offset = $offset + 2;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'Вид занятий');
        $sheet->setCellValue('C' . $offset, 'Дисциплина');
        $sheet->setCellValue('D' . $offset, 'Дата');
        $sheet->setCellValue('E' . $offset, 'Дни');
        $sheet->setCellValue('F' . $offset, 'Часы занятий');
        $sheet->setCellValue('G' . $offset, 'Аудитория');
        $sheet->setCellValue('H' . $offset, 'Учебная группа');

        $sheet->getStyle('A' . $offset . ':H' . $offset)->getFont()->setBold(true);

        $offset++;

        foreach ($teacherSchedule as $schedule) {
            foreach ($schedule['schedule'] as $data) {
                foreach ($data as $item) {
                    $sheet->setCellValue('A' . $offset, $increment);
                    $sheet->setCellValue('B' . $offset, $item['lesson_type']);
                    $sheet->setCellValue('C' . $offset, $item['discipline']);
                    $sheet->setCellValue('D' . $offset, Carbon::parse($schedule['date'])->format('d.m.Y'));
                    $sheet->setCellValue('E' . $offset, $schedule['day']);
                    $sheet->setCellValue('F' . $offset, $item['time']);
                    $sheet->setCellValue('G' . $offset, $item['lesson_place']);

                    foreach ($item['data'] as $group) {
                        $groups .=  $group['group'] . ', ';
                    }

                    $sheet->setCellValue('H' . $offset, substr($groups, 0, -2));

                    $increment++;
                    $offset++;

                    $groups = "";
                }
            }
        }

        $sheet->getStyle('C6:C' . $offset)->getAlignment()->setWrapText(true);

        //Стиль для рамки
        $sheet->getStyle('A5:H' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Выравнивание текста в ячейке
        $sheet->getStyle('A5:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('G6:G' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(60);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(40);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    public function generateGroupReport(Request $request)
    {
        $scheduleController = new ScheduleController();
        $groupSchedule = $scheduleController->getScheduleByGroup($request->group);

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $firstBorderIndex = 1;
        $lastBorderIndex = 1;

        $firstMergeIndex = 1;
        $lastMergeIndex = 1;

        $firstAlignIndex = 1;
        $lastAlignIndex = 1;

        foreach ($groupSchedule as $schedule) {

            $firstBorderIndex = $offset;

            $sheet->setCellValue('A' . $offset, $request->group);

            $sheet->mergeCells('A' . $offset . ':E' . $offset);
            $sheet->getStyle('A' . $offset . ':E' . $offset)->getFont()->setBold(true);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':E' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Дни');
            $sheet->setCellValue('B' . $offset, 'Часы');
            $sheet->setCellValue('C' . $offset, 'Дисциплина');
            $sheet->setCellValue('D' . $offset, 'Преподаватели');
            $sheet->setCellValue('E' . $offset, 'Аудитория');

            $sheet->getStyle('A' . $offset . ':E' . $offset)->getFont()->setBold(true);

            $offset++;

            $firstAlignIndex = $offset;

            foreach ($schedule['data'] as $data) {
                $sheet->setCellValue('A' . $offset, $data['day'] . ', ' . $data['date']);
                $firstMergeIndex = $offset;

                foreach ($data['lessons'] as $lesson) {

                    $sheet->setCellValue('B' . $offset, $lesson['time']);
                    $sheet->setCellValue('C' . $offset, $lesson['lesson_type'] . ' ' . $lesson['discipline']);
                    $sheet->setCellValue('D' . $offset, $lesson['teacher']);
                    $sheet->setCellValue('E' . $offset, $lesson['lesson_place']);

                    $offset++;
                }

                $lastMergeIndex = $offset;
                $sheet->mergeCells('A' . $firstMergeIndex . ':A' . $lastMergeIndex - 1);
            }

            $lastBorderIndex = $offset;
            $lastAlignIndex = $offset;

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $firstAlignIndex . ':A' . ($lastAlignIndex - 1))
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('E' . $firstAlignIndex . ':E' . ($lastAlignIndex - 1))
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                ]));

            //Стиль для рамки
            $sheet->getStyle('A' . $firstBorderIndex . ':E' . ($lastBorderIndex - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));

            $offset++;
        }

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(80);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    public function generateLoadRoomsReport(Request $request)
    {
        $scheduleController = new ScheduleController();
        $weekSchedule = $scheduleController->getScheduleByWeek($request->week);

        $rooms = [
            'C' => '101',
            'D' => '214',
            'E' => '201',
            'F' => '202',
            'G' => '208',
            'H' => '209',
            'I' => '219',
            'J' => '303',
            'K' => '304',
            'L' => '315',
            'M' => '401',
            'N' => '403',
            'O' => '406',
            'P' => '407',
            'Q' => '408',
            'R' => '409',
            'S' => '409а',
            'T' => '410',
            'U' => '413',
            'V' => '501',
            'W' => '503',
            'X' => '506',
            'Y' => '507',
            'Z' => '509',
            'AA' => '510',
            'AB' => '601а',
            'AC' => '601б',
            'AD' => '602а',
            'AE' => '602б',
            'AF' => '604а',
            'AG' => '604б',
            'AH' => '605',
            'AI' => '608а',
            'AJ' => '608б',
            'AK' => '609а',
            'AL' => '609б',
            'AM' => '.СЗ/',
            'AN' => 'Библиотека',
            'AO' => 'ЗалУС',
            'AP' => 'Площ',
            'AQ' => '(АКТЗ)',
            'AR' => 'Корп. 2.1',
            'AS' => 'Корп. 2.2'
        ];

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $firstMergeIndex = 1;
        $lastMergeIndex = 1;

        $sheet->setCellValue('A' . $offset, 'Дни');
        $sheet->setCellValue('B' . $offset, 'Часы');
        $sheet->setCellValue('C' . $offset, '101');
        $sheet->setCellValue('D' . $offset, '214');
        $sheet->setCellValue('E' . $offset, '201');
        $sheet->setCellValue('F' . $offset, '202');
        $sheet->setCellValue('G' . $offset, '208');
        $sheet->setCellValue('H' . $offset, '209');
        $sheet->setCellValue('I' . $offset, '219');
        $sheet->setCellValue('J' . $offset, '303');
        $sheet->setCellValue('K' . $offset, '304');
        $sheet->setCellValue('L' . $offset, '315');
        $sheet->setCellValue('M' . $offset, '401');
        $sheet->setCellValue('N' . $offset, '403');
        $sheet->setCellValue('O' . $offset, '406');
        $sheet->setCellValue('P' . $offset, '407');
        $sheet->setCellValue('Q' . $offset, '408');
        $sheet->setCellValue('R' . $offset, '409');
        $sheet->setCellValue('S' . $offset, '409а');
        $sheet->setCellValue('T' . $offset, '410');
        $sheet->setCellValue('U' . $offset, '413');
        $sheet->setCellValue('V' . $offset, '501');
        $sheet->setCellValue('W' . $offset, '503');
        $sheet->setCellValue('X' . $offset, '506');
        $sheet->setCellValue('Y' . $offset, '507');
        $sheet->setCellValue('Z' . $offset, '509');
        $sheet->setCellValue('AA' . $offset, '510');
        $sheet->setCellValue('AB' . $offset, '601а');
        $sheet->setCellValue('AC' . $offset, '601б');
        $sheet->setCellValue('AD' . $offset, '602а');
        $sheet->setCellValue('AE' . $offset, '602б');
        $sheet->setCellValue('AF' . $offset, '604а');
        $sheet->setCellValue('AG' . $offset, '604б');
        $sheet->setCellValue('AH' . $offset, '605');
        $sheet->setCellValue('AI' . $offset, '608а');
        $sheet->setCellValue('AJ' . $offset, '608б');
        $sheet->setCellValue('AK' . $offset, '609а');
        $sheet->setCellValue('AL' . $offset, '609б');
        $sheet->setCellValue('AM' . $offset, '.СЗ/');
        $sheet->setCellValue('AN' . $offset, 'Библиотека');
        $sheet->setCellValue('AO' . $offset, 'ЗалУС');
        $sheet->setCellValue('AP' . $offset, 'Площ');
        $sheet->setCellValue('AQ' . $offset, '(АКТЗ)');
        $sheet->setCellValue('AR' . $offset, 'Корп. 2.1');
        $sheet->setCellValue('AS' . $offset, 'Корп. 2.2');

        $offset++;

        foreach ($weekSchedule as $value) {
            $firstMergeIndex = $offset;
            $sheet->setCellValue('A' . $offset, $value['day'] . ' ' . Carbon::parse($value['date'])->format('d/m'));
            foreach ($value['schedule']['data'] as $schedule) {
                $sheet->setCellValue('B' . $offset, $schedule['time']);

                $key = array_search($schedule['lesson_place'], $rooms);
                if ($key != false)
                    $sheet->setCellValue($key . $offset, 'x');

                $offset++;
            }
            $lastMergeIndex = $offset;
            $sheet->mergeCells('A' . $firstMergeIndex . ':A' . $lastMergeIndex - 1);
        }

        $sheet->getStyle('A1:AS1')->getFont()->setBold(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('A1:AS' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:AS' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }
}
