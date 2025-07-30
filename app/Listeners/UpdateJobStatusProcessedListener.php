<?php

namespace App\Listeners;

use App\Models\JobStatus;
use Carbon\Carbon;
use Illuminate\Queue\Events\JobProcessed;

class UpdateJobStatusProcessedListener
{
    const STATUS_SUCCESS = 1;

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
    public function handle(JobProcessed $event): void
    {
        $job = $event->job;
        $payload = $job->payload();

        JobStatus::where('job_uuid', '=', $payload['uuid'])
            ->update([
                'status' => self::STATUS_SUCCESS,
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }
}
