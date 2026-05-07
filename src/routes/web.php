<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminStampCorrectionRequestController;

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

// 認証待機画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 認証リンク処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/attendance',[AttendanceController::class, 'create']);
    Route::post('/attendance',[AttendanceController::class, 'clockIn']);

    Route::post('/break/start',[AttendanceController::class, 'breakStart']);
    Route::post('/break/end',[AttendanceController::class, 'breakEnd']);

    Route::post('/attendance/end',[AttendanceController::class, 'clockOut']);

    Route::get('/attendance/list',[AttendanceController::class, 'index']);
    Route::get('/attendance/detail/{id}',[AttendanceController::class, 'show']);
    Route::post('/attendance/detail/{id}',[AttendanceController::class, 'update']);

    Route::get('/stamp_correction_request/list',
        [StampCorrectionRequestController::class, 'index']
    );
});


/*
|--------------------------------------------------------------------------
| Admin Auth
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', function () {
    return view('admin.login');
});

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/admin/attendance/list',[AdminAttendanceController::class, 'index']);
    Route::get('/admin/staff/list',[AdminStaffController::class, 'index']);
    Route::get('/admin/attendance/staff/{id}',[AdminAttendanceController::class, 'staff']);

    Route::get('/admin/attendance/{id}',[AdminAttendanceController::class, 'show']);
    Route::post('/admin/attendance/{id}',[AdminAttendanceController::class, 'update']);

    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',
        [AdminStampCorrectionRequestController::class, 'show']
    );
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}',
        [AdminStampCorrectionRequestController::class, 'admit']
    );

    Route::post('/export', [AdminAttendanceController::class, 'export']);
});