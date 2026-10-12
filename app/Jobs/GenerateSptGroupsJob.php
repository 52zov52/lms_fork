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
    // id из teaching_forms: 1 Очная, 2 Заочная, 3 Заочная (ускоренное), 4 Воскресная,
    // 5 Соискательство, 6 Очно-заочная, 7 Очная (ускоренное)
    private const TEACHING_FORM_IDS = [1, 2, 3, 6, 7]; // при необходимости добавьте 4
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = $this->job->payload();

        JobStatus::create([
            'job_uuid' => $payload['uuid'],
            'service_name' => 'generate_spt_groups',
            'status' => self::STATUS_IN_PROGRESS,
        ]);

        $contingentController = new ContingentController();
        $academicYear = AcademicYearController::getAcademicYear();
        $halfYear = AcademicYearController::getHalfYear();

        // id форм обучения из таблицы teaching_forms:
        // 1 Очная, 2 Заочная, 3 Заочная (ускоренное), 6 Очно-заочная, 7 Очная (ускоренное)
        foreach ([1, 2, 3, 6, 7] as $teachingFormId) {
            foreach ($contingentController->getGroupIdsByTeachingForm($teachingFormId) as $groupId) {
                foreach ($contingentController->getContingent($groupId) as $value) {
                    StudentSptConsentStatus::firstOrCreate(
                        ['user_id' => $value->user_id, 'academic_year' => $academicYear],
                        ['half_year' => $halfYear]
                    );
                }
            }
        }

        JobStatus::where('job_uuid', $payload['uuid'])->update([
            'status' => self::STATUS_SUCCESS,
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
