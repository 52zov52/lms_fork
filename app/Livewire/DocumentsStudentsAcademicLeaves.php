<?php

namespace App\Livewire;

use Livewire\Component;

class DocumentsStudentsAcademicLeaves extends Component
{
    public $user_id;
    public $group_id;

    public function mount($user_id, $group_id)
    {
        $this->user_id = $user_id;
        $this->group_id = $group_id;
    }

    public function render()
    {
        return view('livewire.documents-students-academic-leaves');
    }

    public function redirectToPersonalRecordsCheckSheet()
    {
        return redirect()->route('personal.records.check.sheet', ['user_id' => $this->user_id, 'group_id' => $this->group_id]);
    }

    public function redirectToFormZ01()
    {
        return redirect()->route('form.z01', ['user_id' => $this->user_id, 'group_id' => $this->group_id]);
    }

    public function redirectToFormClaimTemplateDP01()
    {
        return redirect()->route('form.claim.template.dp01', ['user_id' => $this->user_id, 'group_id' => $this->group_id]);
    }

    public function printDocumentAcademicLeaves301()
    {
        return redirect()->route('print.document.academic.leaves.301', ['user_id' => $this->user_id, 'group_id' => $this->group_id]);
    }

    public function printDocumentAcademicLeavesAA01()
    {
        return redirect()->route('print.document.academic.leaves.aa.01', ['user_id' => $this->user_id, 'group_id' => $this->group_id]);
    }

    public function printPersonalFileVerificationSheet()
    {
        return redirect()->route('print.personal.file.verification.sheet', ['user_id' => $this->user_id, 'group_id' => $this->group_id]);
    }
}
