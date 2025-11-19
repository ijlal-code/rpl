@extends('layouts.app')

@section('content')
<div class="py-5" style="min-height: 100vh; background: linear-gradient(135deg, #134E5E, #71B280);">
    <div class="container pb-5">
        <div class="text-center mb-5 text-white animate__animated animate__fadeIn">
            <h2 class="fw-bold display-5">🚶 Dashboard Penumpang</h2>
            <p class="fs-5">Selamat datang, <span class="fw-semibold">{{ auth()->user()->name }}</span></p>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-lg h-100 text-center bg-gradient-info-subtle transition-all hover-scale">
                    <div class="card-body p-4">
                        <i class="bi bi-pin-map display-3 text-info mb-3 animate__animated animate__pulse animate__infinite animate__slower"></i>
                        <h5 class="card-title fw-bold text-dark mb-3">Cari Jadwal & Posisi Angkutan</h5>
                        <p class="text-muted mb-4">Temukan jadwal keberangkatan dan pantau posisi angkutan secara real-time.</p>
                        <a href="{{ route('kepala.arsip.index') }}" class="btn btn-info btn-lg fw-bold px-4">Cari Angkutan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection