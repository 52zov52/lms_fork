<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\ContingentController;
use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Settings as WordSettings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Table as WordTableStyle;

/**
 * Список студентов группы в любом формате: Excel, Word или PDF.
 *
 * $minimal = true  - краткий список: № пп | ФИО | № ст. билета (как в "Списках групп");
 * $minimal = false - полный список со всеми данными (паспорт, адрес, образование, воинский учёт ...).
 */
class StudentListReportController extends Controller
{
    public const FORMATS = ['xlsx', 'docx', 'pdf'];

    public function download($groupId, string $format = 'xlsx', bool $minimal = false): array
    {
        $format = in_array($format, self::FORMATS, true) ? $format : 'xlsx';

        $group = Group::select('id', 'group_name')->findOrFail($groupId);
        $table = $minimal ? $this->minimalTable($groupId) : $this->fullTable($groupId);
        $table['groupName'] = trim($group->group_name);
        $table['minimal'] = $minimal;

        switch ($format) {
            case 'docx':
                $content = $this->buildDocx($table);
                break;
            case 'pdf':
                $content = $this->buildPdf($table);
                break;
            default:
                $content = $this->buildXlsx($table);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'students_');
        file_put_contents($tempFile, $content);

        $safeName = preg_replace('/[\\\\\/:*?"<>|]+/u', '_', $table['groupName']);
        $filename = 'Список_' . $safeName . ($minimal ? '' : '_полный') . '.' . $format;

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    /* ------------------------------------------------------------------
     * ДАННЫЕ
     * ---------------------------------------------------------------- */

    private function minimalTable($groupId): array
    {
        $students = (new ContingentController())->getContingent($groupId);

        $rows = [];
        foreach ($students as $i => $s) {
            $rows[] = [$i + 1, $this->fio($s), $s->ticket_number];
        }

        return [
            'headers' => ['№ пп', 'Фамилия И. О.', '№ ст. билета'],
            'rows' => $rows,
            'xlsxWidths' => [10, 40, 18],
            'docWidths' => [8, 70, 22],        // % ширины
            'centerColumns' => [0, 2],
        ];
    }

    private function fullTable($groupId): array
    {
        $students = (new ContingentController())->getFullContingent($groupId);

        $rows = [];
        foreach ($students as $i => $s) {
            $rows[] = [
                $i + 1,
                $this->fio($s),
                $this->date($s->birthday),
                $this->join([$s->passport_series, $s->passport_number], ' '),
                $s->passport_issuing_authority,
                $this->date($s->passport_issue_date),
                $this->join([
                    $s->country_name, $s->region_name, $s->city_type_name, $s->city_name,
                    $s->district_name, $s->street, $s->building, $s->flat,
                ]),
                $this->join([
                    $s->type_name, $s->name, $s->document_type, $s->document_series,
                    $s->document_number, $this->date($s->document_issue_date), $s->end_year,
                ]),
                $s->military_document,
                $this->join([$s->military_document_series, $s->military_document_number], ' '),
                $s->military_issued_by,
                $this->date($s->military_issue_date),
                $s->reserve_category,
                $s->military_rank,
                $s->military_occupation_profile,
                $s->full_military_specialty_code,
                $s->fitness_category,
                $s->military_commissariat_name,
                $s->military_registration_type_general,
                $s->military_registration_type_special,
                $s->group_name,
                $s->phone,
            ];
        }

        return [
            'headers' => [
                '№ пп', 'ФИО', 'Дата рождения', 'Серия и номер паспорта', 'Кем выдан паспорт',
                'Дата выдачи паспорта', 'Адрес проживания', 'Документ об образовании',
                'Документ об воинском учете', 'Серия и номер документа об воинском учете',
                'Кем выдан документ об воинском учете', 'Дата выдачи документа об воинском учете',
                'Категория запаса', 'Воинское звание', 'Состав (профиль)', 'Полное кодовое обозначение ВУС',
                'Категория годности к военной службе', 'Наименование военного комиссариата по месту воинского учета',
                'Общий воинский учет', 'Специальный воинский учет', 'Группа в которой учится студент', '№ телефона',
            ],
            'rows' => $rows,
            'xlsxWidths' => [10, 35, 15, 15, 35, 15, 35, 35, 35, 35, 35, 15, 15, 15, 15, 15, 15, 35, 15, 15, 15, 15],
            'docWidths' => [2.5, 8, 4.5, 4.5, 6, 4.5, 9, 10, 4, 4.5, 6, 4.5, 3.5, 3.5, 3.5, 3.5, 3.5, 6, 3, 3, 3.5, 4.5],
            'centerColumns' => [0, 2, 3, 5, 9, 11, 12, 13, 14, 15, 16, 20, 21],
        ];
    }

    private function fio($student): string
    {
        return trim(preg_replace('/\s+/u', ' ', $student->last_name . ' ' . $student->first_name . ' ' . $student->middle_name));
    }

    private function join(array $parts, string $glue = ', '): string
    {
        $parts = array_filter(array_map(fn($p) => trim((string)$p), $parts), fn($p) => $p !== '');

        return implode($glue, $parts);
    }

    private function date($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            $date = Carbon::parse($value);
        } catch (\Throwable $e) {
            return (string)$value;
        }

        // 1900-01-01 и подобное - заглушки "нет даты"
        return $date->year <= 1900 ? '' : $date->format('d.m.Y');
    }

    private function title(array $table): array
    {
        return ['СПИСОК', 'студентов учебной группы ' . $table['groupName']];
    }

    /* ------------------------------------------------------------------
     * EXCEL
     * ---------------------------------------------------------------- */

    private function buildXlsx(array $table): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $columnCount = count($table['headers']);
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount);

        $row = 1;

        if ($table['minimal']) {
            [$line1, $line2] = $this->title($table);

            $sheet->setCellValue('A1', $line1);
            $sheet->mergeCells('A1:' . $lastColumn . '1');
            $sheet->setCellValue('A2', $line2);
            $sheet->mergeCells('A2:' . $lastColumn . '2');
            $sheet->getStyle('A1:' . $lastColumn . '2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:A2')->getFont()->setBold(true);

            $row = 4;
        }

        $headerRow = $row;
        $sheet->fromArray([$table['headers']], null, 'A' . $headerRow, true);

        $dataStart = $headerRow + 1;
        $sheet->fromArray($table['rows'], null, 'A' . $dataStart, true);

        $lastRow = max($headerRow, $headerRow + count($table['rows']));
        $range = 'A' . $headerRow . ':' . $lastColumn . $lastRow;

        $sheet->getStyle($range)->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->applyFromArray([
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '808080'],
        ]);

        $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        foreach ($table['centerColumns'] as $index) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->getStyle($letter . $dataStart . ':' . $letter . $lastRow)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        foreach ($table['xlsxWidths'] as $index => $width) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->getColumnDimension($letter)->setWidth($width);
        }

        $sheet->freezePane('A' . $dataStart);

        // Печать: по ширине одной страницы, шапка на каждой странице
        $sheet->getPageSetup()
            ->setOrientation($table['minimal']
                ? \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT
                : \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setRowsToRepeatAtTopByStartAndEnd($headerRow, $headerRow);

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        (new Xlsx($spreadsheet))->save($tmp);
        $content = file_get_contents($tmp);
        @unlink($tmp);

        return $content;
    }

    /* ------------------------------------------------------------------
     * WORD
     * ---------------------------------------------------------------- */

    private function buildDocx(array $table): string
    {
        WordSettings::setOutputEscapingEnabled(true);

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');

        $minimal = $table['minimal'];
        $fontSize = $minimal ? 12 : 7;

        $section = $phpWord->addSection($minimal
            ? [
                'orientation' => 'portrait',
                'marginTop' => 850, 'marginBottom' => 850, 'marginLeft' => 1130, 'marginRight' => 850,
            ]
            : [
                'orientation' => 'landscape',
                'paperSize' => 'A3',
                'marginTop' => 567, 'marginBottom' => 567, 'marginLeft' => 567, 'marginRight' => 567,
            ]);

        [$line1, $line2] = $this->title($table);
        $section->addText($line1, ['size' => 16, 'bold' => $minimal], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        $section->addText($line2, ['size' => 12, 'bold' => true], ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

        // Ширина области таблицы в twips
        $total = $minimal ? 9900 : 22900;

        $wordTable = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 40,
            'layout' => WordTableStyle::LAYOUT_FIXED,
            'unit' => TblWidth::TWIP,
        ]);

        $widths = array_map(fn($p) => (int)floor($total * $p / array_sum($table['docWidths'])), $table['docWidths']);

        $head = ['name' => 'Times New Roman', 'size' => $fontSize, 'bold' => true];
        $body = ['name' => 'Times New Roman', 'size' => $fontSize];
        $center = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];
        $left = ['alignment' => Jc::LEFT, 'spaceAfter' => 0];

        $wordTable->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        foreach ($table['headers'] as $i => $header) {
            $wordTable->addCell($widths[$i], ['valign' => 'center'])->addText($header, $head, $center);
        }

        foreach ($table['rows'] as $row) {
            $wordTable->addRow(null, ['cantSplit' => true]);
            foreach ($row as $i => $value) {
                $wordTable->addCell($widths[$i], ['valign' => 'center'])
                    ->addText((string)$value, $body, in_array($i, $table['centerColumns'], true) ? $center : $left);
            }
        }

        $tmp = tempnam(sys_get_temp_dir(), 'docx_');
        WordIOFactory::createWriter($phpWord, 'Word2007')->save($tmp);
        $content = file_get_contents($tmp);
        @unlink($tmp);

        return $content;
    }

    /* ------------------------------------------------------------------
     * PDF (barryvdh/laravel-dompdf, как в индивидуальной сводке)
     * ---------------------------------------------------------------- */

    private function buildPdf(array $table): string
    {
        $minimal = $table['minimal'];
        $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

        $fontSize = $minimal ? '12px' : '6.5px';
        $total = array_sum($table['docWidths']);

        [$line1, $line2] = $this->title($table);

        $html = '<html><head><meta charset="utf-8"><style>'
            . '@page { margin: ' . ($minimal ? '30px 30px 30px 40px' : '20px') . '; }'
            . 'body { font-family: "DejaVu Sans", sans-serif; font-size: ' . $fontSize . '; }'
            . 'h1 { text-align: center; font-size: ' . ($minimal ? '18px' : '14px') . '; margin: 0; }'
            . 'h2 { text-align: center; font-size: ' . ($minimal ? '13px' : '11px') . '; margin: 2px 0 12px 0; }'
            . 'table { width: 100%; border-collapse: collapse; table-layout: fixed; }'
            . 'th, td { border: 1px solid #000; padding: 2px 3px; word-wrap: break-word; vertical-align: middle; }'
            . 'th { font-weight: bold; text-align: center; }'
            . 'thead { display: table-header-group; }'
            . 'tr { page-break-inside: avoid; }'
            . '.c { text-align: center; }'
            . '</style></head><body>';

        $html .= '<h1>' . $e($line1) . '</h1><h2>' . $e($line2) . '</h2>';
        $html .= '<table><colgroup>';

        foreach ($table['docWidths'] as $w) {
            $html .= '<col style="width: ' . round($w / $total * 100, 2) . '%">';
        }

        $html .= '</colgroup><thead><tr>';

        foreach ($table['headers'] as $header) {
            $html .= '<th>' . $e($header) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($table['rows'] as $row) {
            $html .= '<tr>';
            foreach ($row as $i => $value) {
                $html .= '<td' . (in_array($i, $table['centerColumns'], true) ? ' class="c"' : '') . '>' . $e($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        $pdf = Pdf::loadHTML($html);

        if ($minimal) {
            $pdf->setPaper('a4', 'portrait');
        } else {
            $pdf->setPaper('a3', 'landscape');
        }

        return $pdf->output();
    }
}