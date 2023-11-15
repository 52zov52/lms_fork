<?php

use App\Http\Controllers\PrintConfirmedDocumentRequestsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleReportController;

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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/schedule', function () {
        return view('pages.schedule');
    })->name('schedule');

    Route::get('/operating-activity', function () {
        return view('pages.operating-activity');
    })->name('operating.activity');

    Route::get('/decisions-rectorate-and-council', function () {
        return view('pages.decisions-rectorate-and-council');
    })->name('decisions.rectorate.and.council');

    Route::get('/document-order-requests', function () {
        return view('pages.document-order-request');
    })->name('document.order.requests');

    Route::get('/document-retrieval-requests', function () {
        return view('pages.document-retrieval-request');
    })->name('document.retrieval.requests');

    Route::get('/print-document/{id}', [PrintConfirmedDocumentRequestsController::class, 'printDocument'])->name('print.document');

    Route::get('/contingent', function () {
        return view('pages.contingent');
    })->name('contingent');

    Route::get('/achievement', function () {
        return view('pages.achievement');
    })->name('achievement');

    Route::get('/reports', function () {
        return view('pages.reports');
    })->name('reports');

    Route::get('/feedback', function () {
        return view('pages.feedback-message');
    })->name('feedback');

    Route::get('/generate-teacher-report/{fio}', [ScheduleReportController::class, 'generateTeacherReport'])->name('generate.teacher.report');
    Route::get('/generate-group-report/{group}', [ScheduleReportController::class, 'generateGroupReport'])->name('generate.group.report');
    Route::get('/generate-load-rooms-report/{week}', [ScheduleReportController::class, 'generateLoadRoomsReport'])->name('generate.load.rooms.report');
});
