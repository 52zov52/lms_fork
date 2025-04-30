<?php

namespace App\Livewire\Common;

use App\Http\Controllers\AcademicYearController;
use App\Traits\AsyncInteractChatDataFormat;
use App\Models\AsyncInteractChat;
use App\Models\AsyncInteractChatComment;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsyncInteractChatViewFile extends Component
{
    use AsyncInteractChatDataFormat;

    public string $chatId;

    public function mount($chatId)
    {
        $this->chatId = base64_decode($chatId);
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
                DB::raw("CONCAT(users_data.last_name, ' ', SUBSTRING(users_data.first_name, 1, 1), '. ', SUBSTRING(users_data.middle_name, 1, 1), '.') as fio"),
                'users_data.user_id',
                'async_interact_chat_comments.id as comment_id',
                'async_interact_chat_comments.comment',
                'async_interact_chat_files.path_to_file',
                'async_interact_chat_files.original_filename',
                'async_interact_chat_comments.created_at'
            )
            ->orderBy('comment_id')
            ->get()
            ->toArray());

        return view('livewire.common.async-interact-chat-view-file', [
            'studyYear' => AcademicYearController::getAcademicYearByYear($chat->year),
            'halfYear' => $chat->half_year,
            'discipline' => $chat->discipline,
            'normative' => $chat->normative,
            'group' => $chat->group,
            'materials' => $materials
        ]);
    }
}
