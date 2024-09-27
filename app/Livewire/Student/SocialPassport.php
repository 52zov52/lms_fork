<?php

namespace App\Livewire\Student;

use App\Http\Controllers\Student\SocialPassportController;
use App\Models\SocialPassport as ModelsSocialPassport;
use App\Models\UserData;
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
        $citizenship = '',
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
        $childCategories = [],
        $questionnaireStatus = 0;

    public function render()
    {
        $userData = UserData::select(
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name',
            'users_data.birthday',
            'countries.country_name'
        )
            ->join('users_addresses', 'users_data.user_id', '=', 'users_addresses.user_id')
            ->join('countries', 'users_addresses.country_id', '=', 'countries.id')
            ->where('users_data.user_id', '=', Auth::user()->id)
            ->first()->toArray();

        $this->lastName = $userData['last_name'];
        $this->firstName = $userData['first_name'];
        $this->middleName = $userData['middle_name'];
        $this->birthday = Carbon::parse($userData['birthday'])->format('d.m.Y');

        $socialPassport = ModelsSocialPassport::where('user_id', '=', Auth::user()->id)->first();
        if (!is_null($socialPassport)) {
            $this->citizenship = $socialPassport->citizenship;
            $this->registeredAddress = $socialPassport->registered_address;
            $this->housingConditions = $socialPassport->housing_conditions;
            $this->cohabitants = json_decode($socialPassport->cohabitants, true);
            $this->familyComposition = $socialPassport->family_composition;
            $this->numberOfChildren = $socialPassport->number_of_children;
            $this->fatherLastName = $socialPassport->father_last_name;
            $this->fatherFirstName = $socialPassport->father_first_name;
            $this->fatherMiddleName = $socialPassport->father_middle_name;
            $this->fatherWorkplace = $socialPassport->father_workplace;
            $this->fatherPhone = $socialPassport->father_phone;
            $this->motherLastName = $socialPassport->mother_last_name;
            $this->motherFirstName = $socialPassport->mother_first_name;
            $this->motherMiddleName = $socialPassport->mother_middle_name;
            $this->motherWorkplace = $socialPassport->mother_workplace;
            $this->motherPhone = $socialPassport->mother_phone;
            $this->fatherDisability = $socialPassport->father_disability;
            $this->motherDisability = $socialPassport->mother_disability;
            $this->childCategories = json_decode($socialPassport->child_categories, true);
            $this->questionnaireStatus = $socialPassport->questionnaire_status;
        }else {
            $this->citizenship = $userData['country_name'];
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

    public function backToEdit()
    {
        $socialPassport = ModelsSocialPassport::where('user_id', '=', Auth::user()->id)->first();

        if (!is_null($socialPassport)) {
            $socialPassport->questionnaire_status = 0;
            $socialPassport->user_signature = null;
            $socialPassport->save();

            session()->flash('type', 'success');
            session()->flash('message', 'Запись успешно обновлена!');
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Ошибка обновления записи!');
        }
    }

    private function store()
    {
        $this->validate([
            'citizenship' => 'required|string|max:255',
            'registeredAddress' => 'required|string|max:1500',
            'housingConditions' => 'required',
            'cohabitants' => 'required|array',
            'familyComposition' => 'required',
            'numberOfChildren' => 'required|integer|min:1',
            'fatherLastName' => 'string|max:50',
            'fatherFirstName' => 'string|max:50',
            'fatherMiddleName' => 'string|max:50',
            'fatherWorkplace' => 'string|max:255',
            'fatherPhone' => 'string|max:20',
            'motherLastName' => 'string|max:50',
            'motherFirstName' => 'string|max:50',
            'motherMiddleName' => 'string|max:50',
            'motherWorkplace' => 'string|max:255',
            'motherPhone' => 'string|max:20',
            'childCategories' => 'array'
        ]);

        ModelsSocialPassport::create([
            'user_id' => Auth::user()->id,
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'birthday' => Carbon::parse($this->birthday)->format('Y-m-d'),
            'citizenship' => $this->citizenship,
            'registered_address' => $this->registeredAddress,
            'housing_conditions' => $this->housingConditions,
            'cohabitants' => json_encode($this->cohabitants),
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
            'child_categories' => json_encode($this->childCategories),
            'questionnaire_status' => 1,
            'user_signature' => SocialPassportController::getSignature(Auth::user()->id)
        ]);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    private function update($id)
    {
        $this->validate([
            'citizenship' => 'required|string|max:255',
            'registeredAddress' => 'required|string|max:1500',
            'housingConditions' => 'required',
            'cohabitants' => 'required|array',
            'familyComposition' => 'required',
            'numberOfChildren' => 'required|integer|min:1',
            'fatherLastName' => 'string|max:50',
            'fatherFirstName' => 'string|max:50',
            'fatherMiddleName' => 'string|max:50',
            'fatherWorkplace' => 'string|max:255',
            'fatherPhone' => 'string|max:20',
            'motherLastName' => 'string|max:50',
            'motherFirstName' => 'string|max:50',
            'motherMiddleName' => 'string|max:50',
            'motherWorkplace' => 'string|max:255',
            'motherPhone' => 'string|max:20',
            'childCategories' => 'array'
        ]);

        $socialPassport = ModelsSocialPassport::findOrFail($id);
        $socialPassport->citizenship = $this->citizenship;
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
        $socialPassport->child_categories = $this->childCategories;
        $socialPassport->questionnaire_status = 1;
        $socialPassport->user_signature = SocialPassportController::getSignature(Auth::user()->id);
        $socialPassport->save();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }
}
