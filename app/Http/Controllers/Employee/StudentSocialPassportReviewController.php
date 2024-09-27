<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SocialPassport;
use App\Models\UserData;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class StudentSocialPassportReviewController extends Controller
{
    public static function getSignature($userId): string
    {
        $userData = UserData::select(DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"))
            ->where('user_id', '=', $userId)->first();

        return hash('sha256', $userData->fio . now()->format('Y-m-d'));
    }

    public function downloadStudentSocialPassport($userId)
    {
        $cohabitants = ['Мать', 'Отец', 'Бабушка', 'Дедушка', 'Опекун', 'Проживаю самостоятельно', 'Другое'];

        $childCategories = ['Дети-инвалиды', 'Сироты', 'Дети под опекой', 'Детей участников СВО'];

        $socialPassport = SocialPassport::where('user_id', '=', $userId)->first();

        $filename = time() . '.pdf';
        $pdf = PDF::loadView('reports.pdf.student-social-passport', [
            'socialPassport' => $socialPassport,
            'cohabitants' => $cohabitants,
            'childCategories' => $childCategories
        ]);

        return $pdf->download($filename);
    }
}
