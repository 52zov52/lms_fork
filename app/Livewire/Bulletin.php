<?php

namespace App\Livewire;

use App\Models\StudentGrades;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Bulletin extends Component
{
    public $bulletin_id = 0;

    public function mount($bulletin_id)
    {
        $this->bulletin_id = $bulletin_id;
    }

    public function render()
    {
        $studentGrades = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->where('student_grades.bulletin_id', '=', $this->bulletin_id)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = student_grades.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                'student_grades.grade'
            )
            ->get();

        return view('livewire.bulletin', ['studentGrades' => $studentGrades]);
    }
}
