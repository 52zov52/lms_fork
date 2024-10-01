<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\DailyReportStudentsAcademicLeave::class,
        Commands\DailyUpdateSchedule::class,
        Commands\SyncStudentGrades::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:daily-report-students-academic-leave')
            ->dailyAt('09:00');

        $schedule->command('app:daily-update-schedule')
            ->twiceDaily(8, 18);

        $schedule->command('app:daily-report-on-student-certificates')
            ->dailyAt('15:30');

        $schedule->command('app:sync-student-grades')
            ->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
