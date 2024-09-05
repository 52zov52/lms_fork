<?php

namespace App\Livewire;

use App\Models\UpbringingEvent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class YouthUpbringingEventInfoReport extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $linkToNews = '', $filePath = '';

    public $file = null;
    public $filename = '';
    public $rand = 1;

    public $showModal = false, $isEdit = false;

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
                'upbringing_events.half_year'
            )
            ->paginate(50);

        return view('livewire.youth-upbringing-event-info-report', ['upbringingEvents' => $upbringingEvents]);
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
        }

        $this->showModal = !$this->showModal;
    }

    public function store()
    {
    }

    public function edit($id)
    {
    }

    public function update()
    {
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
