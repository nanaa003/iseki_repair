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
Route::delete('/repair/{id}', [RepairController::class, 'destroy'])->name('repair.destroy');

// Admin Auth
Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

// Admin Dashboard (Protected)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/export', [AdminController::class, 'exportExcel'])->name('dashboard.export');
    Route::get('/duplicates', [AdminController::class, 'duplicates'])->name('duplicates');
    Route::post('/duplicates/exclude', [AdminController::class, 'excludeDuplicate'])->name('duplicates.exclude');
    Route::post('/duplicates/include', [AdminController::class, 'includeDuplicate'])->name('duplicates.include');
});
