<?php

namespace App\Livewire\Student;

use App\Http\Controllers\Student\MyGradeController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyGrades extends Component
{
    public function render()
    {
        $myGradeController = new MyGradeController();
        $myGrades = $myGradeController->getMyGrades(Auth::user()->id);

        return view('livewire.student.my-grades', ['myGrades' => $myGrades]);
    }
}
