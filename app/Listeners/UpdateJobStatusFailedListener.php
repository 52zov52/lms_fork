<?php

namespace App\Listeners;

use App\Models\JobStatus;
use Carbon\Carbon;
use Illuminate\Queue\Events\JobFailed;

class UpdateJobStatusFailedListener
{
    const STATUS_FAILED = 2;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JobFailed $event): void
    {
        $job = $event->job;
        $payload = $job->payload();

        JobStatus::where('job_uuid', '=', $payload['uuid'])
            ->update([
                'status' => self::STATUS_FAILED,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }
}
