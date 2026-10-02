<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\ContingentController;
use App\Models\EJournal;
use App\Models\Group;
use App\Models\WorkStudyPlanCoursesInSemester;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContingentReportController extends Controller
{
    public function downloadFullListOfStudents($groupId)
    {
        $contingentController = new ContingentController();
        $listOfStudents = $contingentController->getFullContingent($groupId);

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;
        $increment = 1;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'ФИО');
        $sheet->setCellValue('C' . $offset, 'Дата рождения');
        $sheet->setCellValue('D' . $offset, 'Серия и номер паспорта');
        $sheet->setCellValue('E' . $offset, 'Кем выдан паспорт');
        $sheet->setCellValue('F' . $offset, 'Дата выдачи паспорта');
        $sheet->setCellValue('G' . $offset, 'Адрес проживания');
        $sheet->setCellValue('H' . $offset, 'Документ об образовании');
        $sheet->setCellValue('I' . $offset, 'Документ об воинском учете');
        $sheet->setCellValue('J' . $offset, 'Серия и номер документа об воинском учете');
        $sheet->setCellValue('K' . $offset, 'Кем выдан документ об воинском учете');
        $sheet->setCellValue('L' . $offset, 'Дата выдачи документа об воинском учете');
        $sheet->setCellValue('M' . $offset, 'Категория запаса');
        $sheet->setCellValue('N' . $offset, 'Воинское звание');
        $sheet->setCellValue('O' . $offset, 'Состав (профиль)');
        $sheet->setCellValue('P' . $offset, 'Полное кодовое обозначение ВУС');
        $sheet->setCellValue('Q' . $offset, 'Категория годности к военной службе');
        $sheet->setCellValue('R' . $offset, 'Наименование военного комиссариата по месту воинского учета');
        $sheet->setCellValue('S' . $offset, 'Общий воинский учет');
        $sheet->setCellValue('T' . $offset, 'Специальный воинский учет');
        $sheet->setCellValue('U' . $offset, 'Группа в которой учится студент');
        $sheet->setCellValue('V' . $offset, '№ телефона');

        $sheet->getStyle('A' . $offset . ':V' . $offset)->getFont()->setBold(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('A' . $offset . ':V' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $offset++;

        for ($i = 0; $i < count($listOfStudents); $i++) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $listOfStudents[$i]->last_name . ' ' . $listOfStudents[$i]->first_name . ' ' . $listOfStudents[$i]->middle_name);
            $sheet->setCellValue('C' . $offset, $listOfStudents[$i]->birthday);
            $sheet->setCellValue('D' . $offset, $listOfStudents[$i]->passport_series . ' ' . $listOfStudents[$i]->passport_number);
            $sheet->setCellValue('E' . $offset, $listOfStudents[$i]->passport_issuing_authority);
            $sheet->setCellValue('F' . $offset, $listOfStudents[$i]->passport_issue_date);
            $sheet->setCellValue('G' . $offset, $listOfStudents[$i]->country_name . ', ' . $listOfStudents[$i]->region_name . ', ' .
                $listOfStudents[$i]->city_type_name . ', ' . $listOfStudents[$i]->city_name . ', ' .
                $listOfStudents[$i]->district_name . ', ' . $listOfStudents[$i]->street . ', ' .
                $listOfStudents[$i]->building . ', ' . $listOfStudents[$i]->flat);
            $sheet->setCellValue('H' . $offset, $listOfStudents[$i]->type_name . ', ' . $listOfStudents[$i]->name . ', ' .
                $listOfStudents[$i]->document_type . ', ' . $listOfStudents[$i]->document_series . ', ' .
                $listOfStudents[$i]->document_number . ', ' . $listOfStudents[$i]->document_issue_date . ', ' .
                $listOfStudents[$i]->end_year);
            $sheet->setCellValue('I' . $offset, $listOfStudents[$i]->military_document);
            $sheet->setCellValue('J' . $offset, $listOfStudents[$i]->military_document_series . ' ' . $listOfStudents[$i]->military_document_number);
            $sheet->setCellValue('K' . $offset, $listOfStudents[$i]->military_issued_by);
            $sheet->setCellValue('L' . $offset, $listOfStudents[$i]->military_issue_date);
            $sheet->setCellValue('M' . $offset, $listOfStudents[$i]->reserve_category);
            $sheet->setCellValue('N' . $offset, $listOfStudents[$i]->military_rank);
            $sheet->setCellValue('O' . $offset, $listOfStudents[$i]->military_occupation_profile);
            $sheet->setCellValue('P' . $offset, $listOfStudents[$i]->full_military_specialty_code);
            $sheet->setCellValue('Q' . $offset, $listOfStudents[$i]->fitness_category);
            $sheet->setCellValue('R' . $offset, $listOfStudents[$i]->military_commissariat_name);
            $sheet->setCellValue('S' . $offset, $listOfStudents[$i]->military_registration_type_general);
            $sheet->setCellValue('T' . $offset, $listOfStudents[$i]->military_registration_type_special);
            $sheet->setCellValue('U' . $offset, $listOfStudents[$i]->group_name);
            $sheet->setCellValue('V' . $offset, $listOfStudents[$i]->phone);

            $offset++;
            $increment++;
        }

        $sheet->getStyle('A1:V' . ($offset - 1))->getAlignment()->setWrapText(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('A2:V' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('C1:C' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('D1:D' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('F1:F' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('J1:J' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('L1:Q' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('U1:U' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('V1:V' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:V' . ($offset - 1))
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
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(35);
        $sheet->getColumnDimension('J')->setWidth(35);
        $sheet->getColumnDimension('K')->setWidth(35);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(15);
        $sheet->getColumnDimension('O')->setWidth(15);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(35);
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(15);
        $sheet->getColumnDimension('U')->setWidth(15);
        $sheet->getColumnDimension('V')->setWidth(15);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

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

    public function downloadEJournalReport($academicYear, $halfYear, $groupId, $wspCoursesInSemesterId)
    {
        $group = Group::where('id', '=', $groupId)->select('id', 'group_name')->first();

        $discipline = WorkStudyPlanCoursesInSemester::join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('work_study_plan_courses_in_semesters.id', '=', $wspCoursesInSemesterId)
            ->select(
                'disciplines.id',
                'disciplines.name'
            )
            ->distinct()
            ->first();

        $teacher = EJournal::join('users_data', 'e_journals.teacher_id', '=', 'users_data.user_id')
            ->where('e_journals.academic_year', '=', $academicYear)
            ->where('e_journals.half_year', '=', $halfYear)
            ->where('e_journals.work_study_plan_courses_in_semester_id', '=', $wspCoursesInSemesterId)
            ->where('e_journals.group_id', '=', $groupId)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
            )
            ->first();

        $eJournal = EJournal::join('users_data', 'e_journals.student_id', '=', 'users_data.user_id')
            ->where('e_journals.academic_year', '=', $academicYear)
            ->where('e_journals.half_year', '=', $halfYear)
            ->where('e_journals.work_study_plan_courses_in_semester_id', '=', $wspCoursesInSemesterId)
            ->where('e_journals.group_id', '=', $groupId)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("SUM(CASE WHEN e_journals.status = 1 THEN 1 ELSE 0 END) as was_present"),
                DB::raw("(COUNT(e_journals.status) - SUM(CASE WHEN e_journals.status = 1 THEN 1 ELSE 0 END)) as was_absent"),
                DB::raw("COUNT(e_journals.status) as total")
            )
            ->groupBy('fio')
            ->get();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;
        $increment = 1;

        $sheet->setCellValue('A' . $offset, 'Группа: ' . $group->group_name);
        $sheet->mergeCells('A' . $offset . ':G' . $offset);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Дисциплина: ' . $discipline->name);
        $sheet->mergeCells('A' . $offset . ':G' . $offset);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Преподаватель: ' . $teacher->fio);
        $sheet->mergeCells('A' . $offset . ':G' . $offset);

        $offset++;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'ФИО');
        $sheet->setCellValue('C' . $offset, 'Кол-во прошедших занятий');
        $sheet->setCellValue('D' . $offset, 'Кол-во пропусков');
        $sheet->setCellValue('E' . $offset, '% пропусков');
        $sheet->setCellValue('F' . $offset, 'Кол-во посещений');
        $sheet->setCellValue('G' . $offset, '% посещений');

        $sheet->getStyle('A' . $offset . ':G' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('A' . $offset . ':G' . $offset)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A' . $offset . ':G' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]));

        $offset++;

        for ($i = 0; $i < count($eJournal); $i++) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $eJournal[$i]->fio);
            $sheet->setCellValue('C' . $offset, $eJournal[$i]->total);
            $sheet->setCellValue('D' . $offset, $eJournal[$i]->was_absent);
            $sheet->setCellValue('E' . $offset, round(($eJournal[$i]->was_absent / $eJournal[$i]->total) * 100, 2));
            $sheet->setCellValue('F' . $offset, $eJournal[$i]->was_present);
            $sheet->setCellValue('G' . $offset, round(($eJournal[$i]->was_present / $eJournal[$i]->total) * 100, 2));

            $offset++;
            $increment++;
        }

        $sheet->getStyle('B1:B' . $offset)->getAlignment()->setWrapText(true);

        $sheet->getStyle('A5' . ':A' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]));

        $sheet->getStyle('C5' . ':G' . $offset)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:G' . ($offset - 1))
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
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        $writer = new Xlsx($spreadsheet);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    public function downloadStudentsEducationReport()
    {
        $students = (new ContingentController())->getStudentsEducationData();
 
        $filename = time() . '.xlsx';
 
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Данные студентов');
 
        $lastColumn = 'N';
 
        $sheet->setCellValue('A1', 'Данные студентов');
        $sheet->mergeCells('A1:' . $lastColumn . '1');
 
        $sheet->setCellValue('F2', 'Документ об образовании');
        $sheet->mergeCells('F2:J2');
 
        $sheet->fromArray([[
            '№ п/п', 'ФИО', 'Пол', 'Дата рождения', 'Гражданство',
            'Наименование', 'Год выдачи', 'Средний балл', 'Место выдачи', 'Кем выдан',
            'Группа', 'Курс', 'Форма обучения', 'Направление подготовки',
        ]], null, 'A3');
 
        $row = 4;
        $number = 1;
 
        foreach ($students as $student) {
            $year = $student->end_year
                ?: ($student->document_issue_date ? Carbon::parse($student->document_issue_date)->year : '');
 
            $sheet->fromArray([[
                $number++,
                trim(preg_replace('/\s+/u', ' ', $student->fio)),
                $student->sex ? 'Ж' : 'М',
                Carbon::parse($student->birthday)->format('d.m.Y'),
                $student->country_name,
                $student->document_type,
                $year,
                $student->average_score,
                $student->city_name,
                $student->organization_name,
                $student->group_name,
                $student->course,
                $student->teaching_form_name,
                $student->sp,
            ]], null, 'A' . $row);
 
            $row++;
        }
 
        $lastRow = max($row - 1, 3);
 
        // Заголовок и шапка
        $sheet->getStyle('A1:' . $lastColumn . '3')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '3')->applyFromArray(['alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ]]);
        $sheet->getStyle('A3:' . $lastColumn . '3')->getAlignment()->setWrapText(true);
 
        // Данные: перенос текста, по центру короткие столбцы
        $sheet->getStyle('A4:' . $lastColumn . $lastRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('A4:' . $lastColumn . $lastRow)->getAlignment()
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
 
        foreach (['A', 'C', 'D', 'G', 'H', 'K', 'L', 'M'] as $column) {
            $sheet->getStyle($column . '4:' . $column . $lastRow)
                ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }
 
        // Рамка
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray([
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '808080'],
            ]);
 
        // Ширина столбцов
        $widths = [
            'A' => 8, 'B' => 35, 'C' => 6, 'D' => 14, 'E' => 22,
            'F' => 30, 'G' => 12, 'H' => 12, 'I' => 20, 'J' => 40,
            'K' => 15, 'L' => 8, 'M' => 16, 'N' => 35,
        ];
 
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
 
        // Закрепить шапку
        $sheet->freezePane('A4');
 
        $writer = new Xlsx($spreadsheet);
 
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);
 
        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
