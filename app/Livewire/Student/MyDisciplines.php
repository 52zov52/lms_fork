<?php

namespace App\Livewire\Student;

use App\Http\Controllers\Student\MyDisciplineController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyDisciplines extends Component
{
    public function render()
    {
        $myDisciplineController = new MyDisciplineController();
        $data = $myDisciplineController->getDisciplinesForStudentInYear(Auth::user()->id);

        dd($data);die();

        return view('livewire.student.my-disciplines');
    }
}
