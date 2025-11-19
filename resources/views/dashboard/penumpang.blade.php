@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Dashboard Penumpang</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Buat Pesanan</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('penumpang.pesan') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Rute</label>
                            <select name="rute_id" class="form-select">
                                @foreach($rute as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_rute }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Kendaraan (opsional)</label>
                            <select name="kendaraan_id" class="form-select">
                                <option value="">Pilih kendaraan</option>
                                @foreach($kendaraan as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }} - {{ $item->plat_nomor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal_keberangkatan" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Jam</label>
                            <input type="time" name="jam_keberangkatan" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Catatan</label>
                            <input type="text" name="catatan" class="form-control">
                        </div>
                        <button class="btn btn-primary w-100">Pesan</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Rekomendasi Jadwal KNN</div>
                <div class="card-body">
                    <a class="btn btn-outline-primary w-100" href="{{ route('rekomendasi.index') }}">Buka Modul Rekomendasi</a>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mt-4">Status Pesanan</h5>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Rute</th>
            <th>Kendaraan</th>
            <th>Jadwal</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pesanan as $item)
            <tr>
                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                <td>{{ $item->kendaraan->nama ?? '-' }}</td>
                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                <td><span class="badge text-bg-secondary">{{ $item->status }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
