<?php

namespace App\Livewire;

use App\Models\FeedbackMessage as ModelsFeedbackMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FeedbackMessage extends Component
{
    public $email = '';
    public $messageText = '';

    public function render()
    {
        return view('livewire.feedback-message');
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

        session()->flash('message-success', 'Сообщение успешно отправлено!');
    }
}
