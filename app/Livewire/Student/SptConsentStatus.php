<?php

namespace App\Livewire\Student;

use App\Http\Controllers\Student\SptConsentStatusController;
use App\Models\StudentSptConsentStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SptConsentStatus extends Component
{
    public function render()
    {
        $studentSptConsentStatus = StudentSptConsentStatus::where('user_id', '=', Auth::user()->id)->get();

        return view('livewire.student.spt-consent-status', ['studentSptConsentStatus' => $studentSptConsentStatus]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function update($id)
    {
        $studentSptConsentStatus = StudentSptConsentStatus::findOrFail($id);
        $studentSptConsentStatus->consent_status = 1;
        $studentSptConsentStatus->signature = SptConsentStatusController::getSignature(Auth::user()->id);
        $studentSptConsentStatus->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function downloadInformedConsent($id)
    {
        return redirect()->route('download.informed.consent', ['id' => $id]);
    }
}
