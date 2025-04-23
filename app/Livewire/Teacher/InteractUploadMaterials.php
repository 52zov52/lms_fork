<?php

namespace App\Livewire\Teacher;

use App\Models\AssignmentAttachment;
use App\Models\AssignmentComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class InteractUploadMaterials extends Component
{
    use WithFileUploads;

    public int $id;
    public int $rand = 1;

    public string $comment = '';
    public $files = [];

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        $materials = $this->dataTransform(AssignmentComment::leftJoin('assignment_attachments', 'assignment_comments.id', '=', 'assignment_attachments.assignment_comment_id')
            ->join('users_data', 'assignment_comments.teacher_id', '=', 'users_data.user_id')
            ->where('assignment_comments.teacher_assignment_id', '=', $this->id)
            ->where('assignment_comments.teacher_id', '=', Auth::user()->id)
            ->select(
                DB::raw("CONCAT(last_name, ' ', SUBSTRING(first_name, 1, 1), '. ', SUBSTRING(middle_name, 1, 1), '.') as fio"),
                'assignment_comments.id as comment_id',
                'assignment_comments.comment',
                'assignment_attachments.path_to_file',
                'assignment_attachments.original_filename',
                'assignment_comments.created_at'
            )
            ->orderBy('comment_id')
            ->get()
            ->toArray());

        return view('livewire.teacher.interact-upload-materials', ['materials' => $materials]);
    }

    public function store()
    {
        $this->validate([
            'comment' => 'nullable|string|max:5000',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png,zip|max:9216'
        ]);

        $assignmentComment = AssignmentComment::create([
            'teacher_assignment_id' => $this->id,
            'teacher_id' => Auth::user()->id,
            'comment' => trim($this->comment)
        ]);

        $dir = now()->format('Y-m-d');

        foreach ($this->files as $file) {
            $pathToFile = $file->store('files' . '/' . $dir, 'public');

            AssignmentAttachment::create([
                'teacher_assignment_id' => $this->id,
                'assignment_comment_id' => $assignmentComment->id,
                'path_to_file' => $pathToFile,
                'original_filename' => $file->getClientOriginalName()
            ]);
        }

        $this->resetFields();

        $this->dispatch('show-toast', message: 'Файл(ы) успешно загрузены!', type: 'success');
    }

    #[On('destroy')]
    public function destroy($assignmentCommentId)
    {
        $assignmentAttachment = AssignmentAttachment::where('assignment_comment_id', '=', $assignmentCommentId)->get();
        foreach ($assignmentAttachment as $key => $value) {
            unlink(storage_path('app/public/' . $value->path_to_file));
        }

        $modelAssignmentAttachment = AssignmentAttachment::where('assignment_comment_id', '=', $assignmentCommentId);
        $modelAssignmentAttachment->delete();

        $assignmentComment = AssignmentComment::findOrFail($assignmentCommentId);
        $assignmentComment->delete();

        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }

    public function removeFileFromArray($key)
    {
        unset($this->files[$key]);
        $this->files = array_values($this->files);
    }

    private function dataTransform($data)
    {
        $result = [];
        $uniqueCommentIds = array_unique(array_column($data, 'comment_id'));

        foreach ($uniqueCommentIds as $uniqueCommentId) {
            $filteredData = array_filter($data, function ($item) use ($uniqueCommentId) {
                return $item['comment_id'] == $uniqueCommentId;
            });

            $uniqueFio = array_unique(array_column(array_map(function ($item) {
                return ["fio" => $item['fio']];
            }, $filteredData), 'fio'));

            $uniqueComment = array_unique(array_column(array_map(function ($item) {
                return ["comment" => $item['comment']];
            }, $filteredData), 'comment'));

            $uniqueCreatedAt = array_unique(array_column(array_map(function ($item) {
                return ["created_at" => Carbon::parse($item['created_at'])->format('d.m.Y')];
            }, $filteredData), 'created_at'));

            $files = array_map(function ($item) {
                return [
                    "path_to_file" => $item['path_to_file'],
                    "original_filename" => $item['original_filename']
                ];
            }, $filteredData);

            $allNull = array_reduce($files, function ($carry, $file) {
                return $carry && is_null($file['path_to_file']) && is_null($file['original_filename']);
            }, true);
            
            $files = $allNull ? null : $files;

            $result[] = [
                'id' => $uniqueCommentId,
                'fio' => $uniqueFio[0],
                'comment' => $uniqueComment[0],
                'created_at' => $uniqueCreatedAt[0],
                'files' => $files
            ];
        }

        return $result;
    }

    private function resetFields()
    {
        $this->comment = '';
        $this->files = [];

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
