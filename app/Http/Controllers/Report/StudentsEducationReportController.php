<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\ContingentController;
use App\Support\EducationOrganizationName;
use App\Support\StudentReportValues;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings as WordSettings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Table as WordTableStyle;

/**
 * Отчет по студентам ("Данные студентов") с выбором колонок и формата: Excel, Word, PDF.
 *
 * Данные берутся из ContingentController::getStudentsEducationData().
 * Колонка "№ п/п" выводится всегда, остальные - по выбору пользователя.
 */
class StudentsEducationReportController extends Controller
{
    public const FORMATS = ['xlsx', 'docx', 'pdf'];

    /** Группа колонок "Документ об образовании" (в Excel - объединённая шапка) */
    private const EDU_GROUP_TITLE = 'Документ об образовании';

    /**
     * Все колонки отчета в порядке вывода.
     *  label  - заголовок;  edu - колонка входит в "Документ об образовании";
     *  xlsx   - ширина в Excel;  doc - относительная ширина в Word/PDF;  center - выравнивание по центру.
     */
    private const COLUMNS = [
        'fio'           => ['label' => 'ФИО',                    'xlsx' => 35, 'doc' => 9,   'center' => false, 'default' => true],
        'sex'           => ['label' => 'Пол',                    'xlsx' => 6,  'doc' => 2.5, 'center' => true,  'default' => true],
        'birthday'      => ['label' => 'Дата рождения',          'xlsx' => 14, 'doc' => 4.5, 'center' => true,  'default' => true],
        'country'       => ['label' => 'Гражданство',            'xlsx' => 22, 'doc' => 5,   'center' => false, 'default' => false],
        'document_type' => ['label' => 'Наименование',           'xlsx' => 30, 'doc' => 6,   'center' => false, 'default' => false, 'edu' => true],
        'year'          => ['label' => 'Год выдачи',             'xlsx' => 12, 'doc' => 3,   'center' => true,  'default' => false, 'edu' => true],
        'average_score' => ['label' => 'Средний балл',           'xlsx' => 12, 'doc' => 3,   'center' => true,  'default' => false, 'edu' => true],
        'city'          => ['label' => 'Место выдачи',           'xlsx' => 20, 'doc' => 5,   'center' => false, 'default' => false, 'edu' => true],
        'organization'  => ['label' => 'Кем выдан',              'xlsx' => 40, 'doc' => 9,   'center' => false, 'default' => false, 'edu' => true],
        'group'         => ['label' => 'Группа',                 'xlsx' => 15, 'doc' => 4,   'center' => true,  'default' => true],
        'course'        => ['label' => 'Курс',                   'xlsx' => 8,  'doc' => 2.5, 'center' => true,  'default' => false],
        'teaching_form' => ['label' => 'Форма обучения',         'xlsx' => 16, 'doc' => 4,   'center' => true,  'default' => false],
        'speciality'    => ['label' => 'Направление подготовки', 'xlsx' => 35, 'doc' => 8,   'center' => false, 'default' => false],
    ];

    /** Для формы: ключ => название колонки */
    public static function columnOptions(): array
    {
        return array_map(
            fn($c) => ($c['edu'] ?? false) ? self::EDU_GROUP_TITLE . ': ' . mb_strtolower($c['label']) : $c['label'],
            self::COLUMNS
        );
    }

    /** Колонки, отмеченные по умолчанию: ФИО, пол, дата рождения, группа */
    public static function defaultColumns(): array
    {
        return array_keys(array_filter(self::COLUMNS, fn($c) => $c['default']));
    }

    /**
     * @param array  $selectedColumns ключи колонок из columnOptions()
     * @param string $format          xlsx | docx | pdf
     */
    public function download(array $selectedColumns = [], string $format = 'xlsx'): array
    {
        $format = in_array($format, self::FORMATS, true) ? $format : 'xlsx';

        // Только известные колонки и строго в порядке отчета; если ничего не выбрано - основные
        $keys = array_values(array_filter(
            array_keys(self::COLUMNS),
            fn($key) => in_array($key, $selectedColumns, true)
        ));

        if (empty($keys)) {
            $keys = self::defaultColumns();
        }

        $students = (new ContingentController())->getStudentsEducationData();

        $rows = [];
        foreach ($students as $i => $student) {
            $row = [$i + 1];
            foreach ($keys as $key) {
                $row[] = $this->value($key, $student);
            }
            $rows[] = $row;
        }

        $table = [
            'keys' => $keys,
            'rows' => $rows,
        ];

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

        $tempFile = tempnam(sys_get_temp_dir(), 'students_edu_');
        file_put_contents($tempFile, $content);

        return [
            'tempFile' => $tempFile,
            'filename' => 'Отчет_по_студентам_' . now()->format('d.m.Y') . '.' . $format,
        ];
    }

    /* ------------------------------------------------------------------
     * ДАННЫЕ
     * ---------------------------------------------------------------- */

    private function value(string $key, $student)
    {
        switch ($key) {
            case 'fio':
                return trim(preg_replace('/\s+/u', ' ', (string)$student->fio));
            case 'sex':
                return $student->sex ? 'Ж' : 'М';
            case 'birthday':
                return $this->date($student->birthday);
            case 'country':
                return StudentReportValues::country($student->country_name);
            case 'document_type':
                return StudentReportValues::documentType($student->document_type);
            case 'year':
                // end_year бывает заглушкой (-1): тогда берем год из даты выдачи
                $year = StudentReportValues::year($student->end_year);

                return $year !== ''
                    ? $year
                    : ($student->document_issue_date ? StudentReportValues::year($this->year($student->document_issue_date)) : '');
            case 'average_score':
                return $student->average_score;
            case 'city':
                return StudentReportValues::city($student->city_name);
            case 'organization':
                // Единое написание названий (ИУБиП и его филиалы, частые сокращения вузов)
                return EducationOrganizationName::normalize($student->organization_name);
            case 'group':
                return (string)$student->group_name;
            case 'course':
                return $student->course;
            case 'teaching_form':
                return (string)$student->teaching_form_name;
            case 'speciality':
                return (string)$student->sp;
        }

        return '';
    }

    private function date($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable $e) {
            return (string)$value;
        }
    }

    private function year($value)
    {
        try {
            return Carbon::parse($value)->year;
        } catch (\Throwable $e) {
            return '';
        }
    }

    /** Заголовки: "№ п/п" + выбранные колонки */
    private function headers(array $keys): array
    {
        return array_merge(['№ п/п'], array_map(fn($key) => self::COLUMNS[$key]['label'], $keys));
    }

    /** Заголовки для Word/PDF (одна строка шапки): колонки документа об образовании поясняются */
    private function flatHeaders(array $keys): array
    {
        return array_merge(['№ п/п'], array_map(function ($key) {
            $c = self::COLUMNS[$key];

            return ($c['edu'] ?? false) ? 'Документ об образовании: ' . mb_strtolower($c['label']) : $c['label'];
        }, $keys));
    }

    private function centerIndexes(array $keys): array
    {
        $result = [0];
        foreach ($keys as $i => $key) {
            if (self::COLUMNS[$key]['center']) {
                $result[] = $i + 1;
            }
        }

        return $result;
    }

    /** Относительные ширины колонок Word/PDF (включая "№ п/п") */
    private function docWidths(array $keys): array
    {
        return array_merge([2.5], array_map(fn($key) => self::COLUMNS[$key]['doc'], $keys));
    }

    /** Размер шрифта по количеству колонок */
    private function fontSize(int $columns): float
    {
        if ($columns <= 6) {
            return 10;
        }

        return $columns <= 9 ? 8 : 7;
    }

    /* ------------------------------------------------------------------
     * EXCEL
     * ---------------------------------------------------------------- */

    private function buildXlsx(array $table): string
    {
        $keys = $table['keys'];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Данные студентов');

        $columnCount = count($keys) + 1;
        $lastColumn = Coordinate::stringFromColumnIndex($columnCount);

        $sheet->setCellValue('A1', 'Данные студентов');
        $sheet->mergeCells('A1:' . $lastColumn . '1');

        // Объединённая шапка "Документ об образовании" над соответствующими колонками
        $eduIndexes = [];
        foreach ($keys as $i => $key) {
            if (self::COLUMNS[$key]['edu'] ?? false) {
                $eduIndexes[] = $i + 2;   // +1 за "№ п/п", +1 т.к. столбцы с 1
            }
        }

        if (!empty($eduIndexes)) {
            $from = Coordinate::stringFromColumnIndex(min($eduIndexes));
            $to = Coordinate::stringFromColumnIndex(max($eduIndexes));
            $sheet->setCellValue($from . '2', self::EDU_GROUP_TITLE);

            if ($from !== $to) {
                $sheet->mergeCells($from . '2:' . $to . '2');
            }
        }

        $sheet->fromArray([$this->headers($keys)], null, 'A3');
        $sheet->fromArray($table['rows'], null, 'A4', true);

        $lastRow = max(3 + count($table['rows']), 3);

        // Заголовок и шапка
        $sheet->getStyle('A1:' . $lastColumn . '3')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastColumn . '3')->applyFromArray(['alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ]]);
        $sheet->getStyle('A3:' . $lastColumn . '3')->getAlignment()->setWrapText(true);

        // Данные
        $sheet->getStyle('A4:' . $lastColumn . $lastRow)->getAlignment()
            ->setWrapText(true)->setVertical(Alignment::VERTICAL_CENTER);

        foreach ($this->centerIndexes($keys) as $index) {
            $letter = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->getStyle($letter . '4:' . $letter . $lastRow)
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Рамка (шапка "Документ об образовании" - только если она есть)
        $borderStart = empty($eduIndexes) ? 3 : 2;
        $sheet->getStyle('A' . $borderStart . ':' . $lastColumn . $lastRow)->getBorders()->getAllBorders()
            ->applyFromArray(['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '808080']]);

        // Ширина столбцов
        $sheet->getColumnDimension('A')->setWidth(8);
        foreach ($keys as $i => $key) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 2))->setWidth(self::COLUMNS[$key]['xlsx']);
        }

        $sheet->freezePane('A4');

        // Печать: по ширине одной страницы, альбомная, шапка на каждой странице
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0)
            ->setRowsToRepeatAtTopByStartAndEnd(3, 3);

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
        (new Xlsx($spreadsheet))->save($tmp);
        $content = file_get_contents($tmp);
        @unlink($tmp);

        return $content;
    }

    /* ------------------------------------------------------------------
     * WORD (A4 альбомная)
     * ---------------------------------------------------------------- */

    private function buildDocx(array $table): string
    {
        WordSettings::setOutputEscapingEnabled(true);

        $keys = $table['keys'];
        $headers = $this->flatHeaders($keys);
        $fontSize = $this->fontSize(count($headers));

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');

        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'pageSizeW' => 16838,
            'pageSizeH' => 11906,
            'marginTop' => 567, 'marginBottom' => 567, 'marginLeft' => 567, 'marginRight' => 567,
        ]);

        $section->addText('Данные студентов', ['size' => 14, 'bold' => true], ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

        $total = 15600;   // 16838 - 2 * 567 с небольшим запасом
        $docWidths = $this->docWidths($keys);
        $sum = array_sum($docWidths);
        $widths = array_map(fn($w) => (int)floor($total * $w / $sum), $docWidths);

        $wordTable = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 40,
            'layout' => WordTableStyle::LAYOUT_FIXED,
            'unit' => TblWidth::TWIP,
        ]);

        $head = ['name' => 'Times New Roman', 'size' => $fontSize, 'bold' => true];
        $body = ['name' => 'Times New Roman', 'size' => $fontSize];
        $center = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];
        $left = ['alignment' => Jc::LEFT, 'spaceAfter' => 0];
        $centerIndexes = $this->centerIndexes($keys);

        $wordTable->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        foreach ($headers as $i => $header) {
            $wordTable->addCell($widths[$i], ['valign' => 'center'])->addText($header, $head, $center);
        }

        foreach ($table['rows'] as $row) {
            $wordTable->addRow(null, ['cantSplit' => true]);
            foreach ($row as $i => $value) {
                $wordTable->addCell($widths[$i], ['valign' => 'center'])
                    ->addText((string)$value, $body, in_array($i, $centerIndexes, true) ? $center : $left);
            }
        }

        $tmp = tempnam(sys_get_temp_dir(), 'docx_');
        WordIOFactory::createWriter($phpWord, 'Word2007')->save($tmp);

        // Размер и ориентацию листа гарантированно прописываем в самом файле (А4 альбомная)
        $this->forceLandscape($tmp);

        $content = file_get_contents($tmp);
        @unlink($tmp);

        return $content;
    }

    /** Подменяет <w:pgSz> в document.xml на A4 альбомный */
    private function forceLandscape(string $file): void
    {
        $zip = new \ZipArchive();

        if ($zip->open($file) !== true) {
            return;
        }

        $xml = $zip->getFromName('word/document.xml');

        if ($xml !== false) {
            $pgSz = '<w:pgSz w:w="16838" w:h="11906" w:orient="landscape"/>';
            $count = 0;
            $xml = preg_replace('/<w:pgSz\b[^>]*\/>/', $pgSz, $xml, -1, $count);

            if ($count === 0) {
                $xml = preg_replace('/(<w:sectPr\b[^>]*>)/', '$1' . $pgSz, $xml, 1);
            }

            $zip->addFromString('word/document.xml', $xml);
        }

        $zip->close();
    }

    /* ------------------------------------------------------------------
     * PDF (barryvdh/laravel-dompdf)
     * ---------------------------------------------------------------- */

    private function buildPdf(array $table): string
    {
        // Отчет по всем студентам - это тысячи строк: dompdf на них требует много памяти и времени
        @ini_set('memory_limit', '2048M');
        @set_time_limit(600);

        $keys = $table['keys'];
        $headers = $this->flatHeaders($keys);
        $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

        $fontSize = $this->fontSize(count($headers)) - 1;   // DejaVu Sans шире Times
        $docWidths = $this->docWidths($keys);
        $sum = array_sum($docWidths);
        $centerIndexes = $this->centerIndexes($keys);

        $html = '<html><head><meta charset="utf-8"><style>'
            . '@page { margin: 20px; }'
            . 'body { font-family: "DejaVu Sans", sans-serif; font-size: ' . $fontSize . 'px; }'
            . 'h1 { text-align: center; font-size: 14px; margin: 0 0 8px 0; }'
            . 'table { width: 100%; border-collapse: collapse; table-layout: fixed; }'
            . 'th, td { border: 1px solid #000; padding: 2px 3px; word-wrap: break-word; vertical-align: middle; }'
            . 'th { font-weight: bold; text-align: center; }'
            . 'thead { display: table-header-group; }'
            . (count($table['rows']) <= 300 ? 'tr { page-break-inside: avoid; }' : '')
            . '.c { text-align: center; }'
            . '</style></head><body>';

        $html .= '<h1>Данные студентов</h1><table><colgroup>';

        foreach ($docWidths as $w) {
            $html .= '<col style="width: ' . round($w / $sum * 100, 2) . '%">';
        }

        $html .= '</colgroup><thead><tr>';

        foreach ($headers as $header) {
            $html .= '<th>' . $e($header) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($table['rows'] as $row) {
            $html .= '<tr>';
            foreach ($row as $i => $value) {
                $html .= '<td' . (in_array($i, $centerIndexes, true) ? ' class="c"' : '') . '>' . $e($value) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></body></html>';

        return Pdf::loadHTML($html)->setPaper('a4', 'landscape')->output();
    }
}