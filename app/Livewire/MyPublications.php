<?php

namespace App\Livewire;

use App\Models\MyPublication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MyPublications extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $id,
        $category,
        $status,
        $title,
        $output_data,
        $year_publishing,
        $volume_full_in_printed_sheets,
        $volume_full_on_pages,
        $volume_author_in_printed_sheets,
        $volume_author_on_pages,
        $name_co_authors,
        $bibliographic_description,
        $file_path,
        $link_elibrary,
        $is_university_affiliation,
        $user_id;

    public $file, $filename, $rand = 1;

    public $isEntryModalVisible = false, $isEdit = false;

    public function render()
    {
        $publications = MyPublication::where('user_id', '=', Auth::user()->id)->paginate(50);

        return view('livewire.my-publications', ['publications' => $publications]);
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
    }

    public function setEntryModalVisible($isEdit, $id = null)
    {
        $this->resetFields();
        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id);
        }

        $this->isEntryModalVisible = !$this->isEntryModalVisible;
    }

    public function store()
    {
        if (!is_null($this->file)) {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'status' => 'required|numeric|min:1',
                'title' => 'required|string|max:500',
                'output_data' => 'required|string|max:1500',
                'year_publishing' => 'required|numeric',
                'volume_full_on_pages' => 'required|numeric',
                'file' => 'file|mimes:pdf|max:3072'
            ]);
        } else {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'status' => 'required|numeric|min:1',
                'title' => 'required|string|max:500',
                'output_data' => 'required|string|max:1500',
                'year_publishing' => 'required|numeric',
                'volume_full_on_pages' => 'required|numeric'
            ]);
        }

        $printed_sheets = $this->calk_printed_sheets($this->volume_full_on_pages);

        MyPublication::create([
            'category' => $this->category,
            'status' => $this->status,
            'title' => $this->title,
            'output_data' => $this->output_data,
            'year_publishing' => $this->year_publishing,
            'volume_full_in_printed_sheets' => $printed_sheets,
            'volume_full_on_pages' => $this->volume_full_on_pages,
            'volume_author_in_printed_sheets' => $printed_sheets,
            'volume_author_on_pages' => $this->volume_full_on_pages,
            'name_co_authors' => $this->name_co_authors,
            'bibliographic_description' => '-',
            'file_path' => (!is_null($this->file)) ? $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public') : null,
            'link_elibrary' => (is_null($this->link_elibrary)) ? null : $this->link_elibrary,
            'is_university_affiliation' => (is_null($this->is_university_affiliation)) ? false : $this->is_university_affiliation,
            'user_id' => Auth::user()->id
        ]);

        $this->setEntryModalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function edit($id)
    {
        $this->id = $id;

        $publications = MyPublication::findOrFail($this->id);
        $this->category = $publications->category;
        $this->status = $publications->status;
        $this->title = $publications->title;
        $this->output_data = $publications->output_data;
        $this->year_publishing = $publications->year_publishing;
        $this->volume_full_in_printed_sheets = $publications->volume_full_in_printed_sheets;
        $this->volume_full_on_pages = $publications->volume_full_on_pages;
        $this->volume_author_in_printed_sheets = $publications->volume_author_in_printed_sheets;
        $this->volume_author_on_pages = $publications->volume_author_on_pages;
        $this->name_co_authors = $publications->name_co_authors;
        $this->bibliographic_description = $publications->bibliographic_description;
        $this->file_path = $publications->file_path;
        $this->link_elibrary = $publications->link_elibrary;

        if (!is_null($publications->file_path)) {
            $data = explode('/', $publications->file_path);
            $this->filename = $data[2];
        }

        $this->is_university_affiliation = ($publications->is_university_affiliation) ? true : false;
    }

    public function update()
    {
        if (!is_null($this->file)) {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'status' => 'required|numeric|min:1',
                'title' => 'required|string|max:500',
                'output_data' => 'required|string|max:1500',
                'year_publishing' => 'required|numeric',
                'volume_full_on_pages' => 'required|numeric',
                'file' => 'file|mimes:pdf|max:3072'
            ]);
        } else {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'status' => 'required|numeric|min:1',
                'title' => 'required|string|max:500',
                'output_data' => 'required|string|max:1500',
                'year_publishing' => 'required|numeric',
                'volume_full_on_pages' => 'required|numeric'
            ]);
        }

        $printed_sheets = $this->calk_printed_sheets($this->volume_full_on_pages);

        $publications = MyPublication::findOrFail($this->id);
        $publications->category = $this->category;
        $publications->status =  $this->status;
        $publications->title = $this->title;
        $publications->output_data = $this->output_data;
        $publications->year_publishing = $this->year_publishing;
        $publications->volume_full_in_printed_sheets = $printed_sheets;
        $publications->volume_full_on_pages = $this->volume_full_on_pages;
        $publications->volume_author_in_printed_sheets = $printed_sheets;
        $publications->volume_author_on_pages = $this->volume_full_on_pages;
        $publications->name_co_authors = $this->name_co_authors;
        $publications->bibliographic_description = '-';

        if (!is_null($this->file)) {
            if (!is_null($publications->file_path)) {
                unlink(storage_path('app/public/' . $publications->file_path));
            }

            $publications->file_path = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
        }

        $publications->link_elibrary = $this->link_elibrary;
        $publications->is_university_affiliation = $this->is_university_affiliation;

        $publications->update();

        $this->setEntryModalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function destroy($id)
    {
        $publications = MyPublication::findOrFail($id);

        if (!is_null($publications->file_path)) {
            unlink(storage_path('app/public/' . $publications->file_path));
        }

        $publications->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    public function print()
    {
        return redirect()->route('print.my.publications');
    }

    public function calk_printed_sheets($pages)
    {
        return round($pages / (0.93 * 16), 2);
    }

    public function resetFields(): void
    {
        $this->id = 0;
        $this->category = 0;
        $this->status = 0;
        $this->title = '';
        $this->output_data = '';
        $this->year_publishing = '';
        $this->volume_full_in_printed_sheets = '';
        $this->volume_full_on_pages = '';
        $this->volume_author_in_printed_sheets = '';
        $this->volume_author_on_pages = '';
        $this->name_co_authors = '';
        $this->bibliographic_description = '';
        $this->file_path = '';
        $this->link_elibrary = '';
        $this->is_university_affiliation = false;
        $this->user_id = 0;

        $this->file = null;
        $this->filename = null;

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
