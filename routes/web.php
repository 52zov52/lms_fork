<?php

use App\Http\Controllers\ContingentReportController;
use App\Http\Controllers\OperatingActivityReportController;
use App\Http\Controllers\PrintConfirmedDocumentRequestsController;
use App\Http\Controllers\PrintDecisionsRectorateAndCouncilController;
use App\Http\Controllers\PrintDocumentsStudentsAcademicLeaves;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\Reports\PrintAdmissionCampaignController;
use App\Http\Controllers\Reports\PrintApplicantAverageScoreController;
use App\Http\Controllers\Reports\PrintDefenseMinistryForm10Controller;
use App\Http\Controllers\Reports\PrintPublicationController;
use App\Http\Controllers\Reports\PrintCurrentAssessmentController;
use App\Http\Controllers\Reports\PrintInterimAssessmentController;
use App\Http\Controllers\Reports\PrintScienceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleReportController;
use App\Http\Controllers\StudyPlanController;

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/elibrary', function () {
        return view('pages.elibrary');
    })->name('elibrary');

    Route::get('/schedule', function () {
        return view('pages.schedule');
    })->name('schedule');

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
    Route::get('/print-admission-campaign-full-report', [PrintAdmissionCampaignController::class, 'printFullReport'])->name('print.admission.campaign.full.report');

    Route::group(['middleware' => ['permission:show-operating-activity-and-decisions-rectorate-and-council']], function () {
        Route::get('/operating-activity', function () {
            return view('pages.operating-activity');
        })->name('operating.activity');
        
        Route::get('/decisions-rectorate', function () {
            return view('pages.decisions-rectorate');
        })->name('decisions.rectorate');

        Route::get('/decisions-council', function () {
            return view('pages.decisions-council');
        })->name('decisions.council');

        Route::get('/print-decisions/{category}/{dateAndNumberDecision}',[PrintDecisionsRectorateAndCouncilController::class, 'printDecisions'])->name('print.decisions');
    });

    Route::group(['middleware' => ['role:Teacher']], function () {
        Route::get('/digital-teacher-profile', function() {
            return view('pages.digital-teacher-profile');
        })->name('digital.teacher.profile');

        Route::get('/academic-workload', function() {
            return view('pages.academic-workload');
        })->name('academic.workload');

        Route::get('/my-publications', function() {
            return view('pages.my-publications');
        })->name('my.publications');

        Route::get('/print-my-publications', [PrintPublicationController::class, 'printMyPublications'])->name('print.my.publications');

        Route::get('/list-publications', function() {
            return view('pages.list-publications');
        })->name('list.publications');

        Route::get('/my-groups', function() {
            return view('pages.my-groups');
        })->name('my.groups');

        Route::get('/teacher-courses/{groupId}', function($groupId) {
            return view('pages.teacher-courses', compact('groupId'));
        })->name('teacher.courses');

        Route::get('/e-journal/{courseTeacherId}', function($courseTeacherId) {
            return view('pages.e-journal', compact('courseTeacherId'));
        })->name('e.journal');

        Route::get('/print-current-assessment/{courseTeacherId}', [PrintCurrentAssessmentController::class, 'printCurrentAssessment'])->name('print.current.assessment');
        Route::get('/print-interim-assessment/{courseTeacherId}', [PrintInterimAssessmentController::class, 'printInterimAssessment'])->name('print.interim.assessment');
    });

    Route::get('/document-order-requests', function () {
        return view('pages.document-order-request');
    })->name('document.order.requests');

    Route::group(['middleware' => ['role:Student']], function () {
        
    });

    Route::group(['middleware' => ['permission:access-to-student-requests-for-certificates-full|access-to-student-requests-for-certificates-spo|access-to-student-requests-for-certificates-vo']], function () {
        Route::get('/document-retrieval-requests', function () {
            return view('pages.document-retrieval-request');
        })->name('document.retrieval.requests');

        Route::get('/print-document/{id}', [PrintConfirmedDocumentRequestsController::class, 'printDocument'])->name('print.document');
    });

    Route::get('/contingent', function () {
        return view('pages.contingent');
    })->name('contingent');

    Route::get('/student-grades', function () {
        return view('pages.student-grades');
    })->name('student.grades');

    Route::get('/print-defense-ministry-form-10/{userId}', [PrintDefenseMinistryForm10Controller::class, 'print'])->name('print.defense.ministry.form.10');

    Route::group(['middleware' => 'permission:access-to-data-on-students-academic-leaves'], function() {
        Route::get('/academic-leave-calendar', function() {
            return view('pages.academic-leave-calendar');
        })->name('academic.leave.calendar');
    });

    Route::get('/documents-students-academic-leaves/{user_id}/{group_id}', function ($user_id, $group_id) {
        return view('pages.documents-students-academic-leaves', ['user_id' => $user_id, 'group_id' => $group_id]);
    })->name('documents.students.academic.leaves');

    Route::get('/personal-records-check-sheet/{user_id}/{group_id}', function ($user_id, $group_id) {
        return view('pages.academic-leave.personal-records-check-sheet', ['user_id' => $user_id, 'group_id' => $group_id]);
    })->name('personal.records.check.sheet');

    Route::get('/form-z01/{user_id}/{group_id}', function ($user_id, $group_id) {
        return view('pages.academic-leave.form-z01', ['user_id' => $user_id, 'group_id' => $group_id]);
    })->name('form.z01');

        Route::get('/form-claim-template-dp01/{user_id}/{group_id}', function ($user_id, $group_id) {
        return view('pages.academic-leave.form-claim-template-dp01', ['user_id' => $user_id, 'group_id' => $group_id]);
    })->name('form.claim.template.dp01');

    Route::get('/print-document-academic-leaves-301/{user_id}/{group_id}', [PrintDocumentsStudentsAcademicLeaves::class, 'printDocumentAcademicLeaves301'])->name('print.document.academic.leaves.301');
    Route::get('/print-document-academic-leaves-aa-01/{user_id}/{group_id}', [PrintDocumentsStudentsAcademicLeaves::class, 'printDocumentAcademicLeavesAA01'])->name('print.document.academic.leaves.aa.01');
    Route::get('/print-personal-file-verification-sheet/{user_id}/{group_id}', [PrintDocumentsStudentsAcademicLeaves::class, 'printPersonalFileVerificationSheet'])->name('print.personal.file.verification.sheet');

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

    Route::get('/generate-qr-code', [QrCodeController::class, 'generateQrCode'])->name('generate.qr.code');

    Route::get('/generate-teacher-report/{fio}', [ScheduleReportController::class, 'generateTeacherReport'])->name('generate.teacher.report');
    Route::get('/generate-group-report/{group}', [ScheduleReportController::class, 'generateGroupReport'])->name('generate.group.report');
    Route::get('/generate-load-rooms-report/{week}', [ScheduleReportController::class, 'generateLoadRoomsReport'])->name('generate.load.rooms.report');

    Route::get('/generate-operating-activity-report/{week}/{fio}', [OperatingActivityReportController::class, 'generateOperatingActivityReport'])->name('generate.operating.activity.report');

    Route::get('/generate-list-of-students-in-group-report/{groupId}', [ContingentReportController::class, 'generateListOfStudentsInGroupReport'])->name('generate.list.of.student.in.group.report');
    Route::get('/generate-contingent-report', [ContingentReportController::class, 'generateContingentReport'])->name('generate.contingent.report');
    Route::get('/generate-contingent-by-age-and-gender-report', [ContingentReportController::class, 'generateContingentByAgeAndGenderReport'])->name('generate.contingent.by.age.and.gender.report');
    Route::get('/generate-contingent-by-age-and-gender-without-teaching-from-report', [ContingentReportController::class, 'generateContingentByAgeAndGenderWithoutTeachingFromReport'])->name('generate.contingent.by.age.and.gender.without.teaching.from.report');

    Route::get('/get-study-plan', [StudyPlanController::class, 'getStudyPlan'])->name('get.study.plan');

    Route::get('/print-count-publication-by-years-and-statuses', [PrintScienceController::class, 'printCountPublicationByYearsAndStatuses'])->name('print.count.publication.by.years.and.statuses');
    Route::get('/print-count-user-publications-by-status-and-year-range', [PrintScienceController::class, 'printCountUserPublicationsByStatusAndYearRange'])->name('print.count.user.publications.by.status.and.year.range');

    Route::get('/print-applicant-average-score', [PrintApplicantAverageScoreController::class, 'print'])->name('print.applicant.average.score');
});
