<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Employee\ContingentController;
use App\Models\SocialPassport;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SocialPassportController extends Controller
{
    public function downloadSocialPassportReport($childCategories, $hasPushkinCard)
    {
        $contingentController = new ContingentController();

        $socialPassport = SocialPassport::join('group_student', 'social_passports.user_id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->select(
                'social_passports.last_name',
                'social_passports.first_name',
                'social_passports.middle_name',
                'social_passports.birthday',
                'social_passports.citizenship',
                'social_passports.registered_address',
                'social_passports.housing_conditions',
                'social_passports.cohabitants',
                'social_passports.marital_status',
                'social_passports.spouse_last_name',
                'social_passports.spouse_first_name',
                'social_passports.spouse_middle_name',
                'social_passports.spouse_workplace',
                'social_passports.spouse_phone',
                'social_passports.family_composition',
                'social_passports.number_of_children',
                'social_passports.father_last_name',
                'social_passports.father_first_name',
                'social_passports.father_middle_name',
                'social_passports.father_workplace',
                'social_passports.father_phone',
                'social_passports.mother_last_name',
                'social_passports.mother_first_name',
                'social_passports.mother_middle_name',
                'social_passports.mother_workplace',
                'social_passports.mother_phone',
                'social_passports.father_disability',
                'social_passports.mother_disability',
                'social_passports.child_categories',
                'social_passports.has_pushkin_card',
                'groups.id as group_id',
                'groups.group_name'
            );

        if (count($childCategories) != 0)
            $socialPassport = $socialPassport->whereJsonContains('social_passports.child_categories', $childCategories);

        if ($hasPushkinCard != 2)
            $socialPassport = $socialPassport->where('social_passports.has_pushkin_card', '=', $hasPushkinCard);

        $socialPassport = $socialPassport->whereIn('groups.id', $contingentController->getGroupIds())->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $offset = 1;
        $increment = 1;

        $sheet->setCellValue('A' . $offset, '№ пп');
        $sheet->setCellValue('B' . $offset, 'Ф.И.О. обучающегося');
        $sheet->setCellValue('C' . $offset, 'Дата рождения');
        $sheet->setCellValue('D' . $offset, 'Гражданство');
        $sheet->setCellValue('E' . $offset, 'Место жительства (по прописке)');
        $sheet->setCellValue('F' . $offset, 'Жилищные условия');
        $sheet->setCellValue('G' . $offset, 'Совместно проживание');
        $sheet->setCellValue('H' . $offset, 'Семейное положение');
        $sheet->setCellValue('I' . $offset, 'Ф.И.О. супруга/супруги');
        $sheet->setCellValue('J' . $offset, 'Место работы супруга/супруги');
        $sheet->setCellValue('K' . $offset, 'Контактный телефон супруга/супруги');
        $sheet->setCellValue('L' . $offset, 'Состав семьи');
        $sheet->setCellValue('M' . $offset, 'Количество детей в семье');
        $sheet->setCellValue('N' . $offset, 'Ф.И.О. отца');
        $sheet->setCellValue('O' . $offset, 'Место работы отца');
        $sheet->setCellValue('P' . $offset, 'Контактный телефон отца');
        $sheet->setCellValue('Q' . $offset, 'Ф.И.О. матери');
        $sheet->setCellValue('R' . $offset, 'Место работы матери');
        $sheet->setCellValue('S' . $offset, 'Контактный телефон матери');
        $sheet->setCellValue('T' . $offset, 'Наличие инвалидности у отца');
        $sheet->setCellValue('U' . $offset, 'Наличие инвалидности у матери');
        $sheet->setCellValue('V' . $offset, 'Категория');
        $sheet->setCellValue('W' . $offset, 'Наличие Пушкинской карты');
        $sheet->setCellValue('X' . $offset, 'Группа');

        $offset++;

        foreach ($socialPassport as $key => $value) {
            $sheet->setCellValue('A' . $offset, $increment);
            $sheet->setCellValue('B' . $offset, $value->last_name . ' ' . $value->first_name . ' ' . $value->middle_name);
            $sheet->setCellValue('C' . $offset, Carbon::parse($value->birthday)->format('d.m.Y'));
            $sheet->setCellValue('D' . $offset, $value->citizenship);
            $sheet->setCellValue('E' . $offset, $value->registered_address);
            $sheet->setCellValue('F' . $offset, $value->housing_conditions);
            $sheet->setCellValue('G' . $offset, implode(', ', json_decode($value->cohabitants, true)));
            $sheet->setCellValue('H' . $offset, ($value->marital_status) ? 'В браке' : 'Не в браке');
            $sheet->setCellValue('I' . $offset, $value->spouse_last_name . ' ' . $value->spouse_first_name . ' ' . $value->spouse_middle_name);
            $sheet->setCellValue('J' . $offset, $value->spouse_workplace);
            $sheet->setCellValue('K' . $offset, $value->spouse_phone);
            $sheet->setCellValue('L' . $offset, $value->family_composition);
            $sheet->setCellValue('M' . $offset, $value->number_of_children);
            $sheet->setCellValue('N' . $offset, $value->father_last_name . ' ' . $value->father_first_name . ' ' . $value->father_middle_name);
            $sheet->setCellValue('O' . $offset, $value->father_workplace);
            $sheet->setCellValue('P' . $offset, $value->father_phone);
            $sheet->setCellValue('Q' . $offset, $value->mother_last_name . ' ' . $value->mother_first_name . ' ' . $value->mother_middle_name);
            $sheet->setCellValue('R' . $offset, $value->mother_workplace);
            $sheet->setCellValue('S' . $offset, $value->mother_phone);
            $sheet->setCellValue('T' . $offset, ($value->father_disability) ? 'Да' : 'Нет');
            $sheet->setCellValue('U' . $offset, ($value->mother_disability) ? 'Да' : 'Нет');
            $sheet->setCellValue('V' . $offset, implode(', ', json_decode($value->child_categories, true)));
            $sheet->setCellValue('W' . $offset, ($value->has_pushkin_card) ? 'Да' : 'Нет');
            $sheet->setCellValue('X' . $offset, $value->group_name);

            $offset++;
            $increment++;
        }

        $sheet->getStyle('A1:X1')->getFont()->setBold(true);

        $sheet->getStyle('A1' . ':X' . $offset)->getAlignment()->setWrapText(true);

        $sheet->getStyle('A1:X1')
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('A1:A' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('C1:C' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('M1:M' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('P1:P' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('S1:S' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('T1:T' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('U1:U' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        $sheet->getStyle('W1:W' . ($offset - 1))
            ->applyFromArray(array('alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]));

        //Стиль для рамки
        $sheet->getStyle('A1:X' . ($offset - 1))
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray(array(
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '808080')
            ));

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(15);
        $sheet->getColumnDimension('M')->setWidth(15);
        $sheet->getColumnDimension('N')->setWidth(30);
        $sheet->getColumnDimension('O')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(15);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getColumnDimension('R')->setWidth(30);
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(20);
        $sheet->getColumnDimension('U')->setWidth(20);
        $sheet->getColumnDimension('V')->setWidth(20);
        $sheet->getColumnDimension('W')->setWidth(20);
        $sheet->getColumnDimension('X')->setWidth(15);

        $writer = new Xlsx($spreadsheet);

        $filename = time() . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);

        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
