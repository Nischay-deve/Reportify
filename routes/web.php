<?php

use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();


Route::get('/privacy-policy', [StaticPageController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms-of-service', [StaticPageController::class, 'termsOfService'])->name('terms.service');

Route::get('/', [App\Http\Controllers\CalendarController::class, 'loadDefaultCenter'])->name('calendar.loadDefaultCenter');

// view save report. It will redirect to pdfView based on the name
Route::get('/view_report/{name}', [App\Http\Controllers\Auth\DownloadController::class, 'viewSavedReport']);

// view save report. It will redirect to home page
Route::get('/view_link/{name}', [App\Http\Controllers\CalendarController::class, 'viewSavedLink']);

Route::group(['prefix' => '{slug}'], function () {
    Route::get('/login', function ($slug) { // Capture the 'slug' parameter
        return view('auth.login', compact('slug'));
    })->name('login');

    Route::post('/authenticate', [App\Http\Controllers\AuthController::class, 'login'])->name('authenticate');

    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('/verify-email/{code}', [App\Http\Controllers\AuthController::class, 'verifyEmail'])->name('verify.email');

    Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPasswordForm'])->name('forgot.password');
    Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'sendResetPasswordEmail'])->name('forgot.password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\AuthController::class, 'showResetPasswordForm'])->name('reset.password.form');
    Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPassword'])->name('reset.password');

    Route::get('/resend-verify-email', [App\Http\Controllers\AuthController::class, 'resedVerifyEmailForm'])->name('resend.verify.email.form');
    Route::post('/resend-verify-email', [App\Http\Controllers\AuthController::class, 'resedVerifyEmail'])->name('resend.verify.email');
});

Route::group(['prefix' => '{slug}', 'middleware' => ['auth.tdty']], function () {

    // Calendar Index
    Route::get('/', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    Route::get('/password/change', [App\Http\Controllers\AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [App\Http\Controllers\AuthController::class, 'changePassword'])->name('password.update');

    Route::get('/convert/old/url', [App\Http\Controllers\AuthController::class, 'showConvertOldUrlForm'])->name('convert.old.url');


    // Calendar popup load
    Route::get('/calendar/load', [App\Http\Controllers\CalendarController::class, 'load'])->name('calendar.load');

    // Save Report & Link
    Route::post('/createsavedreport', [App\Http\Controllers\Auth\DownloadController::class, 'createSavedReport'])->name('report.createsavedreport');
    Route::post('/create-tdty-saved-report', [App\Http\Controllers\CalendarController::class, 'createTdtySavedReport'])->name('calendar.create-tdty-saved-report');

    // Daily Monitoring
    Route::get('/daily-monitoring-report', [App\Http\Controllers\DailyMonitoringController::class, 'index'])->name('dailyMonitoringReport');
    Route::get('/daily-monitoring-report/{date}', [App\Http\Controllers\DailyMonitoringController::class, 'index']);

    // SM Daily Monitoring
    Route::get('/sm-daily-monitoring-report/{date}', [App\Http\Controllers\DailyMonitoringController::class, 'sm_index']);
    Route::get('/sm-daily-monitoring-report-download', [App\Http\Controllers\DailyMonitoringController::class, 'sm_download_index'])->name('download-sm-report');

    // Search SM Daily Monitoring Database
    Route::get('/smmonitoring_download', [App\Http\Controllers\DailyMonitoringController::class, 'pdf'])->name('smmonitoring.pdf');
    Route::get('/smmonitoring_pdfview', [App\Http\Controllers\DailyMonitoringController::class, 'pdfview'])->name('smmonitoring.pdfview');
    Route::get('/smmonitoring_downloadfile', [App\Http\Controllers\DailyMonitoringController::class, 'downloadDatabase'])->name('smmonitoring_download.pdf');
    Route::post('/smmonitoring_fetch-download-options', [App\Http\Controllers\DailyMonitoringController::class, 'fetchDownloadOptions'])->name('smmonitoring.api.fetch-download-options');

    // Search Database
    Route::get('/pdfview',[App\Http\Controllers\Auth\DownloadController::class, 'pdfview'])->withoutMiddleware(['auth.tdty', 'auth'])->name('report.pdfview');
    Route::get('/download', [App\Http\Controllers\Auth\DownloadController::class, 'pdf'])->name('report.pdf');
    Route::get('/downloadfile/{filename}', [App\Http\Controllers\CalendarController::class, 'download']);

    Route::post('/api/fetch-module', [App\Http\Controllers\Auth\DownloadController::class, 'fetchModule'])->name('report.api.fetch-module');
    Route::post('/api/fetch-chapter', [App\Http\Controllers\Auth\DownloadController::class, 'fetchChapter'])->name('report.api.fetch-chapter');
    Route::post('/api/fetch-user', [App\Http\Controllers\Auth\DownloadController::class, 'fetchUser'])->name('report.api.fetch-user');
    Route::post('/api/fetch-state', [App\Http\Controllers\Auth\DownloadController::class, 'fetchState'])->name('report.api.fetch-state');
    Route::post('/api/check-link', [App\Http\Controllers\Auth\DownloadController::class, 'checkLink'])->name('report.api.check-link');
    Route::post('/api/check-link-edit', [App\Http\Controllers\Auth\DownloadController::class, 'checkLinkEdit'])->name('report.api.check-link-edit');
    Route::get('/api/fetch-tag', [App\Http\Controllers\Auth\DownloadController::class, 'fetchTag'])->name('report.api.fetch-tag');
    Route::get('/api/fetch-source', [App\Http\Controllers\Auth\DownloadController::class, 'fetchSource'])->name('report.api.fetch-source');
    Route::post('/fetch-module', [App\Http\Controllers\Auth\DownloadController::class, 'fetchModulePublic'])->name('report.fetch-module');
    Route::post('/fetch-chapter', [App\Http\Controllers\Auth\DownloadController::class, 'fetchChapterPublic'])->name('report.fetch-chapter');
    Route::post('/fetch-download-options', [App\Http\Controllers\Auth\DownloadController::class, 'fetchDownloadOptions'])->name('report.api.fetch-download-options');
    Route::post('/fetch-team-tags', [App\Http\Controllers\Auth\DownloadController::class, 'fetchTeamTags'])->name('report.api.fetch-team-tags');

    //daily monitoring category
    Route::get('/dmr-all-issues/{issue}/{date}', [App\Http\Controllers\Auth\DownloadController::class, 'viewReportAllIssueAndDate'])->name('report.viewReportAllIssue');
    Route::get('/dmr/{issue}/{date}', [App\Http\Controllers\Auth\DownloadController::class, 'viewReportByIssueAndDate'])->name('report.viewReportByIssue');
    Route::get('/{issue}', [App\Http\Controllers\Auth\DownloadController::class, 'viewReportByIssue'])->name('report.viewReportByIssue');
    Route::get('/{issue}/{category}', [App\Http\Controllers\Auth\DownloadController::class, 'viewReportByIssueCat'])->name('report.viewReportByIssueCat');
    Route::get('/{issue}/{category}/{sub_category}', [App\Http\Controllers\Auth\DownloadController::class, 'viewReportByIssueCatSubCat'])->name('report.viewReportByIssueCatSubCat');
});
