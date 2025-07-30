<?php

namespace App\Jobs;

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Employee\ContingentController;
use App\Models\JobStatus;
use App\Models\StudentSptConsentStatus;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSptGroupsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const STATUS_IN_PROGRESS = 0;
    const STATUS_SUCCESS = 1;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = $this->job->payload();

        JobStatus::create([
            'job_uuid' => $payload['uuid'],
            'service_name' => 'generate_spt_groups',
            'status' => self::STATUS_IN_PROGRESS
        ]);

        $contingentController = new ContingentController();
        $groupIds = $contingentController->getGroupIdsByTeachingForm(1);

        $academicYear = AcademicYearController::getAcademicYear();

        foreach ($groupIds as $groupId) {
            $contingent = $contingentController->getContingent($groupId);

            foreach ($contingent as $key => $value) {
                $data = StudentSptConsentStatus::where('user_id', '=', $value->user_id)
                    ->where('academic_year', '=', $academicYear)
                    ->select('id')
                    ->first();

                if (is_null($data)) {
                    StudentSptConsentStatus::create([
                        'user_id' => $value->user_id,
                        'academic_year' => $academicYear,
                        'half_year' => AcademicYearController::getHalfYear()
                    ]);
                }
            }
        }

        JobStatus::where('job_uuid', '=', $payload['uuid'])
            ->update([
                'status' => self::STATUS_SUCCESS,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }
}
