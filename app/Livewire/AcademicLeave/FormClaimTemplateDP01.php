<?php

namespace App\Livewire\AcademicLeave;

use Livewire\Component;

class FormClaimTemplateDP01 extends Component
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
        return view('livewire.academic-leave.form-claim-template-dp01');
    }
}
