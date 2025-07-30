<?php

namespace App\Livewire\Employee;

use App\Jobs\GenerateSptGroupsJob;
use App\Models\JobStatus;
use Livewire\Component;

class GenerateSptGroups extends Component
{
    public object $generateSptGroupsJobs;
    const SERVICE_NAME = 'generate_spt_groups';

    public function render()
    {
        $this->generateSptGroupsJobs = JobStatus::where('service_name', '=', self::SERVICE_NAME)->get();

        return view('livewire.employee.generate-spt-groups');
    }

    public function generateUnconfirmedConsents()
    {
        GenerateSptGroupsJob::dispatch();
        $this->dispatch('show-toast', message: 'Задача на создание групп для СПТ успешно создана', type: 'success');
    }

    public function checkStatuses()
    {
        $this->generateSptGroupsJobs = JobStatus::where('service_name', '=', self::SERVICE_NAME)->get();
    }
}
