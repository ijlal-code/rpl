@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Dashboard Admin</h1>
    <div class="row g-3 mb-4">
        @foreach($statistik as $label => $value)
            <div class="col-md-3">
                <div class="card text-bg-primary h-100">
                    <div class="card-body">
                        <p class="text-uppercase small mb-1">{{ ucfirst($label) }}</p>
                        <h3 class="fw-bold">{{ $value }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <h5>Pesanan Terbaru</h5>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Penumpang</th>
            <th>Rute</th>
            <th>Jadwal</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pesananTerbaru as $item)
            <tr>
                <td>{{ $item->penumpang->name ?? '-' }}</td>
                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                <td><span class="badge text-bg-secondary">{{ $item->status }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @isset($laporan)
        <div class="mt-4">
            <h5>Laporan Lengkap</h5>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Penumpang</th>
                    <th>Sopir</th>
                    <th>Kendaraan</th>
                    <th>Rute</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($laporan as $item)
                    <tr>
                        <td>{{ $item->penumpang->name ?? '-' }}</td>
                        <td>{{ $item->sopir->nama ?? '-' }}</td>
                        <td>{{ $item->kendaraan->nama ?? '-' }}</td>
                        <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                        <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <h5>Diagram Teks</h5>
            <ul>
                <li><strong>DFD Level 0:</strong> {{ $diagrams['dfd0'] }}</li>
                <li><strong>DFD Level 1:</strong> {{ $diagrams['dfd1'] }}</li>
                <li><strong>DFD Level 2:</strong> {{ $diagrams['dfd2'] }}</li>
                <li><strong>ERD:</strong> {{ $diagrams['erd'] }}</li>
                <li><strong>Use Case:</strong> {{ $diagrams['usecase'] }}</li>
                <li><strong>Flowchart:</strong> {{ $diagrams['flowchart'] }}</li>
            </ul>
        </div>
    @endisset
</div>
@endsection
