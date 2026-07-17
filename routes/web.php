<?php

use App\Http\Controllers\LaporanPdfController;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Iuran\Index as IuranIndex;
use App\Livewire\Laporan\Index as LaporanIndex;
use App\Livewire\Pengaturan\Index as PengaturanIndex;
use App\Livewire\Peserta\Index as PesertaIndex;
use App\Livewire\Publik\Index as PublikIndex;
use App\Livewire\Santunan\Index as SantunanIndex;
use App\Support\PeriodeContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Publik — Halaman transparansi (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', PublikIndex::class)->name('publik.index');
Route::get('/publik/{tahun}/surat', [LaporanPdfController::class, 'suratPublik'])->name('publik.surat');
Route::get('/publik/{tahun}/rincian', [LaporanPdfController::class, 'rincianPublik'])->name('publik.rincian');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', Login::class)->name('login');
});

Route::post('/keluar', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('publik.index');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Area Pengurus (butuh login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/peserta', PesertaIndex::class)->name('peserta.index');
    Route::get('/iuran', IuranIndex::class)->name('iuran.index');
    Route::get('/santunan', SantunanIndex::class)->name('santunan.index');
    Route::get('/laporan', LaporanIndex::class)->name('laporan.index');
    Route::get('/pengaturan', PengaturanIndex::class)->name('pengaturan.index');

    // Ganti periode aktif
    Route::post('/periode/pilih', function () {
        $id = (int) request('periode_id');
        if ($id) {
            PeriodeContext::set($id);
        }
        return back();
    })->name('periode.pilih');

    // Export PDF (pengurus)
    Route::get('/laporan/{tahun}/surat', [LaporanPdfController::class, 'surat'])->name('laporan.surat.pdf');
    Route::get('/laporan/{tahun}/rincian', [LaporanPdfController::class, 'rincian'])->name('laporan.rincian.pdf');
    Route::get('/laporan/{tahun}/kartu', [LaporanPdfController::class, 'kartu'])->name('laporan.kartu.pdf');
});
