<?php

namespace App\Livewire\Common;

use App\Http\Controllers\AcademicYearController;
use App\Models\AsyncInteractChat;
use App\Models\AsyncInteractChatComment;
use App\Models\AsyncInteractChatFile;
use App\Traits\AsyncInteractChatDataFormat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AsyncInteractChatUploadFile extends Component
{
    use WithFileUploads, AsyncInteractChatDataFormat;

    public string $chatId;
    public int $mode;
    public int $rand = 1;

    public string $comment = '';
    public $files = [];

    public function mount($chatId, $mode)
    {
        $this->chatId = base64_decode($chatId);
        $this->mode = $mode;
    }

    public function render()
    {
        $chat = AsyncInteractChat::join('disciplines', 'async_interact_chats.discipline_id', '=', 'disciplines.id')
            ->join('study_thread_normatives', 'async_interact_chats.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->join('groups', 'async_interact_chats.group_id', '=', 'groups.id')
            ->where('async_interact_chats.id', '=', (int)$this->chatId)
            ->select(
                'async_interact_chats.year',
                'async_interact_chats.half_year',
                'disciplines.name as discipline',
                'study_thread_normatives.name as normative',
                'groups.group_name as group'
            )
            ->first();

        $materials = $this->dataTransform(AsyncInteractChatComment::leftJoin('async_interact_chat_files', 'async_interact_chat_comments.id', '=', 'async_interact_chat_files.async_interact_chat_comment_id')
            ->join('users_data', 'async_interact_chat_comments.user_id', '=', 'users_data.user_id')
            ->where('async_interact_chat_comments.async_interact_chat_id', '=', (int)$this->chatId)
            ->select(
                'async_interact_chat_comments.id as comment_id',
                DB::raw("CONCAT(users_data.last_name, ' ', SUBSTRING(users_data.first_name, 1, 1), '. ', SUBSTRING(users_data.middle_name, 1, 1), '.') as fio"),
                'users_data.user_id',
                'async_interact_chat_comments.comment',
                'async_interact_chat_files.path_to_file',
                'async_interact_chat_files.original_filename',
                'async_interact_chat_comments.created_at'
            )
            ->orderBy('comment_id')
            ->get()
            ->toArray());

        return view('livewire.common.async-interact-chat-upload-file', [
            'studyYear' => AcademicYearController::getAcademicYearByYear($chat->year),
            'halfYear' => $chat->half_year,
            'discipline' => $chat->discipline,
            'normative' => $chat->normative,
            'group' => $chat->group,
            'materials' => $materials
        ]);
    }

    public function store()
    {
        $this->validate([
            'comment' => 'nullable|string|max:5000',
            'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png,zip|max:9216'
        ]);

        $asyncInteractChatComment = AsyncInteractChatComment::create([
            'async_interact_chat_id' => (int)$this->chatId,
            'user_id' => Auth::user()->id,
            'comment' => trim($this->comment)
        ]);

        $dir = now()->format('Y-m-d');

        foreach ($this->files as $file) {
            $pathToFile = $file->store('files' . '/' . $dir, 'public');

            AsyncInteractChatFile::create([
                'async_interact_chat_comment_id' => $asyncInteractChatComment->id,
                'path_to_file' => $pathToFile,
                'original_filename' => $file->getClientOriginalName()
            ]);
        }

        $this->resetFields();

        $this->dispatch('show-toast', message: 'Файл(ы) успешно загрузены!', type: 'success');
    }

    #[On('destroy')]
    public function destroy($commentId)
    {
        $asyncInteractChatFile = AsyncInteractChatFile::where('async_interact_chat_comment_id', '=', $commentId)->get();
        foreach ($asyncInteractChatFile as $key => $value) {
            unlink(storage_path('app/public/' . $value->path_to_file));
        }

        $modelAsyncInteractChatFile = AsyncInteractChatFile::where('async_interact_chat_comment_id', '=', $commentId);
        $modelAsyncInteractChatFile->delete();

        $asyncInteractChatComment = AsyncInteractChatComment::findOrFail($commentId);
        $asyncInteractChatComment->delete();

        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }

    public function removeFileFromArray($key)
    {
        unset($this->files[$key]);
        $this->files = array_values($this->files);
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
