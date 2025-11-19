@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">MandarMove</h1>
        <p class="lead">Platform angkutan umum dengan rekomendasi jadwal berbasis KNN.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Mulai Sekarang</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Multi Role</h5>
                    <p class="card-text">Admin mengelola rute dan kendaraan, sopir mengonfirmasi pesanan, penumpang memesan perjalanan.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Data Terhubung</h5>
                    <p class="card-text">Rute, kendaraan, sopir, dan pesanan saling terhubung sesuai blueprint di docs/angkutan_knn_laravel.md.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Rekomendasi KNN</h5>
                    <p class="card-text">Prediksi jam keberangkatan terbaik berdasarkan riwayat pesanan yang selesai.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
