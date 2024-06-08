<?php

namespace App\Livewire;

use App\Models\AdmissionCampaign;
use App\Models\AdmissionCampaignDirection;
use App\Models\ApplicantAverageScore as ModelsApplicantAverageScore;
use Livewire\Component;

class ApplicantAverageScore extends Component
{
    public $id,
        $lastName = null,
        $firstName = null,
        $middleName = null,
        $threesCount = 0,
        $foursCount = 0,
        $fivesCount = 0,
        $admissionCampaignDirectionId = 0;

    public $activeAdmissionCampaignId = 0;
    public $directionId = 0;
    public $directions = [];

    public $isEntryModalVisible = false, $isEdit = false;

    public function mount()
    {
        $this->directions = AdmissionCampaignDirection::get();
    }

    public function render()
    {
        $activeAdmissionCampaign = AdmissionCampaign::where('is_active', 1)->select('id')->first();
        $this->activeAdmissionCampaignId = $activeAdmissionCampaign->id;

        $data = ModelsApplicantAverageScore::where('admission_campaign_id', '=', $this->activeAdmissionCampaignId)
            ->where('admission_campaign_direction_id', '=', $this->directionId)
            ->paginate(50);

        return view('livewire.applicant-average-score', ['data' => $data]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function setEntryModalVisible($isEdit, $id = null): void
    {
        $this->resetFields();
        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id);
        }

        $this->isEntryModalVisible = !$this->isEntryModalVisible;
    }

    public function setDirectionId()
    {
        $this->directionId = $this->directionId;
    }

    public function store()
    {
        $this->validate([
            'lastName' => 'required|string|max:50',
            'firstName' => 'required|string|max:50',
            'admissionCampaignDirectionId' => 'required|numeric|min:1',
        ]);

        ModelsApplicantAverageScore::create([
            'last_name' => $this->lastName,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'threes_count' => $this->threesCount,
            'fours_count' => $this->foursCount,
            'fives_count' => $this->fivesCount,
            'average_score' => round((($this->threesCount * 3) + ($this->foursCount * 4) + ($this->fivesCount* 5)) / ($this->threesCount+ $this->foursCount + $this->fivesCount), 1),
            'admission_campaign_id' => $this->activeAdmissionCampaignId,
            'admission_campaign_direction_id' => $this->admissionCampaignDirectionId
        ]);

        $this->setEntryModalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function edit($id)
    {
        $this->id = $id;

        $applicantAverageScore = ModelsApplicantAverageScore::findOrFail($this->id);
        $this->lastName = $applicantAverageScore->last_name;
        $this->firstName = $applicantAverageScore->first_name;
        $this->middleName = $applicantAverageScore->middle_name;
        $this->threesCount = $applicantAverageScore->threes_count;
        $this->foursCount = $applicantAverageScore->fours_count;
        $this->fivesCount = $applicantAverageScore->fives_count;
        $this->admissionCampaignDirectionId = $applicantAverageScore->admission_campaign_direction_id;
    }

    public function update()
    {
        $this->validate([
            'lastName' => 'required|string|max:50',
            'firstName' => 'required|string|max:50',
            'admissionCampaignDirectionId' => 'required|numeric|min:1',
        ]);

        $applicantAverageScore = ModelsApplicantAverageScore::findOrFail($this->id);
        $applicantAverageScore->last_name = $this->lastName;
        $applicantAverageScore->first_name = $this->firstName;
        $applicantAverageScore->middle_name = $this->middleName;
        $applicantAverageScore->threes_count = $this->threesCount;
        $applicantAverageScore->fours_count = $this->foursCount;
        $applicantAverageScore->fives_count = $this->fivesCount;
        $applicantAverageScore->average_score = round((($this->threesCount * 3) + ($this->foursCount * 4) + ($this->fivesCount* 5)) / ($this->threesCount+ $this->foursCount + $this->fivesCount), 1);
        $applicantAverageScore->admission_campaign_direction_id = $this->admissionCampaignDirectionId;
        $applicantAverageScore->update();

        $this->setEntryModalVisible(false);

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно обновлена!');
    }

    public function destroy($id): void
    {
        $applicantAverageScore = ModelsApplicantAverageScore::findOrFail($id);
        $applicantAverageScore->delete();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно удалена!');
    }

    private function resetFields(): void
    {
        $this->id = 0;
        $this->lastName = null;
        $this->firstName = null;
        $this->middleName = null;
        $this->threesCount = 0;
        $this->foursCount = 0;
        $this->fivesCount = 0;
        $this->admissionCampaignDirectionId = 0;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
