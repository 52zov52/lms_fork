<?php

use App\Http\Controllers\Common\InterimAssessmentStatementController;
use App\Http\Controllers\ContingentReportController as OldContingentReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\StudentSocialPassportReviewController;
use App\Http\Controllers\OperatingActivityReportController;
use App\Http\Controllers\PrintDecisionsRectorateAndCouncilController;
use App\Http\Controllers\Report\GroupStudentGradeController;
use App\Http\Controllers\Report\SptController;
use App\Http\Controllers\Report\UvrController;
use App\Http\Controllers\Reports\PrintAdmissionCampaignController;
use App\Http\Controllers\Reports\PrintApplicantAverageScoreController;
use App\Http\Controllers\Reports\PrintDefenseMinistryForm10Controller;
use App\Http\Controllers\Reports\PrintPublicationController;
use App\Http\Controllers\Reports\PrintScienceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleReportController;
use App\Http\Controllers\Student\SptConsentStatusController;
use App\Http\Controllers\StudyPlanController;
use App\Http\Controllers\VideoConferenceFallbackController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/qr-code-document-order-requests', function () {
    return view('qr-code-document-order-requests');
})->name('qr.code.document.order.requests');

Route::get('/qr-code-document-verifications/{verificationCode}', function ($verificationCode) {
    return view('qr-code-document-verifications', compact('verificationCode'));
})->name('qr.code.document.verifications');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/upcoming-events', function () {
        return view('pages.upcoming-events');
    })->name('upcoming.event');

    Route::get('/elibrary', function () {
        return view('pages.elibrary');
    })->name('elibrary');

    Route::get('/schedule', function () {
        return view('pages.schedule');
    })->name('schedule');

    Route::get('/generate-unconfirmed-consents', [SptConsentStatusController::class, 'generateUnconfirmedConsents']);
    Route::get('/download-informed-consent/{id}', [SptConsentStatusController::class, 'downloadInformedConsent'])->name('download.informed.consent');

    //Отчеты
    Route::get('/generate-spt-report', [SptController::class, 'getStudentConsentStatuses'])->name('generate.spt.report');

    //Routes для роли Employee
    Route::group(['prefix' => 'employee'], function () {
        //Операционная деятельность
        Route::get('/operating-activity', function () {
            return view('pages.employee.operating-activity');
        })->name('employee.operating.activity');

        //Решения ректората
        Route::get('/rectorate-decision', function () {
            return view('pages.employee.rectorate-decision');
        })->name('employee.rectorate.decision');

        //Решения уч. совета
        Route::get('/academic-council-decision', function () {
            return view('pages.employee.academic-council-decision');
        })->name('employee.academic.council.decision');

        Route::get('/print-decisions/{category}/{dateAndNumberDecision}', [PrintDecisionsRectorateAndCouncilController::class, 'printDecisions'])->name('print.decisions');

        //Справочник нормативных документов
        Route::get('/regulatory-documents', function () {
            return view('pages.employee.regulatory-document');
        })->name('employee.regulatory.documents');

        //Управление контингентом
        Route::get('/contingent-management', function () {
            return view('pages.employee.contingent-management');
        })->name('employee.contingent.management');

        //Контингент
        Route::get('/contingent', function () {
            return view('pages.employee.contingent');
        })->name('employee.contingent');

        //Календарь академических отпусков
        Route::get('/student-academic-leave-calendar', function () {
            return view('pages.employee.student-academic-leave-calendar');
        })->name('employee.student.academic.leave.calendar');

        //Обработка заявок на документы
        Route::get('/student-document-order-issuances', function () {
            return view('pages.employee.student-document-order-issuances');
        })->name('employee.student.document.order.issuances');

        Route::get('/student-social-passport-review', function () {
            return view('pages.employee.student-social-passport-review');
        })->name('employee.student.social.passport.review');

        //Воинский учёт
        Route::get('military', function () {
            return view('pages.employee.milatary');
        })->name('military');

        //Управление ВР и социализации
        Route::get('uvr-management', function () {
            return view('pages.employee.uvr-management');
        })->name('uvr.management');

        //Мероприятия по ВР и социализации
        Route::get('uvr-event', function () {
            return view('pages.employee.uvr-event');
        })->name('uvr.event');

        Route::get('download-uvr-report', [UvrController::class, 'downloadReport'])->name('download.uvr.report');

        Route::get('/download-student-social-passport/{userId}', [StudentSocialPassportReviewController::class, 'downloadStudentSocialPassport'])->name('download.student.social.passport');
    });

    //Routes для роли Teacher
    Route::group(['prefix' => 'teacher'], function () {
        //Цифровой профиль преподавателя
        Route::get('/digital-profile', function () {
            return view('pages.teacher.digital-profile');
        })->name('teacher.digital.profile');

        //Мои учебные группы
        Route::get('/my-groups', function () {
            return view('pages.teacher.my-groups');
        })->name('teacher.my.groups');

        //Список учебных дисциплин группы по ИД
        Route::get('/course-list/{groupId}', function ($groupId) {
            return view('pages.teacher.course-list', compact('groupId'));
        })->name('teacher.course.list');

        //Журнал
        Route::get('/e-journal/{wspCoursesInSemesterId}/{groupId}', function ($wspCoursesInSemesterId, $groupId) {
            return view('pages.teacher.e-journal', compact('wspCoursesInSemesterId'), compact('groupId'));
        })->name('teacher.e.journal');

        Route::get('/e-journal-group-attendance-report/{wspCoursesInSemesterId}/{groupId}', function ($wspCoursesInSemesterId, $groupId) {
            return view('pages.teacher.group-attendance-report', compact('wspCoursesInSemesterId'), compact('groupId'));
        })->name('teacher.e.journal.group.attendance.report');

        Route::get('/generate-interim-assessment-statement/{wspCoursesInSemesterId}/{groupId}', [InterimAssessmentStatementController::class, 'index'])->name('generate.interim.assessment.statement');

        //Учебная нагрузка
        Route::get('/academic-workload', function () {
            return view('pages.teacher.academic-workload');
        })->name('teacher.academic.workload');

        //График пересдач академических задолженностей у студентов
        Route::get('/academic-retake-schedule', function () {
            return view('pages.teacher.academic-retake-schedule');
        })->name('teacher.academic.retake.schedule');

        //График консультаций студентов
        Route::get('/consultation-schedule', function () {
            return view('pages.teacher.consultation-schedule');
        })->name('teacher.consultation.schedule');

        //Асинхронное взаимодействие
        Route::get('/async-interact', function () {
            return view('pages.common.async-interact-chat');
        })->name('teacher.async.interact.chats');

        //Асинхронное взаимодействие. Загрузка уч. материала
        Route::get('/async-interact/chat/upload-file/{chatId}/{mode}', function ($chatId, $mode) {
            return view('pages.common.async-interact-chat-upload-file', ['chatId' => $chatId, 'mode' => $mode]);
        })->name('teacher.async.interact.chat.upload.files');

        //Асинхронное взаимодействие. Список чатов
        Route::get('/async-interact/chat/list/{year}/{halfYear}/{disciplineId}/{normativeId}/{groupId}', function ($year, $halfYear, $disciplineId, $normativeId, $groupId) {
            return view('pages.common.async-interact-chat-list', [
                'year' => $year,
                'halfYear' => $halfYear,
                'disciplineId' => $disciplineId,
                'normativeId' => $normativeId,
                'groupId' => $groupId
            ]);
        })->name('teacher.async.interact.chat.list');

        //Асинхронное взаимодействие. Чат
        Route::get('/async-interact/chat/{chatId}/{mode}', function ($chatId, $mode) {
            return view('pages.common.async-interact-chat-upload-file', ['chatId' => $chatId, 'mode' => $mode]);
        })->name('teacher.async.interact.with.student.chat');

        //Закрытие сессии ВКС
        Route::get('/logout-video-conference/{id}', [VideoConferenceFallbackController::class, 'logout'])
            ->name('logout.video.conference');
    });

    //Печать индивидуальных оценок студента по ИД
    Route::get('/group-student-grade-report', [GroupStudentGradeController::class, 'print'])->name('group.student.grade.report');

    //Routes для роли Student
    Route::group(['prefix' => 'student'], function () {
        //Мои дисциплины
        Route::get('/my-disciplines', function () {
            return view('pages.student.my-disciplines');
        })->name('student.my.disciplines');

        //Моя успеваемость
        Route::get('/my-grades', function () {
            return view('pages.student.my-grades');
        })->name('student.my.grades');

        //Моя посещаемость
        Route::get('/my-attendance', function () {
            return view('pages.student.my-attendance');
        })->name('student.my.attendance');

        //Графики
        Route::get('/academic-retake-and-consultation-schedule', function () {
            return view('pages.student.academic-retake-and-consultation-schedule');
        })->name('student.academic.retake.and.consultation.schedule');

        //График пересдач академических задолженностей
        Route::get('/academic-retake-schedule-list', function () {
            return view('pages.student.academic-retake-schedule-list');
        })->name('student.academic.retake.schedule.list');

        //График пересдач академических задолженностей
        Route::get('/my-consultation-schedule', function () {
            return view('pages.student.my-consultation-schedule');
        })->name('student.my-consultation.schedule');

        //Согласие на СПТ
        Route::get('/spt-consent-status', function () {
            return view('pages.student.spt-consent-status');
        })->name('student.spt.consent.status');

        //Социальный паспорт обучающегося
        Route::get('/social-passport', function () {
            return view('pages.student.social-passport');
        })->name('student.social.passport');

        //Семейный статус обучающегося
        Route::get('/family-status', function () {
            return view('pages.student.family-status');
        })->name('student.family.status');

        //Асинхронное взаимодействие
        Route::get('/async-interact', function () {
            return view('pages.common.async-interact-chat');
        })->name('student.async.interact.chats');

        //Асинхронное взаимодействие. Чат
        Route::get('/async-interact/chat/{chatId}/{mode}', function ($chatId, $mode) {
            return view('pages.common.async-interact-chat-upload-file', ['chatId' => $chatId, 'mode' => $mode]);
        })->name('student.async.interact.with.teacher.chat');

        //Асинхронное взаимодействие. Просмотр уч. материала
        Route::get('/async-interact/chat/view/files/{chatId}', function ($chatId) {
            return view('pages.common.async-interact-chat-view-file', ['chatId' => $chatId]);
        })->name('student.async.interact.chat.view.files');
    });

    Route::get('/admission-campaign', function () {
        return view('pages.admission-campaign');
    })->name('admission.campaign');

    Route::get('/admission-campaign-stats', function () {
        return view('pages.admission-campaign-stats');
    })->name('admission.campaign.stats');

    Route::get('/applicant-average-score', function () {
        return view('pages.applicant-average-score');
    })->name('applicant.average.score');

    Route::get('/print-admission-campaign-to-day-report', [PrintAdmissionCampaignController::class, 'printToDayReport'])->name('print.admission.campaign.to.day.report');
    Route::get('/print-admission-campaign-full-report/{id}', [PrintAdmissionCampaignController::class, 'printFullReport'])->name('print.admission.campaign.full.report');

    Route::group(['middleware' => ['role:Teacher']], function () {
        Route::get('/my-publications', function () {
            return view('pages.my-publications');
        })->name('my.publications');

        Route::get('/print-my-publications', [PrintPublicationController::class, 'printMyPublications'])->name('print.my.publications');

        Route::get('/list-publications', function () {
            return view('pages.list-publications');
        })->name('list.publications');
    });

    //Url для сервиса заказа справок
    Route::get('/student-document-order-requests', function () {
        return view('pages.student-document-order-requests');
    })->name('student.document.order.requests');

    //Url для анкетирования
    Route::get('/questionnaire', function () {
        return view('pages.questionnaire');
    })->name('questionnaire');

    Route::group(['middleware' => ['permission:access-to-student-requests-for-certificates-full|access-to-student-requests-for-certificates-spo|access-to-student-requests-for-certificates-vo']], function () {
        Route::get('/document-retrieval-requests', function () {
            return view('pages.document-retrieval-request');
        })->name('document.retrieval.requests');
    });

    Route::get('/student-grades', function () {
        return view('pages.student-grades');
    })->name('student.grades');

    Route::get('/bulletin/{bulletin_id}', function ($bulletin_id) {
        return view('pages.bulletin', ['bulletin_id' => $bulletin_id]);
    })->name('bulletin');

    Route::get('/print-defense-ministry-form-10/{userId}', [PrintDefenseMinistryForm10Controller::class, 'print'])->name('print.defense.ministry.form.10');

    Route::get('/achievement', function () {
        return view('pages.achievement');
    })->name('achievement');

    Route::get('/reports', function () {
        return view('pages.reports');
    })->name('reports');

    Route::get('/feedback', function () {
        return view('pages.feedback-message');
    })->name('feedback');

    Route::get('/look-feedback-messange/{id}', function ($id) {
        return view('pages.look-feedback-message', compact('id'));
    })->name('look.feedback.messange');

    Route::get('/generate-teacher-report/{fio}', [ScheduleReportController::class, 'generateTeacherReport'])->name('generate.teacher.report');
    Route::get('/generate-group-report/{group}', [ScheduleReportController::class, 'generateGroupReport'])->name('generate.group.report');
    Route::get('/generate-load-rooms-report/{week}', [ScheduleReportController::class, 'generateLoadRoomsReport'])->name('generate.load.rooms.report');

    Route::get('/generate-operating-activity-report/{week}/{fio}', [OperatingActivityReportController::class, 'generateOperatingActivityReport'])->name('generate.operating.activity.report');

    Route::get('/generate-list-of-students-in-group-report/{groupId}', [OldContingentReportController::class, 'generateListOfStudentsInGroupReport'])->name('generate.list.of.student.in.group.report');
    Route::get('/generate-contingent-report', [OldContingentReportController::class, 'generateContingentReport'])->name('generate.contingent.report');
    Route::get('/generate-contingent-by-age-and-gender-report', [OldContingentReportController::class, 'generateContingentByAgeAndGenderReport'])->name('generate.contingent.by.age.and.gender.report');
    Route::get('/generate-contingent-by-age-and-gender-without-teaching-from-report', [OldContingentReportController::class, 'generateContingentByAgeAndGenderWithoutTeachingFromReport'])->name('generate.contingent.by.age.and.gender.without.teaching.from.report');

    Route::get('/get-study-plan', [StudyPlanController::class, 'getStudyPlan'])->name('get.study.plan');

    Route::get('/print-count-publication-by-years-and-statuses', [PrintScienceController::class, 'printCountPublicationByYearsAndStatuses'])->name('print.count.publication.by.years.and.statuses');
    Route::get('/print-count-user-publications-by-status-and-year-range', [PrintScienceController::class, 'printCountUserPublicationsByStatusAndYearRange'])->name('print.count.user.publications.by.status.and.year.range');

    Route::get('/print-applicant-average-score', [PrintApplicantAverageScoreController::class, 'print'])->name('print.applicant.average.score');
});
