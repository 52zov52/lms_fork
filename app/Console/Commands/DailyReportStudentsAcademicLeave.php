<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyReportStudentsAcademicLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-report-students-academic-leave';

    /**
     * The console command description.
     * Ежедневный отчет по студентам, которые должны выйти из академического отпуска
     * @var string
     */
    protected $description = 'Daily report on students who are due to return from academic leave';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Mail::send('emails.daily-report-students-academic-leave', [
            'info' => 'Test'
        ], function ($message) {
            $message->from('noreply@iubip.ru', 'ИУБиП');
            $message->to('g.miziukov@yandex.ru')->subject('Тест.');
        });

        $this->info('Successfully sent report.');
    }
}
