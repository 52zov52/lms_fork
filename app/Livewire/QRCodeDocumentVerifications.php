<?php

namespace App\Livewire;

use App\Models\QRCodeDocumentVerification;
use Livewire\Component;

class QRCodeDocumentVerifications extends Component
{
    public $verificationCode;

    public function mount($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function render()
    {
        $data = QRCodeDocumentVerification::join('users_data', 'qr_code_document_verifications.user_id', '=', 'users_data.user_id')
            ->select(
                'qr_code_document_verifications.document_number',
                'qr_code_document_verifications.document_date',
                'users_data.last_name_dative', 
                'users_data.first_name_dative', 
                'users_data.middle_name_dative')
            ->where('verification_code', '=', $this->verificationCode)
            ->first();

        return view('livewire.qr-code-document-verifications', [
            'data' => $data
        ]);
    }

    public function generateVerificationCode(): string
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
    }
}
