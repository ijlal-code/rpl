@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Rekomendasi Jadwal KNN</h1>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Form Input</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rekomendasi.hitung') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Jam Keberangkatan</label>
                            <input type="time" name="jam_keberangkatan" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Rute</label>
                            <select name="rute_id" class="form-select">
                                @foreach($rute as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_rute }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Status Sebelumnya</label>
                            <select name="status_sebelumnya" class="form-select">
                                <option value="menunggu">Menunggu</option>
                                <option value="dikonfirmasi">Dikonfirmasi</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nilai K</label>
                            <input type="number" name="k" class="form-control" min="1" max="10" value="3">
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Hitung Rekomendasi</button>
                    </form>
                </div>
            </div>
            @if(session('rekomendasi'))
                <div class="alert alert-success mt-3">
                    Jadwal terbaik: <strong>{{ session('rekomendasi') }}</strong>
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">Dataset & Tetangga Terdekat</div>
                <div class="card-body">
                    <p>Dataset yang digunakan (jam -> rute -> status -> label jam rekomendasi):</p>
                    <ul class="small">
                        @foreach($dataset as $data)
                            <li>{{ implode(',', $data['features']) }} => {{ $data['label'] }}</li>
                        @endforeach
                    </ul>
                    @if(session('neighbors'))
                        <hr>
                        <p class="fw-bold">Hasil perhitungan Euclidean:</p>
                        <ul class="small">
                            @foreach(session('neighbors') as $neighbor)
                                <li>Label {{ $neighbor['label'] }} dengan jarak {{ number_format($neighbor['distance'], 2) }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
