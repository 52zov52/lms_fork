<?php

namespace App\Livewire;

use App\Models\FeedbackMessage;
use App\Models\FeedbackResponses;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LookFeedbackMessage extends Component
{
    public $id;
    public $respons = '';

    public bool $isFeedbackResponsModalVisible = false;
    public bool $isAlertVisible = false;

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        $feedbackMessage = FeedbackMessage::join('users_data', 'users_data.user_id', '=', 'feedback_messages.sender_id')
        ->select(
            'feedback_messages.id',
            'feedback_messages.sender_id',
            'feedback_messages.email',
            'feedback_messages.message_text',
            'feedback_messages.created_at',
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name'
        )
        ->where('feedback_messages.id', '=', $this->id)->first();

        $feedbackResponse = FeedbackResponses::join('users_data', 'users_data.user_id', '=', 'feedback_responses.sender_id')
            ->select(
                'feedback_responses.id',
                'feedback_responses.sender_id',
                'feedback_responses.respons',
                'feedback_responses.created_at',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name'
            )
            ->where('feedback_responses.feedback_message_id', '=', $this->id)->first();

        return view(
            'livewire.look-feedback-message',
            [
                'feedbackMessage' => $feedbackMessage,
                'feedbackResponse' => $feedbackResponse
            ]
        );
    }

    public function feedbackResponsModalVisible(): void
    {
        $this->respons = '';

        $this->resetErrorBag();
        $this->resetValidation();

        $this->isFeedbackResponsModalVisible = true;
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function send(): void
    {
        $this->validate([
            'respons' => 'required|string|max:5000'
        ]);

        FeedbackResponses::create([
            'sender_id' => Auth::user()->id,
            'respons' => $this->respons,
            'feedback_message_id' => $this->id,
        ]);

        $this->isFeedbackResponsModalVisible = false;
        $this->isAlertVisible = true;

        session()->flash('message-success', 'Ответ на сообщение успешно отправлен!');
    }
}
