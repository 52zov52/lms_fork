<?php

namespace App\Livewire\Employee;

use App\Models\RegulatoryDocument as ModelsRegulatoryDocument;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class RegulatoryDocument extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $isCreateOrEditModalVisible = false;
    public $isEdit = false;

    public $regulatoryDocuments = [];
    public $searchText = '';
    public $id, $title, $description, $type, $file, $file_path, $filename;

    public function render()
    {
        if ($this->searchText != '') {
            $this->search();
        } else {
            $this->fecthData();
        }
        
        return view('livewire.employee.regulatory-document');
    }

    public function search()
    {
        $this->regulatoryDocuments = ModelsRegulatoryDocument::where('title', 'like', '%' . $this->searchText . '%')->pagination(20);
    }

    public function fecthData()
    {
        $this->regulatoryDocuments = ModelsRegulatoryDocument::pagination(20);
    }

    public function edit(string $id)
    {
        $regulatoryDocuments = ModelsRegulatoryDocument::findOrFail($id);
        $this->id = $id;
        $this->title = $regulatoryDocuments->title;
        $this->description = $regulatoryDocuments->description;
        $this->type = $regulatoryDocuments->type;
        $this->file_path = $regulatoryDocuments->file_path;
        $this->filename = $regulatoryDocuments->filename;

        $this->isEdit = true;
        $this->isCreateOrEditModalVisible = true;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:9216'
        ]);

        $regulatoryDocuments = new ModelsRegulatoryDocument();
        $regulatoryDocuments->title = $this->title;
        $regulatoryDocuments->description = $this->description;
        $regulatoryDocuments->type = $this->type;

        $filePath = $this->file->store('files/' . now()->format('Y-m-d'), 'public');

        $regulatoryDocuments->file_path = $filePath;
        $regulatoryDocuments->filename = $this->file->getOriggetClientOriginalName();
        $regulatoryDocuments->save();

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно добавлена!', type: 'success');
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:9216'
        ]);

        $regulatoryDocuments = ModelsRegulatoryDocument::findOrFail($this->id);
        $regulatoryDocuments->title = $this->title;
        $regulatoryDocuments->description = $this->description;
        $regulatoryDocuments->type = $this->type;

        $filePath = $this->file->store('files/' . now()->format('Y-m-d'), 'public');

        $regulatoryDocuments->file_path = $filePath;
        $regulatoryDocuments->filename = $this->file->getOriggetClientOriginalName();
        $regulatoryDocuments->update();

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }

    #[On('destroy')]
    public function destroy(string $id)
    {
        $regulatoryDocuments = ModelsRegulatoryDocument::findOrFail($id);

        if (!is_null($regulatoryDocuments->file_path)) {
            unlink(storage_path('app/public/' . $regulatoryDocuments->file_path));
        }

        $regulatoryDocuments->delete();
        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }
}
