<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\ContingentController;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContingentReportController extends Controller
{
    public function downloadListOfStudents($groupId)
    {
        $contingentController = new ContingentController();
        $listOfStudents = $contingentController->getContingent($groupId);

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;
        $increment = 1;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'ФИО');
        $sheet->setCellValue('C' . $offset, '№ зачетки');

        $sheet->getStyle('A' . $offset . ':C' . $offset)->getFont()->setBold(true);

        $offset++;

        for ($i = 0; $i < count($listOfStudents); $i++) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $listOfStudents[$i]->last_name . ' ' . $listOfStudents[$i]->first_name . ' ' . $listOfStudents[$i]->middle_name);
            $sheet->setCellValue('C' . $offset, $listOfStudents[$i]->ticket_number);

            $offset++;
            $increment++;
        }

        //Выравнивание текста в ячейке
        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Выравнивание текста в ячейке
        $sheet->getStyle('C1:C' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:C' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(15);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    public function downloadListOfForeignStudents()
    {
        $contingentController = new ContingentController();
        $listOfForeignStudents = $contingentController->getForeignStudents();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;
        $increment = 1;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'Страна');
        $sheet->setCellValue('C' . $offset, 'ФИО');
        $sheet->setCellValue('D' . $offset, 'Дата рождения');
        $sheet->setCellValue('E' . $offset, 'Серия паспорта');
        $sheet->setCellValue('F' . $offset, 'Номер паспорта');
        $sheet->setCellValue('G' . $offset, 'Кем выдан паспорт');
        $sheet->setCellValue('H' . $offset, 'Дата выдачи паспорта');
        $sheet->setCellValue('I' . $offset, '№ зачетки');
        $sheet->setCellValue('J' . $offset, 'Группа');
        $sheet->setCellValue('K' . $offset, 'Курс');
        $sheet->setCellValue('L' . $offset, 'Форма обучения');
        $sheet->setCellValue('M' . $offset, 'Направление подготовки');

        $sheet->getStyle('A' . $offset . ':M' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('A' . $offset . ':M' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $offset++;

        for ($i = 0; $i < count($listOfForeignStudents); $i++) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $listOfForeignStudents[$i]->country_name);
            $sheet->setCellValue('C' . $offset, $listOfForeignStudents[$i]->fio);
            $sheet->setCellValue('D' . $offset, Carbon::parse($listOfForeignStudents[$i]->birthday)->format('d.m.Y'));
            $sheet->setCellValue('E' . $offset, $listOfForeignStudents[$i]->passport_series);
            $sheet->setCellValue('F' . $offset, $listOfForeignStudents[$i]->passport_number);
            $sheet->setCellValue('G' . $offset, $listOfForeignStudents[$i]->passport_issuing_authority);
            $sheet->setCellValue('H' . $offset, Carbon::parse($listOfForeignStudents[$i]->passport_issue_date)->format('d.m.Y'));
            $sheet->setCellValue('I' . $offset, $listOfForeignStudents[$i]->ticket_number);
            $sheet->setCellValue('J' . $offset, $listOfForeignStudents[$i]->group_name);
            $sheet->setCellValue('K' . $offset, $listOfForeignStudents[$i]->course);
            $sheet->setCellValue('L' . $offset, $listOfForeignStudents[$i]->teaching_form_name);
            $sheet->setCellValue('M' . $offset, $listOfForeignStudents[$i]->sp);

            $offset++;
            $increment++;
        }

        $sheet->getStyle('A1:M' . $offset)->getAlignment()->setWrapText(true);

        //Выравнивание текста по центру в первом столбце
        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('D1:F' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('H1:L' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:M' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(10);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(15);
        $sheet->getColumnDimension('K')->setWidth(10);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(35);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
