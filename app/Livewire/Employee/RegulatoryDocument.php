<?php

namespace App\Livewire\Employee;

use App\Models\RegulatoryDocument as ModelsRegulatoryDocument;
use Illuminate\Support\Facades\DB;
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

    public $searchText = '';
    public $rand = 0;
    public $id = 0, $title = '', $description = '', $type = '', $file = null, $file_path = '', $filename = '';

    public function render()
    {
        if ($this->searchText != '') {
            $regulatoryDocuments = $this->search();
        } else {
            $regulatoryDocuments = $this->fecthData();
        }

        return view('livewire.employee.regulatory-document', ['regulatoryDocuments' => $regulatoryDocuments]);
    }

    public function search()
    {
        return ModelsRegulatoryDocument::where('title', 'like', '%' . $this->searchText . '%')
            ->select(
                'id',
                'title',
                'description',
                'file_path',
                'filename',
                DB::raw("CASE 
                    WHEN type = 'laws' THEN 'Законы'
                    WHEN type = 'decrees' THEN 'Постановления'
                    WHEN type = 'orders' THEN 'Приказы'
                    WHEN type = 'regulations' THEN 'Регламенты'
                    WHEN type = 'standards' THEN 'Стандарты'
                    WHEN type = 'instructions' THEN 'Инструкции'
                    WHEN type = 'rules' THEN 'Правила'
                    WHEN type = 'gost' THEN 'ГОСТ'
                    WHEN type = 'sanitary_norms' THEN 'Санитарные нормы'
                    WHEN type = 'technical_regulations' THEN 'Технические регламенты'
                    ELSE 'Не определен' END as type")
            )
            ->paginate(50);
    }

    public function fecthData()
    {
        return ModelsRegulatoryDocument::select(
            'id',
            'title',
            'description',
            'file_path',
            'filename',
            DB::raw("CASE 
                WHEN type = 'laws' THEN 'Законы'
                WHEN type = 'decrees' THEN 'Постановления'
                WHEN type = 'orders' THEN 'Приказы'
                WHEN type = 'regulations' THEN 'Регламенты'
                WHEN type = 'standards' THEN 'Стандарты'
                WHEN type = 'instructions' THEN 'Инструкции'
                WHEN type = 'rules' THEN 'Правила'
                WHEN type = 'gost' THEN 'ГОСТ'
                WHEN type = 'sanitary_norms' THEN 'Санитарные нормы'
                WHEN type = 'technical_regulations' THEN 'Технические регламенты'
                ELSE 'Не определен' END as type")
        )
            ->paginate(50);
    }

    public function create()
    {
        $this->resetFields();

        $this->isEdit = false;
        $this->isCreateOrEditModalVisible = true;
    }

    public function edit(string $id)
    {
        $this->resetFields();

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
            'description' => 'string',
            'type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:9216'
        ]);

        $regulatoryDocuments = new ModelsRegulatoryDocument();
        $regulatoryDocuments->title = $this->title;
        $regulatoryDocuments->description = $this->description;
        $regulatoryDocuments->type = $this->type;

        $filePath = $this->file->store('files/' . now()->format('Y-m-d'), 'public');

        $regulatoryDocuments->file_path = $filePath;
        $regulatoryDocuments->filename = $this->file->getClientOriginalName();
        $regulatoryDocuments->save();

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно добавлена!', type: 'success');
    }

    public function update()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'string',
            'type' => 'required|string',
        ];

        if (!is_null($this->file)) {
            $rules['file'] = 'required|file|mimes:pdf,doc,docx|max:9216';
        }

        $this->validate($rules);

        $regulatoryDocuments = ModelsRegulatoryDocument::findOrFail($this->id);
        $regulatoryDocuments->title = $this->title;
        $regulatoryDocuments->description = $this->description;
        $regulatoryDocuments->type = $this->type;

        if (!is_null($this->file)) {
            unlink(storage_path('app/public/' . $regulatoryDocuments->file_path));

            $filePath = $this->file->store('files/' . now()->format('Y-m-d'), 'public');
            $regulatoryDocuments->file_path = $filePath;
            $regulatoryDocuments->filename = $this->file->getClientOriginalName();
        }

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

    private function resetFields()
    {
        $this->id = 0;
        $this->title = '';
        $this->description = '';
        $this->type = '';
        $this->file = null;
        $this->file_path = '';
        $this->filename = '';

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
