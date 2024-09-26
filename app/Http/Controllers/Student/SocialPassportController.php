<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserData;
use Illuminate\Support\Facades\DB;

class SocialPassportController extends Controller
{
    public static function getSignature($userId): string
    {
        $userData = UserData::select(DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"))
            ->where('user_id', '=', $userId)->first();

        return hash('sha256', $userData->fio . now()->format('Y-m-d'));
    }
}
