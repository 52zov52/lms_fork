<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContingentReportController extends Controller
{
    public $sumFirstCourse = 0, $sumSecondCourse = 0, $sumThirdCourse = 0, $sumFourthCourse = 0, $sumFiveCourse = 0;
    public $totalSumFirstCourse = 0, $totalSumSecondCourse = 0, $totalSumThirdCourse = 0, $totalSumFourthCourse = 0, $totalSumFiveCourse = 0;

    public function generateContingentReport()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $data = DB::table('groups')
            ->leftJoin('group_student', 'groups.id', '=', 'group_student.group_id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->select(
                'groups.id',
                'groups.group_name',
                'groups.course',
                DB::raw('COUNT(group_student.user_id) as student_count'),
                'specialities.speciality_name',
                'specialities.specialty_code',
                'specialities.level_education_id',
                'study_plans.teaching_form_id'
            )
            ->whereIn('groups.id', $groupId)
            ->groupBy('groups.id', 'groups.group_name', 'groups.course')
            ->orderBy('specialities.specialty_code')
            ->orderBy('groups.course')
            ->get();

        $bachelor = $this->getDataBylevelEducation($data, 6);
        $master = $this->getDataBylevelEducation($data, 7);
        $college = $this->getDataBylevelEducation($data, 2);

        $results = array($bachelor, $master, $college);

        //dd($results);die();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offsetStart = 1;
        $offsetCurrent = 1;

        $lastRecord = "";

        for ($i = 0; $i < count($results); $i++) {
            $sheet->setCellValue('A' . $offsetCurrent, 'Группа');
            $sheet->setCellValue('B' . $offsetCurrent, '1 курс');
            $sheet->setCellValue('C' . $offsetCurrent, '2 курс');
            $sheet->setCellValue('D' . $offsetCurrent, '3 курс');
            $sheet->setCellValue('E' . $offsetCurrent, '4 курс');
            $sheet->setCellValue('F' . $offsetCurrent, '5 курс');

            $sheet->getStyle('A' . $offsetCurrent . ':F' . $offsetCurrent)->getFont()->setBold(true);

            $offsetCurrent++;
            foreach ($results[$i] as $key => $value) {

                if ($lastRecord == "") {
                    $lastRecord = $value->specialty_code . ' ' . $value->speciality_name;
                } elseif ($lastRecord != $value->specialty_code . ' ' . $value->speciality_name) {
                    $sheet->setCellValue('A' . $offsetCurrent, $lastRecord);
                    $sheet->setCellValue('B' . $offsetCurrent, $this->sumFirstCourse);
                    $sheet->setCellValue('C' . $offsetCurrent, $this->sumSecondCourse);
                    $sheet->setCellValue('D' . $offsetCurrent, $this->sumThirdCourse);
                    $sheet->setCellValue('E' . $offsetCurrent, $this->sumFourthCourse);
                    $sheet->setCellValue('F' . $offsetCurrent, $this->sumFiveCourse);

                    $this->sumFirstCourse = 0; $this->sumSecondCourse = 0; $this->sumThirdCourse = 0; $this->sumFourthCourse = 0; $this->sumFiveCourse = 0;

                    $offsetCurrent++;

                    $lastRecord = "";
                }

                $sheet->setCellValue('A' . $offsetCurrent, $value->group_name);
                switch ($value->course) {
                    case 1:
                        $sheet->setCellValue('B' . $offsetCurrent, $value->student_count);
                        $this->sumFirstCourse += $value->student_count;
                        $this->totalSumFirstCourse += $value->student_count;
                        break;
                    case 2:
                        $sheet->setCellValue('C' . $offsetCurrent, $value->student_count);
                        $this->sumSecondCourse += $value->student_count;
                        $this->totalSumSecondCourse += $value->student_count;
                        break;
                    case 3:
                        $sheet->setCellValue('D' . $offsetCurrent, $value->student_count);
                        $this->sumThirdCourse += $value->student_count;
                        $this->totalSumThirdCourse += $value->student_count;
                        break;
                    case 4:
                        $sheet->setCellValue('E' . $offsetCurrent, $value->student_count);
                        $this->sumFourthCourse += $value->student_count;
                        $this->totalSumFourthCourse += $value->student_count;
                        break;
                    case 5:
                        $sheet->setCellValue('F' . $offsetCurrent, $value->student_count);
                        $this->sumFiveCourse += $value->student_count;
                        $this->totalSumFiveCourse += $value->student_count;
                        break;
                    default:
                        break;
                }

                $offsetCurrent++;
            }

            $sheet->setCellValue('A' . $offsetCurrent, $lastRecord);
            $sheet->setCellValue('B' . $offsetCurrent, $this->sumFirstCourse);
            $sheet->setCellValue('C' . $offsetCurrent, $this->sumSecondCourse);
            $sheet->setCellValue('D' . $offsetCurrent, $this->sumThirdCourse);
            $sheet->setCellValue('E' . $offsetCurrent, $this->sumFourthCourse);
            $sheet->setCellValue('F' . $offsetCurrent, $this->sumFiveCourse);

            $this->sumFirstCourse = 0; $this->sumSecondCourse = 0; $this->sumThirdCourse = 0; $this->sumFourthCourse = 0; $this->sumFiveCourse = 0;

            $offsetCurrent++;

            $lastRecord = "";

            $sheet->setCellValue('A' . $offsetCurrent, 'Всего');
            $sheet->setCellValue('B' . $offsetCurrent, $this->totalSumFirstCourse);
            $sheet->setCellValue('C' . $offsetCurrent, $this->totalSumSecondCourse);
            $sheet->setCellValue('D' . $offsetCurrent, $this->totalSumThirdCourse);
            $sheet->setCellValue('E' . $offsetCurrent, $this->totalSumFourthCourse);
            $sheet->setCellValue('F' . $offsetCurrent, $this->totalSumFiveCourse);

            $this->totalSumFirstCourse = 0; $this->totalSumSecondCourse = 0; $this->totalSumThirdCourse = 0; $this->totalSumFourthCourse = 0; $this->totalSumFiveCourse = 0;

            $sheet->getStyle('A' . $offsetCurrent . ':F' . $offsetCurrent)->getFont()->setBold(true);

            //Стиль для рамки
            $sheet->getStyle('A'. $offsetStart .':F' . $offsetCurrent)
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));

            $offsetCurrent = $offsetCurrent + 2;
            $offsetStart = $offsetCurrent;
        }

        $sheet->getStyle('A1:A' . $offsetCurrent)->getAlignment()->setWrapText(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('B1:F' . ($offsetCurrent - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    protected function getDataBylevelEducation($data, $levelEducationId): array
    {
        $tmp = array();

        foreach ($data as $key => $value) {
            if ($value->level_education_id == $levelEducationId) {
                $tmp[] = $value;
            }
        }

        return $tmp;
    }
}
