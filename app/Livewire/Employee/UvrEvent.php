<?php

namespace App\Livewire\Employee;

use App\Models\UpbringingEvent;
use App\Models\UpbringingEventReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class UvrEvent extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $isEditModalVisible = false;
    public bool $isDescriptionModalVisible = false;

    public int $id = 0;
    public $description = null;
    public $linkToNews = null;

    public $file = null;
    public $filePath = null;
    public string $filename = '';
    public int $rand = 1;

    public function render()
    {
        $data = $this->getData();
        return view('livewire.employee.uvr-event', ['data' => $data,]);
    }

    public function getData(): object
    {
        return UpbringingEvent::leftJoin('users_data', 'upbringing_events.user_id', '=', 'users_data.user_id')
            ->join('upbringing_event_reports', 'upbringing_events.id', '=', 'upbringing_event_reports.upbringing_event_id')
            ->where('upbringing_events.user_id', '=', Auth::user()->id)
            ->select(
                'upbringing_events.id',
                'upbringing_events.category',
                'upbringing_events.name',
                'upbringing_events.date',
                'upbringing_events.academic_year',
                'upbringing_events.half_year',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'upbringing_event_reports.link_to_news',
                'upbringing_event_reports.file_path'
            )
            ->orderBy('upbringing_events.date', 'asc')
            ->paginate(50);
    }

    public function edit($id)
    {
        $this->resetFields();

        $upbringingEventReport = UpbringingEventReport::findOrFail($id);
        $this->id = $id;
        $this->linkToNews = $upbringingEventReport->link_to_news;
        $this->filePath = $upbringingEventReport->file_path;

        if (!is_null($upbringingEventReport->file_path)) {
            $data = explode('/', $upbringingEventReport->file_path);
            $this->filename = $data[2];
        }

        $this->isEditModalVisible = !$this->isEditModalVisible;
    }

    public function update()
    {
        if (!is_null($this->file)) {
            $this->validate([
                'linkToNews' => 'required|string|max:500',
                'file' => 'file|mimes:zip|max:5120'
            ]);
        } else {
            $this->validate([
                'linkToNews' => 'required|string|max:255',
            ]);
        }

        $upbringingEventReport = UpbringingEventReport::findOrFail($this->id);
        $upbringingEventReport->link_to_news = $this->linkToNews;

        if (!is_null($this->file)) {
            if (!is_null($upbringingEventReport->file_path)) {
                unlink(storage_path('app/public/' . $upbringingEventReport->file_path));
            }

            $this->filePath = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
            $upbringingEventReport->file_path = $this->filePath;
        }

        $upbringingEventReport->update();

        $this->isEditModalVisible = !$this->isEditModalVisible;
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }

    public function showDescription($id)
    {
        $upbringingEvent = UpbringingEvent::findOrfail($id);
        $this->description = $upbringingEvent->description;
        $this->filePath = $upbringingEvent->file_path;

        if (!is_null($this->filePath)) {
            $this->filename = $upbringingEvent->filename;
        }

        $this->isDescriptionModalVisible = !$this->isDescriptionModalVisible;
    }

    private function resetFields()
    {
        $this->description = null;
        $this->linkToNews = null;
        $this->filePath = null;
        $this->file = null;

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
