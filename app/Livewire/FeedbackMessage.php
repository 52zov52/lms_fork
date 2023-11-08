<?php

namespace App\Livewire;

use App\Models\FeedbackMessage as ModelsFeedbackMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FeedbackMessage extends Component
{
    public $email = '';
    public $messageText = '';

    public bool $isFeedbackModalVisible = false;

    public function render()
    {
        $feedbackMessages = ModelsFeedbackMessage::where('sender_id', '=', Auth::user()->id)->get();

        return view('livewire.feedback-message', ['feedbackMessages' => $feedbackMessages]);
    }

    public function feedbackModalVisible(): void
    {
        $this->isFeedbackModalVisible = true;
    }

    public function send(): void
    {
        $this->validate([
            'email' => 'required|string|email',
            'messageText' => 'required|string|max:5000'
        ]);

        ModelsFeedbackMessage::create([
            'sender_id' => Auth::user()->id,
            'email' => $this->email,
            'subject' => 'Сообщение с формы обратной связи',
            'message_text' => $this->messageText,
        ]);

        $this->email = '';
        $this->messageText = '';

        $this->isFeedbackModalVisible = false;

        session()->flash('message-success', 'Сообщение успешно отправлено!');
    }
}
