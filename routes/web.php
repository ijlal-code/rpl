<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RekomendasiKNNController;
use App\Http\Controllers\RuteController;
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

    Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
    Route::post('/kendaraan', [KendaraanController::class, 'store'])->name('kendaraan.store');
    Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'update'])->name('kendaraan.update');
    Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroy'])->name('kendaraan.destroy');

    Route::get('/rute', [RuteController::class, 'index'])->name('rute.index');
    Route::post('/rute', [RuteController::class, 'store'])->name('rute.store');
    Route::put('/rute/{rute}', [RuteController::class, 'update'])->name('rute.update');
    Route::delete('/rute/{rute}', [RuteController::class, 'destroy'])->name('rute.destroy');

    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    Route::post('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.status');
});

Route::middleware(['auth', 'sopir'])->group(function () {
    Route::get('/sopir', [SopirController::class, 'dashboard'])->name('sopir.dashboard');
    Route::post('/sopir/pesanan/{pesanan}/konfirmasi', [SopirController::class, 'konfirmasi'])->name('sopir.pesanan.konfirmasi');
    Route::post('/sopir/kendaraan/{kendaraan}/status', [SopirController::class, 'ubahStatusKendaraan'])->name('sopir.kendaraan.status');
});

Route::middleware(['auth', 'penumpang'])->group(function () {
    Route::get('/penumpang', [UserController::class, 'dashboard'])->name('penumpang.dashboard');
    Route::post('/penumpang/pesanan', [UserController::class, 'buatPesanan'])->name('penumpang.pesan');
    Route::get('/penumpang/pesanan', [UserController::class, 'pesanan'])->name('penumpang.pesanan');
});
