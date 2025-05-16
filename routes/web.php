<?php 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratController;

Route::get('/login', [SuratController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SuratController::class, 'login'])->name('login.action');
Route::get('/', [SuratController::class, 'index']);

// Allow search on pages without login
Route::get('/pages', [SuratController::class, 'index'])->name('pages.index');

// Route logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Route yang butuh login
Route::middleware('auth')->group(function () {

    // Masukkan semua method resource kecuali index
    Route::get('/pages/create', [SuratController::class, 'create'])->name('pages.create');
    Route::post('/pages', [SuratController::class, 'store'])->name('pages.store');
    Route::get('/pages/{page}', [SuratController::class, 'show'])->name('pages.show');
    Route::get('/pages/{page}/edit', [SuratController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [SuratController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [SuratController::class, 'destroy'])->name('pages.destroy');

    Route::get('/create/{jenis}', [SuratController::class, 'buatSurat']);
    Route::get('/history', [SuratController::class, 'history'])->name('surat.history');
    Route::get('/cetak-surat/{id}', [SuratController::class, 'cetakSurat'])->name('cetak.surat');
    Route::post('/submit/{jenis}', [SuratController::class, 'submitLaporanEUC'])->name('submit.laporan');
});
