<?php

namespace App\Livewire;

use App\Models\AdmissionCampaign as ModelsAdmissionCampaign;
use App\Models\AdmissionCampaignDirection;
use App\Models\AdmissionCampaignStats;
use Carbon\Carbon;
use Livewire\Component;

class AdmissionCampaign extends Component
{
    public $levelEducation = 6;
    public $teachingForm = 1;

    public $applicationsCertificateCount = array();
    public $applicationsDiplomaBachelorCount = array();
    public $applicationsDiplomaCollegeCount = array();

    public $contractsCertificateCount = array();
    public $contractsDiplomaBachelorCount = array();
    public $contractsDiplomaCollegeCount = array();

    public $paidContractsCount = array();

    public function render()
    {
        $admissionCampaign = ModelsAdmissionCampaign::where('is_active', '=', true)->select('id')->first();

        if (!AdmissionCampaignStats::where('created_at', 'like', Carbon::now()->format('Y-m-d') . '%')->exists()) {
            $admissionCampaignDirections = AdmissionCampaignDirection::where('admission_campaign_id', '=', $admissionCampaign->id)
                ->where('is_active', true)
                ->select('id')
                ->get();

            foreach ($admissionCampaignDirections as $key => $value) {
                AdmissionCampaignStats::create([
                    'admission_campaign_direction_id' => $value->id,
                    'admission_campaign_id' => $admissionCampaign->id
                ]);
            }
        }

        $data = AdmissionCampaignStats::join('admission_campaign_directions', 'admission_campaign_stats.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
            ->where('admission_campaign_stats.admission_campaign_id', '=', $admissionCampaign->id)
            ->where('admission_campaign_directions.level_education_id', '=', $this->levelEducation)
            ->where('admission_campaign_directions.teaching_form_id', '=', $this->teachingForm)
            ->select(
                'admission_campaign_stats.id',
                'admission_campaign_directions.speciality_code',
                'admission_campaign_directions.speciality_name',
                'admission_campaign_directions.speciality_profile',
                'admission_campaign_directions.funding_type',
                'admission_campaign_stats.applications_certificate_count',
                'admission_campaign_stats.applications_diploma_bachelor_count',
                'admission_campaign_stats.applications_diploma_college_count',
                'admission_campaign_stats.contracts_certificate_count',
                'admission_campaign_stats.contracts_diploma_bachelor_count',
                'admission_campaign_stats.contracts_diploma_college_count',
                'admission_campaign_stats.paid_contracts_count'
            )
            ->orderBy('admission_campaign_directions.speciality_code')
            ->get();

        foreach ($data as $key => $value) {
            $this->applicationsCertificateCount[$value->id] = $value->applications_certificate_count;
            $this->applicationsDiplomaBachelorCount[$value->id] = $value->applications_diploma_bachelor_count;
            $this->applicationsDiplomaCollegeCount[$value->id] = $value->applications_diploma_college_count;

            $this->contractsCertificateCount[$value->id] = $value->contracts_certificate_count;
            $this->contractsDiplomaBachelorCount[$value->id] = $value->contracts_diploma_bachelor_count;
            $this->contractsDiplomaCollegeCount[$value->id] = $value->contracts_diploma_college_count;

            $this->paidContractsCount[$value->id] = $value->paid_contracts_count;
        }

        return view('livewire.admission-campaign', ['data' => $data]);
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function setLevelEducation($value)
    {
        $this->levelEducation = $value;
    }

    public function setTeachingForm($value)
    {
        $this->teachingForm = $value;
    }

    public function update()
    {
        foreach ($this->applicationsCertificateCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->applications_certificate_count = $value;
            $admissionCampaignStats->update();
        }

        foreach ($this->applicationsDiplomaBachelorCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->applications_diploma_bachelor_count = $value;
            $admissionCampaignStats->update();
        }

        foreach ($this->applicationsDiplomaCollegeCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->applications_diploma_college_count = $value;
            $admissionCampaignStats->update();
        }

        foreach ($this->contractsCertificateCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->contracts_certificate_count = $value;
            $admissionCampaignStats->update();
        }

        foreach ($this->contractsDiplomaBachelorCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->contracts_diploma_bachelor_count = $value;
            $admissionCampaignStats->update();
        }

        foreach ($this->contractsDiplomaCollegeCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->contracts_diploma_college_count = $value;
            $admissionCampaignStats->update();
        }

        foreach ($this->paidContractsCount as $key => $value) {
            $admissionCampaignStats = AdmissionCampaignStats::findOrFail($key);
            $admissionCampaignStats->paid_contracts_count = $value;
            $admissionCampaignStats->update();
        }

        session()->flash('type', 'success');
        session()->flash('message', 'Данные успешно сохранены!');
    }
}
