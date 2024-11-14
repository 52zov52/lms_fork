<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Models\AcademicRetakeSchedule as ModelsAcademicRetakeSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AcademicRetakeSchedule extends Component
{
    use WithPagination;

    public $isCreateOrEditModalVisible = false;
    public $isEdit = false;

    public $id = 0;
    public $userId = 0;
    public $retakeDate = '';
    public $retakeTime = '';
    public $room = '';
    public $academicYear = 0;
    public $halfYear = 0;

    public $listOfLessonTime = [
        '08:20-09:50',
        '10:00-11:30',
        '11:40-13:10',
        '13:30-15:00',
        '15:10-16:40',
        '17:00-18:30',
        '18:40-20:10',
        '20:20-21:50'
    ];

    public $listOfRooms = [
        '201',
        '202',
        '214',
        '219 (Акт. зал)',
        '303',
        '304',
        '315',
        '401',
        '403',
        '406',
        '407',
        '408',
        '409',
        '501',
        '503',
        '506',
        '507',
        '509',
        '510',
        '601',
        '602',
        '604',
        '608',
        '609'
    ];

    public function render()
    {
        $academicRetakeSchedules = ModelsAcademicRetakeSchedule::where('user_id', '=', Auth::user()->id)
            ->paginate(50);

        return view('livewire.teacher.academic-retake-schedule', [
            'academicRetakeSchedules' => $academicRetakeSchedules,
            'listOfLessonTime' => $this->listOfLessonTime,
            'listOfRooms' => $this->listOfRooms
        ]);
    }

    public function show()
    {
        $this->resetFields();
        $this->isEdit = false;
        $this->isCreateOrEditModalVisible = true;
    }

    public function edit($id)
    {
        $this->resetFields();
        $this->isEdit = true;

        $academicRetakeSchedule = ModelsAcademicRetakeSchedule::findOrFail($id);
        $this->id = $id;
        $this->retakeDate = Carbon::parse($academicRetakeSchedule->retake_date)->format('Y-m-d');
        $this->retakeTime = $academicRetakeSchedule->retake_time;
        $this->room = $academicRetakeSchedule->room;
        $this->academicYear = $academicRetakeSchedule->academic_year;
        $this->halfYear = $academicRetakeSchedule->half_year;

        $this->isCreateOrEditModalVisible = true;
    }

    public function store()
    {
        $this->validate([
            'retakeDate' => 'required',
            'retakeTime' => 'required',
            'room' => 'required'
        ]);

        $conflict = $this->checkRetakeScheduleConflict(Carbon::parse($this->retakeDate)->format('Y-m-d'), $this->retakeTime, $this->room, AcademicYearController::getAcademicYear(), AcademicYearController::getHalfYear());

        if (count($conflict) != 0) {
            $this->dispatch('show-toast', message: 'На указанные вами дату, время и аудиторию уже назначена пересдача. Пожалуйста, укажите другие значения.', type: 'error');
        } else {
            $academicRetakeSchedule = new ModelsAcademicRetakeSchedule();
            $academicRetakeSchedule->user_id = Auth::user()->id;
            $academicRetakeSchedule->retake_date = Carbon::parse($this->retakeDate)->format('Y-m-d');
            $academicRetakeSchedule->retake_time = $this->retakeTime;
            $academicRetakeSchedule->room = $this->room;
            $academicRetakeSchedule->academic_year = AcademicYearController::getAcademicYear();
            $academicRetakeSchedule->half_year = AcademicYearController::getHalfYear();
            $academicRetakeSchedule->save();

            $this->isCreateOrEditModalVisible = false;
            $this->dispatch('show-toast', message: 'Запись успешно добавлена!', type: 'success');
        }
    }

    public function update()
    {
        $this->validate([
            'retakeDate' => 'required',
            'retakeTime' => 'required',
            'room' => 'required'
        ]);

        $conflict = $this->checkRetakeScheduleConflict(Carbon::parse($this->retakeDate)->format('Y-m-d'), $this->retakeTime, $this->room, $this->academicYear, $this->halfYear);

        if (count($conflict) != 0) {
            $this->dispatch('show-toast', message: 'На указанные вами дату, время и аудиторию уже назначена пересдача. Пожалуйста, укажите другие значения.', type: 'error');
        } else {
            $academicRetakeSchedule = ModelsAcademicRetakeSchedule::findOrFail($this->id);
            $academicRetakeSchedule->retake_date = Carbon::parse($this->retakeDate)->format('Y-m-d');
            $academicRetakeSchedule->retake_time = $this->retakeTime;
            $academicRetakeSchedule->room = $this->room;
            $academicRetakeSchedule->update();

            $this->isCreateOrEditModalVisible = false;
            $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
        }
    }

    #[On('destroy')]
    public function destroy($id)
    {
        $academicRetakeSchedule = ModelsAcademicRetakeSchedule::findOrFail($id);
        $academicRetakeSchedule->delete();

        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }

    private function checkRetakeScheduleConflict($retakeDate, $retakeTime, $room, $academicYear, $halfYear)
    {
        return ModelsAcademicRetakeSchedule::where('retake_date', '=', Carbon::parse($retakeDate)->format('Y-m-d'))
            ->where('retake_time', '=', $retakeTime)
            ->where('room', '=', $room)
            ->where('academic_year', '=', $academicYear)
            ->where('half_year', '=', $halfYear)
            ->get();
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->userId = 0;
        $this->retakeDate = '';
        $this->retakeTime = '';
        $this->room = '';
        $this->academicYear = 0;
        $this->halfYear = 0;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
