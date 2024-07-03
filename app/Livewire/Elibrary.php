<?php

namespace App\Livewire;

use App\Models\Elibrary as ModelsElibrary;
use Livewire\Component;

class Elibrary extends Component
{
    public $id = 0, $name = '', $url = '', $description = '';

    public $isEntryModalVisible = false, $isEdit = false;

    public function render()
    {
        return view('livewire.elibrary', ['elibraries' => ModelsElibrary::get()]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function setEntryModalVisible($isEdit, $id = null): void
    {
        $this->resetFields();
        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id);
        }

        $this->isEntryModalVisible = !$this->isEntryModalVisible;
    }

    public function update(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'description' => 'required|string|max:1500'
        ]);

        $elibrary = ModelsElibrary::findOrFail($this->id);
        $elibrary->name = $this->name;
        $elibrary->url = $this->url;
        $elibrary->description = $this->description;
        $elibrary->save();

        $this->setEntryModalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    private function edit($id): void
    {
        $this->id = $id;

        $elibrary = ModelsElibrary::findOrFail($this->id);
        $this->name = $elibrary->name;
        $this->url = $elibrary->url;
        $this->description = $elibrary->description;
    }

    public function store(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'description' => 'required|string|max:1500'
        ]);

        ModelsElibrary::create([
            'name' => $this->name,
            'url' => $this->url,
            'description' => $this->description
        ]);

        $this->setEntryModalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function destroy($id): void
    {
        $elibrary = ModelsElibrary::findOrFail($id);
        $elibrary->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->name = "";
        $this->url = "";
        $this->description = "";

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
