<?php

namespace App\Livewire;

use App\Models\FeedbackMessage as ModelsFeedbackMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FeedbackMessage extends Component
{
    use WithPagination;

    public $subject = '';
    public $email = '';
    public $messageText = '';

    public bool $isFeedbackMessageModalVisible = false;
    public bool $isAlertVisible = false;

    public function render()
    {
        $feedbackMessages = ModelsFeedbackMessage::where('sender_id', '=', Auth::user()->id)->paginate(15);

        return view('livewire.feedback-message', ['feedbackMessages' => $feedbackMessages]);
    }

    public function feedbackMessageModalVisible(): void
    {
        $this->subject = '';
        $this->email = '';
        $this->messageText = '';

        $this->resetErrorBag();
        $this->resetValidation();

        $this->isFeedbackMessageModalVisible = true;
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function send(): void
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'email' => 'required|string|email',
            'messageText' => 'required|string|max:5000'
        ]);

        ModelsFeedbackMessage::create([
            'sender_id' => Auth::user()->id,
            'email' => $this->email,
            'subject' => $this->subject,
            'message_text' => $this->messageText,
        ]);

        $this->isFeedbackMessageModalVisible = false;
        $this->isAlertVisible = true;

        session()->flash('message-success', 'Сообщение успешно отправлено!');
    }

    public function redirectToLookFeedbackMessage($id)
    {
        return redirect()->route('look.feedback.messange', ['id' => $id]);
    }
}
