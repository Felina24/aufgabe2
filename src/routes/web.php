<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AttendanceListController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffAttendanceController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

Route::get('/login', fn () => view('auth.user-login'))
    ->middleware('guest')
    ->name('login');

Route::post('/login', [UserAuthController::class, 'login'])
    ->middleware('guest')
    ->name('user.login.post');

Route::post('/logout', function (Request $request) {

    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');

})->name('logout');

Route::get('/admin/login', fn () => view('auth.login'))
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('guest')
    ->name('admin.login.post');

Route::middleware(['auth:web', 'verified', 'user'])->group(function () {

    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn']);
    Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut']);

    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

    Route::get(
        '/attendance/detail/{date}',
        [AttendanceController::class, 'detail']
    )->name('attendance.detail');

    Route::post(
        '/attendance/update/{attendance}',
        [AttendanceController::class, 'update']
    )->name('attendance.update');

    Route::get(
        '/request/list',
        [AttendanceRequestController::class, 'index']
    )->name('request.list');

    Route::post(
        '/attendance/request/{attendance}',
        [AttendanceRequestController::class, 'store']
    )->name('attendance.request');

    Route::get(
        '/request/{attendanceRequest}',
        [AttendanceRequestController::class, 'showUser']
    )->name('request.show');
});

Route::prefix('admin')
    ->middleware(['auth:admin'])
    ->group(function () {

        Route::get('/attendance/list', [AttendanceListController::class, 'index'])
            ->name('admin.attendance.list');

        Route::get(
            '/attendance/detail/{user}/{date}',
            [AttendanceController::class, 'adminDetail']
        )->name('admin.attendance.detail');

        Route::get(
            '/request/list',
            [AttendanceRequestController::class, 'index']
        )->name('admin.request.list');

        Route::get(
            '/request/{attendanceRequest}',
            [AttendanceRequestController::class, 'show']
        )->name('admin.request.show');

        Route::post(
            '/request/approve/{attendanceRequest}',
            [AttendanceRequestController::class, 'approve']
        )->name('admin.request.approve');

        Route::post(
            '/attendance/update/{attendance}',
            [AttendanceController::class, 'update']
        )->name('admin.attendance.update');

        Route::get('/staff/list', [StaffController::class, 'index'])
            ->name('admin.staff.list');

        Route::get('/attendance/staff/{user}', [StaffAttendanceController::class, 'index'])
            ->name('admin.staff.attendance');

        Route::get('/attendance/staff/{user}/csv', [StaffAttendanceController::class, 'exportCsv'])
            ->name('admin.attendance.csv');

        Route::post('/logout', function () {

            Auth::guard('admin')->logout();

            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('admin.login');

        })->name('admin.logout');
});
