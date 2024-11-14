<?php

namespace App\Livewire\Student;

use App\Http\Controllers\AcademicYearController;
use App\Models\AcademicRetakeSchedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AcademicRetakeScheduleList extends Component
{
    use WithPagination;

    public function render()
    {
        $academicRetakeSchedules = AcademicRetakeSchedule::join('users_data', 'academic_retake_schedules.user_id', '=', 'users_data.user_id')
            ->where('academic_retake_schedules.academic_year', '=', AcademicYearController::getAcademicYear())
            ->select(
                'academic_retake_schedules.id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'academic_retake_schedules.retake_date',
                'academic_retake_schedules.retake_time',
                'academic_retake_schedules.room',
                'academic_retake_schedules.academic_year',
                'academic_retake_schedules.half_year'
            )
            ->paginate(50);

        return view('livewire.student.academic-retake-schedule-list', [
            'academicRetakeSchedules' => $academicRetakeSchedules,
        ]);
    }
}
