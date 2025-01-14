<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily notifications about upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Mail::send('emails.upcoming-events', ['data' => 'data'], function ($message) {
            $message->from('iubip@mg-lab.ru', 'ЧОУ ВО ЮУ (ИУБиП)');
            $message->to('g.miziukov@yandex.ru')->subject('ИУБиП список ближайших мероприятий на '. Carbon::now()->format('d.m.Y'));
        });

        $this->info('Successfully send daily notifications about upcoming events.');
    }
}
