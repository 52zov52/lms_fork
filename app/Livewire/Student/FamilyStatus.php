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
        $isSpouseStudying = '',
        $isSpouseInIubip = '',
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

        $this->id = $studentFamilyStatus->id;
        $this->userId = $studentFamilyStatus->user_id;
        $this->isMarried = $studentFamilyStatus->is_married;
        $this->marriageDate = Carbon::parse($studentFamilyStatus->marriage_date)->formate('Y-m-d') ?? '';
        $this->isDivorced = $studentFamilyStatus->is_divorced;
        $this->divorceDate = Carbon::parse($studentFamilyStatus->divorce_date)->formate('Y-m-d') ?? '';
        $this->isWidowed = $studentFamilyStatus->is_widowed;
        $this->previousMarriage1Start = Carbon::parse($studentFamilyStatus->previous_marriage_1_start)->formate('Y-m-d') ?? '';
        $this->previousMarriage1End = Carbon::parse($studentFamilyStatus->previous_marriage_1_end)->formate('Y-m-d') ?? '';
        $this->previousMarriage2Start = Carbon::parse($studentFamilyStatus->previous_marriage_2_start)->formate('Y-m-d') ?? '';
        $this->previousMarriage2End = Carbon::parse($studentFamilyStatus->previous_marriage_2_end)->formate('Y-m-d') ?? '';
        $this->previousMarriage3Start = Carbon::parse($studentFamilyStatus->previous_marriage_3_start)->formate('Y-m-d') ?? '';
        $this->previousMarriage3End = Carbon::parse($studentFamilyStatus->previous_marriage_3_end)->formate('Y-m-d') ?? '';
        $this->previousMarriage4Start = Carbon::parse($studentFamilyStatus->previous_marriage_4_start)->formate('Y-m-d') ?? '';
        $this->previousMarriage4End = Carbon::parse($studentFamilyStatus->previous_marriage_4_end)->formate('Y-m-d') ?? '';
        $this->previousMarriage5Start = Carbon::parse($studentFamilyStatus->previous_marriage_5_start)->formate('Y-m-d') ?? '';
        $this->previousMarriage5End = Carbon::parse($studentFamilyStatus->previous_marriage_5_end)->formate('Y-m-d') ?? '';
        $this->isSpouseStudying = $studentFamilyStatus->is_spouse_studying;
        $this->isSpouseInIubip = $studentFamilyStatus->is_spouse_in_iubip;
        $this->hasChildren = $studentFamilyStatus->has_children;
        $this->child1BirthDate = Carbon::parse($studentFamilyStatus->child_1_birth_date)->formate('Y-m-d') ?? '';
        $this->child2BirthDate = Carbon::parse($studentFamilyStatus->child_2_birth_date)->formate('Y-m-d') ?? '';
        $this->child3BirthDate = Carbon::parse($studentFamilyStatus->child_3_birth_date)->formate('Y-m-d') ?? '';
        $this->child4BirthDate = Carbon::parse($studentFamilyStatus->child_4_birth_date)->formate('Y-m-d') ?? '';
        $this->child5BirthDate = Carbon::parse($studentFamilyStatus->child_5_birth_date)->formate('Y-m-d') ?? '';
        $this->isSingleParent = $studentFamilyStatus->is_single_parent;

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
        StudentFamilyStatus::create([
            'user_id' => Auth::user()->id,
            'is_married' => $this->isMarried,
            'marriage_date' => $this->marriageDate,
            'is_divorced' => $this->isDivorced,
            'divorce_date' => $this->divorceDate,
            'is_widowed' => $this->isWidowed,
            'previous_marriage_1_start' => $this->previousMarriage1Start,
            'previous_marriage_1_end' => $this->previousMarriage1End,
            'previous_marriage_2_start' => $this->previousMarriage2Start,
            'previous_marriage_2_end' => $this->previousMarriage3End,
            'previous_marriage_3_start' => $this->previousMarriage3Start,
            'previous_marriage_3_end' => $this->previousMarriage3End,
            'previous_marriage_4_start' => $this->previousMarriage4Start,
            'previous_marriage_4_end' => $this->previousMarriage4End,
            'previous_marriage_5_start' => $this->previousMarriage5Start,
            'previous_marriage_5_end' => $this->previousMarriage5End,
            'is_spouse_studying' => $this->isSpouseStudying,
            'is_spouse_in_iubip' => $this->isSpouseInIubip,
            'has_children' => $this->hasChildren,
            'child_1_birth_date' => $this->child1BirthDate,
            'child_2_birth_date' => $this->child2BirthDate,
            'child_3_birth_date' => $this->child3BirthDate,
            'child_4_birth_date' => $this->child4BirthDate,
            'child_5_birth_date' => $this->child5BirthDate,
            'is_single_parent' => $this->isSingleParent,
            'user_signature' => $this->getSignature(Auth::user()->id)
        ]);

        $this->dispatch('show-toast', message: 'Данные успешно сохранены!', type: 'success');
    }

    private function update($id)
    {
        $studentFamilyStatus = StudentFamilyStatus::findOrFail($id);
        $studentFamilyStatus->user_id = Auth::user()->id;
        $studentFamilyStatus->is_married = $this->isMarried;
        $studentFamilyStatus->marriage_date = $this->marriageDate;
        $studentFamilyStatus->is_divorced = $this->isDivorced;
        $studentFamilyStatus->divorce_date = $this->divorceDate;
        $studentFamilyStatus->is_widowed = $this->isWidowed;
        $studentFamilyStatus->previous_marriage_1_start = $this->previousMarriage1Start;
        $studentFamilyStatus->previous_marriage_1_end = $this->previousMarriage1End;
        $studentFamilyStatus->previous_marriage_2_start = $this->previousMarriage2Start;
        $studentFamilyStatus->previous_marriage_2_end = $this->previousMarriage3End;
        $studentFamilyStatus->previous_marriage_3_start = $this->previousMarriage3Start;
        $studentFamilyStatus->previous_marriage_3_end = $this->previousMarriage3End;
        $studentFamilyStatus->previous_marriage_4_start = $this->previousMarriage4Start;
        $studentFamilyStatus->previous_marriage_4_end = $this->previousMarriage4End;
        $studentFamilyStatus->previous_marriage_5_start = $this->previousMarriage5Start;
        $studentFamilyStatus->previous_marriage_5_end = $this->previousMarriage5End;
        $studentFamilyStatus->is_spouse_studying = $this->isSpouseStudying;
        $studentFamilyStatus->is_spouse_in_iubip = $this->isSpouseInIubip;
        $studentFamilyStatus->has_children = $this->hasChildren;
        $studentFamilyStatus->child_1_birth_date = $this->child1BirthDate;
        $studentFamilyStatus->child_2_birth_date = $this->child2BirthDate;
        $studentFamilyStatus->child_3_birth_date = $this->child3BirthDate;
        $studentFamilyStatus->child_4_birth_date = $this->child4BirthDate;
        $studentFamilyStatus->child_5_birth_date = $this->child5BirthDate;
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
