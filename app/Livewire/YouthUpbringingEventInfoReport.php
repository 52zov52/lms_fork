<?php

namespace App\Livewire;

use App\Models\UpbringingEvent;
use App\Models\UpbringingEventReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class YouthUpbringingEventInfoReport extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $id = 0, $linkToNews = '', $filePath = '';

    public $file = null;
    public $filename = '';
    public $rand = 1;

    public $showModal = false;

    public function render()
    {
        $upbringingEvents = UpbringingEvent::join('upbringing_event_reports', 'upbringing_events.id', '=', 'upbringing_event_reports.upbringing_event_id')
            ->where('upbringing_events.user_id', '=', Auth::user()->id)
            ->select(
                'upbringing_events.id',
                'upbringing_event_reports.id as upbringing_event_report_id',
                'upbringing_events.name',
                'upbringing_events.description',
                'upbringing_events.date',
                'upbringing_events.academic_year',
                'upbringing_events.half_year',
                'upbringing_event_reports.link_to_news',
                'upbringing_event_reports.file_path'
            )
            ->paginate(50);

        return view('livewire.youth-upbringing-event-info-report', ['upbringingEvents' => $upbringingEvents]);
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
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

        $this->showModal = true;
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

        $this->showModal = false;

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    private function resetFields()
    {
        $this->linkToNews = '';
        $this->filePath = '';
        $this->file = null;

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
