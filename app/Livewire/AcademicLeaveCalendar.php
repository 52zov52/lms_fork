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
    public $isEntryModalVisible = false;
    public $isEdit = false;

    public $id = 0;
    public $user_id = 0;
    public $order_id = 0;
    public $academic_leave_end_date = "";
    public $description = "";
    public $is_academic_leave = false;

    public $orders = [];

    public function render()
    {
        $this->orders = OrderUser::join('orders', 'order_user.order_id', '=', 'orders.id')
            ->join('order_types', 'orders.order_type_id', '=', 'order_types.id')
            ->where('order_user.user_id', '=', $this->user_id)
            ->select(
                'orders.id',
                'orders.order_date',
                DB::raw("CONCAT(order_types.order_type_name, ' №', orders.order_number, ' от ', DATE_FORMAT(orders.order_date, '%d.%m.%Y')) AS order_info")
            )
            ->get();
            
        $studentsAcademicLeaves = StudentsAcademicLeave::join('users_data', 'students_academic_leaves.user_id', '=', 'users_data.user_id')
            ->join('groups', 'students_academic_leaves.group_id', '=', 'groups.id')
            ->where('is_academic_leave', '=', 1)
            ->select(
                'students_academic_leaves.id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'users_data.user_id',
                'groups.id as group_id',
                'groups.group_name',
                'students_academic_leaves.academic_leave_end_date',
                'students_academic_leaves.description'
            )
            ->orderBy('students_academic_leaves.academic_leave_end_date')
            ->get();

        return view('livewire.academic-leave-calendar', [
            'studentsAcademicLeaves' => $studentsAcademicLeaves
        ]);
    }

    public function creatingAndEditingModalFormVisible($isEdit, $id = 0): void
    {
        $this->resetFields();

        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id);
        }

        $this->isEntryModalVisible = true;
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
    }

    public function setOrderId()
    {
        $this->order_id = $this->order_id;

        $order = Order::where('id', '=', $this->order_id)->select('order_date')->first();
        $this->academic_leave_end_date = Carbon::parse($order->order_date)->addYear()->format('d.m.Y');
    }

    public function store(): void
    {
        $this->validate([
            'order_id' => 'required|numeric|min:1',
            'academic_leave_end_date' => 'required',
            'description' => 'nullable|string|max:1500'
        ]);

        StudentsAcademicLeave::create([
            'academic_leave_end_date' => Carbon::parse($this->academic_leave_end_date)->format('Y-m-d'),
            'description' =>  $this->description,
            'is_academic_leave' => $this->isAcademicLeave,
        ]);

        $this->isEntryModalVisible = false;
        $this->resetFields();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function edit($id): void
    {
        $this->id = $id;

        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($this->id);
        $this->user_id = $studentsAcademicLeave->user_id;
        $this->order_id = $studentsAcademicLeave->order_id;
        $this->academic_leave_end_date = (empty($studentsAcademicLeave->academic_leave_end_date)) ? '' : Carbon::parse($studentsAcademicLeave->academic_leave_end_date)->format('d.m.Y');
        $this->description = $studentsAcademicLeave->description;
        $this->is_academic_leave = ($studentsAcademicLeave->is_academic_leave) ? true : false;
    }

    public function update(): void
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
        $this->resetFields();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function resetFields(): void
    {
        $this->id = 0;
        $this->user_id = 0;
        $this->order_id = 0;
        $this->academic_leave_end_date = '';
        $this->description = '';
        $this->is_academic_leave = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
