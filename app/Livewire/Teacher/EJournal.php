<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Common\InterimAssessmentStatementController;
use App\Http\Controllers\Employee\ContingentController;
use App\Models\EJournal as ModelsEJournal;
use App\Models\Group;
use App\Models\WorkStudyPlanCoursesInSemester;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Livewire\Attributes\On;

class EJournal extends Component
{
    public $wspCoursesInSemesterId = 0;
    public $groupId = 0;

    public bool $isShowModalDialog = false;

    public $listfOfLessonType = [
        1 => 'Лекции',
        2 => 'Лабораторные',
        3 => 'Практические',
        4 => 'Контрольная точка',
        5 => 'Контрольная точка (тест)',
        6 => 'Зачет',
        7 => 'Диф. зачет',
        8 => 'Экзамен',
    ];

    public $listOfLessonTime = [
        '08:20-09:50',
        '10:00-11:30',
        '11:40-13:10',
        '13:30-15:00',
        '15:10-16:40',
        '17:00-18:30',
        '18:40-20:10',
        '20:20-21:50'
    ];

    public $listOfStatuses = [
        1 => '.',
        2 => 'Н',
        3 => 'НУ',
        6 => '2',
        7 => '3',
        8 => '4',
        9 => '5',
        10 => 'Зач.',
        11 => 'Незач.',
    ];

    public $status = array();
    public $lessonDate = '', $lessonTime = '', $lessonType = 1, $lessonTheme = 'Тема';

    public function mount($wspCoursesInSemesterId, $groupId)
    {
        $this->wspCoursesInSemesterId = $wspCoursesInSemesterId;
        $this->groupId = $groupId;
    }

    public function render()
    {
        $currentData = Carbon::now()->format('d.m.Y');
        $currentTime = $this->getLessonTime(Carbon::now()->format('H:i'));

        $group = Group::select('group_name')->where('id', '=', $this->groupId)->first();
        $wspCoursesInSemester = WorkStudyPlanCoursesInSemester::select('disciplines.name as discipline_name')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('work_study_plan_courses_in_semesters.id', '=', $this->wspCoursesInSemesterId)
            ->first();

        $contingentController = new ContingentController();
        $contingent = $contingentController->getContingent($this->groupId);

        $journal = ModelsEJournal::where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->orderBy('lesson_date', 'asc')
            ->orderBy('lesson_time', 'asc')
            ->get();

        foreach ($journal as $key => $value) {
            $this->status[$value->id] = $value->status;
        }

        $lessonInfo = ModelsEJournal::select('lesson_date', 'lesson_time', 'lesson_type', 'is_signature')
            ->where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->distinct()
            ->orderBy('lesson_date', 'asc')
            ->orderBy('lesson_time', 'asc')
            ->get();

        return view('livewire.teacher.e-journal', [
            'group_name' => $group->group_name,
            'discipline_name' => $wspCoursesInSemester->discipline_name,
            'contingent' => $contingent,
            'journal' => $journal,
            'lessonInfo' => $lessonInfo,
            'currentData' => $currentData,
            'currentTime' => $currentTime,
            'listfOfLessonType' => $this->listfOfLessonType,
            'listOfLessonTime' => $this->listOfLessonTime,
            'listOfStatuses' => $this->listOfStatuses
        ]);
    }

    public function showModalDialog()
    {
        $this->lessonDate = Carbon::now()->format('Y-m-d');
        $this->lessonTime = $this->getLessonTime(Carbon::now()->format('H:i'));
        $this->lessonType = 1;

        $this->lessonTheme = 'Тема ' . ($this->getCountLessons($this->lessonType) + 1);

        $this->isShowModalDialog = true;
    }

    public function setLessonType(): void
    {
        $this->lessonType = $this->lessonType;
        $this->lessonTheme = 'Тема ' . ($this->getCountLessons($this->lessonType) + 1);
    }

    public function setStatus($id, $studentId)
    {
        ModelsEJournal::where('id', '=', $id)
            ->where('student_id', '=', $studentId)->update([
                'status' => $this->status[$id],
                'who_checked' => Auth::user()->id,
                'checked_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
    }

    public function store()
    {
        $isCheck = ModelsEJournal::where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->where('lesson_date', '=', Carbon::parse($this->lessonDate)->format('Y-m-d'))
            ->where('lesson_time', '=', $this->lessonTime)
            ->count();

        if ($isCheck != 0) {
            $this->dispatch('show-toast', message: 'На указанную дату и время уже созданы записи в эл. журнале!', type: 'error');
        } else {
            $contingentController = new ContingentController();
            $contingent = $contingentController->getContingent($this->groupId);

            foreach ($contingent as $key => $value) {
                ModelsEJournal::create([
                    'teacher_id' => Auth::user()->id,
                    'work_study_plan_courses_in_semester_id' => $this->wspCoursesInSemesterId,
                    'group_id' => $this->groupId,
                    'student_id' => $value->user_id,
                    'status' => 2,
                    'lesson_date' => Carbon::parse($this->lessonDate)->format('Y-m-d'),
                    'lesson_time' => $this->lessonTime,
                    'lesson_type' => $this->lessonType,
                    'lesson_theme' => $this->lessonTheme,
                    'who_checked' => Auth::user()->id,
                    'checked_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'academic_year' => AcademicYearController::getAcademicYear(),
                    'half_year' => AcademicYearController::getHalfYear()
                ]);
            }

            $this->isShowModalDialog = false;
            $this->dispatch('show-toast', message: 'Записи успешно добавлены!', type: 'success');
        }
    }

    public function signature($lessonDate, $lessonTime, $lessonType)
    {
        ModelsEJournal::where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->where('lesson_date', '=', Carbon::parse($lessonDate)->format('Y-m-d'))
            ->where('lesson_time', '=', $lessonTime)
            ->where('lesson_type', '=', (int)$lessonType)
            ->update([
                'is_signature' => true,
                'who_signed' => Auth::user()->id,
                'signatured_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

        $this->dispatch('show-toast', message: 'Журнал успешно подписан!', type: 'success');
    }

    #[On('destroy')]
    public function destroy($lessonDate, $lessonTime, $lessonType)
    {
        ModelsEJournal::where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->where('lesson_date', '=', Carbon::parse($lessonDate)->format('Y-m-d'))
            ->where('lesson_time', '=', $lessonTime)
            ->where('lesson_type', '=', (int)$lessonType)
            ->delete();

        $this->dispatch('show-toast', message: 'Записи успешно удалены!', type: 'success');
    }

    public function generateInterimAssessmentStatementToPdf()
    {
        $isExistExams = $this->checkExams();

        if ($isExistExams == 0) {
            $this->dispatch('show-toast', message: 'У данной группы в эл. журнале отсутствует занятие с типом пр. аттестации. Добавьте новую запись и выберите тип: экзамен, диф. зачёт или зачёт.', type: 'error');
        } else {
            $interimAssessmentStatementController = new InterimAssessmentStatementController();
            $data = $interimAssessmentStatementController->index($this->wspCoursesInSemesterId, $this->groupId);

            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend();
        }
    }

    public function sendInterimAssessmentStatement()
    {
        $isExistExams = $this->checkExams();

        if ($isExistExams == 0) {
            $this->dispatch('show-toast', message: 'У данной группы в эл. журнале отсутствует занятие с типом пр. аттестации. Добавьте новую запись и выберите тип: экзамен, диф. зачёт или зачёт.', type: 'error');
        } else {
            $emailTo = env('AD_EMAIL');

            $interimAssessmentStatementController = new InterimAssessmentStatementController();
            $data = $interimAssessmentStatementController->index($this->wspCoursesInSemesterId, $this->groupId);

            Mail::send('reports.pdf.empty', [], function ($message) use ($emailTo, $data) {
                $message->from('iubip@mg-lab.ru', 'ИУБиП. Ведомости');
                $message->to($emailTo)
                    ->subject(Auth::user()->userData->last_name . ' ' .
                        mb_substr(Auth::user()->userData->first_name, 0, 1) . '. ' .
                        mb_substr(Auth::user()->userData->middle_name, 0, 1) . '. ' .
                        'Ведомость промежуточной аттестации от ' .
                        Carbon::now()->format('d.m.Y'));

                $message->attach($data['tempFile'], [
                    'as' => $data['filename'],
                    'mime' => 'application/pdf',
                ]);
            });

            $this->dispatch('show-toast', message: 'Ведомость промежуточной аттестации успешно отправлена!', type: 'success');
        }
    }

    private function checkExams()
    {
        return ModelsEJournal::where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->whereIn('lesson_type', [6, 7, 8])
            ->count();
    }

    protected function getCountLessons($lessonType)
    {
        return ModelsEJournal::select('lesson_date', 'lesson_time', 'lesson_type')
            ->where('teacher_id', '=', Auth::user()->id)
            ->where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->where('lesson_type', '=', $lessonType)
            ->distinct()
            ->get()->count();
    }

    public function getLessonTime($currentTime): string
    {
        $lessonTime = "";

        if (strtotime($currentTime) >= strtotime('08:20') && strtotime($currentTime) <= strtotime('09:50')) {
            $lessonTime = "08:20-09:50";
        } elseif (strtotime($currentTime) > strtotime('09:50') && strtotime($currentTime) <= strtotime('11:30')) {
            $lessonTime = "10:00-11:30";
        } elseif (strtotime($currentTime) > strtotime('11:30') && strtotime($currentTime) <= strtotime('13:10')) {
            $lessonTime = "11:40-13:10";
        } elseif (strtotime($currentTime) > strtotime('13:10') && strtotime($currentTime) <= strtotime('15:00')) {
            $lessonTime = "13:30-15:00";
        } elseif (strtotime($currentTime) > strtotime('15:00') && strtotime($currentTime) <= strtotime('16:40')) {
            $lessonTime = "15:10-16:40";
        } elseif (strtotime($currentTime) > strtotime('16:40') && strtotime($currentTime) <= strtotime('18:30')) {
            $lessonTime = "17:00-18:30";
        } elseif (strtotime($currentTime) > strtotime('18:30') && strtotime($currentTime) <= strtotime('20:10')) {
            $lessonTime = "18:40-20:10";
        } elseif (strtotime($currentTime) > strtotime('20:10') && strtotime($currentTime) <= strtotime('21:50')) {
            $lessonTime = "20:20-21:50";
        } else {
            $lessonTime = "";
        }

        return $lessonTime;
    }
}
