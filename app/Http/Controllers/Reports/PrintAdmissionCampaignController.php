<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\AdmissionCampaignStats;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PrintAdmissionCampaignController extends Controller
{
    public function print()
    {
        $todayPaidContractsCount = 0;
        $todayContractsCount = 0;

        $college = $this->dataTransform(2);
        $bachelor = $this->dataTransform(6);
        $master = $this->dataTransform(7);
        $phd = $this->dataTransform(4);

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        if (count($bachelor) != 0) {
            $sheet->setCellValue('A' . $offset, 'БАКАЛАВРИАТ');
            $sheet->mergeCells('A1:U1');

            $offset++;

            $sheet->setCellValue('A' . $offset, 'Направление подготовки');
            $sheet->mergeCells('A2:A4');

            $sheet->setCellValue('B' . $offset, 'Завялений на ' . now()->format('d.m.Y'));
            $sheet->mergeCells('B' . $offset . ':J' . $offset);

            $sheet->setCellValue('K' . $offset, 'Договоров на ' . now()->format('d.m.Y'));
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
            $sheet->mergeCells('T3:T4');

            $sheet->setCellValue('U' . $offset, 'Не оплачено');
            $sheet->mergeCells('U3:U4');

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

            $sheet->getStyle('A2:U' . $offset)->getFont()->setBold(true);

            $offset++;

            foreach ($bachelor as $key => $value) {
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

                        $todayPaidContractsCount += $item[1]['today_paid_contracts_count'];
                        $todayContractsCount += $item[1]['today_contracts_certificate_count'] + $item[1]['today_contracts_diploma_bachelor_count'] + $item[1]['today_contracts_diploma_college_count'];
                    }

                    //ЗФО
                    if (isset($item[2])) {
                        $sheet->setCellValue('E' . $offset, $item[2]['today_applications_certificate_count']);
                        $sheet->setCellValue('F' . $offset, $item[2]['today_applications_diploma_bachelor_count']);
                        $sheet->setCellValue('G' . $offset, $item[2]['today_applications_diploma_college_count']);

                        $sheet->setCellValue('N' . $offset, $item[2]['today_contracts_certificate_count']);
                        $sheet->setCellValue('O' . $offset, $item[2]['today_contracts_diploma_bachelor_count']);
                        $sheet->setCellValue('P' . $offset, $item[2]['today_contracts_diploma_college_count']);

                        $todayPaidContractsCount += $item[2]['today_paid_contracts_count'];
                        $todayContractsCount += $item[2]['today_contracts_certificate_count'] + $item[2]['today_contracts_diploma_bachelor_count'] + $item[2]['today_contracts_diploma_college_count'];
                    }

                    //ОЗФО
                    if (isset($item[6])) {
                        $sheet->setCellValue('H' . $offset, $item[6]['today_applications_certificate_count']);
                        $sheet->setCellValue('I' . $offset, $item[6]['today_applications_diploma_bachelor_count']);
                        $sheet->setCellValue('J' . $offset, $item[6]['today_applications_diploma_college_count']);

                        $sheet->setCellValue('Q' . $offset, $item[6]['today_contracts_certificate_count']);
                        $sheet->setCellValue('R' . $offset, $item[6]['today_contracts_diploma_bachelor_count']);
                        $sheet->setCellValue('S' . $offset, $item[6]['today_contracts_diploma_college_count']);

                        $todayPaidContractsCount += $item[6]['today_paid_contracts_count'];
                        $todayContractsCount += $item[6]['today_contracts_certificate_count'] + $item[6]['today_contracts_diploma_bachelor_count'] + $item[6]['today_contracts_diploma_college_count'];
                    }
                }

                $sheet->setCellValue('T' . $offset, $todayPaidContractsCount);
                $sheet->setCellValue('U' . $offset, $todayContractsCount - $todayPaidContractsCount);

                $offset++;
            }

            $sheet->getStyle('A2:A' . $offset)->getAlignment()->setWrapText(true);

            //Стиль для рамки
            $sheet->getStyle('A1:U' . ($offset - 1))
                ->getBorders()
                ->getAllBorders()
                ->applyFromArray(array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '808080')
                ));

            //Выравнивание текста в ячейке
            $sheet->getStyle('A2:A4')
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]));
            $sheet->getStyle('B2:S3')
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));
            $sheet->getStyle('T2:U2')
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));
            $sheet->getStyle('B4:S4')
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));
            $sheet->getStyle('T3:U3')
                ->applyFromArray(array('alignment' => [
                    'textRotation' => 90,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));
            $sheet->getStyle('B4:U' . ($offset - 1))
                ->applyFromArray(array('alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                ]));

            //Устанавливаем ширину полей
            $sheet->getColumnDimension('A')->setWidth(45);
            $sheet->getColumnDimension('B')->setWidth(5);
            $sheet->getColumnDimension('C')->setWidth(5);
            $sheet->getColumnDimension('D')->setWidth(5);
            $sheet->getColumnDimension('E')->setWidth(5);
            $sheet->getColumnDimension('F')->setWidth(5);
            $sheet->getColumnDimension('G')->setWidth(5);
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
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    private function dataTransform($levelEducation)
    {
        $today = Carbon::now()->format('Y-m-d');

        $data = AdmissionCampaignStats::join('admission_campaign_directions', 'admission_campaign_stats.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats.admission_campaign_id', '=', 1)
            ->where('admission_campaign_directions.level_education_id', '=', $levelEducation)
            ->where('admission_campaign_directions.funding_type', '=', 1)
            ->select(
                DB::raw("CONCAT(admission_campaign_directions.speciality_code, ' ',
                admission_campaign_directions.speciality_name, ' ',admission_campaign_directions.speciality_profile) as direction"),
                'admission_campaign_directions.funding_type',
                'admission_campaign_directions.level_education_id',
                'admission_campaign_directions.teaching_form_id',
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.applications_certificate_count ELSE 0 END) as today_applications_certificate_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.applications_diploma_bachelor_count ELSE 0 END) as today_applications_diploma_bachelor_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.applications_diploma_college_count ELSE 0 END) as today_applications_diploma_college_count'),
                DB::raw('SUM(admission_campaign_stats.applications_certificate_count) as total_applications_certificate_count'),
                DB::raw('SUM(admission_campaign_stats.applications_diploma_bachelor_count) as total_applications_diploma_bachelor_count'),
                DB::raw('SUM(admission_campaign_stats.applications_diploma_college_count) as total_applications_diploma_college_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.contracts_certificate_count ELSE 0 END) as today_contracts_certificate_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.contracts_diploma_bachelor_count ELSE 0 END) as today_contracts_diploma_bachelor_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.contracts_diploma_college_count ELSE 0 END) as today_contracts_diploma_college_count'),
                DB::raw('SUM(admission_campaign_stats.contracts_certificate_count) as total_contracts_certificate_count'),
                DB::raw('SUM(admission_campaign_stats.contracts_diploma_bachelor_count) as total_contracts_diploma_bachelor_count'),
                DB::raw('SUM(admission_campaign_stats.contracts_diploma_college_count) as total_contracts_diploma_college_count'),
                DB::raw('SUM(CASE WHEN admission_campaign_stats.created_at LIKE "' . $today . '%" THEN admission_campaign_stats.paid_contracts_count ELSE 0 END) as today_paid_contracts_count'),
                DB::raw('SUM(admission_campaign_stats.paid_contracts_count) as total_paid_contracts_count'),
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
}
