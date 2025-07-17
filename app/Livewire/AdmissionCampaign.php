<?php

namespace App\Livewire;

use App\Models\AdmissionCampaign as ModelsAdmissionCampaign;
use Livewire\Component;

class AdmissionCampaign extends Component
{
    public $isFullReportParamModalVisible = false;
    public $selectedAdmissionCampaignId = 0;

    public function render()
    {
        $admissionCampaign = ModelsAdmissionCampaign::get();

        return view('livewire.admission-campaign', ['admissionCampaign' => $admissionCampaign]);
    }

    public function fullReportParamModalVisible()
    {
        $this->isFullReportParamModalVisible = !$this->isFullReportParamModalVisible;
    }

    public function generateFullReport()
    {
        return redirect()->route('print.admission.campaign.full.report', ['id' => 0]);
    }

    public function generateArchiveFullReport()
    {
        if ($this->selectedAdmissionCampaignId != 0) {
            return redirect()->route('print.admission.campaign.full.report', ['id' => $this->selectedAdmissionCampaignId]);
        } else {
            $this->dispatch('show-toast', message: 'Для формирования отчета необходимо выбрать элемент из списка!', type: 'error');
        }
    }
}
