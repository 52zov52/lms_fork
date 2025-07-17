<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\AdmissionCampaign;
use App\Models\AdmissionCampaignStatsBachelor;
use App\Models\AdmissionCampaignStatsCollege;
use App\Models\AdmissionCampaignStatsMastersDegree;
use App\Models\AdmissionCampaignStatsPhD;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PrintAdmissionCampaignController extends Controller
{
    public function printToDayReport()
    {
        $activeAdmissionCampaign = AdmissionCampaign::where('is_active', 1)->select('id')->first();
        $activeAdmissionCampaignId = $activeAdmissionCampaign->id;

        $collegeCommerce = $this->dataTransformToCollege(1, $activeAdmissionCampaignId);
        $bachelorCommerce = $this->dataTransformToBachelor(1, $activeAdmissionCampaignId);
        $mastersDegreeCommerce = $this->dataTransformToMastersDegree(1, $activeAdmissionCampaignId);
        $phdCommerce = $this->dataTransformToPhD(1, $activeAdmissionCampaignId);

        $collegeBudget = $this->dataTransformToCollege(0, $activeAdmissionCampaignId);
        $bachelorBudget = $this->dataTransformToBachelor(0, $activeAdmissionCampaignId);
        $mastersDegreeBudget = $this->dataTransformToMastersDegree(0, $activeAdmissionCampaignId);
        $phdBudget = $this->dataTransformToPhD(0, $activeAdmissionCampaignId);

        $sumByLevelEducation = $this->getSum($activeAdmissionCampaignId, true);

        $collegeTotalApplication =  $sumByLevelEducation['college'][0]['total_applications_count'] ?? 0;
        $bachelorTotalApplication = $sumByLevelEducation['bachelor'][0]['total_applications_count'] ?? 0;
        $mastersDegreeTotalApplication = $sumByLevelEducation['mastersDegree'][0]['total_applications_count'] ?? 0;
        $phdTotalApplication = $sumByLevelEducation['phd'][0]['total_applications_count'] ?? 0;

        $collegeTotalContract =  $sumByLevelEducation['college'][0]['total_contracts_count'] ?? 0;
        $bachelorTotalContract = $sumByLevelEducation['bachelor'][0]['total_contracts_count'] ?? 0;
        $mastersDegreeTotalContract = $sumByLevelEducation['mastersDegree'][0]['total_contracts_count'] ?? 0;
        $phdTotalContract = $sumByLevelEducation['phd'][0]['total_contracts_count'] ?? 0;

        $collegeTotalPaidContract =  $sumByLevelEducation['college'][0]['total_paid_contracts_count'] ?? 0;
        $bachelorTotalPaidContract = $sumByLevelEducation['bachelor'][0]['total_paid_contracts_count'] ?? 0;
        $mastersDegreeTotalPaidContract = $sumByLevelEducation['mastersDegree'][0]['total_paid_contracts_count'] ?? 0;
        $phdTotalPaidContract = $sumByLevelEducation['phd'][0]['total_paid_contracts_count'] ?? 0;

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $offsetBoldFirst = 1;
        $offsetBorderFirst = 1;

        $offsetContentFirst = 1;
        $offsetContentLast = 1;

        $sheet->setCellValue('A' . $offset, 'ИТОГО НА ' . now()->format('d.m.Y'));
        $sheet->setCellValue('B' . $offset, 'СПО');
        $sheet->setCellValue('C' . $offset, 'Бакалавриат');
        $sheet->setCellValue('D' . $offset, 'Магистратура');
        $sheet->setCellValue('E' . $offset, 'Аспирантура');
        $sheet->setCellValue('F' . $offset, 'Итого');

        $sheet->getStyle('A' . $offset . ':F' . $offset)->getFont()->setBold(true);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Подано заявлений');
        $sheet->setCellValue('B' . $offset, $collegeTotalApplication);
        $sheet->setCellValue('C' . $offset, $bachelorTotalApplication);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalApplication);
        $sheet->setCellValue('E' . $offset, $phdTotalApplication);
        $sheet->setCellValue('F' . $offset, $collegeTotalApplication + $bachelorTotalApplication + $mastersDegreeTotalApplication + $phdTotalApplication);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Заключено договоров');
        $sheet->setCellValue('B' . $offset, $collegeTotalContract);
        $sheet->setCellValue('C' . $offset, $bachelorTotalContract);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalContract);
        $sheet->setCellValue('E' . $offset, $phdTotalContract);
        $sheet->setCellValue('F' . $offset, $collegeTotalContract + $bachelorTotalContract + $mastersDegreeTotalContract + $phdTotalContract);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Оплачено');
        $sheet->setCellValue('B' . $offset, $collegeTotalPaidContract);
        $sheet->setCellValue('C' . $offset, $bachelorTotalPaidContract);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalPaidContract);
        $sheet->setCellValue('E' . $offset, $phdTotalPaidContract);
        $sheet->setCellValue('F' . $offset, $collegeTotalPaidContract +
            $bachelorTotalPaidContract + $mastersDegreeTotalPaidContract +
            $phdTotalPaidContract);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Не оплачено');
        $sheet->setCellValue('B' . $offset, $collegeTotalContract - $collegeTotalPaidContract);
        $sheet->setCellValue('C' . $offset, $bachelorTotalContract - $bachelorTotalPaidContract);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalContract - $mastersDegreeTotalPaidContract);
        $sheet->setCellValue('E' . $offset, $phdTotalContract - $phdTotalPaidContract);
        $sheet->setCellValue('F' . $offset, ($collegeTotalContract + $bachelorTotalContract +
            $mastersDegreeTotalContract + $phdTotalContract) -
            ($collegeTotalPaidContract + $bachelorTotalPaidContract +
                $mastersDegreeTotalPaidContract + $phdTotalPaidContract));

        $offsetContentLast = $offset;

        $sheet->getStyle('A' . $offsetBoldFirst . ':A' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('F' . $offsetBoldFirst . ':F' . $offset)->getFont()->setBold(true);

        $sheet->getStyle('B' . $offsetContentFirst . ':F' . $offsetContentLast)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A' . $offsetBorderFirst . ':F' . $offset)
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        $offset += 2;

        $offsetBoldFirst = $offset;
        $offsetBorderFirst = $offset;

        //Колледж
        if (count($collegeCommerce) != 0 || count($collegeBudget) != 0) {
            $finalCollegeSum = array(
                'final_applications_after_nine_grades_count_ofo' => 0,
                'final_applications_after_eleventh_grades_count_ofo' => 0,
                'final_applications_after_eleventh_grades_count_zfo' => 0,
                'final_contracts_after_nine_grades_count_ofo' => 0,
                'final_contracts_after_eleventh_grades_count_ofo' => 0,
                'final_contracts_after_eleventh_grades_count_zfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0
            );

            $sheet->setCellValue('A' . $offset, 'ПРОГРАММЫ СПО');
            $sheet->mergeCells('A' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Специальность');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 2);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 2)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('B' . $offset . ':D' . $offset);

            $sheet->setCellValue('E' . $offset, 'Всего договоров на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('E' . $offset . ':G' . $offset);

            $sheet->setCellValue('H' . $offset, now()->format('d.m.Y'));
            $sheet->mergeCells('H' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('B' . $offset . ':C' . $offset);

            $sheet->setCellValue('D' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('E' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('E' . $offset . ':F' . $offset);

            $sheet->setCellValue('G' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('H' . $offset, 'Оплачено');
            $sheet->mergeCells('H' . $offset . ':H' . $offset + 1);

            $sheet->setCellValue('I' . $offset, 'Не оплачено');
            $sheet->mergeCells('I' . $offset . ':I' . $offset + 1);

            $sheet->getStyle('B' . $offset - 1 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('H' . $offset . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            $sheet->setCellValue('B' . $offset, 'после 9 класса');
            $sheet->setCellValue('C' . $offset, 'после 11 класса');
            $sheet->setCellValue('D' . $offset, 'после 11 класса');
            $sheet->setCellValue('E' . $offset, 'после 9 класса');
            $sheet->setCellValue('F' . $offset, 'после 11 класса');
            $sheet->setCellValue('G' . $offset, 'после 11 класса');

            $sheet->getStyle('B' . $offset . ':S' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':I' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($collegeCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($collegeCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_after_nine_grades_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['today_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('E' . $offset, $item[1]['today_contracts_after_nine_grades_count']);
                            $sheet->setCellValue('F' . $offset, $item[1]['today_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_after_nine_grades_count'] + $item[1]['today_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_nine_grades_count_ofo'] += $item[1]['today_applications_after_nine_grades_count'];
                            $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo'] += $item[1]['today_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_nine_grades_count_ofo'] += $item[1]['today_contracts_after_nine_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo'] += $item[1]['today_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[1]['today_contracts_after_nine_grades_count'] + $item[1]['today_contracts_after_eleventh_grades_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('D' . $offset, $item[2]['today_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('G' . $offset, $item[2]['today_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo'] += $item[2]['today_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo'] += $item[2]['today_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[2]['today_contracts_after_eleventh_grades_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($collegeBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($collegeBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_after_nine_grades_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['today_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('E' . $offset, $item[1]['today_contracts_after_nine_grades_count']);
                            $sheet->setCellValue('F' . $offset, $item[1]['today_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_after_nine_grades_count'] + $item[1]['today_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_nine_grades_count_ofo'] += $item[1]['today_applications_after_nine_grades_count'];
                            $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo'] += $item[1]['today_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_nine_grades_count_ofo'] += $item[1]['today_contracts_after_nine_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo'] += $item[1]['today_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[1]['today_contracts_after_nine_grades_count'] + $item[1]['today_contracts_after_eleventh_grades_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('D' . $offset, $item[2]['today_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('G' . $offset, $item[2]['today_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo'] += $item[2]['today_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo'] += $item[2]['today_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[2]['today_contracts_after_eleventh_grades_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalCollegeSum['final_applications_after_nine_grades_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo']);
            $sheet->setCellValue('D' . $offset, $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo']);
            $sheet->setCellValue('E' . $offset, $finalCollegeSum['final_contracts_after_nine_grades_count_ofo']);
            $sheet->setCellValue('F' . $offset, $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo']);
            $sheet->setCellValue('G' . $offset, $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo']);
            $sheet->setCellValue('H' . $offset, $finalCollegeSum['final_paid_contracts_count']);
            $sheet->setCellValue('I' . $offset, $finalCollegeSum['final_contracts_count'] - $finalCollegeSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalCollegeSum['final_applications_after_nine_grades_count_ofo'] +
                $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo'] +
                $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalCollegeSum['final_contracts_after_nine_grades_count_ofo'] +
                $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo'] +
                $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':I' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':I' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $offset += 1;

        //Бакалавриат
        if (count($bachelorCommerce) != 0 || count($bachelorBudget) != 0) {
            $offsetBoldFirst = $offset;
            $offsetBorderFirst = $offset;

            $finalBachelorSum = array(
                'final_applications_certificate_count_ofo' => 0,
                'final_applications_diploma_bachelor_count_ofo' => 0,
                'final_applications_diploma_college_count_ofo' => 0,
                'final_contracts_certificate_count_ofo' => 0,
                'final_contracts_diploma_bachelor_count_ofo' => 0,
                'final_contracts_diploma_college_count_ofo' => 0,
                'final_applications_certificate_count_zfo' => 0,
                'final_applications_diploma_bachelor_count_zfo' => 0,
                'final_applications_diploma_college_count_zfo' => 0,
                'final_contracts_certificate_count_zfo' => 0,
                'final_contracts_diploma_bachelor_count_zfo' => 0,
                'final_contracts_diploma_college_count_zfo' => 0,
                'final_applications_certificate_count_ozfo' => 0,
                'final_applications_diploma_bachelor_count_ozfo' => 0,
                'final_applications_diploma_college_count_ozfo' => 0,
                'final_contracts_certificate_count_ozfo' => 0,
                'final_contracts_diploma_bachelor_count_ozfo' => 0,
                'final_contracts_diploma_college_count_ozfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0,
            );

            $sheet->setCellValue('A' . $offset, 'БАКАЛАВРИАТ');
            $sheet->mergeCells('A' . $offset . ':U' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 2);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 2)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('B' . $offset . ':J' . $offset);

            $sheet->setCellValue('K' . $offset, 'Всего договоров на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('K' . $offset . ':S' . $offset);

            $sheet->setCellValue('T' . $offset, now()->format('d.m.Y'));
            $sheet->mergeCells('T' . $offset . ':U' . $offset);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('B' . $offset . ':D' . $offset);

            $sheet->setCellValue('E' . $offset, 'Заочная ф.о.');
            $sheet->mergeCells('E' . $offset . ':G' . $offset);

            $sheet->setCellValue('H' . $offset, 'Очно-заочная ф.о.');
            $sheet->mergeCells('H' . $offset . ':J' . $offset);

            $sheet->setCellValue('K' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('K' . $offset . ':M' . $offset);

            $sheet->setCellValue('N' . $offset, 'Заочная ф.о.');
            $sheet->mergeCells('N' . $offset . ':P' . $offset);

            $sheet->setCellValue('Q' . $offset, 'Очно-заочная ф.о.');
            $sheet->mergeCells('Q' . $offset . ':S' . $offset);

            $sheet->setCellValue('T' . $offset, 'Оплачено');
            $sheet->mergeCells('T' . $offset . ':T' . $offset + 1);

            $sheet->setCellValue('U' . $offset, 'Не оплачено');
            $sheet->mergeCells('U' . $offset . ':U' . $offset + 1);

            $sheet->getStyle('B' . $offset - 1 . ':U' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('T' . $offset . ':U' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            $sheet->setCellValue('B' . $offset, 'На базе аттестата');
            $sheet->setCellValue('C' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('D' . $offset, 'Диплом СПО');

            $sheet->setCellValue('E' . $offset, 'На базе аттестата');
            $sheet->setCellValue('F' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('G' . $offset, 'Диплом СПО');

            $sheet->setCellValue('H' . $offset, 'На базе аттестата');
            $sheet->setCellValue('I' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('J' . $offset, 'Диплом СПО');

            $sheet->setCellValue('K' . $offset, 'На базе аттестата');
            $sheet->setCellValue('L' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('M' . $offset, 'Диплом СПО');

            $sheet->setCellValue('N' . $offset, 'На базе аттестата');
            $sheet->setCellValue('O' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('P' . $offset, 'Диплом СПО');

            $sheet->setCellValue('Q' . $offset, 'На базе аттестата');
            $sheet->setCellValue('R' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('S' . $offset, 'Диплом СПО');

            $sheet->getStyle('B' . $offset . ':S' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':U' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($bachelorCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':U' . $offset);

                $sheet->getStyle('A' . $offset . ':U' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($bachelorCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_certificate_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['today_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['today_applications_diploma_college_count']);

                            $sheet->setCellValue('K' . $offset, $item[1]['today_contracts_certificate_count']);
                            $sheet->setCellValue('L' . $offset, $item[1]['today_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('M' . $offset, $item[1]['today_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_certificate_count'] + $item[1]['today_contracts_diploma_bachelor_count'] + $item[1]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ofo'] += $item[1]['today_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ofo'] += $item[1]['today_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ofo'] += $item[1]['today_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ofo'] += $item[1]['today_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo'] += $item[1]['today_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ofo'] += $item[1]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[1]['today_contracts_certificate_count'] + $item[1]['today_contracts_diploma_bachelor_count'] + $item[1]['today_contracts_diploma_college_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('E' . $offset, $item[2]['today_applications_certificate_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['today_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('G' . $offset, $item[2]['today_applications_diploma_college_count']);

                            $sheet->setCellValue('N' . $offset, $item[2]['today_contracts_certificate_count']);
                            $sheet->setCellValue('O' . $offset, $item[2]['today_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('P' . $offset, $item[2]['today_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_certificate_count'] + $item[2]['today_contracts_diploma_bachelor_count'] + $item[2]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_zfo'] += $item[2]['today_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_zfo'] += $item[2]['today_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_zfo'] += $item[2]['today_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_zfo'] += $item[2]['today_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo'] += $item[2]['today_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_zfo'] += $item[2]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[2]['today_contracts_certificate_count'] + $item[2]['today_contracts_diploma_bachelor_count'] + $item[2]['today_contracts_diploma_college_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('H' . $offset, $item[6]['today_applications_certificate_count']);
                            $sheet->setCellValue('I' . $offset, $item[6]['today_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('J' . $offset, $item[6]['today_applications_diploma_college_count']);

                            $sheet->setCellValue('Q' . $offset, $item[6]['today_contracts_certificate_count']);
                            $sheet->setCellValue('R' . $offset, $item[6]['today_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('S' . $offset, $item[6]['today_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[6]['today_paid_contracts_count'];
                            $totalContractsCount += $item[6]['today_contracts_certificate_count'] + $item[6]['today_contracts_diploma_bachelor_count'] + $item[6]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ozfo'] += $item[6]['today_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo'] += $item[6]['today_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ozfo'] += $item[6]['today_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ozfo'] += $item[6]['today_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo'] += $item[6]['today_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ozfo'] += $item[6]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[6]['today_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[6]['today_contracts_certificate_count'] + $item[6]['today_contracts_diploma_bachelor_count'] + $item[6]['today_contracts_diploma_college_count'];
                        }
                    }

                    $sheet->setCellValue('T' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('U' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':U' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($bachelorBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':U' . $offset);

                $sheet->getStyle('A' . $offset . ':U' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($bachelorBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_certificate_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['today_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['today_applications_diploma_college_count']);

                            $sheet->setCellValue('K' . $offset, $item[1]['today_contracts_certificate_count']);
                            $sheet->setCellValue('L' . $offset, $item[1]['today_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('M' . $offset, $item[1]['today_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_certificate_count'] + $item[1]['today_contracts_diploma_bachelor_count'] + $item[1]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ofo'] += $item[1]['today_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ofo'] += $item[1]['today_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ofo'] += $item[1]['today_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ofo'] += $item[1]['today_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo'] += $item[1]['today_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ofo'] += $item[1]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[1]['today_contracts_certificate_count'] + $item[1]['today_contracts_diploma_bachelor_count'] + $item[1]['today_contracts_diploma_college_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('E' . $offset, $item[2]['today_applications_certificate_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['today_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('G' . $offset, $item[2]['today_applications_diploma_college_count']);

                            $sheet->setCellValue('N' . $offset, $item[2]['today_contracts_certificate_count']);
                            $sheet->setCellValue('O' . $offset, $item[2]['today_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('P' . $offset, $item[2]['today_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_certificate_count'] + $item[2]['today_contracts_diploma_bachelor_count'] + $item[2]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_zfo'] += $item[2]['today_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_zfo'] += $item[2]['today_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_zfo'] += $item[2]['today_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_zfo'] += $item[2]['today_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo'] += $item[2]['today_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_zfo'] += $item[2]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[2]['today_contracts_certificate_count'] + $item[2]['today_contracts_diploma_bachelor_count'] + $item[2]['today_contracts_diploma_college_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('H' . $offset, $item[6]['today_applications_certificate_count']);
                            $sheet->setCellValue('I' . $offset, $item[6]['today_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('J' . $offset, $item[6]['today_applications_diploma_college_count']);

                            $sheet->setCellValue('Q' . $offset, $item[6]['today_contracts_certificate_count']);
                            $sheet->setCellValue('R' . $offset, $item[6]['today_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('S' . $offset, $item[6]['today_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[6]['today_paid_contracts_count'];
                            $totalContractsCount += $item[6]['today_contracts_certificate_count'] + $item[6]['today_contracts_diploma_bachelor_count'] + $item[6]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ozfo'] += $item[6]['today_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo'] += $item[6]['today_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ozfo'] += $item[6]['today_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ozfo'] += $item[6]['today_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo'] += $item[6]['today_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ozfo'] += $item[6]['today_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[6]['today_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[6]['today_contracts_certificate_count'] + $item[6]['today_contracts_diploma_bachelor_count'] + $item[6]['today_contracts_diploma_college_count'];
                        }
                    }

                    $sheet->setCellValue('T' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('U' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':U' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalBachelorSum['final_applications_certificate_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalBachelorSum['final_applications_diploma_bachelor_count_ofo']);
            $sheet->setCellValue('D' . $offset, $finalBachelorSum['final_applications_diploma_college_count_ofo']);
            $sheet->setCellValue('E' . $offset, $finalBachelorSum['final_applications_certificate_count_zfo']);
            $sheet->setCellValue('F' . $offset, $finalBachelorSum['final_applications_diploma_bachelor_count_zfo']);
            $sheet->setCellValue('G' . $offset, $finalBachelorSum['final_applications_diploma_college_count_zfo']);
            $sheet->setCellValue('H' . $offset, $finalBachelorSum['final_applications_certificate_count_ozfo']);
            $sheet->setCellValue('I' . $offset, $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo']);
            $sheet->setCellValue('J' . $offset, $finalBachelorSum['final_applications_diploma_college_count_ozfo']);
            $sheet->setCellValue('K' . $offset, $finalBachelorSum['final_contracts_certificate_count_ofo']);
            $sheet->setCellValue('L' . $offset, $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo']);
            $sheet->setCellValue('M' . $offset, $finalBachelorSum['final_contracts_diploma_college_count_ofo']);
            $sheet->setCellValue('N' . $offset, $finalBachelorSum['final_contracts_certificate_count_zfo']);
            $sheet->setCellValue('O' . $offset, $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo']);
            $sheet->setCellValue('P' . $offset, $finalBachelorSum['final_contracts_diploma_college_count_zfo']);
            $sheet->setCellValue('Q' . $offset, $finalBachelorSum['final_contracts_certificate_count_ozfo']);
            $sheet->setCellValue('R' . $offset, $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo']);
            $sheet->setCellValue('S' . $offset, $finalBachelorSum['final_contracts_diploma_college_count_ozfo']);
            $sheet->setCellValue('T' . $offset, $finalBachelorSum['final_paid_contracts_count']);
            $sheet->setCellValue('U' . $offset, $finalBachelorSum['final_contracts_count'] - $finalBachelorSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalBachelorSum['final_applications_certificate_count_ofo'] +
                $finalBachelorSum['final_applications_diploma_bachelor_count_ofo'] +
                $finalBachelorSum['final_applications_diploma_college_count_ofo'] +
                $finalBachelorSum['final_applications_certificate_count_zfo'] +
                $finalBachelorSum['final_applications_diploma_bachelor_count_zfo'] +
                $finalBachelorSum['final_applications_diploma_college_count_zfo'] +
                $finalBachelorSum['final_applications_certificate_count_ozfo'] +
                $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo'] +
                $finalBachelorSum['final_applications_diploma_college_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':U' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalBachelorSum['final_contracts_certificate_count_ofo'] +
                $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo'] +
                $finalBachelorSum['final_contracts_diploma_college_count_ofo'] +
                $finalBachelorSum['final_contracts_certificate_count_zfo'] +
                $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo'] +
                $finalBachelorSum['final_contracts_diploma_college_count_zfo'] +
                $finalBachelorSum['final_contracts_certificate_count_ozfo'] +
                $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo'] +
                $finalBachelorSum['final_contracts_diploma_college_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':U' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':U' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':U' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':U' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $offset += 1;

        //Магистратура
        if (count($mastersDegreeCommerce) != 0 || count($mastersDegreeBudget) != 0) {
            $offsetBoldFirst = $offset;
            $offsetBorderFirst = $offset;

            $finalMastersDegreeSum = array(
                'final_applications_count_ofo' => 0,
                'final_contracts_count_ofo' => 0,
                'final_applications_count_zfo' => 0,
                'final_contracts_count_zfo' => 0,
                'final_applications_count_ozfo' => 0,
                'final_contracts_count_ozfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0,
            );

            $sheet->setCellValue('A' . $offset, 'МАГИСТРАТУРА');
            $sheet->mergeCells('A' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 1);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 1)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('B' . $offset . ':D' . $offset);

            $sheet->setCellValue('E' . $offset, 'Всего договоров на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('E' . $offset . ':G' . $offset);

            $sheet->setCellValue('H' . $offset, now()->format('d.m.Y'));
            $sheet->mergeCells('H' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('C' . $offset, 'Заочная ф.о.');
            $sheet->setCellValue('D' . $offset, 'Очно-заочная ф.о.');

            $sheet->setCellValue('E' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('F' . $offset, 'Заочная ф.о.');
            $sheet->setCellValue('G' . $offset, 'Очно-заочная ф.о.');

            $sheet->setCellValue('H' . $offset, 'Оплачено');
            $sheet->mergeCells('H' . $offset . ':H' . $offset);

            $sheet->setCellValue('I' . $offset, 'Не оплачено');
            $sheet->mergeCells('I' . $offset . ':I' . $offset);

            $sheet->getStyle('B' . $offset - 1 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('B' . $offset . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':I' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($mastersDegreeCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($mastersDegreeCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[1]['today_contracts_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ofo'] += $item[1]['today_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ofo'] += $item[1]['today_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[1]['today_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['today_applications_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['today_contracts_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_zfo'] += $item[2]['today_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_zfo'] += $item[2]['today_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[2]['today_contracts_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('D' . $offset, $item[6]['today_applications_count']);
                            $sheet->setCellValue('G' . $offset, $item[6]['today_contracts_count']);

                            $totalPaidContractsCount += $item[6]['today_paid_contracts_count'];
                            $totalContractsCount += $item[6]['today_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ozfo'] += $item[6]['today_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ozfo'] += $item[6]['today_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[6]['today_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[6]['today_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($mastersDegreeBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($mastersDegreeBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[1]['today_contracts_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ofo'] += $item[1]['today_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ofo'] += $item[1]['today_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[1]['today_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['today_applications_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['today_contracts_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_zfo'] += $item[2]['today_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_zfo'] += $item[2]['today_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[2]['today_contracts_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('D' . $offset, $item[6]['today_applications_count']);
                            $sheet->setCellValue('G' . $offset, $item[6]['today_contracts_count']);

                            $totalPaidContractsCount += $item[6]['today_paid_contracts_count'];
                            $totalContractsCount += $item[6]['today_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ozfo'] += $item[6]['today_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ozfo'] += $item[6]['today_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[6]['today_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[6]['today_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalMastersDegreeSum['final_applications_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalMastersDegreeSum['final_applications_count_zfo']);
            $sheet->setCellValue('D' . $offset, $finalMastersDegreeSum['final_applications_count_ozfo']);
            $sheet->setCellValue('E' . $offset, $finalMastersDegreeSum['final_contracts_count_ofo']);
            $sheet->setCellValue('F' . $offset, $finalMastersDegreeSum['final_contracts_count_zfo']);
            $sheet->setCellValue('G' . $offset, $finalMastersDegreeSum['final_contracts_count_ozfo']);
            $sheet->setCellValue('H' . $offset, $finalMastersDegreeSum['final_paid_contracts_count']);
            $sheet->setCellValue('I' . $offset, $finalMastersDegreeSum['final_contracts_count'] - $finalMastersDegreeSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalMastersDegreeSum['final_applications_count_ofo'] +
                $finalMastersDegreeSum['final_applications_count_zfo'] +
                $finalMastersDegreeSum['final_applications_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalMastersDegreeSum['final_contracts_count_ofo'] +
                $finalMastersDegreeSum['final_contracts_count_zfo'] +
                $finalMastersDegreeSum['final_contracts_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':I' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':I' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $offset += 1;

        //Аспирантура
        if (count($phdCommerce) != 0 || count($phdBudget) != 0) {
            $offsetBoldFirst = $offset;
            $offsetBorderFirst = $offset;

            $finalPhDSum = array(
                'final_applications_count_ofo' => 0,
                'final_contracts_count_ofo' => 0,
                'final_applications_count_zfo' => 0,
                'final_contracts_count_zfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0,
            );

            $sheet->setCellValue('A' . $offset, 'АСПИРАНТУРА');
            $sheet->mergeCells('A' . $offset . ':G' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 1);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 1)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('B' . $offset . ':C' . $offset);

            $sheet->setCellValue('D' . $offset, 'Всего договоров на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('D' . $offset . ':E' . $offset);

            $sheet->getStyle('B' . $offset . ':D' . $offset)->getAlignment()->setWrapText(true);

            $sheet->setCellValue('F' . $offset, now()->format('d.m.Y'));
            $sheet->mergeCells('F' . $offset . ':G' . $offset);

            $sheet->getRowDimension($offset)->setRowHeight(25);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('C' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('D' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('E' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('F' . $offset, 'Оплачено');
            $sheet->mergeCells('F' . $offset . ':F' . $offset);

            $sheet->setCellValue('G' . $offset, 'Не оплачено');
            $sheet->mergeCells('G' . $offset . ':G' . $offset);

            $sheet->getStyle('B' . $offset - 1 . ':G' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('B' . $offset . ':G' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':G' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($phdCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':G' . $offset);

                $sheet->getStyle('A' . $offset . ':G' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($phdCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['today_contracts_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_count'];

                            $finalPhDSum['final_applications_count_ofo'] += $item[1]['today_applications_count'];
                            $finalPhDSum['final_contracts_count_ofo'] += $item[1]['today_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[1]['today_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['today_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[2]['today_contracts_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_count'];

                            $finalPhDSum['final_applications_count_zfo'] += $item[2]['today_applications_count'];
                            $finalPhDSum['final_contracts_count_zfo'] += $item[2]['today_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[2]['today_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('F' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('G' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':G' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($phdBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':G' . $offset);

                $sheet->getStyle('A' . $offset . ':G' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($phdBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['today_applications_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['today_contracts_count']);

                            $totalPaidContractsCount += $item[1]['today_paid_contracts_count'];
                            $totalContractsCount += $item[1]['today_contracts_count'];

                            $finalPhDSum['final_applications_count_ofo'] += $item[1]['today_applications_count'];
                            $finalPhDSum['final_contracts_count_ofo'] += $item[1]['today_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[1]['today_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[1]['today_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['today_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[2]['today_contracts_count']);

                            $totalPaidContractsCount += $item[2]['today_paid_contracts_count'];
                            $totalContractsCount += $item[2]['today_contracts_count'];

                            $finalPhDSum['final_applications_count_zfo'] += $item[2]['today_applications_count'];
                            $finalPhDSum['final_contracts_count_zfo'] += $item[2]['today_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[2]['today_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[2]['today_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('F' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('G' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':G' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalPhDSum['final_applications_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalPhDSum['final_applications_count_zfo']);
            $sheet->setCellValue('D' . $offset, $finalPhDSum['final_contracts_count_ofo']);
            $sheet->setCellValue('E' . $offset, $finalPhDSum['final_contracts_count_zfo']);
            $sheet->setCellValue('F' . $offset, $finalPhDSum['final_paid_contracts_count']);
            $sheet->setCellValue('G' . $offset, $finalPhDSum['final_contracts_count'] - $finalPhDSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalPhDSum['final_applications_count_ofo'] + $finalPhDSum['final_applications_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':G' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalPhDSum['final_contracts_count_ofo'] + $finalPhDSum['final_contracts_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':G' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':G' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':G' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':G' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $sheet->getStyle('A1:U' . $offset)->applyFromArray([
            'font' => [
                'size' => 9,
            ],
        ]);

        $sheet->getStyle('A1:A' . $offset)->getAlignment()->setWrapText(true);

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(7);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(7);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(6);
        $sheet->getColumnDimension('I')->setWidth(6);
        $sheet->getColumnDimension('J')->setWidth(6);
        $sheet->getColumnDimension('K')->setWidth(5);
        $sheet->getColumnDimension('L')->setWidth(5);
        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(5);
        $sheet->getColumnDimension('O')->setWidth(5);
        $sheet->getColumnDimension('P')->setWidth(5);
        $sheet->getColumnDimension('Q')->setWidth(6);
        $sheet->getColumnDimension('R')->setWidth(6);
        $sheet->getColumnDimension('S')->setWidth(6);
        $sheet->getColumnDimension('T')->setWidth(8);
        $sheet->getColumnDimension('U')->setWidth(8);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    public function printFullReport($id)
    {
        if ($id == 0) {
            $activeAdmissionCampaign = AdmissionCampaign::where('is_active', 1)->select('id')->first();
            $activeAdmissionCampaignId = $activeAdmissionCampaign->id;

            $applicationPeriodStartDate = Carbon::parse(AdmissionCampaign::where('is_active', '=', true)
                ->select('application_period_start_date')
                ->first()->application_period_start_date)->format('d.m.Y');
        } else {
            $activeAdmissionCampaign = AdmissionCampaign::where('id', $id)->select('id')->first();
            $activeAdmissionCampaignId = $activeAdmissionCampaign->id;

            $applicationPeriodStartDate = Carbon::parse(AdmissionCampaign::where('id', $id)
                ->select('application_period_start_date')
                ->first()->application_period_start_date)->format('d.m.Y');
        }

        $collegeCommerce = $this->dataTransformToCollege(1, $activeAdmissionCampaignId);
        $bachelorCommerce = $this->dataTransformToBachelor(1, $activeAdmissionCampaignId);
        $mastersDegreeCommerce = $this->dataTransformToMastersDegree(1, $activeAdmissionCampaignId);
        $phdCommerce = $this->dataTransformToPhD(1, $activeAdmissionCampaignId);

        $collegeBudget = $this->dataTransformToCollege(0, $activeAdmissionCampaignId);
        $bachelorBudget = $this->dataTransformToBachelor(0, $activeAdmissionCampaignId);
        $mastersDegreeBudget = $this->dataTransformToMastersDegree(0, $activeAdmissionCampaignId);
        $phdBudget = $this->dataTransformToPhD(0, $activeAdmissionCampaignId);

        $sumByLevelEducation = $this->getSum($activeAdmissionCampaignId);

        $collegeTotalApplication =  $sumByLevelEducation['college'][0]['total_applications_count'] ?? 0;
        $bachelorTotalApplication = $sumByLevelEducation['bachelor'][0]['total_applications_count'] ?? 0;
        $mastersDegreeTotalApplication = $sumByLevelEducation['mastersDegree'][0]['total_applications_count'] ?? 0;
        $phdTotalApplication = $sumByLevelEducation['phd'][0]['total_applications_count'] ?? 0;

        $collegeTotalContract =  $sumByLevelEducation['college'][0]['total_contracts_count'] ?? 0;
        $bachelorTotalContract = $sumByLevelEducation['bachelor'][0]['total_contracts_count'] ?? 0;
        $mastersDegreeTotalContract = $sumByLevelEducation['mastersDegree'][0]['total_contracts_count'] ?? 0;
        $phdTotalContract = $sumByLevelEducation['phd'][0]['total_contracts_count'] ?? 0;

        $collegeTotalPaidContract =  $sumByLevelEducation['college'][0]['total_paid_contracts_count'] ?? 0;
        $bachelorTotalPaidContract = $sumByLevelEducation['bachelor'][0]['total_paid_contracts_count'] ?? 0;
        $mastersDegreeTotalPaidContract = $sumByLevelEducation['mastersDegree'][0]['total_paid_contracts_count'] ?? 0;
        $phdTotalPaidContract = $sumByLevelEducation['phd'][0]['total_paid_contracts_count'] ?? 0;

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $offsetBoldFirst = 1;
        $offsetBorderFirst = 1;

        $offsetContentFirst = 1;
        $offsetContentLast = 1;

        $studyYear = Carbon::parse($applicationPeriodStartDate)->format('Y') . '/' . Carbon::parse($applicationPeriodStartDate)->addYear()->format('Y');

        $sheet->setCellValue('A' . $offset, 'ИТОГО НА ' . $studyYear);
        $sheet->setCellValue('B' . $offset, 'СПО');
        $sheet->setCellValue('C' . $offset, 'Бакалавриат');
        $sheet->setCellValue('D' . $offset, 'Магистратура');
        $sheet->setCellValue('E' . $offset, 'Аспирантура');
        $sheet->setCellValue('F' . $offset, 'Итого');

        $sheet->getStyle('A' . $offset . ':F' . $offset)->getFont()->setBold(true);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Подано заявлений');
        $sheet->setCellValue('B' . $offset, $collegeTotalApplication);
        $sheet->setCellValue('C' . $offset, $bachelorTotalApplication);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalApplication);
        $sheet->setCellValue('E' . $offset, $phdTotalApplication);
        $sheet->setCellValue('F' . $offset, $collegeTotalApplication + $bachelorTotalApplication + $mastersDegreeTotalApplication + $phdTotalApplication);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Заключено договоров');
        $sheet->setCellValue('B' . $offset, $collegeTotalContract);
        $sheet->setCellValue('C' . $offset, $bachelorTotalContract);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalContract);
        $sheet->setCellValue('E' . $offset, $phdTotalContract);
        $sheet->setCellValue('F' . $offset, $collegeTotalContract +
            $bachelorTotalContract + $mastersDegreeTotalContract +
            $phdTotalContract);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Оплачено');
        $sheet->setCellValue('B' . $offset, $collegeTotalPaidContract);
        $sheet->setCellValue('C' . $offset, $bachelorTotalPaidContract);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalPaidContract);
        $sheet->setCellValue('E' . $offset, $phdTotalPaidContract);
        $sheet->setCellValue('F' . $offset, $collegeTotalPaidContract +
            $bachelorTotalPaidContract + $mastersDegreeTotalPaidContract +
            $phdTotalPaidContract);

        $offset++;

        $sheet->setCellValue('A' . $offset, 'Не оплачено');
        $sheet->setCellValue('B' . $offset, $collegeTotalContract - $collegeTotalPaidContract);
        $sheet->setCellValue('C' . $offset, $bachelorTotalContract - $bachelorTotalPaidContract);
        $sheet->setCellValue('D' . $offset, $mastersDegreeTotalContract - $mastersDegreeTotalPaidContract);
        $sheet->setCellValue('E' . $offset, $phdTotalContract - $phdTotalPaidContract);
        $sheet->setCellValue('F' . $offset, ($collegeTotalContract + $bachelorTotalContract +
            $mastersDegreeTotalContract + $phdTotalContract) -
            ($collegeTotalPaidContract + $bachelorTotalPaidContract +
                $mastersDegreeTotalPaidContract + $phdTotalPaidContract));

        $offsetContentLast = $offset;

        $sheet->getStyle('A' . $offsetBoldFirst . ':A' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('F' . $offsetBoldFirst . ':F' . $offset)->getFont()->setBold(true);

        $sheet->getStyle('B' . $offsetContentFirst . ':F' . $offsetContentLast)
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A' . $offsetBorderFirst . ':F' . $offset)
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        $offset += 2;

        $offsetBoldFirst = $offset;
        $offsetBorderFirst = $offset;

        //Колледж
        if (count($collegeCommerce) != 0 || count($collegeBudget) != 0) {
            $finalCollegeSum = array(
                'final_applications_after_nine_grades_count_ofo' => 0,
                'final_applications_after_eleventh_grades_count_ofo' => 0,
                'final_applications_after_eleventh_grades_count_zfo' => 0,
                'final_contracts_after_nine_grades_count_ofo' => 0,
                'final_contracts_after_eleventh_grades_count_ofo' => 0,
                'final_contracts_after_eleventh_grades_count_zfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0
            );

            $sheet->setCellValue('A' . $offset, 'ПРОГРАММЫ СПО');
            $sheet->mergeCells('A' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Специальность');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 2);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 2)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('B' . $offset . ':D' . $offset);

            $sheet->setCellValue('E' . $offset, 'Всего договоров с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('E' . $offset . ':G' . $offset);

            $sheet->setCellValue('H' . $offset, $studyYear);
            $sheet->mergeCells('H' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('B' . $offset . ':C' . $offset);

            $sheet->setCellValue('D' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('E' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('E' . $offset . ':F' . $offset);

            $sheet->setCellValue('G' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('H' . $offset, 'Оплачено');
            $sheet->mergeCells('H' . $offset . ':H' . $offset + 1);

            $sheet->setCellValue('I' . $offset, 'Не оплачено');
            $sheet->mergeCells('I' . $offset . ':I' . $offset + 1);

            $sheet->getStyle('B' . $offset - 1 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('H' . $offset . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            $sheet->setCellValue('B' . $offset, 'после 9 класса');
            $sheet->setCellValue('C' . $offset, 'после 11 класса');
            $sheet->setCellValue('D' . $offset, 'после 11 класса');
            $sheet->setCellValue('E' . $offset, 'после 9 класса');
            $sheet->setCellValue('F' . $offset, 'после 11 класса');
            $sheet->setCellValue('G' . $offset, 'после 11 класса');

            $sheet->getStyle('B' . $offset . ':S' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':I' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($collegeCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($collegeCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_after_nine_grades_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['total_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('E' . $offset, $item[1]['total_contracts_after_nine_grades_count']);
                            $sheet->setCellValue('F' . $offset, $item[1]['total_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_after_nine_grades_count'] + $item[1]['total_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_nine_grades_count_ofo'] += $item[1]['total_applications_after_nine_grades_count'];
                            $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo'] += $item[1]['total_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_nine_grades_count_ofo'] += $item[1]['total_contracts_after_nine_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo'] += $item[1]['total_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[1]['total_contracts_after_nine_grades_count'] + $item[1]['total_contracts_after_eleventh_grades_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('D' . $offset, $item[2]['total_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('G' . $offset, $item[2]['total_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo'] += $item[2]['total_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo'] += $item[2]['total_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[2]['total_contracts_after_eleventh_grades_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($collegeBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($collegeBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_after_nine_grades_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['total_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('E' . $offset, $item[1]['total_contracts_after_nine_grades_count']);
                            $sheet->setCellValue('F' . $offset, $item[1]['total_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_after_nine_grades_count'] + $item[1]['total_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_nine_grades_count_ofo'] += $item[1]['total_applications_after_nine_grades_count'];
                            $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo'] += $item[1]['total_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_nine_grades_count_ofo'] += $item[1]['total_contracts_after_nine_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo'] += $item[1]['total_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[1]['total_contracts_after_nine_grades_count'] + $item[1]['total_contracts_after_eleventh_grades_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('D' . $offset, $item[2]['total_applications_after_eleventh_grades_count']);

                            $sheet->setCellValue('G' . $offset, $item[2]['total_contracts_after_eleventh_grades_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_after_eleventh_grades_count'];

                            $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo'] += $item[2]['total_applications_after_eleventh_grades_count'];
                            $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo'] += $item[2]['total_contracts_after_eleventh_grades_count'];
                            $finalCollegeSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalCollegeSum['final_contracts_count'] += $item[2]['total_contracts_after_eleventh_grades_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalCollegeSum['final_applications_after_nine_grades_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo']);
            $sheet->setCellValue('D' . $offset, $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo']);
            $sheet->setCellValue('E' . $offset, $finalCollegeSum['final_contracts_after_nine_grades_count_ofo']);
            $sheet->setCellValue('F' . $offset, $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo']);
            $sheet->setCellValue('G' . $offset, $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo']);
            $sheet->setCellValue('H' . $offset, $finalCollegeSum['final_paid_contracts_count']);
            $sheet->setCellValue('I' . $offset, $finalCollegeSum['final_contracts_count'] - $finalCollegeSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalCollegeSum['final_applications_after_nine_grades_count_ofo'] +
                $finalCollegeSum['final_applications_after_eleventh_grades_count_ofo'] +
                $finalCollegeSum['final_applications_after_eleventh_grades_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalCollegeSum['final_contracts_after_nine_grades_count_ofo'] +
                $finalCollegeSum['final_contracts_after_eleventh_grades_count_ofo'] +
                $finalCollegeSum['final_contracts_after_eleventh_grades_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':I' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':I' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $offset += 1;

        //Бакалавриат
        if (count($bachelorCommerce) != 0 || count($bachelorBudget) != 0) {
            $offsetBoldFirst = $offset;
            $offsetBorderFirst = $offset;

            $finalBachelorSum = array(
                'final_applications_certificate_count_ofo' => 0,
                'final_applications_diploma_bachelor_count_ofo' => 0,
                'final_applications_diploma_college_count_ofo' => 0,
                'final_contracts_certificate_count_ofo' => 0,
                'final_contracts_diploma_bachelor_count_ofo' => 0,
                'final_contracts_diploma_college_count_ofo' => 0,
                'final_applications_certificate_count_zfo' => 0,
                'final_applications_diploma_bachelor_count_zfo' => 0,
                'final_applications_diploma_college_count_zfo' => 0,
                'final_contracts_certificate_count_zfo' => 0,
                'final_contracts_diploma_bachelor_count_zfo' => 0,
                'final_contracts_diploma_college_count_zfo' => 0,
                'final_applications_certificate_count_ozfo' => 0,
                'final_applications_diploma_bachelor_count_ozfo' => 0,
                'final_applications_diploma_college_count_ozfo' => 0,
                'final_contracts_certificate_count_ozfo' => 0,
                'final_contracts_diploma_bachelor_count_ozfo' => 0,
                'final_contracts_diploma_college_count_ozfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0,
            );

            $sheet->setCellValue('A' . $offset, 'БАКАЛАВРИАТ');
            $sheet->mergeCells('A' . $offset . ':U' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 2);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 2)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('B' . $offset . ':J' . $offset);

            $sheet->setCellValue('K' . $offset, 'Всего договоров с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('K' . $offset . ':S' . $offset);

            $sheet->setCellValue('T' . $offset, $studyYear);
            $sheet->mergeCells('T' . $offset . ':U' . $offset);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('B' . $offset . ':D' . $offset);

            $sheet->setCellValue('E' . $offset, 'Заочная ф.о.');
            $sheet->mergeCells('E' . $offset . ':G' . $offset);

            $sheet->setCellValue('H' . $offset, 'Очно-заочная ф.о.');
            $sheet->mergeCells('H' . $offset . ':J' . $offset);

            $sheet->setCellValue('K' . $offset, 'Очная ф.о.');
            $sheet->mergeCells('K' . $offset . ':M' . $offset);

            $sheet->setCellValue('N' . $offset, 'Заочная ф.о.');
            $sheet->mergeCells('N' . $offset . ':P' . $offset);

            $sheet->setCellValue('Q' . $offset, 'Очно-заочная ф.о.');
            $sheet->mergeCells('Q' . $offset . ':S' . $offset);

            $sheet->setCellValue('T' . $offset, 'Оплачено');
            $sheet->mergeCells('T' . $offset . ':T' . $offset + 1);

            $sheet->setCellValue('U' . $offset, 'Не оплачено');
            $sheet->mergeCells('U' . $offset . ':U' . $offset + 1);

            $sheet->getStyle('B' . $offset - 1 . ':U' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('T' . $offset . ':U' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            $sheet->setCellValue('B' . $offset, 'На базе аттестата');
            $sheet->setCellValue('C' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('D' . $offset, 'Диплом СПО');

            $sheet->setCellValue('E' . $offset, 'На базе аттестата');
            $sheet->setCellValue('F' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('G' . $offset, 'Диплом СПО');

            $sheet->setCellValue('H' . $offset, 'На базе аттестата');
            $sheet->setCellValue('I' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('J' . $offset, 'Диплом СПО');

            $sheet->setCellValue('K' . $offset, 'На базе аттестата');
            $sheet->setCellValue('L' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('M' . $offset, 'Диплом СПО');

            $sheet->setCellValue('N' . $offset, 'На базе аттестата');
            $sheet->setCellValue('O' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('P' . $offset, 'Диплом СПО');

            $sheet->setCellValue('Q' . $offset, 'На базе аттестата');
            $sheet->setCellValue('R' . $offset, 'Диплом ЮУ');
            $sheet->setCellValue('S' . $offset, 'Диплом СПО');

            $sheet->getStyle('B' . $offset . ':S' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':U' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($bachelorCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':U' . $offset);

                $sheet->getStyle('A' . $offset . ':U' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($bachelorCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_certificate_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['total_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['total_applications_diploma_college_count']);

                            $sheet->setCellValue('K' . $offset, $item[1]['total_contracts_certificate_count']);
                            $sheet->setCellValue('L' . $offset, $item[1]['total_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('M' . $offset, $item[1]['total_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_certificate_count'] + $item[1]['total_contracts_diploma_bachelor_count'] + $item[1]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ofo'] += $item[1]['total_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ofo'] += $item[1]['total_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ofo'] += $item[1]['total_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ofo'] += $item[1]['total_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo'] += $item[1]['total_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ofo'] += $item[1]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[1]['total_contracts_certificate_count'] + $item[1]['total_contracts_diploma_bachelor_count'] + $item[1]['total_contracts_diploma_college_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('E' . $offset, $item[2]['total_applications_certificate_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['total_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('G' . $offset, $item[2]['total_applications_diploma_college_count']);

                            $sheet->setCellValue('N' . $offset, $item[2]['total_contracts_certificate_count']);
                            $sheet->setCellValue('O' . $offset, $item[2]['total_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('P' . $offset, $item[2]['total_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_certificate_count'] + $item[2]['total_contracts_diploma_bachelor_count'] + $item[2]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_zfo'] += $item[2]['total_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_zfo'] += $item[2]['total_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_zfo'] += $item[2]['total_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_zfo'] += $item[2]['total_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo'] += $item[2]['total_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_zfo'] += $item[2]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[2]['total_contracts_certificate_count'] + $item[2]['total_contracts_diploma_bachelor_count'] + $item[2]['total_contracts_diploma_college_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('H' . $offset, $item[6]['total_applications_certificate_count']);
                            $sheet->setCellValue('I' . $offset, $item[6]['total_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('J' . $offset, $item[6]['total_applications_diploma_college_count']);

                            $sheet->setCellValue('Q' . $offset, $item[6]['total_contracts_certificate_count']);
                            $sheet->setCellValue('R' . $offset, $item[6]['total_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('S' . $offset, $item[6]['total_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[6]['total_paid_contracts_count'];
                            $totalContractsCount += $item[6]['total_contracts_certificate_count'] + $item[6]['total_contracts_diploma_bachelor_count'] + $item[6]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ozfo'] += $item[6]['total_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo'] += $item[6]['total_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ozfo'] += $item[6]['total_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ozfo'] += $item[6]['total_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo'] += $item[6]['total_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ozfo'] += $item[6]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[6]['total_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[6]['total_contracts_certificate_count'] + $item[6]['total_contracts_diploma_bachelor_count'] + $item[6]['total_contracts_diploma_college_count'];
                        }
                    }

                    $sheet->setCellValue('T' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('U' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':U' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($bachelorBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':U' . $offset);

                $sheet->getStyle('A' . $offset . ':U' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($bachelorBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_certificate_count']);
                            $sheet->setCellValue('C' . $offset, $item[1]['total_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['total_applications_diploma_college_count']);

                            $sheet->setCellValue('K' . $offset, $item[1]['total_contracts_certificate_count']);
                            $sheet->setCellValue('L' . $offset, $item[1]['total_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('M' . $offset, $item[1]['total_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_certificate_count'] + $item[1]['total_contracts_diploma_bachelor_count'] + $item[1]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ofo'] += $item[1]['total_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ofo'] += $item[1]['total_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ofo'] += $item[1]['total_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ofo'] += $item[1]['total_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo'] += $item[1]['total_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ofo'] += $item[1]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[1]['total_contracts_certificate_count'] + $item[1]['total_contracts_diploma_bachelor_count'] + $item[1]['total_contracts_diploma_college_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('E' . $offset, $item[2]['total_applications_certificate_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['total_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('G' . $offset, $item[2]['total_applications_diploma_college_count']);

                            $sheet->setCellValue('N' . $offset, $item[2]['total_contracts_certificate_count']);
                            $sheet->setCellValue('O' . $offset, $item[2]['total_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('P' . $offset, $item[2]['total_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_certificate_count'] + $item[2]['total_contracts_diploma_bachelor_count'] + $item[2]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_zfo'] += $item[2]['total_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_zfo'] += $item[2]['total_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_zfo'] += $item[2]['total_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_zfo'] += $item[2]['total_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo'] += $item[2]['total_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_zfo'] += $item[2]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[2]['total_contracts_certificate_count'] + $item[2]['total_contracts_diploma_bachelor_count'] + $item[2]['total_contracts_diploma_college_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('H' . $offset, $item[6]['total_applications_certificate_count']);
                            $sheet->setCellValue('I' . $offset, $item[6]['total_applications_diploma_bachelor_count']);
                            $sheet->setCellValue('J' . $offset, $item[6]['total_applications_diploma_college_count']);

                            $sheet->setCellValue('Q' . $offset, $item[6]['total_contracts_certificate_count']);
                            $sheet->setCellValue('R' . $offset, $item[6]['total_contracts_diploma_bachelor_count']);
                            $sheet->setCellValue('S' . $offset, $item[6]['total_contracts_diploma_college_count']);

                            $totalPaidContractsCount += $item[6]['total_paid_contracts_count'];
                            $totalContractsCount += $item[6]['total_contracts_certificate_count'] + $item[6]['total_contracts_diploma_bachelor_count'] + $item[6]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_applications_certificate_count_ozfo'] += $item[6]['total_applications_certificate_count'];
                            $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo'] += $item[6]['total_applications_diploma_bachelor_count'];
                            $finalBachelorSum['final_applications_diploma_college_count_ozfo'] += $item[6]['total_applications_diploma_college_count'];

                            $finalBachelorSum['final_contracts_certificate_count_ozfo'] += $item[6]['total_contracts_certificate_count'];
                            $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo'] += $item[6]['total_contracts_diploma_bachelor_count'];
                            $finalBachelorSum['final_contracts_diploma_college_count_ozfo'] += $item[6]['total_contracts_diploma_college_count'];

                            $finalBachelorSum['final_paid_contracts_count'] += $item[6]['total_paid_contracts_count'];
                            $finalBachelorSum['final_contracts_count'] += $item[6]['total_contracts_certificate_count'] + $item[6]['total_contracts_diploma_bachelor_count'] + $item[6]['total_contracts_diploma_college_count'];
                        }
                    }

                    $sheet->setCellValue('T' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('U' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':U' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalBachelorSum['final_applications_certificate_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalBachelorSum['final_applications_diploma_bachelor_count_ofo']);
            $sheet->setCellValue('D' . $offset, $finalBachelorSum['final_applications_diploma_college_count_ofo']);
            $sheet->setCellValue('E' . $offset, $finalBachelorSum['final_applications_certificate_count_zfo']);
            $sheet->setCellValue('F' . $offset, $finalBachelorSum['final_applications_diploma_bachelor_count_zfo']);
            $sheet->setCellValue('G' . $offset, $finalBachelorSum['final_applications_diploma_college_count_zfo']);
            $sheet->setCellValue('H' . $offset, $finalBachelorSum['final_applications_certificate_count_ozfo']);
            $sheet->setCellValue('I' . $offset, $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo']);
            $sheet->setCellValue('J' . $offset, $finalBachelorSum['final_applications_diploma_college_count_ozfo']);
            $sheet->setCellValue('K' . $offset, $finalBachelorSum['final_contracts_certificate_count_ofo']);
            $sheet->setCellValue('L' . $offset, $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo']);
            $sheet->setCellValue('M' . $offset, $finalBachelorSum['final_contracts_diploma_college_count_ofo']);
            $sheet->setCellValue('N' . $offset, $finalBachelorSum['final_contracts_certificate_count_zfo']);
            $sheet->setCellValue('O' . $offset, $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo']);
            $sheet->setCellValue('P' . $offset, $finalBachelorSum['final_contracts_diploma_college_count_zfo']);
            $sheet->setCellValue('Q' . $offset, $finalBachelorSum['final_contracts_certificate_count_ozfo']);
            $sheet->setCellValue('R' . $offset, $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo']);
            $sheet->setCellValue('S' . $offset, $finalBachelorSum['final_contracts_diploma_college_count_ozfo']);
            $sheet->setCellValue('T' . $offset, $finalBachelorSum['final_paid_contracts_count']);
            $sheet->setCellValue('U' . $offset, $finalBachelorSum['final_contracts_count'] - $finalBachelorSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalBachelorSum['final_applications_certificate_count_ofo'] +
                $finalBachelorSum['final_applications_diploma_bachelor_count_ofo'] +
                $finalBachelorSum['final_applications_diploma_college_count_ofo'] +
                $finalBachelorSum['final_applications_certificate_count_zfo'] +
                $finalBachelorSum['final_applications_diploma_bachelor_count_zfo'] +
                $finalBachelorSum['final_applications_diploma_college_count_zfo'] +
                $finalBachelorSum['final_applications_certificate_count_ozfo'] +
                $finalBachelorSum['final_applications_diploma_bachelor_count_ozfo'] +
                $finalBachelorSum['final_applications_diploma_college_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':U' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalBachelorSum['final_contracts_certificate_count_ofo'] +
                $finalBachelorSum['final_contracts_diploma_bachelor_count_ofo'] +
                $finalBachelorSum['final_contracts_diploma_college_count_ofo'] +
                $finalBachelorSum['final_contracts_certificate_count_zfo'] +
                $finalBachelorSum['final_contracts_diploma_bachelor_count_zfo'] +
                $finalBachelorSum['final_contracts_diploma_college_count_zfo'] +
                $finalBachelorSum['final_contracts_certificate_count_ozfo'] +
                $finalBachelorSum['final_contracts_diploma_bachelor_count_ozfo'] +
                $finalBachelorSum['final_contracts_diploma_college_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':U' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':U' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':U' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':U' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $offset += 1;

        //Магистратура
        if (count($mastersDegreeCommerce) != 0 || count($mastersDegreeBudget) != 0) {
            $offsetBoldFirst = $offset;
            $offsetBorderFirst = $offset;

            $finalMastersDegreeSum = array(
                'final_applications_count_ofo' => 0,
                'final_contracts_count_ofo' => 0,
                'final_applications_count_zfo' => 0,
                'final_contracts_count_zfo' => 0,
                'final_applications_count_ozfo' => 0,
                'final_contracts_count_ozfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0,
            );

            $sheet->setCellValue('A' . $offset, 'МАГИСТРАТУРА');
            $sheet->mergeCells('A' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 1);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 1)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('B' . $offset . ':D' . $offset);

            $sheet->setCellValue('E' . $offset, 'Всего договоров с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('E' . $offset . ':G' . $offset);

            $sheet->setCellValue('H' . $offset, $studyYear);
            $sheet->mergeCells('H' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('C' . $offset, 'Заочная ф.о.');
            $sheet->setCellValue('D' . $offset, 'Очно-заочная ф.о.');

            $sheet->setCellValue('E' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('F' . $offset, 'Заочная ф.о.');
            $sheet->setCellValue('G' . $offset, 'Очно-заочная ф.о.');

            $sheet->setCellValue('H' . $offset, 'Оплачено');
            $sheet->mergeCells('H' . $offset . ':H' . $offset);

            $sheet->setCellValue('I' . $offset, 'Не оплачено');
            $sheet->mergeCells('I' . $offset . ':I' . $offset);

            $sheet->getStyle('B' . $offset - 1 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('B' . $offset . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':I' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($mastersDegreeCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($mastersDegreeCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[1]['total_contracts_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ofo'] += $item[1]['total_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ofo'] += $item[1]['total_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[1]['total_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['total_applications_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['total_contracts_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_zfo'] += $item[2]['total_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_zfo'] += $item[2]['total_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[2]['total_contracts_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('D' . $offset, $item[6]['total_applications_count']);
                            $sheet->setCellValue('G' . $offset, $item[6]['total_contracts_count']);

                            $totalPaidContractsCount += $item[6]['total_paid_contracts_count'];
                            $totalContractsCount += $item[6]['total_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ozfo'] += $item[6]['total_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ozfo'] += $item[6]['total_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[6]['total_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[6]['total_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($mastersDegreeBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':I' . $offset);

                $sheet->getStyle('A' . $offset . ':I' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($mastersDegreeBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[1]['total_contracts_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ofo'] += $item[1]['total_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ofo'] += $item[1]['total_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[1]['total_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['total_applications_count']);
                            $sheet->setCellValue('F' . $offset, $item[2]['total_contracts_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_zfo'] += $item[2]['total_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_zfo'] += $item[2]['total_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[2]['total_contracts_count'];
                        }

                        //ОЗФО
                        if (isset($item[6])) {
                            $sheet->setCellValue('D' . $offset, $item[6]['total_applications_count']);
                            $sheet->setCellValue('G' . $offset, $item[6]['total_contracts_count']);

                            $totalPaidContractsCount += $item[6]['total_paid_contracts_count'];
                            $totalContractsCount += $item[6]['total_contracts_count'];

                            $finalMastersDegreeSum['final_applications_count_ozfo'] += $item[6]['total_applications_count'];
                            $finalMastersDegreeSum['final_contracts_count_ozfo'] += $item[6]['total_contracts_count'];

                            $finalMastersDegreeSum['final_paid_contracts_count'] += $item[6]['total_paid_contracts_count'];
                            $finalMastersDegreeSum['final_contracts_count'] += $item[6]['total_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('H' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('I' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':I' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalMastersDegreeSum['final_applications_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalMastersDegreeSum['final_applications_count_zfo']);
            $sheet->setCellValue('D' . $offset, $finalMastersDegreeSum['final_applications_count_ozfo']);
            $sheet->setCellValue('E' . $offset, $finalMastersDegreeSum['final_contracts_count_ofo']);
            $sheet->setCellValue('F' . $offset, $finalMastersDegreeSum['final_contracts_count_zfo']);
            $sheet->setCellValue('G' . $offset, $finalMastersDegreeSum['final_contracts_count_ozfo']);
            $sheet->setCellValue('H' . $offset, $finalMastersDegreeSum['final_paid_contracts_count']);
            $sheet->setCellValue('I' . $offset, $finalMastersDegreeSum['final_contracts_count'] - $finalMastersDegreeSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalMastersDegreeSum['final_applications_count_ofo'] +
                $finalMastersDegreeSum['final_applications_count_zfo'] +
                $finalMastersDegreeSum['final_applications_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalMastersDegreeSum['final_contracts_count_ofo'] +
                $finalMastersDegreeSum['final_contracts_count_zfo'] +
                $finalMastersDegreeSum['final_contracts_count_ozfo']);
            $sheet->mergeCells('B' . $offset . ':I' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':I' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':I' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':I' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $offset += 1;

        //Аспирантура
        if (count($phdCommerce) != 0 || count($phdBudget) != 0) {
            $offsetBoldFirst = $offset;
            $offsetBorderFirst = $offset;

            $finalPhDSum = array(
                'final_applications_count_ofo' => 0,
                'final_contracts_count_ofo' => 0,
                'final_applications_count_zfo' => 0,
                'final_contracts_count_zfo' => 0,
                'final_paid_contracts_count' => 0,
                'final_contracts_count' => 0,
            );

            $sheet->setCellValue('A' . $offset, 'АСПИРАНТУРА');
            $sheet->mergeCells('A' . $offset . ':G' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A' . $offset . ':A' . $offset + 1);

            //Выравнивание текста в ячейке
            $sheet->getStyle('A' . $offset . ':A' . $offset + 1)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));

            $sheet->setCellValue('B' . $offset, 'Всего заявлений с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('B' . $offset . ':C' . $offset);

            $sheet->setCellValue('D' . $offset, 'Всего договоров с ' . $applicationPeriodStartDate);
            $sheet->mergeCells('D' . $offset . ':E' . $offset);

            $sheet->getStyle('B' . $offset . ':D' . $offset)->getAlignment()->setWrapText(true);

            $sheet->setCellValue('F' . $offset, $studyYear);
            $sheet->mergeCells('F' . $offset . ':G' . $offset);

            $sheet->getRowDimension($offset)->setRowHeight(25);

            $offset++;

            $sheet->setCellValue('B' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('C' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('D' . $offset, 'Очная ф.о.');
            $sheet->setCellValue('E' . $offset, 'Заочная ф.о.');

            $sheet->setCellValue('F' . $offset, 'Оплачено');
            $sheet->mergeCells('F' . $offset . ':F' . $offset);

            $sheet->setCellValue('G' . $offset, 'Не оплачено');
            $sheet->mergeCells('G' . $offset . ':G' . $offset);

            $sheet->getStyle('B' . $offset - 1 . ':G' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('B' . $offset . ':G' . $offset)
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $sheet->getStyle('A' . $offsetBoldFirst . ':G' . $offset)->getFont()->setBold(true);

            $offset++;

            if (count($phdCommerce) != 0) {
                $sheet->setCellValue('A' . $offset, 'По договорам с оплатой стоимости обучения');
                $sheet->mergeCells('A' . $offset . ':G' . $offset);

                $sheet->getStyle('A' . $offset . ':G' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($phdCommerce as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['total_contracts_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_count'];

                            $finalPhDSum['final_applications_count_ofo'] += $item[1]['total_applications_count'];
                            $finalPhDSum['final_contracts_count_ofo'] += $item[1]['total_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[1]['total_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['total_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[2]['total_contracts_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_count'];

                            $finalPhDSum['final_applications_count_zfo'] += $item[2]['total_applications_count'];
                            $finalPhDSum['final_contracts_count_zfo'] += $item[2]['total_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[2]['total_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('F' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('G' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':G' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            if (count($phdBudget) != 0) {
                $sheet->setCellValue('A' . $offset, 'Бюджетные места');
                $sheet->mergeCells('A' . $offset . ':G' . $offset);

                $sheet->getStyle('A' . $offset . ':G' . $offset)->getFont()->setBold(true);

                $offset++;
                $offsetContentFirst = $offset;

                foreach ($phdBudget as $key => $value) {
                    $totalPaidContractsCount = 0;
                    $totalContractsCount = 0;

                    $sheet->setCellValue('A' . $offset, $key);

                    foreach ($value as $item) {
                        //ОФО
                        if (isset($item[1])) {
                            $sheet->setCellValue('B' . $offset, $item[1]['total_applications_count']);
                            $sheet->setCellValue('D' . $offset, $item[1]['total_contracts_count']);

                            $totalPaidContractsCount += $item[1]['total_paid_contracts_count'];
                            $totalContractsCount += $item[1]['total_contracts_count'];

                            $finalPhDSum['final_applications_count_ofo'] += $item[1]['total_applications_count'];
                            $finalPhDSum['final_contracts_count_ofo'] += $item[1]['total_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[1]['total_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[1]['total_contracts_count'];
                        }

                        //ЗФО
                        if (isset($item[2])) {
                            $sheet->setCellValue('C' . $offset, $item[2]['total_applications_count']);
                            $sheet->setCellValue('E' . $offset, $item[2]['total_contracts_count']);

                            $totalPaidContractsCount += $item[2]['total_paid_contracts_count'];
                            $totalContractsCount += $item[2]['total_contracts_count'];

                            $finalPhDSum['final_applications_count_zfo'] += $item[2]['total_applications_count'];
                            $finalPhDSum['final_contracts_count_zfo'] += $item[2]['total_contracts_count'];

                            $finalPhDSum['final_paid_contracts_count'] += $item[2]['total_paid_contracts_count'];
                            $finalPhDSum['final_contracts_count'] += $item[2]['total_contracts_count'];
                        }
                    }

                    $sheet->setCellValue('F' . $offset, $totalPaidContractsCount);
                    $sheet->setCellValue('G' . $offset, $totalContractsCount - $totalPaidContractsCount);

                    $offset++;
                    $offsetContentLast = $offset;

                    $sheet->getStyle('B' . $offsetContentFirst . ':G' . $offsetContentLast)
                        ->applyFromArray(array('alignment' => [
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]));
                }
            }

            $sheet->setCellValue('A' . $offset, 'Итого');
            $sheet->setCellValue('B' . $offset, $finalPhDSum['final_applications_count_ofo']);
            $sheet->setCellValue('C' . $offset, $finalPhDSum['final_applications_count_zfo']);
            $sheet->setCellValue('D' . $offset, $finalPhDSum['final_contracts_count_ofo']);
            $sheet->setCellValue('E' . $offset, $finalPhDSum['final_contracts_count_zfo']);
            $sheet->setCellValue('F' . $offset, $finalPhDSum['final_paid_contracts_count']);
            $sheet->setCellValue('G' . $offset, $finalPhDSum['final_contracts_count'] - $finalPhDSum['final_paid_contracts_count']);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого заявлений');
            $sheet->setCellValue('B' . $offset, $finalPhDSum['final_applications_count_ofo'] + $finalPhDSum['final_applications_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':G' . $offset);

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Итого договоров');
            $sheet->setCellValue('B' . $offset, $finalPhDSum['final_contracts_count_ofo'] + $finalPhDSum['final_contracts_count_zfo']);
            $sheet->mergeCells('B' . $offset . ':G' . $offset);

            $sheet->getStyle('A' . $offset - 2 . ':G' . $offset)->getFont()->setBold(true);

            $sheet->getStyle('B' . $offset - 2 . ':G' . $offset)
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            $offset++;

            //Стиль для рамки
            $sheet->getStyle('A' . $offsetBorderFirst . ':G' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));
        }

        $sheet->getStyle('A1:U' . $offset)->applyFromArray([
            'font' => [
                'size' => 9,
            ],
        ]);

        $sheet->getStyle('A1:A' . $offset)->getAlignment()->setWrapText(true);

        //Устанавливаем ширину полей
        $sheet->getColumnDimension('A')->setWidth(45);
        $sheet->getColumnDimension('B')->setWidth(7);
        $sheet->getColumnDimension('C')->setWidth(12);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(12);
        $sheet->getColumnDimension('F')->setWidth(7);
        $sheet->getColumnDimension('G')->setWidth(10);
        $sheet->getColumnDimension('H')->setWidth(6);
        $sheet->getColumnDimension('I')->setWidth(6);
        $sheet->getColumnDimension('J')->setWidth(6);
        $sheet->getColumnDimension('K')->setWidth(5);
        $sheet->getColumnDimension('L')->setWidth(5);
        $sheet->getColumnDimension('M')->setWidth(5);
        $sheet->getColumnDimension('N')->setWidth(5);
        $sheet->getColumnDimension('O')->setWidth(5);
        $sheet->getColumnDimension('P')->setWidth(5);
        $sheet->getColumnDimension('Q')->setWidth(6);
        $sheet->getColumnDimension('R')->setWidth(6);
        $sheet->getColumnDimension('S')->setWidth(6);
        $sheet->getColumnDimension('T')->setWidth(8);
        $sheet->getColumnDimension('U')->setWidth(8);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    private function dataTransformToCollege($fundingType, $activeAdmissionCampaignId)
    {
        $today = Carbon::now()->format('Y-m-d');

        $data = AdmissionCampaignStatsCollege::join('admission_campaign_directions', 'admission_campaign_stats_colleges.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_colleges.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 2)
            ->where('admission_campaign_directions.funding_type', '=', $fundingType)
            ->select(
                DB::raw("CONCAT(admission_campaign_directions.speciality_code, ' ',
                admission_campaign_directions.speciality_name) as direction"),
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id',
                DB::raw('SUM(CASE WHEN admission_campaign_stats_colleges.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_colleges.applications_after_nine_grades_count ELSE 0 END) as today_applications_after_nine_grades_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_colleges.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_colleges.applications_after_eleventh_grades_count ELSE 0 END) as today_applications_after_eleventh_grades_count'),
                DB::raw('SUM(admission_campaign_stats_colleges.applications_after_nine_grades_count) as total_applications_after_nine_grades_count'),
                DB::raw('SUM(admission_campaign_stats_colleges.applications_after_eleventh_grades_count) as total_applications_after_eleventh_grades_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_colleges.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_colleges.contracts_after_nine_grades_count ELSE 0 END) as today_contracts_after_nine_grades_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_colleges.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_colleges.contracts_after_eleventh_grades_count ELSE 0 END) as today_contracts_after_eleventh_grades_count'),
                DB::raw('SUM(admission_campaign_stats_colleges.contracts_after_nine_grades_count) as total_contracts_after_nine_grades_count'),
                DB::raw('SUM(admission_campaign_stats_colleges.contracts_after_eleventh_grades_count) as total_contracts_after_eleventh_grades_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_colleges.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_colleges.paid_contracts_count ELSE 0 END) as today_paid_contracts_count'),
                DB::raw('SUM(admission_campaign_stats_colleges.paid_contracts_count) as total_paid_contracts_count'),
            )
            ->groupBy(
                'admission_campaign_directions.speciality_code',
                'admission_campaign_directions.speciality_name',
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id'
            )
            ->get()->toArray();

        $result = collect($data)->groupBy('direction')->map(function ($items) {
            return collect($items)->map(function ($item) {
                return [
                    $item['teaching_form_id'] => [
                        'today_applications_after_nine_grades_count' => $item['today_applications_after_nine_grades_count'],
                        'today_applications_after_eleventh_grades_count' => $item['today_applications_after_eleventh_grades_count'],
                        'today_contracts_after_nine_grades_count' => $item['today_contracts_after_nine_grades_count'],
                        'today_contracts_after_eleventh_grades_count' => $item['today_contracts_after_eleventh_grades_count'],
                        'total_applications_after_nine_grades_count' => $item['total_applications_after_nine_grades_count'],
                        'total_applications_after_eleventh_grades_count' => $item['total_applications_after_eleventh_grades_count'],
                        'total_contracts_after_nine_grades_count' => $item['total_contracts_after_nine_grades_count'],
                        'total_contracts_after_eleventh_grades_count' => $item['total_contracts_after_eleventh_grades_count'],
                        'today_paid_contracts_count' => $item['today_paid_contracts_count'],
                        'total_paid_contracts_count' => $item['total_paid_contracts_count']
                    ]
                ];
            })->all();
        })->toArray();

        return $result;
    }

    private function dataTransformToBachelor($fundingType, $activeAdmissionCampaignId)
    {
        $today = Carbon::now()->format('Y-m-d');

        $data = AdmissionCampaignStatsBachelor::join('admission_campaign_directions', 'admission_campaign_stats_bachelors.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_bachelors.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 6)
            ->where('admission_campaign_directions.funding_type', '=', $fundingType)
            ->select(
                DB::raw("CONCAT(admission_campaign_directions.speciality_code, ' ',
                admission_campaign_directions.speciality_name, ' ',admission_campaign_directions.speciality_profile) as direction"),
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id',
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.applications_certificate_count ELSE 0 END) as today_applications_certificate_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.applications_diploma_bachelor_count ELSE 0 END) as today_applications_diploma_bachelor_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.applications_diploma_college_count ELSE 0 END) as today_applications_diploma_college_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.applications_certificate_count) as total_applications_certificate_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.applications_diploma_bachelor_count) as total_applications_diploma_bachelor_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.applications_diploma_college_count) as total_applications_diploma_college_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.contracts_certificate_count ELSE 0 END) as today_contracts_certificate_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.contracts_diploma_bachelor_count ELSE 0 END) as today_contracts_diploma_bachelor_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.contracts_diploma_college_count ELSE 0 END) as today_contracts_diploma_college_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.contracts_certificate_count) as total_contracts_certificate_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.contracts_diploma_bachelor_count) as total_contracts_diploma_bachelor_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.contracts_diploma_college_count) as total_contracts_diploma_college_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_bachelors.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_bachelors.paid_contracts_count ELSE 0 END) as today_paid_contracts_count'),
                DB::raw('SUM(admission_campaign_stats_bachelors.paid_contracts_count) as total_paid_contracts_count'),
            )
            ->groupBy(
                'admission_campaign_directions.speciality_code',
                'admission_campaign_directions.speciality_name',
                'admission_campaign_directions.speciality_profile',
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id'
            )
            ->get()->toArray();

        $result = collect($data)->groupBy('direction')->map(function ($items) {
            return collect($items)->map(function ($item) {
                return [
                    $item['teaching_form_id'] => [
                        'today_applications_certificate_count' => $item['today_applications_certificate_count'],
                        'today_applications_diploma_bachelor_count' => $item['today_applications_diploma_bachelor_count'],
                        'today_applications_diploma_college_count' => $item['today_applications_diploma_college_count'],
                        'total_applications_certificate_count' => $item['total_applications_certificate_count'],
                        'total_applications_diploma_bachelor_count' => $item['total_applications_diploma_bachelor_count'],
                        'total_applications_diploma_college_count' => $item['total_applications_diploma_college_count'],
                        'today_contracts_certificate_count' => $item['today_contracts_certificate_count'],
                        'today_contracts_diploma_bachelor_count' => $item['today_contracts_diploma_bachelor_count'],
                        'today_contracts_diploma_college_count' => $item['today_contracts_diploma_college_count'],
                        'total_contracts_certificate_count' => $item['total_contracts_certificate_count'],
                        'total_contracts_diploma_bachelor_count' => $item['total_contracts_diploma_bachelor_count'],
                        'total_contracts_diploma_college_count' => $item['total_contracts_diploma_college_count'],
                        'today_paid_contracts_count' => $item['today_paid_contracts_count'],
                        'total_paid_contracts_count' => $item['total_paid_contracts_count']
                    ]
                ];
            })->all();
        })->toArray();

        return $result;
    }

    private function dataTransformToMastersDegree($fundingType, $activeAdmissionCampaignId)
    {
        $today = Carbon::now()->format('Y-m-d');

        $data = AdmissionCampaignStatsMastersDegree::join('admission_campaign_directions', 'admission_campaign_stats_masters_degrees.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_masters_degrees.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 7)
            ->where('admission_campaign_directions.funding_type', '=', $fundingType)
            ->select(
                DB::raw("CONCAT(admission_campaign_directions.speciality_code, ' ',
                admission_campaign_directions.speciality_name, ' ',admission_campaign_directions.speciality_profile) as direction"),
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id',
                DB::raw('SUM(CASE WHEN admission_campaign_stats_masters_degrees.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_masters_degrees.applications_count ELSE 0 END) as today_applications_count'),
                DB::raw('SUM(admission_campaign_stats_masters_degrees.applications_count) as total_applications_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_masters_degrees.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_masters_degrees.contracts_count ELSE 0 END) as today_contracts_count'),
                DB::raw('SUM(admission_campaign_stats_masters_degrees.contracts_count) as total_contracts_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_masters_degrees.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_masters_degrees.paid_contracts_count ELSE 0 END) as today_paid_contracts_count'),
                DB::raw('SUM(admission_campaign_stats_masters_degrees.paid_contracts_count) as total_paid_contracts_count'),
            )
            ->groupBy(
                'admission_campaign_directions.speciality_code',
                'admission_campaign_directions.speciality_name',
                'admission_campaign_directions.speciality_profile',
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id'
            )
            ->get()->toArray();

        $result = collect($data)->groupBy('direction')->map(function ($items) {
            return collect($items)->map(function ($item) {
                return [
                    $item['teaching_form_id'] => [
                        'today_applications_count' => $item['today_applications_count'],
                        'total_applications_count' => $item['total_applications_count'],
                        'today_contracts_count' => $item['today_contracts_count'],
                        'total_contracts_count' => $item['total_contracts_count'],
                        'today_paid_contracts_count' => $item['today_paid_contracts_count'],
                        'total_paid_contracts_count' => $item['total_paid_contracts_count']
                    ]
                ];
            })->all();
        })->toArray();

        return $result;
    }

    private function dataTransformToPhD($fundingType, $activeAdmissionCampaignId)
    {
        $today = Carbon::now()->format('Y-m-d');

        $data = AdmissionCampaignStatsPhD::join('admission_campaign_directions', 'admission_campaign_stats_phd.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_phd.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 4)
            ->where('admission_campaign_directions.funding_type', '=', $fundingType)
            ->select(
                DB::raw("CONCAT(admission_campaign_directions.speciality_code, ' ',
                admission_campaign_directions.speciality_name) as direction"),
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id',
                DB::raw('SUM(CASE WHEN admission_campaign_stats_phd.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_phd.applications_count ELSE 0 END) as today_applications_count'),
                DB::raw('SUM(admission_campaign_stats_phd.applications_count) as total_applications_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_phd.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_phd.contracts_count ELSE 0 END) as today_contracts_count'),
                DB::raw('SUM(admission_campaign_stats_phd.contracts_count) as total_contracts_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats_phd.created_at LIKE "' . $today . '%" THEN admission_campaign_stats_phd.paid_contracts_count ELSE 0 END) as today_paid_contracts_count'),
                DB::raw('SUM(admission_campaign_stats_phd.paid_contracts_count) as total_paid_contracts_count'),
            )
            ->groupBy(
                'admission_campaign_directions.speciality_code',
                'admission_campaign_directions.speciality_name',
                'admission_campaign_directions.speciality_profile',
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id'
            )
            ->get()->toArray();

        $result = collect($data)->groupBy('direction')->map(function ($items) {
            return collect($items)->map(function ($item) {
                return [
                    $item['teaching_form_id'] => [
                        'today_applications_count' => $item['today_applications_count'],
                        'total_applications_count' => $item['total_applications_count'],
                        'today_contracts_count' => $item['today_contracts_count'],
                        'total_contracts_count' => $item['total_contracts_count'],
                        'today_paid_contracts_count' => $item['today_paid_contracts_count'],
                        'total_paid_contracts_count' => $item['total_paid_contracts_count']
                    ]
                ];
            })->all();
        })->toArray();

        return $result;
    }

    private function getSum($activeAdmissionCampaignId, $isToDay = false)
    {
        $college = AdmissionCampaignStatsCollege::join('admission_campaign_directions', 'admission_campaign_stats_colleges.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_colleges.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 2);

        if ($isToDay) {
            $college->where('admission_campaign_stats_colleges.created_at', 'like', now()->format('Y-m-d') . '%');
        }

        $college = $college->select(
            DB::raw('SUM(admission_campaign_stats_colleges.applications_after_nine_grades_count) + 
                    SUM(admission_campaign_stats_colleges.applications_after_eleventh_grades_count) as total_applications_count'),
            DB::raw('SUM(admission_campaign_stats_colleges.contracts_after_nine_grades_count) + 
                    SUM(admission_campaign_stats_colleges.contracts_after_eleventh_grades_count) as total_contracts_count'),
            DB::raw('SUM(admission_campaign_stats_colleges.paid_contracts_count) as total_paid_contracts_count'),
        )->groupBy(
            'admission_campaign_directions.level_education_id'
        )->get()->toArray();

        $bachelor = AdmissionCampaignStatsBachelor::join('admission_campaign_directions', 'admission_campaign_stats_bachelors.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_bachelors.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 6);

        if ($isToDay) {
            $bachelor->where('admission_campaign_stats_bachelors.created_at', 'like', now()->format('Y-m-d') . '%');
        }

        $bachelor = $bachelor->select(
            DB::raw('SUM(admission_campaign_stats_bachelors.applications_certificate_count) + 
                    SUM(admission_campaign_stats_bachelors.applications_diploma_bachelor_count) +
                    SUM(admission_campaign_stats_bachelors.applications_diploma_college_count) as total_applications_count'),
            DB::raw('SUM(admission_campaign_stats_bachelors.contracts_certificate_count) + 
                    SUM(admission_campaign_stats_bachelors.contracts_diploma_bachelor_count) +
                    SUM(admission_campaign_stats_bachelors.contracts_diploma_college_count) as total_contracts_count'),
            DB::raw('SUM(admission_campaign_stats_bachelors.paid_contracts_count) as total_paid_contracts_count'),
        )->groupBy(
            'admission_campaign_directions.level_education_id'
        )->get()->toArray();


        $mastersDegree = AdmissionCampaignStatsMastersDegree::join('admission_campaign_directions', 'admission_campaign_stats_masters_degrees.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_masters_degrees.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 7);

        if ($isToDay) {
            $mastersDegree->where('admission_campaign_stats_masters_degrees.created_at', 'like', now()->format('Y-m-d') . '%');
        }

        $mastersDegree = $mastersDegree->select(
            DB::raw('SUM(admission_campaign_stats_masters_degrees.applications_count) as total_applications_count'),
            DB::raw('SUM(admission_campaign_stats_masters_degrees.contracts_count) total_contracts_count'),
            DB::raw('SUM(admission_campaign_stats_masters_degrees.paid_contracts_count) as total_paid_contracts_count'),
        )->groupBy(
            'admission_campaign_directions.level_education_id'
        )->get()->toArray();

        $phd = AdmissionCampaignStatsPhD::join('admission_campaign_directions', 'admission_campaign_stats_phd.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats_phd.admission_campaign_id', '=', $activeAdmissionCampaignId)
            ->where('admission_campaign_directions.level_education_id', '=', 4);

        if ($isToDay) {
            $phd->where('admission_campaign_stats_phd.created_at', 'like', now()->format('Y-m-d') . '%');
        }

        $phd = $phd->select(
            DB::raw('SUM(admission_campaign_stats_phd.applications_count) as total_applications_count'),
            DB::raw('SUM(admission_campaign_stats_phd.contracts_count) total_contracts_count'),
            DB::raw('SUM(admission_campaign_stats_phd.paid_contracts_count) as total_paid_contracts_count'),
        )->groupBy(
            'admission_campaign_directions.level_education_id'
        )->get()->toArray();

        return [
            'college' => $college,
            'bachelor' => $bachelor,
            'mastersDegree' => $mastersDegree,
            'phd' => $phd
        ];
    }
}
