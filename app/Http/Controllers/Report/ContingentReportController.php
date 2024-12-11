<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\ContingentController;
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
        $sheet->setCellValue('D' . $offset, '№ зачетки');
        $sheet->setCellValue('E' . $offset, 'Группа');
        $sheet->setCellValue('F' . $offset, 'Курс');
        $sheet->setCellValue('G' . $offset, 'Форма обучения');
        $sheet->setCellValue('H' . $offset, 'Направление подготовки');

        $sheet->getStyle('A' . $offset . ':H' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('A' . $offset . ':H' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $offset++;

        for ($i = 0; $i < count($listOfForeignStudents); $i++) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $listOfForeignStudents[$i]->country_name);
            $sheet->setCellValue('C' . $offset, $listOfForeignStudents[$i]->fio);
            $sheet->setCellValue('D' . $offset, $listOfForeignStudents[$i]->ticket_number);
            $sheet->setCellValue('E' . $offset, $listOfForeignStudents[$i]->group_name);
            $sheet->setCellValue('F' . $offset, $listOfForeignStudents[$i]->course);
            $sheet->setCellValue('G' . $offset, $listOfForeignStudents[$i]->teaching_form_name);
            $sheet->setCellValue('H' . $offset, $listOfForeignStudents[$i]->sp);

            $offset++;
            $increment++;
        }

        $sheet->getStyle('A1:H' . $offset)->getAlignment()->setWrapText(true);

        //Выравнивание текста по центру в первом столбце
        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('D1:D' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('E1:E' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('F1:F' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('G1:G' . ($offset - 1))
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
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(35);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
