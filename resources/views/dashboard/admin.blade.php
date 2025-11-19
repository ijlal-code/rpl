@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Dashboard Admin</h2>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body"><strong>Sopir</strong><p class="display-6">{{ $totalSopir }}</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body"><strong>Rute</strong><p class="display-6">{{ $totalRute }}</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body"><strong>Kendaraan</strong><p class="display-6">{{ $totalKendaraan }}</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm"><div class="card-body"><strong>Pesanan</strong><p class="display-6">{{ $totalPesanan }}</p></div></div>
        </div>
    </div>
    <div class="alert alert-info mt-4">Kelola sopir, rute, kendaraan, dan pantau pesanan aktif melalui menu di atas.</div>
</div>
@endsection
