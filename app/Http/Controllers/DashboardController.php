<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public $additionalInfoAboutUser = '';

    public function index()
    {
        if (Auth::user()->hasRole('Student')) {
            $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->where('group_student.user_id', '=', Auth::user()->id)
                ->select('groups.group_name')
                ->orderBy('group_student.id', 'desc')
                ->first();

            $this->additionalInfoAboutUser = $groupStudent->group_name;
        } else {
        }

        return view('dashboard', ['additionalInfoAboutUser' => $this->additionalInfoAboutUser]);
    }
}
