@extends('layouts.app')

@section('content')
<div class="py-5" style="min-height: 100vh; background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);">
    <div class="container pb-5">
        <div class="text-center mb-5 text-white animate__animated animate__fadeIn">
            <h2 class="fw-bold display-5">🚍 Dashboard Sopir / Operator Angkutan</h2>
            <p class="fs-5">Selamat datang, <span class="fw-semibold">{{ auth()->user()->name }}</span></p>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-lg h-100 text-center bg-gradient-primary-subtle transition-all hover-scale">
                    <div class="card-body p-4">
                        <i class="bi bi-calendar-week display-3 text-primary mb-3 animate__animated animate__pulse animate__infinite animate__slower"></i>
                        <h5 class="card-title fw-bold text-dark mb-3">Input Jadwal & Posisi</h5>
                        <p class="text-muted mb-4">Masukkan jadwal keberangkatan atau perbarui posisi kendaraan.</p>
                        <a href="{{ route('sekretaris.arsip.create') }}" class="btn btn-primary btn-lg fw-bold px-4">Input Data</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-lg h-100 text-center bg-gradient-success-subtle transition-all hover-scale">
                    <div class="card-body p-4">
                        <i class="bi bi-map display-3 text-success mb-3 animate__animated animate__pulse animate__infinite animate__slower"></i>
                        <h5 class="card-title fw-bold text-dark mb-3">Kelola Data Rute</h5>
                        <p class="text-muted mb-4">Atur data Rute (seperti Kategori) untuk layanan yang lebih baik.</p>
                        <a href="{{ route('sekretaris.kategori.index') }}" class="btn btn-success btn-lg fw-bold px-4">Kelola Rute</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-lg h-100 text-center bg-gradient-warning-subtle transition-all hover-scale">
                    <div class="card-body p-4">
                        <i class="bi bi-speedometer2 display-3 text-warning mb-3 animate__animated animate__pulse animate__infinite animate__slower"></i>
                        <h5 class="card-title fw-bold text-dark mb-3">Lihat & Update Jadwal</h5>
                        <p class="text-muted mb-4">Kelola dan perbarui jadwal/posisi yang telah diinput.</p>
                        <a href="{{ route('sekretaris.arsip.index') }}" class="btn btn-warning btn-lg fw-bold px-4">Lihat Jadwal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection