<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SuratController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Authentication Routes ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.action');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Public Dashboard Route ---
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// --- Routes Requiring Authentication ---
Route::middleware('auth')->group(function () {
    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    // Letter Management
    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/create/{jenis}', [SuratController::class, 'create'])->name('create');
        Route::post('/store/{jenis}', [SuratController::class, 'store'])->name('store');
        Route::post('/preview/{jenis}', [SuratController::class, 'preview'])->name('preview');
        Route::get('/{id}/edit', [SuratController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [SuratController::class, 'update'])->name('update');
        Route::get('/{id}/print', [SuratController::class, 'print'])->name('print');
    });
});