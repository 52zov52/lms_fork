<?php

namespace App\Livewire;

use App\Models\AdmissionCampaign;
use App\Models\AdmissionCampaignDirection;
use App\Models\AdmissionCampaignStatsBachelor;
use App\Models\AdmissionCampaignStatsCollege;
use App\Models\AdmissionCampaignStatsMastersDegree;
use App\Models\AdmissionCampaignStatsPhD;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdmissionCampaignStats extends Component
{
    public $levelEducation = 2;
    public $teachingForm = 1;

    //college
    public $collegeApplicationsAfterNineGradesCount = array();
    public $collegeApplicationsAfterEleventhGradesCount = array();
    public $collegeContractsAfterNineGradesCount = array();
    public $collegeContractsAfterEleventhGradesCount = array();
    public $collegePaidContractsCount = array();

    //bachelor
    public $bachelorApplicationsCertificateCount = array();
    public $bachelorApplicationsDiplomaBachelorCount = array();
    public $bachelorApplicationsDiplomaCollegeCount = array();
    public $bachelorContractsCertificateCount = array();
    public $bachelorContractsDiplomaBachelorCount = array();
    public $bachelorContractsDiplomaCollegeCount = array();
    public $bachelorPaidContractsCount = array();

    //mastersDegree
    public $mastersDegreeApplicationsCount = array();
    public $mastersDegreeContractsCount = array();
    public $mastersDegreePaidContractsCount = array();

    //PhD
    public $phdApplicationsCount = array();
    public $phdContractsCount = array();
    public $phdPaidContractsCount = array();

    public $college = [], $bachelor = [], $mastersDegree = [], $phd = [];

    public $listOfDate = [];
    public $selectedDate = "";

    public function render()
    {
        $admissionCampaign = AdmissionCampaign::where('is_active', '=', true)->select('id')->first();

        $this->listOfDate = $this->getListOfDate($admissionCampaign->id);
        $this->selectedDate = (empty($this->selectedDate)) ? Carbon::now()->format('d.m.Y') : Carbon::parse($this->selectedDate)->format('d.m.Y');

        $admissionCampaignDirections = AdmissionCampaignDirection::where('admission_campaign_id', '=', $admissionCampaign->id)
            ->where('is_active', true)
            ->select('id', 'level_education_id')
            ->get();

        if (!AdmissionCampaignStatsCollege::where('created_at', 'like', Carbon::now()->format('Y-m-d') . '%')->exists()) {
            foreach ($admissionCampaignDirections as $key => $value) {
                if ($value->level_education_id == 2) {
                    AdmissionCampaignStatsCollege::create([
                        'admission_campaign_direction_id' => $value->id,
                        'admission_campaign_id' => $admissionCampaign->id
                    ]);
                }
            }
        }

        if (!AdmissionCampaignStatsBachelor::where('created_at', 'like', Carbon::now()->format('Y-m-d') . '%')->exists()) {
            foreach ($admissionCampaignDirections as $key => $value) {
                if ($value->level_education_id == 6) {
                    AdmissionCampaignStatsBachelor::create([
                        'admission_campaign_direction_id' => $value->id,
                        'admission_campaign_id' => $admissionCampaign->id
                    ]);
                }
            }
        }

        if (!AdmissionCampaignStatsMastersDegree::where('created_at', 'like', Carbon::now()->format('Y-m-d') . '%')->exists()) {
            foreach ($admissionCampaignDirections as $key => $value) {
                if ($value->level_education_id == 7) {
                    AdmissionCampaignStatsMastersDegree::create([
                        'admission_campaign_direction_id' => $value->id,
                        'admission_campaign_id' => $admissionCampaign->id
                    ]);
                }
            }
        }

        if (!AdmissionCampaignStatsPhD::where('created_at', 'like', Carbon::now()->format('Y-m-d') . '%')->exists()) {
            foreach ($admissionCampaignDirections as $key => $value) {
                if ($value->level_education_id == 4) {
                    AdmissionCampaignStatsPhD::create([
                        'admission_campaign_direction_id' => $value->id,
                        'admission_campaign_id' => $admissionCampaign->id
                    ]);
                }
            }
        }

        if ($this->levelEducation == 2) {
            $this->bachelor = [];
            $this->mastersDegree = [];
            $this->phd = [];

            $this->college = AdmissionCampaignStatsCollege::join('admission_campaign_directions', 'admission_campaign_stats_colleges.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
                ->where('admission_campaign_stats_colleges.admission_campaign_id', '=', $admissionCampaign->id)
                ->where('admission_campaign_directions.level_education_id', '=', $this->levelEducation)
                ->where('admission_campaign_directions.teaching_form_id', '=', $this->teachingForm)
                ->where('admission_campaign_stats_colleges.created_at', 'like', Carbon::parse($this->selectedDate)->format('Y-m-d') . '%')
                ->select(
                    'admission_campaign_stats_colleges.id',
                    'admission_campaign_directions.speciality_code',
                    'admission_campaign_directions.speciality_name',
                    'admission_campaign_directions.speciality_profile',
                    'admission_campaign_directions.funding_type',
                    'admission_campaign_stats_colleges.applications_after_nine_grades_count',
                    'admission_campaign_stats_colleges.applications_after_eleventh_grades_count',
                    'admission_campaign_stats_colleges.contracts_after_nine_grades_count',
                    'admission_campaign_stats_colleges.contracts_after_eleventh_grades_count',
                    'admission_campaign_stats_colleges.paid_contracts_count'
                )
                ->orderBy('admission_campaign_directions.speciality_code')
                ->get();

            foreach ($this->college as $key => $value) {
                $this->collegeApplicationsAfterNineGradesCount[$value->id] = $value->applications_after_nine_grades_count;
                $this->collegeApplicationsAfterEleventhGradesCount[$value->id] = $value->applications_after_eleventh_grades_count;
                $this->collegeContractsAfterNineGradesCount[$value->id] = $value->contracts_after_nine_grades_count;
                $this->collegeContractsAfterEleventhGradesCount[$value->id] = $value->contracts_after_eleventh_grades_count;
                $this->collegePaidContractsCount[$value->id] = $value->paid_contracts_count;
            }
        }

        if ($this->levelEducation == 6) {
            $this->college = [];
            $this->mastersDegree = [];
            $this->phd = [];

            $this->bachelor = AdmissionCampaignStatsBachelor::join('admission_campaign_directions', 'admission_campaign_stats_bachelors.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
                ->where('admission_campaign_stats_bachelors.admission_campaign_id', '=', $admissionCampaign->id)
                ->where('admission_campaign_directions.level_education_id', '=', $this->levelEducation)
                ->where('admission_campaign_directions.teaching_form_id', '=', $this->teachingForm)
                ->where('admission_campaign_stats_bachelors.created_at', 'like', Carbon::parse($this->selectedDate)->format('Y-m-d') . '%')
                ->select(
                    'admission_campaign_stats_bachelors.id',
                    'admission_campaign_directions.speciality_code',
                    'admission_campaign_directions.speciality_name',
                    'admission_campaign_directions.speciality_profile',
                    'admission_campaign_directions.funding_type',
                    'admission_campaign_stats_bachelors.applications_certificate_count',
                    'admission_campaign_stats_bachelors.applications_diploma_bachelor_count',
                    'admission_campaign_stats_bachelors.applications_diploma_college_count',
                    'admission_campaign_stats_bachelors.contracts_certificate_count',
                    'admission_campaign_stats_bachelors.contracts_diploma_bachelor_count',
                    'admission_campaign_stats_bachelors.contracts_diploma_college_count',
                    'admission_campaign_stats_bachelors.paid_contracts_count'
                )
                ->orderBy('admission_campaign_directions.speciality_code')
                ->get();

            foreach ($this->bachelor as $key => $value) {
                $this->bachelorApplicationsCertificateCount[$value->id] = $value->applications_certificate_count;
                $this->bachelorApplicationsDiplomaBachelorCount[$value->id] = $value->applications_diploma_bachelor_count;
                $this->bachelorApplicationsDiplomaCollegeCount[$value->id] = $value->applications_diploma_college_count;
                $this->bachelorContractsCertificateCount[$value->id] = $value->contracts_certificate_count;
                $this->bachelorContractsDiplomaBachelorCount[$value->id] = $value->contracts_diploma_bachelor_count;
                $this->bachelorContractsDiplomaCollegeCount[$value->id] = $value->contracts_diploma_college_count;
                $this->bachelorPaidContractsCount[$value->id] = $value->paid_contracts_count;
            }
        }

        if ($this->levelEducation == 7) {
            $this->college = [];
            $this->bachelor = [];
            $this->phd = [];

            $this->mastersDegree = AdmissionCampaignStatsMastersDegree::join('admission_campaign_directions', 'admission_campaign_stats_masters_degrees.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
                ->where('admission_campaign_stats_masters_degrees.admission_campaign_id', '=', $admissionCampaign->id)
                ->where('admission_campaign_directions.level_education_id', '=', $this->levelEducation)
                ->where('admission_campaign_directions.teaching_form_id', '=', $this->teachingForm)
                ->where('admission_campaign_stats_masters_degrees.created_at', 'like', Carbon::parse($this->selectedDate)->format('Y-m-d') . '%')
                ->select(
                    'admission_campaign_stats_masters_degrees.id',
                    'admission_campaign_directions.speciality_code',
                    'admission_campaign_directions.speciality_name',
                    'admission_campaign_directions.speciality_profile',
                    'admission_campaign_directions.funding_type',
                    'admission_campaign_stats_masters_degrees.applications_count',
                    'admission_campaign_stats_masters_degrees.contracts_count',
                    'admission_campaign_stats_masters_degrees.paid_contracts_count'
                )
                ->orderBy('admission_campaign_directions.speciality_code')
                ->get();

            foreach ($this->mastersDegree as $key => $value) {
                $this->mastersDegreeApplicationsCount[$value->id] = $value->applications_count;
                $this->mastersDegreeContractsCount[$value->id] = $value->contracts_count;
                $this->mastersDegreePaidContractsCount[$value->id] = $value->paid_contracts_count;
            }
        }

        if ($this->levelEducation == 4) {
            $this->college = [];
            $this->bachelor = [];
            $this->mastersDegree = [];

            $this->phd = AdmissionCampaignStatsPhD::join('admission_campaign_directions', 'admission_campaign_stats_phd.admission_campaign_direction_id', '=', 'admission_campaign_directions.id')
                ->where('admission_campaign_stats_phd.admission_campaign_id', '=', $admissionCampaign->id)
                ->where('admission_campaign_directions.level_education_id', '=', $this->levelEducation)
                ->where('admission_campaign_directions.teaching_form_id', '=', $this->teachingForm)
                ->where('admission_campaign_stats_phd.created_at', 'like', Carbon::parse($this->selectedDate)->format('Y-m-d') . '%')
                ->select(
                    'admission_campaign_stats_phd.id',
                    'admission_campaign_directions.speciality_code',
                    'admission_campaign_directions.speciality_name',
                    'admission_campaign_directions.speciality_profile',
                    'admission_campaign_directions.funding_type',
                    'admission_campaign_stats_phd.applications_count',
                    'admission_campaign_stats_phd.contracts_count',
                    'admission_campaign_stats_phd.paid_contracts_count'
                )
                ->orderBy('admission_campaign_directions.speciality_code')
                ->get();

            foreach ($this->phd as $key => $value) {
                $this->phdApplicationsCount[$value->id] = $value->applications_count;
                $this->phdContractsCount[$value->id] = $value->contracts_count;
                $this->phdPaidContractsCount[$value->id] = $value->paid_contracts_count;
            }
        }

        return view('livewire.admission-campaign-stats', [
            'college' => $this->college,
            'bachelor' => $this->bachelor,
            'mastersDegree' => $this->mastersDegree,
            'phd' => $this->phd
        ]);
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

    public function setDay()
    {
        $this->selectedDate = $this->selectedDate;
    }

    public function update()
    {
        if ($this->levelEducation == 2) {
            foreach ($this->collegeApplicationsAfterNineGradesCount as $key => $value) {
                $admissionCampaignStatsCollege = AdmissionCampaignStatsCollege::findOrFail($key);
                $admissionCampaignStatsCollege->applications_after_nine_grades_count = $value;
                $admissionCampaignStatsCollege->update();
            }

            foreach ($this->collegeApplicationsAfterEleventhGradesCount as $key => $value) {
                $admissionCampaignStatsCollege = AdmissionCampaignStatsCollege::findOrFail($key);
                $admissionCampaignStatsCollege->applications_after_eleventh_grades_count = $value;
                $admissionCampaignStatsCollege->update();
            }

            foreach ($this->collegeContractsAfterNineGradesCount as $key => $value) {
                $admissionCampaignStatsCollege = AdmissionCampaignStatsCollege::findOrFail($key);
                $admissionCampaignStatsCollege->contracts_after_nine_grades_count = $value;
                $admissionCampaignStatsCollege->update();
            }

            foreach ($this->collegeContractsAfterEleventhGradesCount as $key => $value) {
                $admissionCampaignStatsCollege = AdmissionCampaignStatsCollege::findOrFail($key);
                $admissionCampaignStatsCollege->contracts_after_eleventh_grades_count = $value;
                $admissionCampaignStatsCollege->update();
            }

            foreach ($this->collegePaidContractsCount as $key => $value) {
                $admissionCampaignStatsCollege = AdmissionCampaignStatsCollege::findOrFail($key);
                $admissionCampaignStatsCollege->paid_contracts_count = $value;
                $admissionCampaignStatsCollege->update();
            }
        }

        if ($this->levelEducation == 6) {
            foreach ($this->bachelorApplicationsCertificateCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->applications_certificate_count = $value;
                $admissionCampaignStatsBachelor->update();
            }

            foreach ($this->bachelorApplicationsDiplomaBachelorCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->applications_diploma_bachelor_count = $value;
                $admissionCampaignStatsBachelor->update();
            }

            foreach ($this->bachelorApplicationsDiplomaCollegeCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->applications_diploma_college_count = $value;
                $admissionCampaignStatsBachelor->update();
            }

            foreach ($this->bachelorContractsCertificateCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->contracts_certificate_count = $value;
                $admissionCampaignStatsBachelor->update();
            }

            foreach ($this->bachelorContractsDiplomaBachelorCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->contracts_diploma_bachelor_count = $value;
                $admissionCampaignStatsBachelor->update();
            }

            foreach ($this->bachelorContractsDiplomaCollegeCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->contracts_diploma_college_count = $value;
                $admissionCampaignStatsBachelor->update();
            }

            foreach ($this->bachelorPaidContractsCount as $key => $value) {
                $admissionCampaignStatsBachelor = AdmissionCampaignStatsBachelor::findOrFail($key);
                $admissionCampaignStatsBachelor->paid_contracts_count = $value;
                $admissionCampaignStatsBachelor->update();
            }
        }

        if ($this->levelEducation == 7) {
            foreach ($this->mastersDegreeApplicationsCount as $key => $value) {
                $admissionCampaignStatsMastersDegree = AdmissionCampaignStatsMastersDegree::findOrFail($key);
                $admissionCampaignStatsMastersDegree->applications_count = $value;
                $admissionCampaignStatsMastersDegree->update();
            }

            foreach ($this->mastersDegreeContractsCount as $key => $value) {
                $admissionCampaignStatsMastersDegree = AdmissionCampaignStatsMastersDegree::findOrFail($key);
                $admissionCampaignStatsMastersDegree->contracts_count = $value;
                $admissionCampaignStatsMastersDegree->update();
            }

            foreach ($this->mastersDegreePaidContractsCount as $key => $value) {
                $admissionCampaignStatsMastersDegree = AdmissionCampaignStatsMastersDegree::findOrFail($key);
                $admissionCampaignStatsMastersDegree->paid_contracts_count = $value;
                $admissionCampaignStatsMastersDegree->update();
            }
        }

        if ($this->levelEducation == 4) {
            foreach ($this->phdApplicationsCount as $key => $value) {
                $admissionCampaignStatsPhD = AdmissionCampaignStatsPhD::findOrFail($key);
                $admissionCampaignStatsPhD->applications_count = $value;
                $admissionCampaignStatsPhD->update();
            }

            foreach ($this->phdContractsCount as $key => $value) {
                $admissionCampaignStatsPhD = AdmissionCampaignStatsPhD::findOrFail($key);
                $admissionCampaignStatsPhD->contracts_count = $value;
                $admissionCampaignStatsPhD->update();
            }

            foreach ($this->phdPaidContractsCount as $key => $value) {
                $admissionCampaignStatsPhD = AdmissionCampaignStatsPhD::findOrFail($key);
                $admissionCampaignStatsPhD->paid_contracts_count = $value;
                $admissionCampaignStatsPhD->update();
            }
        }

        session()->flash('type', 'success');
        session()->flash('message', 'Данные успешно сохранены!');
    }

    private function getListOfDate($admission_campaign_id): array
    {
        $daysOfWeekCollege = AdmissionCampaignStatsCollege::where('admission_campaign_id', '=', $admission_campaign_id)
            ->select(DB::raw("DISTINCT DATE_FORMAT(created_at, '%d.%m.%Y') as date_of_week"))
            ->get();
        $daysOfWeekBachelor = AdmissionCampaignStatsBachelor::where('admission_campaign_id', '=', $admission_campaign_id)
            ->select(DB::raw("DISTINCT DATE_FORMAT(created_at, '%d.%m.%Y') as date_of_week"))
            ->get();
        $daysOfWeekMastersDegree = AdmissionCampaignStatsMastersDegree::where('admission_campaign_id', '=', $admission_campaign_id)
            ->select(DB::raw("DISTINCT DATE_FORMAT(created_at, '%d.%m.%Y') as date_of_week"))
            ->get();
        $daysOfWeekPhD = AdmissionCampaignStatsPhD::where('admission_campaign_id', '=', $admission_campaign_id)
            ->select(DB::raw("DISTINCT DATE_FORMAT(created_at, '%d.%m.%Y') as date_of_week"))
            ->get();

        $collegeDates = $daysOfWeekCollege->pluck('date_of_week');
        $bachelorDates = $daysOfWeekBachelor->pluck('date_of_week');
        $mastersDates = $daysOfWeekMastersDegree->pluck('date_of_week');
        $phdDates = $daysOfWeekPhD->pluck('date_of_week');

        $dateOfWeeks = $collegeDates
            ->merge($bachelorDates)
            ->merge($mastersDates)
            ->merge($phdDates)
            ->unique()
            ->all();

        return array_values($dateOfWeeks);
    }

    public function printFullReport()
    {
        return redirect()->route('print.admission.campaign.full.report');
    }
}
