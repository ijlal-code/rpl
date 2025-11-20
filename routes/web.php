<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RekomendasiKNNController;
use App\Http\Controllers\SopirController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'sopir' => redirect()->route('sopir.dashboard'),
            default => redirect()->route('penumpang.dashboard'),
        };
    })->name('dashboard');

    Route::get('/rekomendasi', [RekomendasiKNNController::class, 'index'])->name('rekomendasi.index');
    Route::post('/rekomendasi', [RekomendasiKNNController::class, 'rekomendasi'])->name('rekomendasi.hitung');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/laporan', [AdminController::class, 'laporan'])->name('admin.laporan');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::post('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
});

Route::middleware(['auth', 'sopir'])->group(function () {
    Route::get('/sopir', [SopirController::class, 'dashboard'])->name('sopir.dashboard');
    Route::post('/sopir/pesanan/{pesanan}/konfirmasi', [SopirController::class, 'konfirmasi'])->name('sopir.pesanan.konfirmasi');
    Route::post('/sopir/jadwal', [SopirController::class, 'simpanJadwal'])->name('sopir.jadwal.store');
    Route::patch('/sopir/jadwal/{jadwal}', [SopirController::class, 'perbaruiJadwal'])->name('sopir.jadwal.update');
    Route::delete('/sopir/jadwal/{jadwal}', [SopirController::class, 'hapusJadwal'])->name('sopir.jadwal.destroy');
});

Route::middleware(['auth', 'penumpang'])->group(function () {
    Route::get('/penumpang', [UserController::class, 'dashboard'])->name('penumpang.dashboard');
    Route::post('/penumpang/pesanan', [UserController::class, 'buatPesanan'])->name('penumpang.pesan');
    Route::get('/penumpang/pesanan', [UserController::class, 'pesanan'])->name('penumpang.pesanan');
    Route::post('/penumpang/pesanan/{pesanan}/batal', [UserController::class, 'batalkanPesanan'])->name('penumpang.pesanan.batalkan');
});
