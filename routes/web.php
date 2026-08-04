<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RepairController;
use App\Http\Controllers\AdminController;

// Input page (Guest)
Route::get('/', [RepairController::class, 'index'])->name('repair.index');
Route::get('/add', [RepairController::class, 'create'])->name('repair.create');
Route::post('/repair', [RepairController::class, 'store'])->name('repair.store');
Route::post('/repair/verify', [RepairController::class, 'verifyTractor'])->name('repair.verifyTractor');
Route::post('/repair/verify-nik', [RepairController::class, 'verifyNik'])->name('repair.verifyNik');

Route::get('/finish/{id}', [RepairController::class, 'finishForm'])->name('repair.finishForm');
Route::post('/finish/{id}', [RepairController::class, 'updateFinish'])->name('repair.updateFinish');
Route::put('/repair/{id}', [RepairController::class, 'update'])->name('repair.update');
Route::delete('/repair/{id}', [RepairController::class, 'destroy'])->name('repair.destroy');

// Admin Auth
Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkScheduleController;

// Admin Dashboard (Protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/export', [AdminController::class, 'exportExcel'])->name('dashboard.export');
    Route::get('/duplicates', [AdminController::class, 'duplicates'])->name('duplicates');
    Route::post('/duplicates/exclude', [AdminController::class, 'excludeDuplicate'])->name('duplicates.exclude');
    Route::post('/duplicates/include', [AdminController::class, 'includeDuplicate'])->name('duplicates.include');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Work Schedules (Pengaturan Jam Kerja)
    Route::get('/work-schedules', [WorkScheduleController::class, 'index'])->name('work-schedules.index');
    Route::post('/work-schedules', [WorkScheduleController::class, 'store'])->name('work-schedules.store');
    Route::delete('/work-schedules/{id}', [WorkScheduleController::class, 'destroy'])->name('work-schedules.destroy');
});
