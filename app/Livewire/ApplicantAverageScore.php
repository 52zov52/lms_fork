<?php

namespace App\Livewire;

use App\Models\AdmissionCampaign;
use App\Models\AdmissionCampaignDirection;
use App\Models\ApplicantAverageScore as ModelsApplicantAverageScore;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicantAverageScore extends Component
{
    use WithPagination;

    public $id,
        $lastName = null,
        $firstName = null,
        $middleName = null,
        $phone = null,
        $email = null,
        $threesCount = 0,
        $foursCount = 0,
        $fivesCount = 0,
        $completedClassesCount = 9,
        $admissionCampaignDirectionId = 0,
        $is_contracted = false;

    public $activeAdmissionCampaignId = 0;
    public $directionId = 0;
    public $directions = [];

    public $totalAverageScore = 0.0;
    public $contractAverageScore = 0.0;

    public $isEntryModalVisible = false, $isEdit = false;

    public function mount()
    {
        $this->directions = AdmissionCampaignDirection::where('level_education_id', '=', 2)->get();
    }

    public function render()
    {
        $activeAdmissionCampaign = AdmissionCampaign::where('is_active', 1)->select('id')->first();
        $this->activeAdmissionCampaignId = $activeAdmissionCampaign->id;

        $data = ModelsApplicantAverageScore::where('admission_campaign_id', '=', $this->activeAdmissionCampaignId)
            ->where('admission_campaign_direction_id', '=', $this->directionId)
            ->orderBy('average_score', 'DESC')
            ->paginate(50);

        if ($this->directionId != 0) {
            $averageScoreData = ModelsApplicantAverageScore::where('admission_campaign_id', '=', $this->activeAdmissionCampaignId)
                ->where('admission_campaign_direction_id', '=', $this->directionId)
                ->select(
                    DB::raw("COUNT('id') as num_rows"),
                    DB::raw('SUM(average_score) as sum_average_score'),
                    DB::raw("SUM(CASE WHEN is_contracted = 1 THEN 1 ELSE 0 END) as contract_num_rows"),
                    DB::raw('SUM(CASE WHEN is_contracted = 1 THEN average_score ELSE 0 END) as sum_contract_average_score'),
                )
                ->first();

                $this->totalAverageScore = round($averageScoreData->sum_average_score / $averageScoreData->num_rows, 1);
                $this->contractAverageScore = round($averageScoreData->sum_contract_average_score / $averageScoreData->contract_num_rows, 1);
        }

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
            'phone' => $this->phone,
            'email' => $this->email,
            'threes_count' => $this->threesCount,
            'fours_count' => $this->foursCount,
            'fives_count' => $this->fivesCount,
            'completed_classes_count' => $this->completedClassesCount,
            'average_score' => round((($this->threesCount * 3) + ($this->foursCount * 4) + ($this->fivesCount * 5)) / ($this->threesCount + $this->foursCount + $this->fivesCount), 1),
            'admission_campaign_id' => $this->activeAdmissionCampaignId,
            'admission_campaign_direction_id' => $this->admissionCampaignDirectionId,
            'is_contracted' => ($this->is_contracted) ? true : false
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
        $this->phone = $applicantAverageScore->phone;
        $this->email = $applicantAverageScore->email;
        $this->threesCount = $applicantAverageScore->threes_count;
        $this->foursCount = $applicantAverageScore->fours_count;
        $this->fivesCount = $applicantAverageScore->fives_count;
        $this->completedClassesCount = $applicantAverageScore->completed_classes_count;
        $this->admissionCampaignDirectionId = $applicantAverageScore->admission_campaign_direction_id;
        $this->is_contracted = ($applicantAverageScore->is_contracted) ? true : false;
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
        $applicantAverageScore->phone = $this->phone;
        $applicantAverageScore->email = $this->email;
        $applicantAverageScore->threes_count = $this->threesCount;
        $applicantAverageScore->fours_count = $this->foursCount;
        $applicantAverageScore->fives_count = $this->fivesCount;
        $applicantAverageScore->completed_classes_count = $this->completedClassesCount;
        $applicantAverageScore->average_score = round((($this->threesCount * 3) + ($this->foursCount * 4) + ($this->fivesCount * 5)) / ($this->threesCount + $this->foursCount + $this->fivesCount), 1);
        $applicantAverageScore->admission_campaign_direction_id = $this->admissionCampaignDirectionId;
        $applicantAverageScore->is_contracted = ($this->is_contracted) ? true : false;
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

    public function print()
    {
        return redirect()->route('print.applicant.average.score');
    }

    private function resetFields(): void
    {
        $this->id = 0;
        $this->lastName = null;
        $this->firstName = null;
        $this->middleName = null;
        $this->phone = null;
        $this->email = null;
        $this->threesCount = 0;
        $this->foursCount = 0;
        $this->fivesCount = 0;
        $this->completedClassesCount = 9;
        $this->admissionCampaignDirectionId = 0;
        $this->is_contracted = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
