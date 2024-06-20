<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\MyPublication;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PrintScienceController extends Controller
{
    public function printCountPublicationByYearsAndStatuses()
    {
        $data = MyPublication::where('is_university_affiliation', '=', 1)
            ->select(
                'year_publishing',
                DB::raw("COUNT(CASE WHEN status = 1 THEN 1 END) AS rinc"),
                DB::raw("COUNT(CASE WHEN status = 2 THEN 1 END) AS vak"),
                DB::raw("COUNT(CASE WHEN status = 3 THEN 1 END) AS wos"),
                DB::raw("COUNT(CASE WHEN status = 4 THEN 1 END) AS scopus"),
                DB::raw("COUNT(CASE WHEN status = 5 THEN 1 END) AS isbn"),
                DB::raw("COUNT(CASE WHEN output_data LIKE '%еждународ%научн%конференц%' THEN 1 END) AS number_articles_international_conferences"),
                DB::raw("COUNT(CASE WHEN output_data LIKE '%конференц%'THEN 1 END) AS total_number_articles_conferences")
            )->groupBy('year_publishing')
            ->get();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $sheet->setCellValue('A' . $offset, 'Год');
        $sheet->setCellValue('B' . $offset, 'РИНЦ');
        $sheet->setCellValue('C' . $offset, 'ВАК');
        $sheet->setCellValue('D' . $offset, 'WoS');
        $sheet->setCellValue('E' . $offset, 'Scopus');
        $sheet->setCellValue('F' . $offset, 'ISBN');
        $sheet->setCellValue('G' . $offset, 'Кол-во статей по результатам международных конференций');
        $sheet->setCellValue('H' . $offset, 'Всего кол-во статей по результатам конференций');

        $sheet->getStyle('A' . $offset . ':H' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('G1:H1')->getAlignment()->setWrapText(true);

        $offset++;

        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $offset, $value->year_publishing);
            $sheet->setCellValue('B' . $offset, $value->rinc);
            $sheet->setCellValue('C' . $offset, $value->vak);
            $sheet->setCellValue('D' . $offset, $value->wos);
            $sheet->setCellValue('E' . $offset, $value->scopus);
            $sheet->setCellValue('F' . $offset, $value->isbn);
            $sheet->setCellValue('G' . $offset, $value->number_articles_international_conferences);
            $sheet->setCellValue('H' . $offset, $value->total_number_articles_conferences);

            $offset++;
        }

        //Выравнивание текста в ячейке
        $sheet->getStyle('A1:H' . ($offset - 1))
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
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(10);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(10);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(35);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }

    public function printCountUserPublicationsByStatusAndYearRange()
    {
        $data = MyPublication::join('users_data', 'my_publications.user_id', '=', 'users_data.user_id')
            ->where('my_publications.is_university_affiliation', '=', 1)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) AS fio"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.status = 1) THEN 1 END) AS rinc_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.status = 2) THEN 1 END) AS vak_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.status = 3) THEN 1 END) AS wos_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.status = 4) THEN 1 END) AS scopus_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.status = 5) THEN 1 END) AS isbn_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.output_data LIKE '%еждународ%научн%конференц%') THEN 1 END) AS number_articles_international_conferences_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) AND my_publications.output_data LIKE '%конференц%') THEN 1 END) AS number_articles_conferences_current_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.status = 1) THEN 1 END) AS rinc_last_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.status = 2) THEN 1 END) AS vak_last_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.status = 3) THEN 1 END) AS wos_last_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.status = 4) THEN 1 END) AS scopus_last_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.status = 5) THEN 1 END) AS isbn_last_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.output_data LIKE '%еждународ%научн%конференц%') THEN 1 END) AS number_articles_international_conferences_last_year"),
                DB::raw("COUNT(CASE WHEN (year_publishing = YEAR(CURDATE()) - 1 AND my_publications.output_data LIKE '%конференц%') THEN 1 END) AS number_articles_conferences_last_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.status = 1) THEN 1 END) AS rinc_from_five_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.status = 2) THEN 1 END) AS vak_from_five_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.status = 3) THEN 1 END) AS wos_from_five_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.status = 4) THEN 1 END) AS scopus_from_five_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.status = 5) THEN 1 END) AS isbn_from_five_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.output_data LIKE '%еждународ%научн%конференц%') THEN 1 END) AS number_articles_international_conferences_from_five_year"),
                DB::raw("COUNT(CASE WHEN ((year_publishing >= YEAR(CURDATE()) - 5 AND year_publishing <= YEAR(CURDATE())) AND my_publications.output_data LIKE '%конференц%') THEN 1 END) AS number_articles_conferences_from_five_year")
            )
            ->groupBy('fio')
            ->orderBy('fio')
            ->get();

        $filename = time() . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;

        $sheet->setCellValue('A' . $offset, 'ФИО');
        $sheet->mergeCells('A' . $offset . ':A2');

        $sheet->setCellValue('B' . $offset, 'РИНЦ');
        $sheet->mergeCells('B' . $offset . ':D' . $offset);

        $sheet->setCellValue('E' . $offset, 'ВАК');
        $sheet->mergeCells('E' . $offset . ':G' . $offset);

        $sheet->setCellValue('H' . $offset, 'WoS');
        $sheet->mergeCells('H' . $offset . ':J' . $offset);

        $sheet->setCellValue('K' . $offset, 'Scopus');
        $sheet->mergeCells('K' . $offset . ':M' . $offset);

        $sheet->setCellValue('N' . $offset, 'ISBN');
        $sheet->mergeCells('N' . $offset . ':P' . $offset);

        $sheet->setCellValue('Q' . $offset, 'Кол-во статей по результатам международных конференций');
        $sheet->mergeCells('Q' . $offset . ':S' . $offset);

        $sheet->setCellValue('T' . $offset, 'Всего кол-во статей по результатам конференций');
        $sheet->mergeCells('T' . $offset . ':V' . $offset);

        $sheet->getStyle('A' . $offset . ':V' . $offset)->getFont()->setBold(true);
        $sheet->getStyle('Q1:V1')->getAlignment()->setWrapText(true);

        $sheet->getRowDimension($offset)->setRowHeight(55);

        $offset++;

        $sheet->setCellValue('B' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('C' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('D' . $offset, 'за 5 лет');
        $sheet->setCellValue('E' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('F' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('G' . $offset, 'за 5 лет');
        $sheet->setCellValue('H' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('I' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('J' . $offset, 'за 5 лет');
        $sheet->setCellValue('K' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('L' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('M' . $offset, 'за 5 лет');
        $sheet->setCellValue('N' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('O' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('P' . $offset, 'за 5 лет');
        $sheet->setCellValue('Q' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('R' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('S' . $offset, 'за 5 лет');
        $sheet->setCellValue('T' . $offset, Carbon::now()->format('Y'));
        $sheet->setCellValue('U' . $offset, Carbon::now()->subYear()->format('Y'));
        $sheet->setCellValue('V' . $offset, 'за 5 лет');

        $offset++;

        foreach ($data as $key => $value) {
            $sheet->setCellValue('A' . $offset, $value->fio);
            $sheet->setCellValue('B' . $offset, $value->rinc_current_year);
            $sheet->setCellValue('C' . $offset, $value->rinc_last_year);
            $sheet->setCellValue('D' . $offset, $value->rinc_from_five_year);
            $sheet->setCellValue('E' . $offset, $value->vak_current_year);
            $sheet->setCellValue('F' . $offset, $value->vak_last_year);
            $sheet->setCellValue('G' . $offset, $value->vak_from_five_year);
            $sheet->setCellValue('H' . $offset, $value->wos_current_year);
            $sheet->setCellValue('I' . $offset, $value->wos_last_year);
            $sheet->setCellValue('J' . $offset, $value->wos_from_five_year);
            $sheet->setCellValue('K' . $offset, $value->scopus_current_year);
            $sheet->setCellValue('L' . $offset, $value->scopus_last_year);
            $sheet->setCellValue('M' . $offset, $value->scopus_from_five_year);
            $sheet->setCellValue('N' . $offset, $value->isbn_current_year);
            $sheet->setCellValue('O' . $offset, $value->isbn_last_year);
            $sheet->setCellValue('P' . $offset, $value->isbn_from_five_year);
            $sheet->setCellValue('Q' . $offset, $value->number_articles_international_conferences_current_year);
            $sheet->setCellValue('R' . $offset, $value->number_articles_international_conferences_last_year);
            $sheet->setCellValue('S' . $offset, $value->number_articles_international_conferences_from_five_year);
            $sheet->setCellValue('T' . $offset, $value->number_articles_conferences_current_year);
            $sheet->setCellValue('U' . $offset, $value->number_articles_conferences_last_year);
            $sheet->setCellValue('V' . $offset, $value->number_articles_conferences_from_five_year);

            $offset++;
        }

        $sheet->getStyle('A1:A' . $offset)->getAlignment()->setWrapText(true);

        //Выравнивание текста в ячейке
        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
            ]));

        $sheet->getStyle('B1:V' . ($offset - 1))
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
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(7);
        $sheet->getColumnDimension('C')->setWidth(7);
        $sheet->getColumnDimension('D')->setWidth(9);
        $sheet->getColumnDimension('E')->setWidth(7);
        $sheet->getColumnDimension('F')->setWidth(7);
        $sheet->getColumnDimension('G')->setWidth(9);
        $sheet->getColumnDimension('H')->setWidth(7);
        $sheet->getColumnDimension('I')->setWidth(7);
        $sheet->getColumnDimension('J')->setWidth(9);
        $sheet->getColumnDimension('K')->setWidth(7);
        $sheet->getColumnDimension('L')->setWidth(7);
        $sheet->getColumnDimension('M')->setWidth(9);
        $sheet->getColumnDimension('N')->setWidth(7);
        $sheet->getColumnDimension('O')->setWidth(7);
        $sheet->getColumnDimension('P')->setWidth(9);
        $sheet->getColumnDimension('Q')->setWidth(7);
        $sheet->getColumnDimension('R')->setWidth(7);
        $sheet->getColumnDimension('S')->setWidth(9);
        $sheet->getColumnDimension('T')->setWidth(7);
        $sheet->getColumnDimension('U')->setWidth(7);
        $sheet->getColumnDimension('V')->setWidth(9);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer->save('php://output');
    }
}
