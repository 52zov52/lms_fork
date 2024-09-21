<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\StudentSptConsentStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SptController extends Controller
{
    public function getStudentConsentStatuses()
    {
        $studentSptConsentStatus = StudentSptConsentStatus::select(
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name',
            'groups.group_name',
            'student_spt_consent_statuses.academic_year',
            'student_spt_consent_statuses.half_year',
            'student_spt_consent_statuses.consent_status'
        )
            ->join('users_data', 'student_spt_consent_statuses.user_id', '=', 'users_data.user_id')
            ->join('group_student', function ($join) {
                $join->on('student_spt_consent_statuses.user_id', '=', 'group_student.user_id')
                     ->whereRaw('group_student.id = (SELECT MAX(id) FROM group_student gs WHERE gs.user_id = group_student.user_id)');
            })
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->get();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $offset = 1;

        $sheet->setCellValue('A' . $offset, 'ФИО');
        $sheet->setCellValue('B' . $offset, 'Группа');
        $sheet->setCellValue('C' . $offset, 'Учебный год');
        $sheet->setCellValue('D' . $offset, 'Полугодие');
        $sheet->setCellValue('E' . $offset, 'Статус согласия');

        $sheet->getStyle('A' . $offset . ':E' . $offset)->getFont()->setBold(true);

        $offset++;

        foreach ($studentSptConsentStatus as $key => $value) {
            $sheet->setCellValue('A' . $offset, $value->last_name . ' ' . $value->first_name . ' ' . $value->middle_name);
            $sheet->setCellValue('B' . $offset, $value->group_name);
            $sheet->setCellValue('C' . $offset, $value->academic_year);
            $sheet->setCellValue('D' . $offset, $value->half_year);
            $sheet->setCellValue('E' . $offset, ($value->consent_status) ? 'Подтверждена' : 'Не подтверждена');

            $offset++;
        }

        //Выравнивание текста в ячейке
        $sheet->getStyle('A1:E1')
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('B1:E' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:E' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(50);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(20);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }
}
