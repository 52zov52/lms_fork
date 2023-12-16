<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use App\Models\StudentsAcademicLeave;
use Carbon\Carbon;
use Livewire\Component;

class Contingent extends Component
{
    public int $groupId = 0;
    public $contingent = null;

    public $isCreatingAndEditingModalFormVisible = false;
    public $isEdit = false;

    public $id = 0;
    public $academicLeaveEndDate = "";
    public $description = "";
    public $isAcademicLeave = false;

    public function render()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $groups = Group::select('id', 'group_name')->whereIn('id', $groupId)->orderBy('group_name', 'asc')->get();
        $studentsAcademicLeaves = StudentsAcademicLeave::select(
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
            ->join('users_data', 'students_academic_leaves.user_id', '=', 'users_data.user_id')
            ->join('groups', 'students_academic_leaves.group_id', '=', 'groups.id')
            ->where('is_academic_leave', '=', 1)
            ->orderBy('students_academic_leaves.academic_leave_end_date')
            ->get();

        return view('livewire.contingent', [
            'groups' => $groups,
            'contingent' => $this->contingent,
            'studentsAcademicLeaves' => $studentsAcademicLeaves
        ]);
    }

    public function creatingAndEditingModalFormVisible($isEdit, $id = 0): void
    {
        $this->resetFields();

        $this->isEdit = $isEdit;

        if ($this->isEdit)
            $this->edit($id);

        $this->isCreatingAndEditingModalFormVisible = true;
    }

    public function store(): void
    {
        $this->validate([
            'academicLeaveEndDate' => 'required',
            'description' => 'string|max:1500'
        ]);

        StudentsAcademicLeave::create([
            'academic_leave_end_date' => Carbon::parse($this->academicLeaveEndDate)->format('Y-m-d'),
            'description' =>  $this->description,
            'is_academic_leave' => $this->isAcademicLeave,
        ]);

        $this->isCreatingAndEditingModalFormVisible = false;
        $this->resetFields();

        session()->flash('message-success', 'Запись успешно добавлена!');
    }

    public function edit($id): void
    {
        $this->id = $id;

        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($this->id);
        $this->academicLeaveEndDate = (empty($studentsAcademicLeave->academic_leave_end_date)) ? '' : Carbon::parse($studentsAcademicLeave->academic_leave_end_date)->format('d.m.Y');
        $this->description = $studentsAcademicLeave->description;
        $this->isAcademicLeave = ($studentsAcademicLeave->is_academic_leave) ? true : false;
    }

    public function update(): void
    {
        $this->validate([
            'academicLeaveEndDate' => 'required',
            'description' => 'string|max:1500'
        ]);

        $studentsAcademicLeave = StudentsAcademicLeave::findOrFail($this->id);
        $studentsAcademicLeave->academic_leave_end_date = Carbon::parse($this->academicLeaveEndDate)->format('Y-m-d');
        $studentsAcademicLeave->description = $this->description;
        $studentsAcademicLeave->is_academic_leave = ($this->isAcademicLeave) ? true : false;
        $studentsAcademicLeave->save();

        $this->isCreatingAndEditingModalFormVisible = false;
        $this->resetFields();

        session()->flash('message-success', 'Запись успешно отредактирована!');
    }

    public function getContingent(): void
    {
        $contingents = new ContingentController();
        $this->contingent = $contingents->getContingent($this->groupId);
    }

    public function redirectToDocumentsStudentsAcademicLeaves($user_id, $group_id)
    {
        return redirect()->route('documents.students.academic.leaves', ['user_id' => $user_id, 'group_id' => $group_id]);
    }

    public function resetFields(): void
    {
        $this->academicLeaveEndDate = '';
        $this->description = '';
        $this->isAcademicLeave = false;
    }
}
