<?php

namespace App\Livewire\Student;

use App\Http\Controllers\Student\SocialPassportController;
use App\Models\SocialPassport as ModelsSocialPassport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SocialPassport extends Component
{
    public $id = 0,
        $lastName = '',
        $firstName = '',
        $middleName = '',
        $birthday = '',
        $registeredAddress = '',
        $housingConditions = '',
        $cohabitants = [],
        $familyComposition = '',
        $numberOfChildren = 1,
        $fatherLastName = '',
        $fatherFirstName = '',
        $fatherMiddleName = '',
        $fatherWorkplace = '',
        $fatherPhone = '',
        $motherLastName = '',
        $motherFirstName = '',
        $motherMiddleName = '',
        $motherWorkplace = '',
        $motherPhone = '',
        $fatherDisability = false,
        $motherDisability = false,
        $categories = [];

    public function render()
    {
        $userData = SocialPassportController::getFio();

        $this->lastName = $userData['last_name'];
        $this->firstName = $userData['first_name'];
        $this->middleName = $userData['middle_name'];
        $this->birthday = Carbon::parse($userData['birthday'])->format('d.m.Y');

        $socialPassport = ModelsSocialPassport::where('user_id', '=', Auth::user()->id)->first();
        if (!is_null($socialPassport)) {
            $this->categories = json_decode($socialPassport->category, true);
        }

        return view('livewire.student.social-passport');
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function save()
    {
        $socialPassport = ModelsSocialPassport::where('user_id', '=', Auth::user()->id)->first();

        if (is_null($socialPassport)) {
            $this->store();
        } else {
            $this->update($socialPassport->id);
        }
    }

    private function store()
    {
        ModelsSocialPassport::create([
            'user_id' => Auth::user()->id,
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'birthday' => Carbon::parse($this->birthday)->format('Y-m-d'),
            'registered_address' => $this->registeredAddress,
            'housing_conditions' => $this->housingConditions,
            'cohabitants' => $this->cohabitants,
            'family_composition' => $this->familyComposition,
            'number_of_children' => $this->numberOfChildren,
            'father_last_name' => $this->fatherLastName,
            'father_first_name' => $this->fatherFirstName,
            'father_middle_name' => $this->fatherMiddleName,
            'father_workplace' => $this->fatherWorkplace,
            'father_phone' => $this->fatherPhone,
            'mother_last_name' => $this->motherLastName,
            'mother_first_name' => $this->motherFirstName,
            'mother_middle_name' => $this->motherMiddleName,
            'mother_workplace' => $this->motherWorkplace,
            'mother_phone' => $this->motherPhone,
            'father_disability' => $this->fatherDisability,
            'mother_disability' => $this->motherDisability,
            'category' => $this->categories
        ]);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    private function update($id)
    {
        $socialPassport = ModelsSocialPassport::findOrFail($id);
        $socialPassport->registered_address = $this->registeredAddress;
        $socialPassport->housing_conditions = $this->housingConditions;
        $socialPassport->cohabitants = $this->cohabitants;
        $socialPassport->family_composition = $this->familyComposition;
        $socialPassport->number_of_children = $this->numberOfChildren;
        $socialPassport->father_last_name = $this->fatherLastName;
        $socialPassport->father_first_name = $this->fatherFirstName;
        $socialPassport->father_middle_name = $this->fatherMiddleName;
        $socialPassport->father_workplace = $this->fatherWorkplace;
        $socialPassport->father_phone = $this->fatherPhone;
        $socialPassport->mother_last_name = $this->motherLastName;
        $socialPassport->mother_first_name = $this->motherFirstName;
        $socialPassport->mother_middle_name = $this->motherMiddleName;
        $socialPassport->mother_workplace = $this->motherWorkplace;
        $socialPassport->mother_phone = $this->motherPhone;
        $socialPassport->father_disability = $this->fatherDisability;
        $socialPassport->mother_disability = $this->motherDisability;
        $socialPassport->category = $this->categories;
        $socialPassport->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }
}
