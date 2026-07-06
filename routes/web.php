<?php

use App\Http\Controllers\WeeklyReportExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/weekly-reports/{weeklyReport}/export-pdf', WeeklyReportExportController::class)
        ->name('weekly-reports.export-pdf');
});
