<?php

namespace App\Console\Commands;

use App\Models\StudentsAcademicLeave;
use Carbon\Carbon;
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
        $studentsAcademicLeaves = StudentsAcademicLeave::select(
            'students_academic_leaves.id',
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name',
            'groups.group_name',
            'students_academic_leaves.academic_leave_end_date',
            'students_academic_leaves.description'
        )
            ->join('users_data', 'students_academic_leaves.user_id', '=', 'users_data.user_id')
            ->join('groups', 'students_academic_leaves.group_id', '=', 'groups.id')
            ->where('is_academic_leave', '=', 1)
            ->where('students_academic_leaves.academic_leave_end_date', '=', Carbon::now()->format('Y-m-d'))
            ->orderBy('students_academic_leaves.academic_leave_end_date')
            ->get();

        if (count($studentsAcademicLeaves) != 0) {
            Mail::send('emails.daily-report-students-academic-leave', [
                'studentsAcademicLeaves' => $studentsAcademicLeaves
            ], function ($message) {
                $message->from('iubip@mg-lab.ru', 'ИУБиП');
                $message->to('g.miziukov@yandex.ru')->subject('Тест.');
            });

            $this->info('Successfully sent report.');
        }
    }
}
