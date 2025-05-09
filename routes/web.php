<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;

Route::get('/login', [SuratController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SuratController::class, 'login'])->name('login.action');
Route::get('/', [SuratController::class, 'index']);

// Route logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Route yang butuh login
Route::middleware('auth')->group(function () {
    
    Route::resource('pages', SuratController::class);
    
    Route::get('/create/{jenis}', [SuratController::class, 'buatSurat']);

    Route::post('/submit/{jenis}', [SuratController::class, 'submitLaporanEUC'])->name('submit.laporan');
});