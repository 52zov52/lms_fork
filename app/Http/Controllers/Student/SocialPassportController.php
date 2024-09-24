<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialPassportController extends Controller
{
    public static function getFio(): array
    {
        return UserData::select('last_name', 'first_name', 'middle_name', 'birthday')
            ->where('user_id', '=', Auth::user()->id)
            ->first()->toArray();
    }
}
