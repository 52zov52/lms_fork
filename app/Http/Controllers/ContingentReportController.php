<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContingentReportController extends Controller
{
    public $groupId = [
        2406, 2409, 2397, 2410, 2393, 2414, 2415, 2416, 2404, 2417, 2407, 2411, 2412, 2413, 2361, 2402, 2362, 2405, 2392, 2352, 2353,
        2408, 2426, 2424, 2425, 2401, 2368, 2350, 2351, 2365, 2403, 2355, 2356, 2357, 2358, 2345, 2298, 2348, 2282, 2283, 2346, 2292,
        2349, 2301, 2302, 2347, 2288, 2289, 2290, 2291, 2303, 2304, 2385, 2337, 2422, 2339, 2308, 2398, 2222, 2340, 2395, 2341, 2306,
        2399, 2218, 2342, 2281, 2420, 2220, 2221, 2421, 2396, 2343, 2310, 2400, 2423, 2216, 2344, 2329, 2338, 2309, 2245, 2257, 2369,
        2371, 2372, 2319, 2376, 2323, 2261, 2205, 2377, 2324, 2325, 2256, 2208, 2375, 2327, 2260, 2259, 2378, 2320, 2248, 2192, 2379,
        2380, 2321, 2322, 2373, 2374, 2326, 2446, 2382, 2383, 2384, 2389, 2385, 2331, 2269, 2386, 2387, 2388, 2332, 2334

    ];

    public $sumFirstCourse = 0, $sumSecondCourse = 0, $sumThirdCourse = 0, $sumFourthCourse = 0, $sumFiveCourse = 0;
    public $totalSumFirstCourse = 0, $totalSumSecondCourse = 0, $totalSumThirdCourse = 0, $totalSumFourthCourse = 0, $totalSumFiveCourse = 0;

    public function generateContingentReport()
    {
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
            ->whereIn('groups.id', $this->groupId)
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
