<?php

namespace App\Livewire\Student;

use App\Models\StudentFamilyStatus;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FamilyStatus extends Component
{
    public $id = 0,
        $userId = 0,
        $isMarried = false,
        $marriageDate = '',
        $isDivorced = false,
        $divorceDate = '',
        $isWidowed = false,
        $previousMarriage1Start = '',
        $previousMarriage1End = '',
        $previousMarriage2Start = '',
        $previousMarriage2End = '',
        $previousMarriage3Start = '',
        $previousMarriage3End = '',
        $previousMarriage4Start = '',
        $previousMarriage4End = '',
        $previousMarriage5Start = '',
        $previousMarriage5End = '',
        $isSpouseStudying = false,
        $isSpouseInIubip = false,
        $hasChildren = false,
        $child1BirthDate = '',
        $child2BirthDate = '',
        $child3BirthDate = '',
        $child4BirthDate = '',
        $child5BirthDate = '',
        $isSingleParent = false;

    public function render()
    {
        $studentFamilyStatus = StudentFamilyStatus::where('user_id', '=', Auth::user()->id)->first();

        if (!is_null($studentFamilyStatus)) {
            $this->id = $studentFamilyStatus->id;
            $this->userId = $studentFamilyStatus->user_id;
            $this->isMarried = $studentFamilyStatus->is_married;
            $this->marriageDate = (is_null($studentFamilyStatus->marriage_date)) ? null : Carbon::parse($studentFamilyStatus->marriage_date)->format('Y-m-d') ?? '';
            $this->isDivorced = $studentFamilyStatus->is_divorced;
            $this->divorceDate = (is_null($studentFamilyStatus->divorce_date)) ? null : Carbon::parse($studentFamilyStatus->divorce_date)->format('Y-m-d') ?? '';
            $this->isWidowed = $studentFamilyStatus->is_widowed;
            $this->previousMarriage1Start = (is_null($studentFamilyStatus->previous_marriage_1_start)) ? null : Carbon::parse($studentFamilyStatus->c)->format('Y-m-d') ?? '';
            $this->previousMarriage1End = (is_null($studentFamilyStatus->previous_marriage_1_end)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_1_end)->format('Y-m-d') ?? '';
            $this->previousMarriage2Start = (is_null($studentFamilyStatus->previous_marriage_2_start)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_2_start)->format('Y-m-d') ?? '';
            $this->previousMarriage2End = (is_null($studentFamilyStatus->previous_marriage_2_end)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_2_end)->format('Y-m-d') ?? '';
            $this->previousMarriage3Start = (is_null($studentFamilyStatus->previous_marriage_3_start)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_3_start)->format('Y-m-d') ?? '';
            $this->previousMarriage3End = (is_null($studentFamilyStatus->previous_marriage_3_end)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_3_end)->format('Y-m-d') ?? '';
            $this->previousMarriage4Start = (is_null($studentFamilyStatus->previous_marriage_4_start)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_4_start)->format('Y-m-d') ?? '';
            $this->previousMarriage4End = (is_null($studentFamilyStatus->previous_marriage_4_end)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_4_end)->format('Y-m-d') ?? '';
            $this->previousMarriage5Start = (is_null($studentFamilyStatus->previous_marriage_5_start)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_5_start)->format('Y-m-d') ?? '';
            $this->previousMarriage5End = (is_null($studentFamilyStatus->previous_marriage_5_end)) ? null : Carbon::parse($studentFamilyStatus->previous_marriage_5_end)->format('Y-m-d') ?? '';
            $this->isSpouseStudying = $studentFamilyStatus->is_spouse_studying;
            $this->isSpouseInIubip = $studentFamilyStatus->is_spouse_in_iubip;
            $this->hasChildren = $studentFamilyStatus->has_children;
            $this->child1BirthDate = (is_null($studentFamilyStatus->child_1_birth_date)) ? null : Carbon::parse($studentFamilyStatus->child_1_birth_date)->format('Y-m-d') ?? '';
            $this->child2BirthDate = (is_null($studentFamilyStatus->child_2_birth_date)) ? null : Carbon::parse($studentFamilyStatus->child_2_birth_date)->format('Y-m-d') ?? '';
            $this->child3BirthDate = (is_null($studentFamilyStatus->child_3_birth_date)) ? null : Carbon::parse($studentFamilyStatus->child_3_birth_date)->format('Y-m-d') ?? '';
            $this->child4BirthDate = (is_null($studentFamilyStatus->child_4_birth_date)) ? null : Carbon::parse($studentFamilyStatus->child_4_birth_date)->format('Y-m-d') ?? '';
            $this->child5BirthDate = (is_null($studentFamilyStatus->child_5_birth_date)) ? null : Carbon::parse($studentFamilyStatus->child_5_birth_date)->format('Y-m-d') ?? '';
            $this->isSingleParent = $studentFamilyStatus->is_single_parent;
        }

        return view('livewire.student.family-status');
    }

    public function save()
    {
        $studentFamilyStatus = StudentFamilyStatus::where('user_id', '=', Auth::user()->id)->first();

        if (is_null($studentFamilyStatus)) {
            $this->store();
        } else {
            $this->update($studentFamilyStatus->id);
        }
    }

    private function store()
    {
        $studentFamilyStatus = new StudentFamilyStatus();
        $studentFamilyStatus->user_id = Auth::user()->id;
        $studentFamilyStatus->is_married = $this->isMarried;

        if ($this->isMarried) {
            $this->validate([
                'marriageDate' => 'required'
            ]);

            $studentFamilyStatus->marriage_date = Carbon::parse($this->marriageDate)->format('Y-m-d');
        }

        $studentFamilyStatus->is_divorced = $this->isDivorced;

        if ($this->isDivorced) {
            $this->validate([
                'divorceDate' => 'required'
            ]);

            $studentFamilyStatus->divorce_date = Carbon::parse($this->divorceDate)->format('Y-m-d');
        }

        $studentFamilyStatus->is_widowed = $this->isWidowed;
        $studentFamilyStatus->previous_marriage_1_start = (empty($this->previousMarriage1Start)) ? null : Carbon::parse($this->previousMarriage1Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_1_end = (empty($this->previousMarriage1End)) ? null : Carbon::parse($this->previousMarriage1End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_2_start = (empty($this->previousMarriage2Start)) ? null : Carbon::parse($this->previousMarriage2Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_2_end = (empty($this->previousMarriage2End)) ? null : Carbon::parse($this->previousMarriage2End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_3_start = (empty($this->previousMarriage3Start)) ? null : Carbon::parse($this->previousMarriage3Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_3_end = (empty($this->previousMarriage3End)) ? null : Carbon::parse($this->previousMarriage3End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_4_start = (empty($this->previousMarriage4Start)) ? null : Carbon::parse($this->previousMarriage4Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_4_end = (empty($this->previousMarriage4End)) ? null : Carbon::parse($this->previousMarriage4End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_5_start = (empty($this->previousMarriage5Start)) ? null : Carbon::parse($this->previousMarriage5Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_5_end = (empty($this->previousMarriage5End)) ? null : Carbon::parse($this->previousMarriage5End)->format('Y-m-d');
        $studentFamilyStatus->is_spouse_studying = $this->isSpouseStudying;
        $studentFamilyStatus->is_spouse_in_iubip = $this->isSpouseInIubip;
        $studentFamilyStatus->has_children = $this->hasChildren;
        $studentFamilyStatus->child_1_birth_date = (empty($this->child1BirthDate)) ? null : Carbon::parse($this->child1BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_2_birth_date = (empty($this->child2BirthDate)) ? null : Carbon::parse($this->child2BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_3_birth_date = (empty($this->child3BirthDate)) ? null : Carbon::parse($this->child3BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_4_birth_date = (empty($this->child4BirthDate)) ? null : Carbon::parse($this->child4BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_5_birth_date = (empty($this->child5BirthDate)) ? null : Carbon::parse($this->child5BirthDate)->format('Y-m-d');
        $studentFamilyStatus->is_single_parent = $this->isSingleParent;
        $studentFamilyStatus->user_signature = $this->getSignature(Auth::user()->id);
        $studentFamilyStatus->save();

        $this->dispatch('show-toast', message: 'Данные успешно сохранены!', type: 'success');
    }

    private function update($id)
    {
        $studentFamilyStatus = StudentFamilyStatus::findOrFail($id);
        $studentFamilyStatus->user_id = Auth::user()->id;
        $studentFamilyStatus->is_married = $this->isMarried;

        if ($this->isMarried) {
            $this->validate([
                'marriageDate' => 'required'
            ]);

            $studentFamilyStatus->marriage_date = Carbon::parse($this->marriageDate)->format('Y-m-d');
        }

        $studentFamilyStatus->is_divorced = $this->isDivorced;

        if ($this->isDivorced) {
            $this->validate([
                'divorceDate' => 'required'
            ]);

            $studentFamilyStatus->divorce_date = Carbon::parse($this->divorceDate)->format('Y-m-d');
        }

        $studentFamilyStatus->is_widowed = $this->isWidowed;
        $studentFamilyStatus->previous_marriage_1_start = (empty($this->previousMarriage1Start)) ? null : Carbon::parse($this->previousMarriage1Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_1_end = (empty($this->previousMarriage1End)) ? null : Carbon::parse($this->previousMarriage1End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_2_start = (empty($this->previousMarriage2Start)) ? null : Carbon::parse($this->previousMarriage2Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_2_end = (empty($this->previousMarriage2End)) ? null : Carbon::parse($this->previousMarriage2End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_3_start = (empty($this->previousMarriage3Start)) ? null : Carbon::parse($this->previousMarriage3Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_3_end = (empty($this->previousMarriage3End)) ? null : Carbon::parse($this->previousMarriage3End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_4_start = (empty($this->previousMarriage4Start)) ? null : Carbon::parse($this->previousMarriage4Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_4_end = (empty($this->previousMarriage4End)) ? null : Carbon::parse($this->previousMarriage4End)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_5_start = (empty($this->previousMarriage5Start)) ? null : Carbon::parse($this->previousMarriage5Start)->format('Y-m-d');
        $studentFamilyStatus->previous_marriage_5_end = (empty($this->previousMarriage5End)) ? null : Carbon::parse($this->previousMarriage5End)->format('Y-m-d');
        $studentFamilyStatus->is_spouse_studying = $this->isSpouseStudying;
        $studentFamilyStatus->is_spouse_in_iubip = $this->isSpouseInIubip;
        $studentFamilyStatus->has_children = $this->hasChildren;
        $studentFamilyStatus->child_1_birth_date = (empty($this->child1BirthDate)) ? null : Carbon::parse($this->child1BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_2_birth_date = (empty($this->child2BirthDate)) ? null : Carbon::parse($this->child2BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_3_birth_date = (empty($this->child3BirthDate)) ? null : Carbon::parse($this->child3BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_4_birth_date = (empty($this->child4BirthDate)) ? null : Carbon::parse($this->child4BirthDate)->format('Y-m-d');
        $studentFamilyStatus->child_5_birth_date = (empty($this->child5BirthDate)) ? null : Carbon::parse($this->child5BirthDate)->format('Y-m-d');
        $studentFamilyStatus->is_single_parent = $this->isSingleParent;
        $studentFamilyStatus->user_signature = $this->getSignature(Auth::user()->id);
        $studentFamilyStatus->update();

        $this->dispatch('show-toast', message: 'Данные успешно обновлены!', type: 'success');
    }

    private function getSignature($userId): string
    {
        $userData = UserData::select(DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"))
            ->where('user_id', '=', $userId)->first();

        return hash('sha256', $userData->fio . now()->format('Y-m-d'));
    }
}
