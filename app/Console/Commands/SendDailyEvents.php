<?php

namespace App\Console\Commands;

use App\Models\RectorateAndCouncilDecision;
use App\Models\UpbringingEvent;
use App\Models\User;
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
        $users = User::whereNotNull('email')->select('id', 'email')->get();
        $emailFrom = env('MAIL_FROM_ADDRESS');

        foreach ($users as $key => $value) {
            $emailTo = $value->email;

            $rectorateDecisionEvents = RectorateAndCouncilDecision::where('category', '=', 1)
                ->where('executor_id', $value->id)
                ->where('due_date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('due_date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
                ->select('task', 'due_date')
                ->orderBy('due_date')
                ->get();

            $upbringingEvents = UpbringingEvent::where('user_id', $value->id)
                ->where('date', '>=', Carbon::now()->format('Y-m-d'))
                ->where('date', '<=', Carbon::now()->addDays(3)->format('Y-m-d'))
                ->select('name', 'date')
                ->orderBy('date')
                ->get();

            Mail::send('emails.upcoming-events', [
                'rectorateDecisionEvents' => $rectorateDecisionEvents,
                'upbringingEvents' => $upbringingEvents
            ], function ($message) use ($emailFrom, $emailTo) {
                $message->from($emailFrom, 'ЧОУ ВО ЮУ (ИУБиП)');
                $message->to($emailTo)->subject('ИУБиП список ближайших мероприятий на период с ' . Carbon::now()->format('d.m.Y') . ' по ' . Carbon::now()->addDays(3)->format('d.m.Y'));
            });
        }

        $this->info('Successfully send daily notifications about upcoming events.');
    }
}
