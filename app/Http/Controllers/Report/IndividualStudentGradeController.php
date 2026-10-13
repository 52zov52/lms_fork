<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\StudentExamRetakeProtocol;
use App\Models\StudentGrades;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class IndividualStudentGradeController extends Controller
{
    /** Доступные форматы выгрузки */
    public const FORMATS = ['docx', 'xlsx', 'pdf'];

    /** Виды контроля, оценка по которым выводится как 1 / 0 (зачёт / незачёт) */
    private const PASS_FAIL = ['Зачет', 'Зачёт', 'Комплексный зачёт', 'Комплексный зачет', 'Отчет по практике'];

    public function print($userId, string $format = 'docx')
    {
        $format = in_array($format, self::FORMATS, true) ? $format : 'docx';

        $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
            ->where('user_id', '=', $userId)
            ->select(
                'group_student.id',
                'group_student.group_id',
                'groups.work_study_plan_id',
                'groups.group_name'
            )
            ->orderBy('id', 'desc')
            ->first();

        if (is_null($groupStudent)) {
            throw new \DomainException('Не удалось определить группу студента.');
        }

        $studentGrades = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('student_grades.user_id', '=', $userId)
            ->where('work_study_plans.id', '=', $groupStudent->work_study_plan_id)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                DB::raw("CASE WHEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) IS NOT NULL 
                    THEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) 
                    WHEN (SELECT grade FROM student_exam_retake_protocols WHERE student_exam_retake_protocols.user_id = student_grades.user_id 
                            AND student_exam_retake_protocols.work_study_plan_course_control_types_in_semesters_id = bulletins.work_study_plan_course_control_types_in_semesters_id ORDER BY id DESC LIMIT 1) IS NOT NULL
                    THEN (SELECT grade FROM student_exam_retake_protocols WHERE student_exam_retake_protocols.user_id = student_grades.user_id 
                            AND student_exam_retake_protocols.work_study_plan_course_control_types_in_semesters_id = bulletins.work_study_plan_course_control_types_in_semesters_id ORDER BY id DESC LIMIT 1)
                    ELSE student_grades.grade END as grade")
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->get()->toArray();

        $studentExamRetakeProtocol = StudentExamRetakeProtocol::join('users_data', 'student_exam_retake_protocols.user_id', '=', 'users_data.user_id')
            ->join('work_study_plan_course_control_types_in_semesters', 'student_exam_retake_protocols.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('student_exam_retake_protocols.user_id', '=', $userId)
            ->where('work_study_plans.id', '=', $groupStudent->work_study_plan_id)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                'student_exam_retake_protocols.grade'
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->get()->toArray();

        $merged = array_merge($studentGrades, $studentExamRetakeProtocol);
        $unique = array_map('unserialize', array_unique(array_map('serialize', $merged)));

        if (empty($unique)) {
            throw new \DomainException('У студента нет оценок для формирования ведомости.');
        }

        $fio = $studentGrades[0]['fio'] ?? ($unique[0]['fio'] ?? '');
        $groupName = $groupStudent->group_name;
        $bySemester = $this->groupStudentGradesBySemester($unique);

        return match ($format) {
            'xlsx' => $this->renderXlsx($fio, $groupName, $bySemester),
            'pdf'  => $this->renderPdf($fio, $groupName, $bySemester),
            default => $this->renderDocx($fio, $groupName, $bySemester),
        };
    }

    private function groupStudentGradesBySemester($studentGrades): array
    {
        $results = [];
        $semesters = array_unique(array_column($studentGrades, 'semester_number'));
        sort($semesters);

        foreach ($semesters as $value) {
            $results[$value] = array_values(array_filter($studentGrades, function ($item) use ($value) {
                return $item['semester_number'] == $value;
            }));
        }

        return $results;
    }

    /** Оценка в человекочитаемом виде: 1/0 для зачётов, число для остальных */
    private function displayGrade(string $controlName, $grade): string
    {
        if (is_null($grade)) {
            return '';
        }

        if (in_array(trim($controlName), self::PASS_FAIL, true)) {
            return ((int) $grade === 1) ? 'зачёт' : 'незачёт';
        }

        return (string) (int) $grade;
    }

    /** === DOCX через PhpWord === */
    private function renderDocx(string $fio, string $groupName, array $bySemester): array
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        $section = $phpWord->addSection([
            'orientation'  => 'portrait',
            'pageSizeW'    => 11906,
            'pageSizeH'    => 16838,
            'marginTop'    => 850,
            'marginBottom' => 850,
            'marginLeft'   => 850,
            'marginRight'  => 850,
        ]);

        $section->addText(
            'ИНДИВИДУАЛЬНАЯ ВЕДОМОСТЬ УСПЕВАЕМОСТИ',
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 100]
        );
        $section->addText('Студент: ' . $fio, ['size' => 12], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        $section->addText('Группа: ' . $groupName, ['size' => 12], ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

        $center = ['alignment' => Jc::CENTER];
        $left = ['alignment' => Jc::LEFT];
        $headerFont = ['bold' => true, 'size' => 11];
        $font = ['size' => 11];

        foreach ($bySemester as $semester => $rows) {
            $section->addText('Семестр ' . $semester, ['bold' => true, 'size' => 12], ['spaceBefore' => 200, 'spaceAfter' => 100]);

            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 60,
            ]);

            $table->addRow(null, ['tblHeader' => true]);
            $table->addCell(600, ['valign' => 'center'])->addText('№', $headerFont, $center);
            $table->addCell(6000, ['valign' => 'center'])->addText('Дисциплина', $headerFont, $center);
            $table->addCell(3000, ['valign' => 'center'])->addText('Вид контроля', $headerFont, $center);
            $table->addCell(1800, ['valign' => 'center'])->addText('Оценка', $headerFont, $center);

            $i = 1;
            foreach ($rows as $row) {
                $table->addRow();
                $table->addCell(600, ['valign' => 'center'])->addText((string) $i++, $font, $center);
                $table->addCell(6000, ['valign' => 'center'])->addText((string) $row['discipline_name'], $font, $left);
                $table->addCell(3000, ['valign' => 'center'])->addText((string) $row['control_name'], $font, $center);
                $table->addCell(1800, ['valign' => 'center'])->addText($this->displayGrade((string) $row['control_name'], $row['grade'] ?? null), $font, $center);
            }
        }

        $filename = time() . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    /** === XLSX через PhpSpreadsheet === */
    private function renderXlsx(string $fio, string $groupName, array $bySemester): array
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ведомость');

        $sheet->setCellValue('A1', 'ИНДИВИДУАЛЬНАЯ ВЕДОМОСТЬ УСПЕВАЕМОСТИ');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', 'Студент: ' . $fio);
        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A3', 'Группа: ' . $groupName);
        $sheet->mergeCells('A3:D3');

        $row = 5;
        foreach ($bySemester as $semester => $rows) {
            $sheet->setCellValue('A' . $row, 'Семестр ' . $semester);
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            $row++;

            $sheet->fromArray(['№', 'Дисциплина', 'Вид контроля', 'Оценка'], null, 'A' . $row);
            $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setBold(true);
            $row++;

            $i = 1;
            foreach ($rows as $r) {
                $sheet->setCellValue('A' . $row, $i++);
                $sheet->setCellValue('B' . $row, (string) $r['discipline_name']);
                $sheet->setCellValue('C' . $row, (string) $r['control_name']);
                $sheet->setCellValue('D' . $row, $this->displayGrade((string) $r['control_name'], $r['grade'] ?? null));
                $row++;
            }

            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(55);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(14);

        $filename = time() . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        (new XlsxWriter($spreadsheet))->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    /** === PDF через DomPDF с Unicode-шрифтом === */
    private function renderPdf(string $fio, string $groupName, array $bySemester): array
    {
        $filename = time() . '.pdf';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        // DejaVu Sans идёт в комплекте с DomPDF и содержит кириллицу.
        // Без этой опции русские буквы рендерятся как «?».
        $pdf = PDF::setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
        ])->loadView('reports.pdf.individual-student-grades', [
            'fio' => $fio,
            'groupName' => $groupName,
            'studentGrades' => $bySemester,
            'gradesDisplay' => $this->buildDisplayGrades($bySemester),
        ]);

        $pdf->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    /**
     * Для PDF-шаблона — заранее сформированные текстовые оценки,
     * чтобы не тащить displayGrade() в blade.
     */
    private function buildDisplayGrades(array $bySemester): array
    {
        $result = [];
        foreach ($bySemester as $semester => $rows) {
            foreach ($rows as $i => $row) {
                $result[$semester][$i] = $this->displayGrade((string) $row['control_name'], $row['grade'] ?? null);
            }
        }
        return $result;
    }
}