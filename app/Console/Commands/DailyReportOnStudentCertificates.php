<?php

namespace App\Console\Commands;

use App\Models\DocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyReportOnStudentCertificates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-report-on-student-certificates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily report on the number of applications from students for certificates of student status in the context of secondary vocational education and higher education';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $countRequestSPO = DocumentOrderRequest::join('users', 'document_order_requests.requester_id', '=', 'users.id')
            ->join('group_student', 'users.id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->where('specialities.level_education_id', '=', 2)
            ->where('document_order_requests.status', '=', 1)
            ->where('document_order_requests.created_at', 'like', Carbon::now()->format('Y-m-d').'%')
            ->count();

        $countRequestVO = DocumentOrderRequest::join('users', 'document_order_requests.requester_id', '=', 'users.id')
            ->join('group_student', 'users.id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
            ->join('specialities', 'study_plans.speciality_id', '=', 'specialities.id')
            ->where('specialities.level_education_id', '<>', 2)
            ->where('document_order_requests.created_at', 'like', Carbon::now()->format('Y-m-d').'%')
            ->where('document_order_requests.status', '=', 1)
            ->count();

        Mail::send('emails.daily-report-on-student-certificates', [
            'countRequestSPO' => $countRequestSPO,
            'countRequestVO' => $countRequestVO
        ], function ($message) {
            $message->from('iubip@mg-lab.ru', 'ИУБиП отчет по справкам о статусе студента');
            $message->to('miziukov@iubip.ru')->subject('Количество заявок от студентов на справки о статусе студента на ' . Carbon::now()->format('d.m.Y'));
        });

        $this->info('Successfully sent report.');
    }
}
