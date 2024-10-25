<?php

namespace App\Livewire\Employee;

use App\Models\Order;
use App\Models\OrderUser;
use App\Models\StudentsAcademicLeave;
use Carbon\Carbon;
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
        if ($this->userId != 0) {
            $this->orders = OrderUser::join('orders', 'order_user.order_id', '=', 'orders.id')
                ->join('order_types', 'orders.order_type_id', '=', 'order_types.id')
                ->where('order_user.user_id', '=', $this->userId)
                ->select(
                    'orders.id',
                    'orders.order_date',
                    DB::raw("CONCAT(order_types.order_type_name, ' №', orders.order_number, ' от ', DATE_FORMAT(orders.order_date, '%d.%m.%Y')) AS info")
                )
                ->get();
        }

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

    public function setOrderId()
    {
        $this->orderId = $this->orderId;

        $order = Order::where('id', '=', $this->orderId)->select('order_date')->first();
        $this->academicLeaveEndDate = Carbon::parse($order->order_date)->addYear()->format('Y-m-d');
    }

    public function edit($id, $orderId = null)
    {
        $this->resetFields();
        
        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($id);
        $this->id = $id;
        $this->userId = $studentsAcademicLeave->user_id;
        $this->orderId = (is_null($orderId)) ? 0 : $orderId;
        $this->academicLeaveEndDate = (empty($studentsAcademicLeave->academic_leave_end_date)) ? '' : $studentsAcademicLeave->academic_leave_end_date;
        $this->description = $studentsAcademicLeave->description;
        $this->isAcademicLeave = ($studentsAcademicLeave->is_academic_leave) ? true : false;

        $this->isShowModalDialog = true;
    }

    public function update()
    {
        $this->validate([
            'orderId' => 'required|numeric|min:1',
            'academicLeaveEndDate' => 'required',
            'description' => 'nullable|string|max:1500'
        ]);

        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($this->id);
        $studentsAcademicLeave->order_id = $this->orderId;
        $studentsAcademicLeave->academic_leave_end_date = Carbon::parse($this->academicLeaveEndDate)->format('Y-m-d');
        $studentsAcademicLeave->description = $this->description;
        $studentsAcademicLeave->is_academic_leave = ($this->isAcademicLeave) ? true : false;
        $studentsAcademicLeave->save();

        $this->isShowModalDialog = false;
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->userId = 0;
        $this->orderId = 0;
        $this->academicLeaveEndDate = '';
        $this->description = "";
        $this->isAcademicLeave = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
