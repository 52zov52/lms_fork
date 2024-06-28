<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderUser;
use App\Models\StudentsAcademicLeave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AcademicLeaveCalendar extends Component
{
    public $id = 0,
        $user_id = 0,
        $order_id = 0,
        $academic_leave_end_date = "",
        $description = "",
        $is_academic_leave = false;

    public $orders = [];

    public $isEntryModalVisible = false, $isEdit = false;

    public function render()
    {
        if ($this->user_id != 0) {
            $this->orders = OrderUser::join('orders', 'order_user.order_id', '=', 'orders.id')
                ->join('order_types', 'orders.order_type_id', '=', 'order_types.id')
                ->where('order_user.user_id', '=', $this->user_id)
                ->select(
                    'orders.id',
                    'orders.order_date',
                    DB::raw("CONCAT(order_types.order_type_name, ' №', orders.order_number, ' от ', DATE_FORMAT(orders.order_date, '%d.%m.%Y')) AS info")
                )
                ->get();
        }

        $data = StudentsAcademicLeave::join('users_data', 'students_academic_leaves.user_id', '=', 'users_data.user_id')
            ->join('groups', 'students_academic_leaves.group_id', '=', 'groups.id')
            ->where('is_academic_leave', '=', 1)
            ->select(
                'students_academic_leaves.id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'users_data.user_id',
                'groups.id as group_id',
                'groups.group_name',
                'students_academic_leaves.order_id',
                DB::raw("CASE WHEN students_academic_leaves.academic_leave_end_date IS NOT NULL THEN DATE_FORMAT(students_academic_leaves.academic_leave_end_date, '%d.%m.%Y') ELSE 'Отсутствует' END as academic_leave_end_date"),
                'students_academic_leaves.description'
            )
            ->orderBy('students_academic_leaves.academic_leave_end_date')
            ->paginate(50);

        return view('livewire.academic-leave-calendar', ['data' => $data]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function setEntryModalVisible($isEdit, $id = null, $userId = null, $orderId = null): void
    {
        $this->resetFields();
        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->order_id = (is_null($orderId)) ? 0 : $orderId;
            $this->user_id = $userId;
            $this->edit($id);
        }

        $this->isEntryModalVisible = !$this->isEntryModalVisible;
    }

    public function setOrderId()
    {
        $this->order_id = $this->order_id;

        $order = Order::where('id', '=', $this->order_id)->select('order_date')->first();
        $this->academic_leave_end_date = Carbon::parse($order->order_date)->addYear()->format('d.m.Y');
    }

    private function edit($id)
    {
        $this->id = $id;

        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($this->id);
        $this->academic_leave_end_date = (empty($studentsAcademicLeave->academic_leave_end_date)) ? '' : Carbon::parse($studentsAcademicLeave->academic_leave_end_date)->format('d.m.Y');
        $this->description = $studentsAcademicLeave->description;
        $this->is_academic_leave = ($studentsAcademicLeave->is_academic_leave) ? true : false;
    }

    public function update()
    {
        $this->validate([
            'order_id' => 'required|numeric|min:1',
            'academic_leave_end_date' => 'required',
            'description' => 'nullable|string|max:1500'
        ]);

        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($this->id);
        $studentsAcademicLeave->order_id = $this->order_id;
        $studentsAcademicLeave->academic_leave_end_date = Carbon::parse($this->academic_leave_end_date)->format('Y-m-d');
        $studentsAcademicLeave->description = $this->description;
        $studentsAcademicLeave->is_academic_leave = ($this->is_academic_leave) ? true : false;
        $studentsAcademicLeave->save();

        $this->isEntryModalVisible = false;

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->user_id = 0;
        $this->order_id = 0;
        $this->academic_leave_end_date = "";
        $this->description = "";
        $this->is_academic_leave = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
