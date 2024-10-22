<?php

namespace App\Livewire\Employee;

use App\Models\StudentsAcademicLeave;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StudentAcademicLeaveCalendar extends Component
{
    use WithPagination;

    public bool $isShowModalDialog = false;

    public $id = 0,
        $userId = 0,
        $orderId = 0,
        $academicLeaveEndDate = '',
        $description = '',
        $isAcademicLeave = false;

    public $orders = [];

    public function render()
    {
        $studentsAcademicLeave = StudentsAcademicLeave::join('users_data', 'students_academic_leaves.user_id', '=', 'users_data.user_id')
            ->join('groups', 'students_academic_leaves.group_id', '=', 'groups.id')
            ->leftJoin('orders', 'students_academic_leaves.order_id', '=', 'orders.id')
            ->leftJoin('order_types', 'orders.order_type_id', '=', 'order_types.id')
            ->where('is_academic_leave', '=', 1)
            ->select(
                'students_academic_leaves.id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("CASE WHEN students_academic_leaves.order_id IS NOT NULL THEN CONCAT(order_types.order_type_name, ' №', orders.order_number, ' от ', DATE_FORMAT(orders.order_date, '%d.%m.%Y')) ELSE 'Не выбран' END AS order_info"),
                'users_data.user_id',
                'groups.id as group_id',
                'groups.group_name',
                'students_academic_leaves.order_id',
                DB::raw("CASE WHEN students_academic_leaves.academic_leave_end_date IS NOT NULL THEN DATE_FORMAT(students_academic_leaves.academic_leave_end_date, '%d.%m.%Y') ELSE 'Отсутствует' END AS academic_leave_end_date"),
                'students_academic_leaves.description'
            )
            ->orderBy('students_academic_leaves.academic_leave_end_date')
            ->paginate(50);

        return view('livewire.employee.student-academic-leave-calendar', [
            'studentsAcademicLeave' => $studentsAcademicLeave
        ]);
    }

    public function edit($id) {}

    public function update()
    {
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }
}
