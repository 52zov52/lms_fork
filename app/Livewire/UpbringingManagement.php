<?php

namespace App\Livewire;

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Report\UvrController;
use App\Models\UpbringingEvent;
use App\Models\UpbringingEventReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UpbringingManagement extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $id = 0,
        $category,
        $name = '',
        $description = '',
        $date = '',
        $academicYear = '',
        $halfYear = 0,
        $userId = 0,
        $file_path = '',
        $file = null;

    public string $filename = '';
    public int $rand = 1;

    public $showModal = false, $isEdit = false;

    public $listOfUsers = [
        14733 => 'Абдуллаева Афсана Магоммедовна',
        2656 => 'Агаркова Наталья Юрьевна',
        15318 => 'Акперов Гурру Имран-оглы',
        24940 => 'Акперов Теймур Имран оглы',
        2347 => 'Акперова Марзия Гади кызы',
        8399 => 'Алекперова Эльмира Ахмедовна',
        24191 => 'Артамонова Анна Геннадьевна',
        28704 => 'Балатова Умгат Абдулварисовна',
        13693 => 'Батищев Денис Романович',
        27421 => 'Блохина Анжелика Юрьевна',
        25694 => 'Вовчук Инна Анатольевна',
        2241 => 'Галоян Яна Эдуардовна',
        18624 => 'Григорьева Наталья Станиславовна',
        28674 => 'Дынник Дмитрий Игоревич',
        19077 => 'Коренякина Наталья Николаевна',
        3422 => 'Кузнецова Светлана Михайловна',
        2574 => 'Мартынов Борис Викторович',
        12474 => 'Махотенко Марина Александровна',
        27414 => 'Мизюков Григорий Сергеевич',
        17809 => 'Мурадова Сафура Шиховна',
        23377 => 'Семерентьева Марианна Алексеевна',
        18717 => 'Сенина Юлия Евгеньевна',
        23655 => 'Тлепцеришева Сюзанна Аслановна',
        2447 => 'Фоменко Андрей Иванович',
        19952 => 'Шевердина Ирина Викторовна',
        24829 => 'Шпилевская Елена Вячеславовна'
    ];

    public $listOfCategories = [
        1 => 'Федеральные',
        2 => 'Региональные',
        3 => 'В рамках академии'
    ];

    public function render()
    {
        $upbringingEvents = UpbringingEvent::leftJoin('users_data', 'upbringing_events.user_id', '=', 'users_data.user_id')
            ->join('upbringing_event_reports', 'upbringing_events.id', '=', 'upbringing_event_reports.upbringing_event_id')
            ->select(
                'upbringing_events.id',
                'upbringing_events.category',
                'upbringing_events.name',
                'upbringing_events.description',
                'upbringing_events.date',
                'upbringing_events.academic_year',
                'upbringing_events.half_year',
                'upbringing_events.file_path as event_file',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'upbringing_event_reports.link_to_news',
                'upbringing_event_reports.file_path'
            )
            ->orderBy('upbringing_events.date', 'asc')
            ->paginate(50);

        return view('livewire.upbringing-management', [
            'upbringingEvents' => $upbringingEvents,
            'listOfCategories' => $this->listOfCategories
        ]);
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
        if (!is_null($this->file)) {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'name' => 'required|string|max:1500',
                'date' => 'required',
                'academicYear' => 'required|string',
                'halfYear' => 'required|numeric|min:1',
                'userId' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png,zip|max:3072'
            ]);
        } else {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'name' => 'required|string|max:1500',
                'date' => 'required',
                'academicYear' => 'required|string',
                'halfYear' => 'required|numeric|min:1',
                'userId' => 'required|numeric|min:1'
            ]);
        }

        $filePath = (!is_null($this->file)) ? $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public') : null;

        $upbringingEvent = UpbringingEvent::create([
            'category' => $this->category,
            'name' => $this->name,
            'description' => $this->description,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'academic_year' => $this->academicYear,
            'half_year' => $this->halfYear,
            'user_id' => $this->userId,
            'file_path' =>  $filePath,
            'filename' => (!is_null($this->file)) ? $this->file->getClientOriginalName() : null
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
        $this->category = $upbringingEvent->category ?? 0;
        $this->name = $upbringingEvent->name;
        $this->description = $upbringingEvent->description;
        $this->date = $upbringingEvent->date;
        $this->academicYear = $upbringingEvent->academic_year;
        $this->halfYear = $upbringingEvent->half_year;
        $this->userId = $upbringingEvent->user_id;
        $this->file_path = $upbringingEvent->file_path;

        if (!is_null($upbringingEvent->file_path)) {
            $data = explode('/', $upbringingEvent->file_path);
            $this->filename = $data[2];
        }
    }

    public function update()
    {
        if (!is_null($this->file)) {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'name' => 'required|string|max:1500',
                'date' => 'required',
                'academicYear' => 'required|string',
                'halfYear' => 'required|numeric|min:1',
                'userId' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png,zip|max:3072'
            ]);
        } else {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'name' => 'required|string|max:1500',
                'date' => 'required',
                'academicYear' => 'required|string',
                'halfYear' => 'required|numeric|min:1',
                'userId' => 'required|numeric|min:1'
            ]);
        }

        $upbringingEvent = UpbringingEvent::findOrfail($this->id);
        $upbringingEvent->category = $this->category;
        $upbringingEvent->name = $this->name;
        $upbringingEvent->description = $this->description;
        $upbringingEvent->date = Carbon::parse($this->date)->format('Y-m-d');
        $upbringingEvent->academic_year = $this->academicYear;
        $upbringingEvent->half_year = $this->halfYear;
        $upbringingEvent->user_id = $this->userId;

        if (!is_null($this->file)) {
            if (!is_null($upbringingEvent->file_path)) {
                unlink(storage_path('app/public/' . $upbringingEvent->file_path));
            }

            $filePath = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
            $upbringingEvent->file_path = $filePath;
            $upbringingEvent->filename = $this->file->getClientOriginalName();
        }

        $upbringingEvent->update();

        $this->modalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function destroy($id)
    {
        $UpbringingEvent = UpbringingEvent::findOrFail($id);

        if (!is_null($UpbringingEvent->file_path)) {
            unlink(storage_path('app/public/' . $UpbringingEvent->file_path));
        }

        $UpbringingEvent->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    public function downloadReport()
    {
        $uvrReport = new UvrController();
        $data = $uvrReport->downloadReport();
        
        return response()->download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->category = 0;
        $this->name = '';
        $this->description = '';
        $this->date = '';
        $this->academicYear = '';
        $this->halfYear = '';
        $this->userId = 0;
        $this->file = null;

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
