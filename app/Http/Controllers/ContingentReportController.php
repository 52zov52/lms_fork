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

    public $sumBakFirstCourse = 0, $sumBakSecondCourse = 0, $sumBakThirdCourse = 0, $sumBakFourthCourse = 0, $sumBakFiveCourse = 0;
    public $sumMagFirstCourse = 0, $sumMagSecondCourse = 0, $sumMagThirdCourse = 0, $sumMagFourthCourse = 0, $sumMagFiveCourse = 0;
    public $totalSumVOFirstCourse = 0, $totalSumVOSecondCourse = 0, $totalSumVOThirdCourse = 0, $totalSumVOFourthCourse = 0, $totalSumVOFiveCourse = 0;

    public $sumSPOFirstCourse = 0, $sumSPOSecondCourse = 0, $sumSPOThirdCourse = 0, $sumSPOFourthCourse = 0, $sumSPOFiveCourse = 0;
    public $totalSumSPOFirstCourse = 0, $totalSumSPOSecondCourse = 0, $totalSumSPOThirdCourse = 0, $totalSumSPOFourthCourse = 0, $totalSumSPOFiveCourse = 0;

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
                'groups.year_completion',
                DB::raw('COUNT(group_student.user_id) as student_count'),
                'specialities.speciality_name',
                'specialities.specialty_code',
                'specialities.level_education_id',
                'study_plans.teaching_form_id',
                'study_plans.number_years',
                'study_plans.number_months',
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
        $sheet->mergeCells('A' . $offsetCurrent . ':H' . $offsetCurrent);
        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'на ' . Carbon::now()->format('d.m.Y') . ' г.');
        $sheet->mergeCells('A' . $offsetCurrent . ':H' . $offsetCurrent);
        $offsetCurrent++;

        foreach ($results as $key => $results) {
            foreach ($results as $key => $levelEducationData) {
                $levelEducation = $key;

                $sheet->setCellValue('A' . $offsetCurrent, $key);
                $sheet->mergeCells('A' . $offsetCurrent . ':H' . $offsetCurrent);
                $offsetCurrent++;

                foreach ($levelEducationData as $key => $teachingFormData) {
                    foreach ($teachingFormData as $key => $data) {
                        $sheet->setCellValue('A' . $offsetCurrent, $key);
                        $sheet->mergeCells('A' . $offsetCurrent . ':H' . $offsetCurrent);
                        $offsetCurrent = $offsetCurrent + 2;

                        //Выравнивание текста в ячейке
                        $sheet->getStyle('A' . $offsetStart . ':H' . ($offsetCurrent))
                            ->applyFromArray(array('alignment' => [
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                            ]));

                        $sheet->getStyle('A' . $offsetStart . ':H' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetStart = $offsetCurrent;

                        $sheet->setCellValue('A' . $offsetCurrent, 'Группа');
                        $sheet->setCellValue('B' . $offsetCurrent, 'Срок обучения');
                        $sheet->setCellValue('C' . $offsetCurrent, '1 курс');
                        $sheet->setCellValue('D' . $offsetCurrent, '2 курс');
                        $sheet->setCellValue('E' . $offsetCurrent, '3 курс');
                        $sheet->setCellValue('F' . $offsetCurrent, '4 курс');
                        $sheet->setCellValue('G' . $offsetCurrent, '5 курс');
                        $sheet->setCellValue('H' . $offsetCurrent, 'Год выпуска');

                        $sheet->getStyle('A' . $offsetCurrent . ':H' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetCurrent++;

                        foreach ($data as $key => $value) {

                            if ($speciality == "") {
                                $speciality = $value->specialty_code . ' ' . $value->speciality_name;
                            } elseif ($speciality != $value->specialty_code . ' ' . $value->speciality_name) {
                                $sheet->setCellValue('A' . $offsetCurrent, $speciality);
                                $sheet->setCellValue('B' . $offsetCurrent, '');
                                $sheet->setCellValue('C' . $offsetCurrent, $this->sumFirstCourse);
                                $sheet->setCellValue('D' . $offsetCurrent, $this->sumSecondCourse);
                                $sheet->setCellValue('E' . $offsetCurrent, $this->sumThirdCourse);
                                $sheet->setCellValue('F' . $offsetCurrent, $this->sumFourthCourse);
                                $sheet->setCellValue('G' . $offsetCurrent, $this->sumFiveCourse);
                                $sheet->setCellValue('H' . $offsetCurrent, '');

                                $sheet->getStyle('A' . $offsetCurrent . ':H' . $offsetCurrent)->getFont()->setBold(true);

                                $this->sumFirstCourse = 0;
                                $this->sumSecondCourse = 0;
                                $this->sumThirdCourse = 0;
                                $this->sumFourthCourse = 0;
                                $this->sumFiveCourse = 0;

                                $offsetCurrent++;

                                $speciality = $value->specialty_code . ' ' . $value->speciality_name;
                            }

                            $sheet->setCellValue('A' . $offsetCurrent, $value->group_name);
                            $trainingPeriod = $value->number_years . ' г. ' . $value->number_months . ' м. ';
                            $sheet->setCellValue('B' . $offsetCurrent, $trainingPeriod);
                            switch ($value->course) {
                                case 1:
                                    $sheet->setCellValue('C' . $offsetCurrent, $value->student_count);
                                    $this->sumFirstCourse += $value->student_count;
                                    $this->totalSumFirstCourse += $value->student_count;
                                    switch ($levelEducation) {
                                        case 'БАКАЛАВРИАТ':
                                            $this->sumBakFirstCourse += $value->student_count;
                                            $this->totalSumVOFirstCourse += $value->student_count;
                                            break;
                                        case 'МАГИСТРАТУРА':
                                            $this->sumMagFirstCourse += $value->student_count;
                                            $this->totalSumVOFirstCourse += $value->student_count;
                                            break;
                                        default:
                                            $this->sumSPOFirstCourse += $value->student_count;
                                            $this->totalSumSPOFirstCourse += $value->student_count;
                                            break;
                                    }
                                    break;
                                case 2:
                                    $sheet->setCellValue('D' . $offsetCurrent, $value->student_count);
                                    $this->sumSecondCourse += $value->student_count;
                                    $this->totalSumSecondCourse += $value->student_count;
                                    switch ($levelEducation) {
                                        case 'БАКАЛАВРИАТ':
                                            $this->sumBakSecondCourse += $value->student_count;
                                            $this->totalSumVOSecondCourse += $value->student_count;
                                            break;
                                        case 'МАГИСТРАТУРА':
                                            $this->sumMagSecondCourse += $value->student_count;
                                            $this->totalSumVOSecondCourse += $value->student_count;
                                            break;
                                        default:
                                            $this->sumSPOSecondCourse += $value->student_count;
                                            $this->totalSumSPOSecondCourse += $value->student_count;
                                            break;
                                    }
                                    break;
                                case 3:
                                    $sheet->setCellValue('E' . $offsetCurrent, $value->student_count);
                                    $this->sumThirdCourse += $value->student_count;
                                    $this->totalSumThirdCourse += $value->student_count;
                                    switch ($levelEducation) {
                                        case 'БАКАЛАВРИАТ':
                                            $this->sumBakThirdCourse += $value->student_count;
                                            $this->totalSumVOThirdCourse += $value->student_count;
                                            break;
                                        case 'МАГИСТРАТУРА':
                                            $this->sumMagThirdCourse += $value->student_count;
                                            $this->totalSumVOThirdCourse += $value->student_count;
                                            break;
                                        default:
                                            $this->sumSPOThirdCourse += $value->student_count;
                                            $this->totalSumSPOThirdCourse += $value->student_count;
                                            break;
                                    }
                                    break;
                                case 4:
                                    $sheet->setCellValue('F' . $offsetCurrent, $value->student_count);
                                    $this->sumFourthCourse += $value->student_count;
                                    $this->totalSumFourthCourse += $value->student_count;
                                    switch ($levelEducation) {
                                        case 'БАКАЛАВРИАТ':
                                            $this->sumBakFourthCourse += $value->student_count;
                                            $this->totalSumVOFourthCourse += $value->student_count;
                                            break;
                                        case 'МАГИСТРАТУРА':
                                            $this->sumMagFourthCourse += $value->student_count;
                                            $this->totalSumVOFourthCourse += $value->student_count;
                                            break;
                                        default:
                                            $this->sumSPOFourthCourse += $value->student_count;
                                            $this->totalSumSPOFourthCourse += $value->student_count;
                                            break;
                                    }
                                    break;
                                case 5:
                                    $sheet->setCellValue('G' . $offsetCurrent, $value->student_count);
                                    $this->sumFiveCourse += $value->student_count;
                                    $this->totalSumFiveCourse += $value->student_count;
                                    switch ($levelEducation) {
                                        case 'БАКАЛАВРИАТ':
                                            $this->sumBakFiveCourse += $value->student_count;
                                            $this->totalSumVOFiveCourse += $value->student_count;
                                            break;
                                        case 'МАГИСТРАТУРА':
                                            $this->sumMagFiveCourse += $value->student_count;
                                            $this->totalSumVOFiveCourse += $value->student_count;
                                            break;
                                        default:
                                            $this->sumSPOFiveCourse += $value->student_count;
                                            $this->totalSumSPOFiveCourse += $value->student_count;
                                            break;
                                    }
                                    break;
                                default:
                                    break;
                            }
                            $sheet->setCellValue('H' . $offsetCurrent, (!is_null($value->year_completion) && !empty($value->year_completion)) ? Carbon::parse($value->year_completion)->format('Y') : '');
                            $offsetCurrent++;
                        }

                        $sheet->setCellValue('A' . $offsetCurrent, $speciality);
                        $sheet->setCellValue('B' . $offsetCurrent, '');
                        $sheet->setCellValue('C' . $offsetCurrent, $this->sumFirstCourse);
                        $sheet->setCellValue('D' . $offsetCurrent, $this->sumSecondCourse);
                        $sheet->setCellValue('E' . $offsetCurrent, $this->sumThirdCourse);
                        $sheet->setCellValue('F' . $offsetCurrent, $this->sumFourthCourse);
                        $sheet->setCellValue('G' . $offsetCurrent, $this->sumFiveCourse);
                        $sheet->setCellValue('H' . $offsetCurrent, '');

                        $sheet->getStyle('A' . $offsetCurrent . ':H' . $offsetCurrent)->getFont()->setBold(true);

                        $this->sumFirstCourse = 0;
                        $this->sumSecondCourse = 0;
                        $this->sumThirdCourse = 0;
                        $this->sumFourthCourse = 0;
                        $this->sumFiveCourse = 0;

                        $offsetCurrent++;

                        $speciality = "";

                        $sheet->setCellValue('A' . $offsetCurrent, 'Всего');
                        $sheet->setCellValue('B' . $offsetCurrent, '');
                        $sheet->setCellValue('C' . $offsetCurrent, $this->totalSumFirstCourse);
                        $sheet->setCellValue('D' . $offsetCurrent, $this->totalSumSecondCourse);
                        $sheet->setCellValue('E' . $offsetCurrent, $this->totalSumThirdCourse);
                        $sheet->setCellValue('F' . $offsetCurrent, $this->totalSumFourthCourse);
                        $sheet->setCellValue('G' . $offsetCurrent, $this->totalSumFiveCourse);
                        $sheet->setCellValue('H' . $offsetCurrent, '');

                        $this->totalSumFirstCourse = 0;
                        $this->totalSumSecondCourse = 0;
                        $this->totalSumThirdCourse = 0;
                        $this->totalSumFourthCourse = 0;
                        $this->totalSumFiveCourse = 0;

                        $sheet->getStyle('A' . $offsetCurrent . ':H' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetCurrent++;

                        //Стиль для рамки
                        $sheet->getStyle('A' . $offsetStart . ':H' . $offsetCurrent - 1)
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

        $sheet->setCellValue('A' . $offsetCurrent, 'КОНТИНГЕНТ ЧОУ ВО ЮУ (ИУБиП) (ПОЛНЫЙ) НА ' . Carbon::now()->format('d.m.Y') . ' г.');
        $sheet->mergeCells('A' . $offsetCurrent . ':H' . $offsetCurrent);
        $sheet->getStyle('A' . $offsetCurrent . ':H' . $offsetCurrent)->getFont()->setBold(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('A' . $offsetCurrent . ':H' . ($offsetCurrent))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $offsetCurrent += 2;
        $offsetStart = $offsetCurrent;

        $sheet->setCellValue('A' . $offsetCurrent, '');
        $sheet->setCellValue('B' . $offsetCurrent, '1 курс');
        $sheet->setCellValue('C' . $offsetCurrent, '2 курс');
        $sheet->setCellValue('D' . $offsetCurrent, '3 курс');
        $sheet->setCellValue('E' . $offsetCurrent, '4 курс');
        $sheet->setCellValue('F' . $offsetCurrent, '5 курс');
        $sheet->setCellValue('G' . $offsetCurrent, 'Всего');
        $sheet->mergeCells('G' . $offsetCurrent . ':H' . $offsetCurrent);

        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'Всего ВО');
        $sheet->setCellValue('B' . $offsetCurrent, $this->totalSumVOFirstCourse);
        $sheet->setCellValue('C' . $offsetCurrent, $this->totalSumVOSecondCourse);
        $sheet->setCellValue('D' . $offsetCurrent, $this->totalSumVOThirdCourse);
        $sheet->setCellValue('E' . $offsetCurrent, $this->totalSumVOFourthCourse);
        $sheet->setCellValue('F' . $offsetCurrent, $this->totalSumVOFiveCourse);
        $sheet->setCellValue('G' . $offsetCurrent, $this->totalSumVOFirstCourse +
            $this->totalSumVOSecondCourse +
            $this->totalSumVOThirdCourse +
            $this->totalSumVOFourthCourse +
            $this->totalSumVOFiveCourse);
        $sheet->mergeCells('G' . $offsetCurrent . ':H' . $offsetCurrent);

        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'Всего СПО');
        $sheet->setCellValue('B' . $offsetCurrent, $this->totalSumSPOFirstCourse);
        $sheet->setCellValue('C' . $offsetCurrent, $this->totalSumSPOSecondCourse);
        $sheet->setCellValue('D' . $offsetCurrent, $this->totalSumSPOThirdCourse);
        $sheet->setCellValue('E' . $offsetCurrent, $this->totalSumSPOFourthCourse);
        $sheet->setCellValue('F' . $offsetCurrent, $this->totalSumSPOFiveCourse);
        $sheet->setCellValue('G' . $offsetCurrent, $this->totalSumSPOFirstCourse +
            $this->totalSumSPOSecondCourse +
            $this->totalSumSPOThirdCourse +
            $this->totalSumSPOFourthCourse +
            $this->totalSumSPOFiveCourse);
        $sheet->mergeCells('G' . $offsetCurrent . ':H' . $offsetCurrent);

        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'ИТОГО по ВУЗу');
        $sheet->setCellValue('B' . $offsetCurrent, $this->totalSumVOFirstCourse + $this->totalSumSPOFirstCourse);
        $sheet->setCellValue('C' . $offsetCurrent, $this->totalSumVOSecondCourse + $this->totalSumSPOSecondCourse);
        $sheet->setCellValue('D' . $offsetCurrent, $this->totalSumVOThirdCourse + $this->totalSumSPOThirdCourse);
        $sheet->setCellValue('E' . $offsetCurrent, $this->totalSumVOFourthCourse + $this->totalSumSPOFourthCourse);
        $sheet->setCellValue('F' . $offsetCurrent, $this->totalSumVOFiveCourse + $this->totalSumSPOFiveCourse);
        $sheet->setCellValue('G' . $offsetCurrent, ($this->totalSumVOFirstCourse +
            $this->totalSumVOSecondCourse +
            $this->totalSumVOThirdCourse +
            $this->totalSumVOFourthCourse +
            $this->totalSumVOFiveCourse) +
            ($this->totalSumSPOFirstCourse +
                $this->totalSumSPOSecondCourse +
                $this->totalSumSPOThirdCourse +
                $this->totalSumSPOFourthCourse +
                $this->totalSumSPOFiveCourse));
        $sheet->mergeCells('G' . $offsetCurrent . ':H' . $offsetCurrent);

        $offsetCurrent++;

        //Стиль для рамки
        $sheet->getStyle('A' . $offsetStart . ':H' . $offsetCurrent - 1)
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        $sheet->getStyle('A1:A' . $offsetCurrent)->getAlignment()->setWrapText(true);
        $sheet->getStyle('B1:B' . $offsetCurrent)->getAlignment()->setWrapText(true);
        $sheet->getStyle('H1:H' . $offsetCurrent)->getAlignment()->setWrapText(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('B1:H' . ($offsetCurrent - 1))
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
        $sheet->getColumnDimension('G')->setWidth(10);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    public function generateContingentByAgeAndGenderReport()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $data = DB::table('groups')
            ->leftJoin('group_student', 'groups.id', '=', 'group_student.group_id')
            ->leftJoin('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->select(
                DB::raw('COUNT(group_student.user_id) as student_count'),
                DB::raw('COUNT(CASE WHEN users_data.sex = 0 THEN 1 END) as male_student_count'),
                DB::raw('COUNT(CASE WHEN users_data.sex = 1 THEN 1 END) as female_student_count'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) < 15 THEN 1 END) as age_min_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) < 15 AND users_data.sex = 1 THEN 1 END) as female_age_min_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 15 THEN 1 END) as age_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 15 AND users_data.sex = 1 THEN 1 END) as female_age_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 16 THEN 1 END) as age_16'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 16 AND users_data.sex = 1 THEN 1 END) as female_age_16'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 17 THEN 1 END) as age_17'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 17 AND users_data.sex = 1 THEN 1 END) as female_age_17'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 18 THEN 1 END) as age_18'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 18 AND users_data.sex = 1 THEN 1 END) as female_age_18'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 19 THEN 1 END) as age_19'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 19 AND users_data.sex = 1 THEN 1 END) as female_age_19'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 20 THEN 1 END) as age_20'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 20 AND users_data.sex = 1 THEN 1 END) as female_age_20'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 21 THEN 1 END) as age_21'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 21 AND users_data.sex = 1 THEN 1 END) as female_age_21'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 22 THEN 1 END) as age_22'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 22 AND users_data.sex = 1 THEN 1 END) as female_age_22'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 23 THEN 1 END) as age_23'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 23 AND users_data.sex = 1 THEN 1 END) as female_age_23'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 24 THEN 1 END) as age_24'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 24 AND users_data.sex = 1 THEN 1 END) as female_age_24'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 25 THEN 1 END) as age_25'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 25 AND users_data.sex = 1 THEN 1 END) as female_age_25'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 26 THEN 1 END) as age_26'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 26 AND users_data.sex = 1 THEN 1 END) as female_age_26'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 27 THEN 1 END) as age_27'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 27 AND users_data.sex = 1 THEN 1 END) as female_age_27'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 28 THEN 1 END) as age_28'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 28 AND users_data.sex = 1 THEN 1 END) as female_age_28'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 29 THEN 1 END) as age_29'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 29 AND users_data.sex = 1 THEN 1 END) as female_age_29'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 30 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 34 THEN 1 END) as age_30_34'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 30 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 34  AND users_data.sex = 1 THEN 1 END) as female_age_30_34'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 35 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 39 THEN 1 END) as age_35_39'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 35 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 39 AND users_data.sex = 1 THEN 1 END) as female_age_35_39'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 40 THEN 1 END) as age_max_40'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 40 AND users_data.sex = 1 THEN 1 END) as female_age_max_40'),
                'specialities.level_education_id',
                'study_plans.teaching_form_id'
            )
            ->whereIn('groups.id', $groupId)
            ->groupBy('specialities.level_education_id', 'study_plans.teaching_form_id')
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

        $sheet->setCellValue('A' . $offsetCurrent, 'КОНТИНГЕНТ ОБУЧАЮЩИХСЯ ЧОУ ВО ЮУ (ИУБиП)');
        $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);
        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'на ' . Carbon::now()->format('d.m.Y') . ' г.');
        $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);
        $offsetCurrent++;

        foreach ($results as $key => $results) {
            foreach ($results as $key => $levelEducationData) {
                $sheet->setCellValue('A' . $offsetCurrent, $key);
                $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);
                $offsetCurrent++;

                foreach ($levelEducationData as $key => $teachingFormData) {
                    foreach ($teachingFormData as $key => $data) {
                        $sheet->setCellValue('A' . $offsetCurrent, $key);
                        $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);
                        $offsetCurrent = $offsetCurrent + 2;

                        //Выравнивание текста в ячейке
                        $sheet->getStyle('A' . $offsetStart . ':D' . ($offsetCurrent))
                            ->applyFromArray(array('alignment' => [
                                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                            ]));

                        $sheet->getStyle('A' . $offsetStart . ':D' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetStart = $offsetCurrent;

                        $sheet->setCellValue('A' . $offsetCurrent, 'Наименование показателей');
                        $sheet->setCellValue('B' . $offsetCurrent, 'Принято (обучается)');
                        $sheet->setCellValue('C' . $offsetCurrent, 'из них мужчин');
                        $sheet->setCellValue('D' . $offsetCurrent, 'из них женщин');

                        $sheet->getStyle('A' . $offsetCurrent . ':D' . $offsetCurrent)->getFont()->setBold(true);
                        $offsetCurrent++;

                        foreach ($data as $key => $value) {
                            $sheet->setCellValue('A' . $offsetCurrent, 'Всего');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->student_count);
                            $sheet->setCellValue('C' . $offsetCurrent, $value->male_student_count);
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_student_count);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, 'В возрасте (число полных лет на 1 января следущего года) моложе 15 лем');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_min_15);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_min_15 - $value->female_age_min_15));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_min_15);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '15 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_15);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_15 - $value->female_age_15));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_15);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '16 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_16);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_16 - $value->female_age_16));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_16);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '17 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_17);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_17 - $value->female_age_17));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_17);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '18 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_18);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_18 - $value->female_age_18));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_18);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '19 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_19);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_19 - $value->female_age_19));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_19);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '20 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_20);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_20 - $value->female_age_20));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_20);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '21 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_21);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_21 - $value->female_age_21));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_21);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '22 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_22);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_22 - $value->female_age_22));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_22);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '23 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_23);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_23 - $value->female_age_23));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_23);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '24 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_24);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_24 - $value->female_age_24));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_24);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '25 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_25);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_25 - $value->female_age_25));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_25);;
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '26 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_26);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_26 - $value->female_age_26));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_26);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '27 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_27);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_27 - $value->female_age_27));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_27);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '28 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_28);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_28 - $value->female_age_28));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_28);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '29 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_29);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_29 - $value->female_age_29));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_29);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '30 - 34 года');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_30_34);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_30_34 - $value->female_age_30_34));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_30_34);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '35 - 39 лет');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_35_39);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_35_39 - $value->female_age_35_39));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_35_39);
                            $offsetCurrent++;

                            $sheet->setCellValue('A' . $offsetCurrent, '40 лет и старше');
                            $sheet->setCellValue('B' . $offsetCurrent, $value->age_max_40);
                            $sheet->setCellValue('C' . $offsetCurrent, ($value->age_max_40 - $value->female_age_max_40));
                            $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_max_40);
                            $offsetCurrent++;
                        }

                        //Стиль для рамки
                        $sheet->getStyle('A' . $offsetStart . ':D' . $offsetCurrent - 1)
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
        $sheet->getStyle('B1:D' . ($offsetCurrent - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    public function generateContingentByAgeAndGenderWithoutTeachingFromReport()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $data = DB::table('groups')
            ->leftJoin('group_student', 'groups.id', '=', 'group_student.group_id')
            ->leftJoin('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->select(
                DB::raw('COUNT(group_student.user_id) as student_count'),
                DB::raw('COUNT(CASE WHEN users_data.sex = 0 THEN 1 END) as male_student_count'),
                DB::raw('COUNT(CASE WHEN users_data.sex = 1 THEN 1 END) as female_student_count'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) < 15 THEN 1 END) as age_min_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) < 15 AND users_data.sex = 1 THEN 1 END) as female_age_min_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 15 THEN 1 END) as age_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 15 AND users_data.sex = 1 THEN 1 END) as female_age_15'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 16 THEN 1 END) as age_16'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 16 AND users_data.sex = 1 THEN 1 END) as female_age_16'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 17 THEN 1 END) as age_17'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 17 AND users_data.sex = 1 THEN 1 END) as female_age_17'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 18 THEN 1 END) as age_18'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 18 AND users_data.sex = 1 THEN 1 END) as female_age_18'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 19 THEN 1 END) as age_19'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 19 AND users_data.sex = 1 THEN 1 END) as female_age_19'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 20 THEN 1 END) as age_20'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 20 AND users_data.sex = 1 THEN 1 END) as female_age_20'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 21 THEN 1 END) as age_21'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 21 AND users_data.sex = 1 THEN 1 END) as female_age_21'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 22 THEN 1 END) as age_22'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 22 AND users_data.sex = 1 THEN 1 END) as female_age_22'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 23 THEN 1 END) as age_23'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 23 AND users_data.sex = 1 THEN 1 END) as female_age_23'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 24 THEN 1 END) as age_24'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 24 AND users_data.sex = 1 THEN 1 END) as female_age_24'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 25 THEN 1 END) as age_25'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 25 AND users_data.sex = 1 THEN 1 END) as female_age_25'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 26 THEN 1 END) as age_26'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 26 AND users_data.sex = 1 THEN 1 END) as female_age_26'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 27 THEN 1 END) as age_27'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 27 AND users_data.sex = 1 THEN 1 END) as female_age_27'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 28 THEN 1 END) as age_28'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 28 AND users_data.sex = 1 THEN 1 END) as female_age_28'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 29 THEN 1 END) as age_29'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) = 29 AND users_data.sex = 1 THEN 1 END) as female_age_29'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 30 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 34 THEN 1 END) as age_30_34'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 30 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 34  AND users_data.sex = 1 THEN 1 END) as female_age_30_34'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 35 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 39 THEN 1 END) as age_35_39'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 35 AND TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) <= 39 AND users_data.sex = 1 THEN 1 END) as female_age_35_39'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 40 THEN 1 END) as age_max_40'),
                DB::raw('COUNT(CASE WHEN TRUNCATE(DATEDIFF(CURDATE(), users_data.birthday)/365, 0) >= 40 AND users_data.sex = 1 THEN 1 END) as female_age_max_40'),
                'specialities.level_education_id'
            )
            ->whereIn('groups.id', $groupId)
            ->groupBy('specialities.level_education_id')
            ->get()->toArray();

        $bachelor = $this->getDataByLevelEducation($data, 6);
        $master = $this->getDataByLevelEducation($data, 7);
        $college = $this->getDataByLevelEducation($data, 2);

        $results = array(['БАКАЛАВРИАТ' => $bachelor], ['МАГИСТРАТУРА' => $master], ['СРЕДНЕЕ ПРОФЕССИОНАЛЬНОЕ ОБРАЗОВАНИЕ' => $college]);

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offsetStart = 1;
        $offsetCurrent = 1;

        $sheet->setCellValue('A' . $offsetCurrent, 'КОНТИНГЕНТ ОБУЧАЮЩИХСЯ ЧОУ ВО ЮУ (ИУБиП)');
        $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);
        $offsetCurrent++;

        $sheet->setCellValue('A' . $offsetCurrent, 'на ' . Carbon::now()->format('d.m.Y') . ' г.');
        $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);
        $offsetCurrent++;

        foreach ($results as $key => $results) {
            foreach ($results as $key => $levelEducationData) {
                $sheet->setCellValue('A' . $offsetCurrent, $key);
                $sheet->mergeCells('A' . $offsetCurrent . ':D' . $offsetCurrent);

                //Выравнивание текста в ячейке
                $sheet->getStyle('A' . $offsetStart . ':D' . ($offsetCurrent))
                    ->applyFromArray(array('alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    ]));

                $sheet->getStyle('A' . $offsetStart . ':D' . $offsetCurrent)->getFont()->setBold(true);
                $offsetCurrent++;

                $sheet->setCellValue('A' . $offsetCurrent, 'Наименование показателей');
                $sheet->setCellValue('B' . $offsetCurrent, 'Принято (обучается)');
                $sheet->setCellValue('C' . $offsetCurrent, 'из них мужчин');
                $sheet->setCellValue('D' . $offsetCurrent, 'из них женщин');

                $sheet->getStyle('A' . $offsetCurrent . ':D' . $offsetCurrent)->getFont()->setBold(true);
                $offsetCurrent++;

                foreach ($levelEducationData as $key => $value) {
                    $sheet->setCellValue('A' . $offsetCurrent, 'Всего');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->student_count);
                    $sheet->setCellValue('C' . $offsetCurrent, $value->male_student_count);
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_student_count);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, 'В возрасте (число полных лет на 1 января следущего года) моложе 15 лем');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_min_15);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_min_15 - $value->female_age_min_15));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_min_15);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '15 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_15);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_15 - $value->female_age_15));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_15);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '16 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_16);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_16 - $value->female_age_16));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_16);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '17 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_17);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_17 - $value->female_age_17));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_17);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '18 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_18);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_18 - $value->female_age_18));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_18);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '19 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_19);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_19 - $value->female_age_19));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_19);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '20 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_20);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_20 - $value->female_age_20));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_20);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '21 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_21);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_21 - $value->female_age_21));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_21);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '22 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_22);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_22 - $value->female_age_22));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_22);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '23 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_23);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_23 - $value->female_age_23));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_23);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '24 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_24);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_24 - $value->female_age_24));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_24);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '25 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_25);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_25 - $value->female_age_25));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_25);;
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '26 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_26);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_26 - $value->female_age_26));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_26);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '27 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_27);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_27 - $value->female_age_27));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_27);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '28 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_28);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_28 - $value->female_age_28));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_28);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '29 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_29);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_29 - $value->female_age_29));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_29);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '30 - 34 года');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_30_34);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_30_34 - $value->female_age_30_34));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_30_34);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '35 - 39 лет');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_35_39);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_35_39 - $value->female_age_35_39));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_35_39);
                    $offsetCurrent++;

                    $sheet->setCellValue('A' . $offsetCurrent, '40 лет и старше');
                    $sheet->setCellValue('B' . $offsetCurrent, $value->age_max_40);
                    $sheet->setCellValue('C' . $offsetCurrent, ($value->age_max_40 - $value->female_age_max_40));
                    $sheet->setCellValue('D' . $offsetCurrent, $value->female_age_max_40);
                    $offsetCurrent++;
                }

                //Стиль для рамки
                $sheet->getStyle('A' . $offsetStart . ':D' . $offsetCurrent - 1)
                    ->getBorders()
                    ->getAllBorders()
                    ->applyFromArray(array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '808080')
                    ));

                $offsetStart = $offsetCurrent;
            }
        }

        $sheet->getStyle('A1:A' . $offsetCurrent)->getAlignment()->setWrapText(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('B1:D' . ($offsetCurrent - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);

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
