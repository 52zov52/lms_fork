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
        $is_university_affiliation,
        $user_id;

    public $file, $filename, $rand = 1;

    public $isEntryModalVisible = false, $isEdit = false;

    public function render()
    {
        $publications = MyPublication::where('user_id', '=', Auth::user()->id)->get();

        return view('livewire.my-publications', ['publications' => $publications]);
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
    }

    public function setEntryModalVisible()
    {
        $this->isEntryModalVisible = !$this->isEntryModalVisible;
    }

    public function store()
    {
        $this->validate([
            'category' => 'required|numeric|min:1',
            'status' => 'required|numeric|min:1',
            'title' => 'required|string|max:500',
            'output_data' => 'required|string|max:1500',
            'year_publishing' => 'required|numeric',
            'volume_full_on_pages' => 'required|numeric',
            'name_co_authors' => 'string|max:500',
            'file' => 'file|mimes:pdf|max:3072'
        ]);

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
            'file_path' => $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public'),
            'is_university_affiliation' => (is_null($this->is_university_affiliation)) ? false : $this->is_university_affiliation,
            'user_id' => Auth::user()->id
        ]);

        $this->setEntryModalVisible();

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

        if (!is_null($publications->file_path)) {
            $data = explode('/', $publications->file_path);
            $this->filename = $data[2];
        }

        $this->is_university_affiliation = $publications->is_university_affiliation;
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
                'name_co_authors' => 'string|max:500',
                'file' => 'file|mimes:pdf|max:3072'
            ]);
        } else {
            $this->validate([
                'category' => 'required|numeric|min:1',
                'status' => 'required|numeric|min:1',
                'title' => 'required|string|max:500',
                'output_data' => 'required|string|max:1500',
                'year_publishing' => 'required|numeric',
                'volume_full_on_pages' => 'required|numeric',
                'name_co_authors' => 'string|max:500'
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

        $publications->is_university_affiliation = $this->is_university_affiliation;

        $publications->update();

        $this->setEntryModalVisible();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function destroy($id)
    {
        $publications = MyPublication::findOrFail($id);
        $publications->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    public function calk_printed_sheets($pages)
    {
        return round($pages / (0.93 * 16), 2);
    }
}
