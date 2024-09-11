<?php

namespace App\Livewire\Student;

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Student\MyDisciplineController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyDisciplines extends Component
{
    public function render()
    {
        $myDisciplineController = new MyDisciplineController();
        $myDisciplines = $myDisciplineController->getDisciplinesForStudentInYear(Auth::user()->id);

        return view('livewire.student.my-disciplines', [
            'myDisciplines' => $myDisciplines,
            'academicYear' => AcademicYearController::getAcademicYear()
        ]);
    }
}
