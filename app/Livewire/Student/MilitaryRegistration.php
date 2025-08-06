<?php

namespace App\Livewire\Student;

use App\Models\MilitaryRegistrationStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MilitaryRegistration extends Component
{
    public $reserveCategory,
        $militaryRank,
        $militaryOccupationProfile,
        $fullMilitarySpecialtyCode,
        $fitnessCategory,
        $militaryCommissariatName,
        $militaryRegistrationTypeGeneral,
        $militaryRegistrationTypeSpecial,
        $militaryDocument,
        $documentSeries,
        $documentNumber,
        $issuedBy,
        $issueDate,
        $userId,
        $status,
        $createdAt,
        $updatedAt;

    const SAVE_STATUS = 1;
    const UPDATE_STATUS = 2;

    public function render()
    {
        $militaryRegistrationStudent = MilitaryRegistrationStudent::where('user_id', '=', Auth::user()->id)
            ->first();

        if(!is_null($militaryRegistrationStudent)){
            $this->reserveCategory = $militaryRegistrationStudent->reserve_category;
            $this->militaryRank = $militaryRegistrationStudent->military_rank;
            $this->militaryOccupationProfile = $militaryRegistrationStudent->military_occupation_profile;
            $this->fullMilitarySpecialtyCode = $militaryRegistrationStudent->full_military_specialty_code;
            $this->fitnessCategory = $militaryRegistrationStudent->fitness_category;
            $this->militaryCommissariatName = $militaryRegistrationStudent->military_commissariat_name;
            $this->militaryRegistrationTypeGeneral = $militaryRegistrationStudent->military_registration_type_general;
            $this->militaryRegistrationTypeSpecial = $militaryRegistrationStudent->military_registration_type_special;
            $this->militaryDocument = $militaryRegistrationStudent->military_document;
            $this->documentSeries = $militaryRegistrationStudent->document_series;
            $this->documentNumber = $militaryRegistrationStudent->document_number;
            $this->issuedBy = $militaryRegistrationStudent->issued_by;
            $this->issueDate = $militaryRegistrationStudent->issue_date;
            $this->userId = $militaryRegistrationStudent->user_id;
            $this->status = $militaryRegistrationStudent->status;
            $this->createdAt = Carbon::parse($militaryRegistrationStudent->created_at)->format('d.m.Y H:m:s');
            $this->updatedAt = Carbon::parse($militaryRegistrationStudent->updated_at)->format('d.m.Y H:m:s');
        }

        return view('livewire.student.military-registration');
    }

    public function action()
    {
        $militaryRegistrationStudent = MilitaryRegistrationStudent::where('user_id', '=', Auth::user()->id)
            ->select('id')
            ->first();

        if (is_null($militaryRegistrationStudent)) {
            $this->store();
        } else {
            $this->update($militaryRegistrationStudent->id);
        }
    }

    public function store()
    {
        $this->validate([
            'reserveCategory' => 'nullable|string|max:10',
            'militaryRank' => 'nullable|string|max:50',
            'militaryOccupationProfile' => 'nullable|string|max:50',
            'fullMilitarySpecialtyCode' => 'nullable|string|max:50',
            'fitnessCategory' => 'required|string|max:10',
            'militaryCommissariatName' => 'required|string|max:255',
            'militaryRegistrationTypeGeneral' => 'nullable|string|max:255',
            'militaryRegistrationTypeSpecial' => 'nullable|string|max:255',
            'militaryDocument' => 'required|string|max:255',
            'documentSeries' => 'required|string|max:50',
            'documentNumber' => 'required|string|max:50',
            'issuedBy' => 'required|string|max:255',
            'issueDate' => 'required',
        ]);

        MilitaryRegistrationStudent::create([
            'reserve_category' => $this->reserveCategory,
            'military_rank' => $this->militaryRank,
            'military_occupation_profile' => $this->militaryOccupationProfile,
            'full_military_specialty_code' => $this->fullMilitarySpecialtyCode,
            'fitness_category' => $this->fitnessCategory,
            'military_commissariat_name' => $this->militaryCommissariatName,
            'military_registration_type_general' => $this->militaryRegistrationTypeGeneral,
            'military_registration_type_special' => $this->militaryRegistrationTypeSpecial,
            'military_document' => $this->militaryDocument,
            'document_series' => $this->documentSeries,
            'document_number' => $this->documentNumber,
            'issued_by' => $this->issuedBy,
            'issue_date' => Carbon::parse($this->issueDate)->format('Y-m-d'),
            'user_id' => Auth::user()->id,
            'status' => self::SAVE_STATUS
        ]);

        $this->dispatch('show-toast', message: 'Данные успешно сохранены!', type: 'success');
    }

    public function update($id)
    {
        $this->validate([
            'reserveCategory' => 'nullable|string|max:10',
            'militaryRank' => 'nullable|string|max:50',
            'militaryOccupationProfile' => 'nullable|string|max:50',
            'fullMilitarySpecialtyCode' => 'nullable|string|max:50',
            'fitnessCategory' => 'required|string|max:10',
            'militaryCommissariatName' => 'required|string|max:255',
            'militaryRegistrationTypeGeneral' => 'nullable|string|max:255',
            'militaryRegistrationTypeSpecial' => 'nullable|string|max:255',
            'militaryDocument' => 'required|string|max:255',
            'documentSeries' => 'required|string|max:50',
            'documentNumber' => 'required|string|max:50',
            'issuedBy' => 'required|string|max:255',
            'issueDate' => 'required',
        ]);

        $militaryRegistrationStudent = MilitaryRegistrationStudent::findOrFail($id);
        $militaryRegistrationStudent->reserve_category = $this->reserveCategory;
        $militaryRegistrationStudent->military_rank = $this->militaryRank;
        $militaryRegistrationStudent->military_occupation_profile = $this->militaryOccupationProfile;
        $militaryRegistrationStudent->full_military_specialty_code = $this->fullMilitarySpecialtyCode;
        $militaryRegistrationStudent->fitness_category = $this->fitnessCategory;
        $militaryRegistrationStudent->military_commissariat_name = $this->militaryCommissariatName;
        $militaryRegistrationStudent->military_registration_type_general = $this->militaryRegistrationTypeGeneral;
        $militaryRegistrationStudent->military_registration_type_special = $this->militaryRegistrationTypeSpecial;
        $militaryRegistrationStudent->military_document = $this->militaryDocument;
        $militaryRegistrationStudent->document_series = $this->documentSeries;
        $militaryRegistrationStudent->document_number = $this->documentNumber;
        $militaryRegistrationStudent->issued_by = $this->issuedBy;
        $militaryRegistrationStudent->issue_date = Carbon::parse($this->issueDate)->format('Y-m-d');
        $militaryRegistrationStudent->status = self::UPDATE_STATUS;
        $militaryRegistrationStudent->save();

        $this->dispatch('show-toast', message: 'Данные успешно обновлены!', type: 'success');
    }
}
