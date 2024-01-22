<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContingentReportController extends Controller
{
    public $sumFirstCourse = 0, $sumSecondCourse = 0, $sumThirdCourse = 0, $sumFourthCourse = 0, $sumFiveCourse = 0;
    public $totalSumFirstCourse = 0, $totalSumSecondCourse = 0, $totalSumThirdCourse = 0, $totalSumFourthCourse = 0, $totalSumFiveCourse = 0;

    public function generateListOfStudentsInGroupReport($groupId)
    {
        $contingents = new ContingentController();
        $listOfStudents = $contingents->getContingent($groupId);

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

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

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
            ->get()->toArray();

        $bachelor = $this->getDataByLevelEducation($data, 6);
        $master = $this->getDataByLevelEducation($data, 7);
        $college = $this->getDataByLevelEducation($data, 2);

        $bachelor = $this->getDataByTeachingForm($bachelor);
        $master = $this->getDataByTeachingForm($master);
        $college = $this->getDataByTeachingForm($college);

        $results = array(['БАКАЛАВРИАТ' => $bachelor], ['МАГИСТРАТУРА' => $master], ['СРЕДНЕЕ ПРОФЕССИОНАЛЬНОЕ ОБРАЗОВАНИЕ' => $college]);

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offsetStart = 1;
        $offsetCurrent = 1;

        $speciality = "";

        $sheet->setCellValue('A' . $offsetCurrent, 'КОНТИНГЕНТ ОБУЧАЮЩИХСЯ ЧОУ ВО ЮУ (ИУБиП)');
        $sheet->mergeCells('A' . $offsetCurrent . ':F' . $offsetCurrent);
        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'на '. Carbon::now()->format('d.m.Y H:i:s'));
        $sheet->mergeCells('A' . $offsetCurrent . ':F' . $offsetCurrent);
        $offsetCurrent++;

        foreach ($results as $key => $results) {
            foreach ($results as $key => $levelEducationData) {
                $sheet->setCellValue('A' . $offsetCurrent, $key);
                $sheet->mergeCells('A' . $offsetCurrent . ':F' . $offsetCurrent);
                $offsetCurrent++;

                foreach ($levelEducationData as $key => $teachingFormData) {
                    foreach ($teachingFormData as $key => $data) {
                        $sheet->setCellValue('A' . $offsetCurrent, $key);
                        $sheet->mergeCells('A' . $offsetCurrent . ':F' . $offsetCurrent);
                        $offsetCurrent = $offsetCurrent + 2;

                        //Выравнивание текста в ячейке
                        $sheet->getStyle('A' . $offsetStart . ':F' . ($offsetCurrent))
                            ->applyFromArray(array('alignment' => [
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                            ]));

                        $sheet->getStyle('A' . $offsetStart . ':F' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetStart = $offsetCurrent;

                        $sheet->setCellValue('A' . $offsetCurrent, 'Группа');
                        $sheet->setCellValue('B' . $offsetCurrent, '1 курс');
                        $sheet->setCellValue('C' . $offsetCurrent, '2 курс');
                        $sheet->setCellValue('D' . $offsetCurrent, '3 курс');
                        $sheet->setCellValue('E' . $offsetCurrent, '4 курс');
                        $sheet->setCellValue('F' . $offsetCurrent, '5 курс');

                        $sheet->getStyle('A' . $offsetCurrent . ':F' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetCurrent++;

                        foreach ($data as $key => $value) {

                            if ($speciality == "") {
                                $speciality = $value->specialty_code . ' ' . $value->speciality_name;
                            } elseif ($speciality != $value->specialty_code . ' ' . $value->speciality_name) {
                                $sheet->setCellValue('A' . $offsetCurrent, $speciality);
                                $sheet->setCellValue('B' . $offsetCurrent, $this->sumFirstCourse);
                                $sheet->setCellValue('C' . $offsetCurrent, $this->sumSecondCourse);
                                $sheet->setCellValue('D' . $offsetCurrent, $this->sumThirdCourse);
                                $sheet->setCellValue('E' . $offsetCurrent, $this->sumFourthCourse);
                                $sheet->setCellValue('F' . $offsetCurrent, $this->sumFiveCourse);

                                $sheet->getStyle('A' . $offsetCurrent . ':F' . $offsetCurrent)->getFont()->setBold(true);

                                $this->sumFirstCourse = 0;
                                $this->sumSecondCourse = 0;
                                $this->sumThirdCourse = 0;
                                $this->sumFourthCourse = 0;
                                $this->sumFiveCourse = 0;

                                $offsetCurrent++;

                                $speciality = $value->specialty_code . ' ' . $value->speciality_name;
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

                        $sheet->setCellValue('A' . $offsetCurrent, $speciality);
                        $sheet->setCellValue('B' . $offsetCurrent, $this->sumFirstCourse);
                        $sheet->setCellValue('C' . $offsetCurrent, $this->sumSecondCourse);
                        $sheet->setCellValue('D' . $offsetCurrent, $this->sumThirdCourse);
                        $sheet->setCellValue('E' . $offsetCurrent, $this->sumFourthCourse);
                        $sheet->setCellValue('F' . $offsetCurrent, $this->sumFiveCourse);

                        $sheet->getStyle('A' . $offsetCurrent . ':F' . $offsetCurrent)->getFont()->setBold(true);

                        $this->sumFirstCourse = 0;
                        $this->sumSecondCourse = 0;
                        $this->sumThirdCourse = 0;
                        $this->sumFourthCourse = 0;
                        $this->sumFiveCourse = 0;

                        $offsetCurrent++;

                        $speciality = "";

                        $sheet->setCellValue('A' . $offsetCurrent, 'Всего');
                        $sheet->setCellValue('B' . $offsetCurrent, $this->totalSumFirstCourse);
                        $sheet->setCellValue('C' . $offsetCurrent, $this->totalSumSecondCourse);
                        $sheet->setCellValue('D' . $offsetCurrent, $this->totalSumThirdCourse);
                        $sheet->setCellValue('E' . $offsetCurrent, $this->totalSumFourthCourse);
                        $sheet->setCellValue('F' . $offsetCurrent, $this->totalSumFiveCourse);

                        $this->totalSumFirstCourse = 0;
                        $this->totalSumSecondCourse = 0;
                        $this->totalSumThirdCourse = 0;
                        $this->totalSumFourthCourse = 0;
                        $this->totalSumFiveCourse = 0;

                        $sheet->getStyle('A' . $offsetCurrent . ':F' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetCurrent++;

                        //Стиль для рамки
                        $sheet->getStyle('A' . $offsetStart . ':F' . $offsetCurrent - 1)
                            ->getBorders()
                            ->getAllBorders()
                            ->applyFromArray(array(
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => array('rgb' => '808080')
                            ));

                        $offsetStart = $offsetCurrent;
                    }
                    $offsetCurrent++;
                }
            }
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

    protected function getDataByLevelEducation($data, $levelEducationId): array
    {
        $tmp = array();

        foreach ($data as $key => $value) {
            if ($value->level_education_id == $levelEducationId) {
                $tmp[] = $value;
            }
        }

        return $tmp;
    }

    protected function getDataByTeachingForm($data): array
    {
        $tmp = array();
        $teachinFormName = '';

        $uniqueTeachingForm = array_unique(array_column($data, 'teaching_form_id'));

        foreach ($uniqueTeachingForm as $key => $teachingForm) {
            $tmpTeachingFormData = array();

            foreach ($data as $key => $value) {
                if ($value->teaching_form_id == $teachingForm) {
                    $tmpTeachingFormData[] = $value;
                }
            }

            switch ($teachingForm) {
                case 1:
                    $teachinFormName = 'ОЧНАЯ ФОРМА ОБУЧЕНИЯ';
                    break;
                case 2:
                    $teachinFormName = 'ЗАОЧНАЯ ФОРМА ОБУЧЕНИЯ';
                    break;
                case 3:
                    $teachinFormName = 'ЗАОЧНАЯ (УСК) ФОРМА ОБУЧЕНИЯ';
                    break;
                default:
                    $teachinFormName = 'ОЧНО-ЗАОЧНАЯ ФОРМА ОБУЧЕНИЯ';
                    break;
            }

            array_push($tmp, [$teachinFormName => $tmpTeachingFormData]);
            unset($tmpTeachingFormData);
        }

        return $tmp;
    }
}
