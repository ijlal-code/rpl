@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 840px;">
    <h2 class="mb-3">Rekomendasi Jadwal (KNN)</h2>
    <form method="GET" action="{{ route('rekomendasi.index') }}" class="card shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Pilih Rute</label>
                <select name="rute_id" class="form-select" required>
                    <option value="">-- pilih rute --</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" @selected(request('rute_id') == $route->id)>{{ $route->nama_rute }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jam Keberangkatan</label>
                <input type="time" name="jam_keberangkatan" class="form-control" value="{{ request('jam_keberangkatan') }}" required>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Hitung Rekomendasi</button>
            </div>
        </div>
    </form>

    @if($recommendations)
        <div class="alert alert-success">
            <h5 class="alert-heading">Rekomendasi</h5>
            <p>KNN menyarankan berangkat pukul <strong>{{ $recommendations['waktu'] }}</strong>.</p>
            @if($recommendations['kendaraan'])
                <p>Sering digunakan: kendaraan ID #{{ $recommendations['kendaraan'] }}.</p>
            @endif
            <small class="text-muted">Berdasarkan {{ $recommendations['totalSampel'] }} pesanan sebelumnya.</small>
        </div>
    @elseif(request()->filled('rute_id'))
        <div class="alert alert-warning">Belum ada data historis untuk rute yang dipilih.</div>
    @endif
</div>
@endsection
