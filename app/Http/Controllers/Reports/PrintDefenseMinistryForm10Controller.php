<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;

class PrintDefenseMinistryForm10Controller extends Controller
{
    public function print($userId)
    {
        $userInfo = DB::table('users_data')
            ->leftJoin('users_addresses', 'users_data.user_id', '=', 'users_addresses.user_id')
            ->leftJoin('countries', 'users_addresses.country_id', '=', 'countries.id')
            ->leftJoin('regions', 'users_addresses.region_id', '=', 'regions.id')
            ->leftJoin('cities', 'users_addresses.city_id', '=', 'cities.id')
            ->leftJoin('city_types', 'cities.city_type_id', '=', 'city_types.id')
            ->leftJoin('districts', 'users_addresses.district_id', '=', 'districts.id')
            ->where('users_data.user_id', '=', $userId)
            ->select(
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'users_data.birthday',
                'users_data.passport_series',
                'users_data.passport_number',
                'users_data.passport_issue_date',
                'users_data.passport_issuing_authority',
                'countries.country_name',
                'regions.region_name',
                'city_types.name as city_type_name',
                'cities.city_name',
                'districts.district_name',
                'users_addresses.street',
                'users_addresses.building',
                'users_addresses.flat',
                'users_addresses.phone'
            )
            ->first();

        $city_type_name = "";
        switch ($userInfo->city_type_name) {
            case 'город':
                $city_type_name = "г.";
                break;
            case 'поселок':
                $city_type_name = "п.";
                break;
            case 'село':
                $city_type_name = "с.";
                break;
            default:
                $city_type_name = $userInfo->city_type_name;
                break;
        }

        $district_name = ($userInfo->district_name != "" && !is_null($userInfo->district_name)) ? $userInfo->district_name . ', ' : '';
        $street = ($userInfo->street != "" && !is_null($userInfo->street)) ? 'ул.' . $userInfo->street . ', ' : '';
        $building = ($userInfo->building != "" && !is_null($userInfo->building)) ? 'д.' . $userInfo->building . ', '  : '';
        $flat = ($userInfo->flat != "" && !is_null($userInfo->flat)) ? 'кв.' . $userInfo->flat . ', '  : '';

        $address = preg_replace('/,(\s*)$/', '$1', $userInfo->country_name . ', ' . $userInfo->region_name . ', ' . $city_type_name . $userInfo->city_name . ', ' . $district_name . $street . $building . $flat);

        $userEducationBackgound = DB::table('user_education_backgrounds')
            ->leftJoin('education_organizations', 'user_education_backgrounds.education_organization_id', '=', 'education_organizations.id')
            ->leftJoin('documents', 'user_education_backgrounds.document_id', '=', 'documents.id')
            ->leftJoin('type_educations', 'user_education_backgrounds.type_education_id', '=', 'type_educations.id')
            ->where('user_education_backgrounds.user_id', '=', $userId)
            ->select(
                'type_educations.type_name',
                'education_organizations.name',
                'documents.document_type',
                'user_education_backgrounds.document_series',
                'user_education_backgrounds.document_number',
                'user_education_backgrounds.document_issue_date',
                'user_education_backgrounds.end_year'
            )
            ->first();

        $userOrder = DB::table('order_user')
            ->join('orders', 'order_user.order_id', '=', 'orders.id')
            ->where('order_user.user_id', '=', $userId)
            ->where('orders.order_number', '<>', -1)
            ->select('orders.order_date', 'orders.order_number', 'orders.description')
            ->get();

        $path  = Storage::disk('public')->path('template/forma_10.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('last_name', $userInfo->last_name);
        $templateProcessor->setValue('first_name', $userInfo->first_name);
        $templateProcessor->setValue('middle_name', $userInfo->middle_name);
        $templateProcessor->setValue('birthday', Carbon::parse($userInfo->birthday)->format('d.m.Y'));
        $templateProcessor->setValue('passport_series', $userInfo->passport_series);
        $templateProcessor->setValue('passport_number', $userInfo->passport_number);
        $templateProcessor->setValue('passport_issue_date', Carbon::parse($userInfo->passport_issue_date)->format('d.m.Y'));
        $templateProcessor->setValue('passport_issuing_authority', $userInfo->passport_issuing_authority);
        $templateProcessor->setValue('type_name', $userEducationBackgound->type_name);
        $templateProcessor->setValue('name', $userEducationBackgound->name);
        $templateProcessor->setValue('seria_number', $userEducationBackgound->document_series . ' ' . $userEducationBackgound->document_number);
        $templateProcessor->setValue('end_year', $userEducationBackgound->end_year);
        $templateProcessor->setValue('address', $address);
        $templateProcessor->setValue('mobile_phone', $userInfo->phone);

        $tableFontHeaderStyle = array('name' => 'Times New Roman');
        $tableFontStyle = array('name' => 'Times New Roman');

        $table = new Table(array('borderSize' => 7, 'borderColor' => 'black', 'unit' => TblWidth::TWIP));
        $table->addRow();
        $table->addCell(2200)->addText('Дата и номер приказа (распоряжения)', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addCell(5500)->addText('Наименование должности, на которуюпринят (переведен) или с которой уволен', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addCell(2500)->addText('В какой военный комиссариат направлены сведения', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addCell(2500)->addText('Дата и исходящий номер сведений гражданина', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addRow();
        $table->addCell(2200)->addText('1', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addCell(5500)->addText('2', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addCell(2500)->addText('3', $tableFontHeaderStyle, array('align' => 'center'));
        $table->addCell(2500)->addText('4', $tableFontHeaderStyle, array('align' => 'center'));

        foreach ($userOrder as $key => $value) {
            $table->addRow();
            $table->addCell(2200)->addText(Carbon::parse($value->order_date)->format('d.m.Y') . ' № ' . $value->order_number, $tableFontStyle);
            $table->addCell(5500)->addText($value->description, $tableFontStyle);
            $table->addCell(2500)->addText("", $tableFontStyle);
            $table->addCell(2500)->addText("", $tableFontStyle);
        }

        $templateProcessor->setComplexBlock('educationBackgound', $table);

        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $templateProcessor->saveAs("php://output");
    }
}
