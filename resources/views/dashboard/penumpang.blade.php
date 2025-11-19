@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Dashboard Penumpang</h2>
        <a class="btn btn-primary" href="{{ route('pesanan.create') }}">Pesan Perjalanan</a>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Daftar Rute</h5>
            <ul class="list-group list-group-flush">
                @forelse($routes as $rute)
                    <li class="list-group-item">
                        <strong>{{ $rute->nama_rute }}</strong><br>
                        {{ $rute->titik_berangkat }} → {{ $rute->titik_tujuan }} ({{ $rute->estimasi_waktu }} menit)
                    </li>
                @empty
                    <li class="list-group-item">Belum ada rute.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Pesanan Terbaru</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Rute</th><th>Kendaraan</th><th>Jam</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($pesanan as $item)
                            <tr>
                                <td>{{ $item->rute->nama_rute }}</td>
                                <td>{{ $item->kendaraan?->nama_kendaraan ?? 'Menunggu penugasan' }}</td>
                                <td>{{ $item->jam_keberangkatan }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_',' ', $item->status)) }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
