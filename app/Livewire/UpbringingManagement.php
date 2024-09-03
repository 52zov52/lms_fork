<?php

namespace App\Livewire;

use App\Models\UpbringingEvent;
use Livewire\Component;

class UpbringingManagement extends Component
{
    public $showModal = false, $isEdit = false;

    public function render()
    {
        $upbringingEvents = UpbringingEvent::paginate(50);
        return view('livewire.upbringing-management', ['upbringingEvents' => $upbringingEvents]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function modalVisible($isEdit, $id = null): void
    {
        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id);
        }

        $this->showModal = !$this->showModal;
    }

    public function store()
    {
        $this->modalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function edit($id) 
    {

    }

    public function update() 
    {
        $this->modalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function destroy($id): void
    {
        $UpbringingEvent = UpbringingEvent::findOrFail($id);
        $UpbringingEvent->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }
}
