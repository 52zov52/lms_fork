<?php

namespace App\Console\Commands;

use App\Http\Controllers\ScheduleController;
use Illuminate\Console\Command;

class DailyUpdateSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-update-schedule';

    /**
     * The console command description.
     * Ежедневное обновление расписание преподавателей и студентов
     * @var string
     */
    protected $description = 'Daily updated schedule of teachers and students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scheduleController = new ScheduleController();
        $scheduleController->uploadSchedule();

        $this->info('Successfully updated schedule.');
    }
}
