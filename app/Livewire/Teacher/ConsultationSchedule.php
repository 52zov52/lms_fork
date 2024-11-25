<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Models\ConsultationSchedule as ModelsConsultationSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ConsultationSchedule extends Component
{
    use WithPagination;

    public $isCreateOrEditModalVisible = false;
    public $isEdit = false;

    //Переменные модели
    public $id = 0;
    public $userId = 0;
    public $disciplineId = 0;
    public $consultationDate = '';
    public $consultationTime = '';
    public $room = '';
    public $groups = [];
    public $academicYear = 0;
    public $halfYear = 0;
    public $description = '';

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

    public $listOfGroups = [];

    public function render()
    {
        $listOfDisciplines = DB::table('group_study_thread_subgroup')
            ->join('study_thread_subgroups', 'group_study_thread_subgroup.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('study_thread_subgroup_user', 'study_thread_subgroups.id', '=', 'study_thread_subgroup_user.study_thread_subgroup_id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('chair_discipline', 'study_threads.chair_discipline_id', '=', 'chair_discipline.id')
            ->join('disciplines', 'chair_discipline.discipline_id', '=', 'disciplines.id')
            ->where('study_thread_subgroup_user.user_id', Auth::user()->id)
            ->where('study_threads.year', '=', AcademicYearController::getYear())
            ->where('study_threads.semester', '=', AcademicYearController::getHalfYear())
            ->select(
                'disciplines.id',
                'disciplines.name',
            )
            ->distinct()
            ->orderBy('disciplines.name')
            ->get();

        $consultationSchedules = ModelsConsultationSchedule::join('disciplines', 'consultation_schedules.discipline_id', '=', 'disciplines.id')
            ->where('consultation_schedules.user_id', '=', Auth::user()->id)
            ->select(
                'consultation_schedules.id',
                'disciplines.name',
                'consultation_schedules.consultation_date',
                'consultation_schedules.consultation_time',
                'consultation_schedules.room',
                'consultation_schedules.groups',
                'consultation_schedules.academic_year',
                'consultation_schedules.half_year',
                'consultation_schedules.description'
            )
            ->paginate(50);

        return view('livewire.teacher.consultation-schedule', [
            'listOfDisciplines' => $listOfDisciplines,
            'listOfLessonTime' => $this->listOfLessonTime,
            'listOfRooms' => $this->listOfRooms,
            'consultationSchedules' => $consultationSchedules
        ]);
    }

    public function setDisciplineId()
    {
        $this->groups = [];
        $this->disciplineId = $this->disciplineId;
        $this->listOfGroups = $this->getGroups();
    }

    private function getGroups()
    {
        return DB::table('group_study_thread_subgroup')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('study_thread_subgroups', 'group_study_thread_subgroup.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('chair_discipline', 'study_threads.chair_discipline_id', '=', 'chair_discipline.id')
            ->where('chair_discipline.discipline_id', '=', $this->disciplineId)
            ->where('study_threads.year', '=', AcademicYearController::getYear())
            ->where('study_threads.semester', '=', AcademicYearController::getHalfYear())
            ->select(
                'groups.id',
                'groups.group_name',
            )
            ->distinct()
            ->orderBy('groups.group_name')
            ->get();
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

        $consultationSchedule = ModelsConsultationSchedule::findOrFail($id);
        $this->id = $id;
        $this->userId = $consultationSchedule->user_id;
        $this->disciplineId = $consultationSchedule->discipline_id;
        $this->consultationDate = $consultationSchedule->consultation_date;
        $this->consultationTime = $consultationSchedule->consultation_time;
        $this->room = $consultationSchedule->room;
        $this->groups = json_decode($consultationSchedule->groups);
        $this->academicYear = $consultationSchedule->academic_year;
        $this->halfYear = $consultationSchedule->half_year;
        $this->description = $consultationSchedule->description;

        $this->isEdit = true;
        $this->isCreateOrEditModalVisible = true;
    }

    public function store()
    {
        $this->validate([
            'disciplineId' => 'required|min:1',
            'consultationDate' => 'required|date_format:Y-m-d',
            'consultationTime' => 'required|string',
            'room' => 'required|string',
            'groups' => 'required|array',
            'description' => 'nullable|string|max:1500',
        ]);

        ModelsConsultationSchedule::create([
            'user_id' => Auth::user()->id,
            'discipline_id' => $this->disciplineId,
            'consultation_date' => Carbon::parse($this->consultationDate)->format('Y-m-d'),
            'consultation_time' => $this->consultationTime,
            'room' => $this->room,
            'groups' => $this->groups,
            'academic_year' => AcademicYearController::getAcademicYear(),
            'half_year' => AcademicYearController::getHalfYear(),
            'description' => $this->description,
        ]);

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно добавлена!', type: 'success');
    }

    public function update()
    {
        $this->validate([
            'disciplineId' => 'required|min:1',
            'consultationDate' => 'required|date_format:Y-m-d',
            'consultationTime' => 'required|string',
            'room' => 'required|string',
            'groups' => 'required|array',
            'description' => 'nullable|string|max:1500',
        ]);

        $consultationSchedule = ModelsConsultationSchedule::findOrFail($this->id);
        $consultationSchedule->discipline_id = $this->disciplineId;
        $consultationSchedule->consultation_date = Carbon::parse($this->consultationDate)->format('Y-m-d');
        $consultationSchedule->consultation_time = $this->consultationTime;
        $consultationSchedule->room = $this->room;
        $consultationSchedule->groups = json_encode($this->groups);
        $consultationSchedule->description = $this->description;
        $consultationSchedule->update();

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }

    #[On('destroy')]
    public function destroy($id)
    {
        $consultationSchedule = ModelsConsultationSchedule::findOrFail($id);
        $consultationSchedule->delete();

        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->userId = 0;
        $this->disciplineId = 0;
        $this->consultationDate = '';
        $this->consultationTime = '';
        $this->room = '';
        $this->groups = [];
        $this->academicYear = 0;
        $this->halfYear = 0;
        $this->description = '';

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
