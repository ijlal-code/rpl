<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RekomendasiKNNController;
use App\Http\Controllers\RuteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('regis');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('admin')->group(function () {
        Route::get('/admin/sopir', [AdminController::class, 'sopir'])->name('admin.sopir');
        Route::post('/admin/sopir', [AdminController::class, 'storeSopir'])->name('admin.sopir.store');
        Route::resource('rute', RuteController::class)->except(['show']);
        Route::resource('kendaraan', KendaraanController::class)->except(['show']);
        Route::get('/admin/pesanan', [PesananController::class, 'adminIndex'])->name('admin.pesanan');
        Route::patch('/admin/pesanan/{pesanan}', [PesananController::class, 'updateStatus'])->name('admin.pesanan.update');
    });

    Route::middleware('sopir')->group(function () {
        Route::get('/sopir/pesanan', [PesananController::class, 'sopirIndex'])->name('sopir.pesanan');
        Route::patch('/sopir/pesanan/{pesanan}', [PesananController::class, 'updateStatus'])->name('sopir.pesanan.update');
    });

    Route::middleware('penumpang')->group(function () {
        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
        Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
        Route::get('/rekomendasi', [RekomendasiKNNController::class, 'index'])->name('rekomendasi.index');
    });
});
