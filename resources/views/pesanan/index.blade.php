@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pesanan Saya</h2>
        <a class="btn btn-primary" href="{{ route('pesanan.create') }}">Pesan Perjalanan</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Rute</th><th>Kendaraan</th><th>Jam</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($pesanan as $item)
                    <tr>
                        <td>{{ $item->rute->nama_rute }}</td>
                        <td>{{ $item->kendaraan?->nama_kendaraan ?? 'Menunggu penugasan' }}</td>
                        <td>{{ $item->jam_keberangkatan }}</td>
                        <td>{{ ucfirst(str_replace('_',' ', $item->status)) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
