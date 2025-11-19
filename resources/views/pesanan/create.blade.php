@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <h2 class="mb-3">Pesan Perjalanan</h2>
    <form method="POST" action="{{ route('pesanan.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Rute</label>
            <select name="rute_id" class="form-select" required>
                @foreach($rute as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_rute }} ({{ $item->titik_berangkat }} → {{ $item->titik_tujuan }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kendaraan (opsional)</label>
            <select name="kendaraan_id" class="form-select">
                <option value="">Pilih otomatis oleh sopir</option>
                @foreach($kendaraan as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kendaraan }} - {{ $item->rute->nama_rute }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Jam Keberangkatan</label>
            <input type="time" name="jam_keberangkatan" class="form-control" required>
        </div>
        <button class="btn btn-primary">Kirim Pesanan</button>
    </form>
</div>
@endsection
