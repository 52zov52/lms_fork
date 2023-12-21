<?php

namespace App\Livewire;

use App\Models\FeedbackMessage as ModelsFeedbackMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackMessage extends Component
{
    use WithPagination;

    public $email = '';
    public $messageText = '';

    public bool $isFeedbackMessageModalVisible = false, $isFeedbackResponsModalVisible = false, $isAlertVisible = false;

    public function render()
    {
        $feedbackMessages = ModelsFeedbackMessage::where('sender_id', '=', Auth::user()->id)->paginate(5);

        return view('livewire.feedback-message', ['feedbackMessages' => $feedbackMessages]);
    }

    public function feedbackMessageModalVisible(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->isFeedbackMessageModalVisible = true;
    }

    public function feedbackResponsModalVisible(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->isFeedbackResponsModalVisible = true;
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function sendMessage(): void
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

        $this->isFeedbackMessageModalVisible = false;
        $this->isAlertVisible = true;

        session()->flash('message-success', 'Сообщение успешно отправлено!');
    }

    public function sendRespons(): void
    {
        $this->isFeedbackResponsModalVisible = false;
        $this->isAlertVisible = true;

        session()->flash('message-success', 'Ответ на сообщение успешно отправлен!');
    }
}
