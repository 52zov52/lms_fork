<?php

namespace App\Livewire;

use App\Http\Controllers\AcademicYearController;
use App\Models\UpbringingEvent;
use App\Models\UpbringingEventReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UpbringingManagement extends Component
{
    use WithPagination;

    public $id = 0,
        $name = '',
        $description = '',
        $date = '',
        $academicYear = '',
        $halfYear = 0,
        $userId = 0;

    public $showModal = false, $isEdit = false;

    public $listOfUsers = [
        23425 => 'Абдуллаева Афсана Магоммедовна',
        2656 => 'Агаркова Наталья Юрьевна',
        24205 => 'Акперов Гурру Имран-оглы',
        24940 => 'Акперов Теймур Имран оглы',
        27456 => 'Акперова Марзия Гади кызы',
        20383 => 'Алекперов Ильгар Джаби оглы',
        19355 => 'Алекперова Эльмира Ахмедовна',
        24191 => 'Артамонова Анна Геннадьевна',
        28704 => 'Балатова Умгат Абдулварисовна',
        13693 => 'Батищев Денис Романович',
        27421 => 'Блохина Анжелика Юрьевна',
        25694 => 'Вовчук Инна Анатольевна',
        22781 => 'Галоян Яна Эдуардовна',
        18624 => 'Григорьева Наталья Станиславовна',
        5290 => 'Жданова Елена Юрьевна',
        23421 => 'Завацкая Ксения Вячеславовна',
        27327 => 'Кабаненко Михаил Николаевич',
        26960 => 'Козлова Евгения Анатольевна',
        19077 => 'Коренякина Наталья Николаевна',
        3422 => 'Кузнецова Светлана Михайловна',
        20352 => 'Максименко Алина Андреевна',
        23654 => 'Мартынов Борис Викторович',
        24174 => 'Махотенко Марина Александровна',
        27414 => 'Мизюков Григорий Сергеевич',
        19356 => 'Мурадова Сафура Шиховна',
        28702 => 'Прудан Ирина Георгиевна',
        24178 => 'Семерентьева Марианна Алексеевна',
        18717 => 'Сенина Юлия Евгеньевна',
        23655 => 'Тлепцеришева Сюзанна Аслановна',
        24213 => 'Фоменко Андрей Иванович',
        20397 => 'Шевердина Ирина Викторовна',
        27598 => 'Шкаев Димитрий Геннадиевич',
        24829 => 'Шпилевская Елена Вячеславовна'
    ];

    public function render()
    {
        $upbringingEvents = UpbringingEvent::leftJoin('users_data', 'upbringing_events.user_id', '=', 'users_data.user_id')
            ->join('upbringing_event_reports', 'upbringing_events.id', '=', 'upbringing_event_reports.upbringing_event_id')
            ->select(
                'upbringing_events.id',
                'upbringing_events.name',
                'upbringing_events.description',
                'upbringing_events.date',
                'upbringing_events.academic_year',
                'upbringing_events.half_year',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'upbringing_event_reports.link_to_news',
                'upbringing_event_reports.file_path'
            )
            ->paginate(50);

        return view('livewire.upbringing-management', ['upbringingEvents' => $upbringingEvents]);
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
    }

    public function modalVisible($isEdit, $id = null)
    {
        $this->resetFields();
        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id);
        } else {
            $this->academicYear = AcademicYearController::getAcademicYear();
        }

        $this->showModal = !$this->showModal;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:1500',
            'date' => 'required',
            'academicYear' => 'required|string',
            'halfYear' => 'required|numeric|min:1',
            'userId' => 'required|numeric|min:1'
        ]);

        $upbringingEvent = UpbringingEvent::create([
            'name' => $this->name,
            'description' => $this->description,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'academic_year' => $this->academicYear,
            'half_year' => $this->halfYear,
            'user_id' => $this->userId
        ]);

        UpbringingEventReport::create([
            'upbringing_event_id' => $upbringingEvent->id,
            'status' => 1
        ]);

        $this->modalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function edit($id)
    {
        $upbringingEvent = UpbringingEvent::findOrfail($id);
        $this->id = $id;
        $this->name = $upbringingEvent->name;
        $this->description = $upbringingEvent->description;
        $this->date = Carbon::parse($upbringingEvent->date)->format('d.m.Y');
        $this->academicYear = $upbringingEvent->academic_year;
        $this->halfYear = $upbringingEvent->half_year;
        $this->userId = $upbringingEvent->user_id;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:1500',
            'date' => 'required',
            'academicYear' => 'required|string',
            'halfYear' => 'required|numeric|min:1',
            'userId' => 'required|numeric|min:1'
        ]);

        $upbringingEvent = UpbringingEvent::findOrfail($this->id);
        $upbringingEvent->name = $this->name;
        $upbringingEvent->description = $this->description;
        $upbringingEvent->date = Carbon::parse($this->date)->format('Y-m-d');
        $upbringingEvent->academic_year = $this->academicYear;
        $upbringingEvent->half_year = $this->halfYear;
        $upbringingEvent->user_id = $this->userId;
        $upbringingEvent->update();

        $this->modalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function destroy($id)
    {
        $UpbringingEvent = UpbringingEvent::findOrFail($id);
        $UpbringingEvent->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->name = '';
        $this->description = '';
        $this->date = '';
        $this->academicYear = '';
        $this->halfYear = '';
        $this->userId = 0;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
