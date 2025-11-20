@extends('layouts.app')

@section('content')
@php
    $ruteNama = $jadwal->rute->nama_rute ?? '';
    $rutePilihan = match ($ruteNama) {
        'Majene - Polewali' => 'majene_polewali',
        'Polewali - Majene' => 'polewali_majene',
        default => 'custom',
    };
    $customRute = $rutePilihan === 'custom' ? $ruteNama : '';
@endphp

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="mb-1">Edit Jadwal</h1>
            <p class="text-muted mb-0">Perbarui rute, waktu keberangkatan, status, dan catatan jadwal sopir.</p>
        </div>
        <a href="{{ route('sopir.dashboard') }}#jadwal-saya" class="btn btn-outline-secondary">Kembali</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('sopir.jadwal.update', $jadwal) }}" class="d-flex flex-column gap-3">
                @csrf
                @method('PATCH')

                <div>
                    <label class="form-label">Rute</label>
                    <div class="d-flex flex-column gap-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rute_pilihan" id="rute-majene-polewali" value="majene_polewali" @checked(old('rute_pilihan', $rutePilihan) === 'majene_polewali') required>
                            <label class="form-check-label" for="rute-majene-polewali">Majene - Polewali</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rute_pilihan" id="rute-polewali-majene" value="polewali_majene" @checked(old('rute_pilihan', $rutePilihan) === 'polewali_majene') required>
                            <label class="form-check-label" for="rute-polewali-majene">Polewali - Majene</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rute_pilihan" id="rute-custom" value="custom" @checked(old('rute_pilihan', $rutePilihan) === 'custom') required>
                            <label class="form-check-label" for="rute-custom">Rute lain (tulis manual)</label>
                        </div>
                        <input type="text" name="custom_rute" class="form-control" placeholder="Contoh: Majene - Mamuju" aria-label="Rute lain" value="{{ old('custom_rute', $customRute) }}">
                        <small class="text-muted">Isi jika memilih rute lain. Gunakan format Asal - Tujuan.</small>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal_keberangkatan" value="{{ old('tanggal_keberangkatan', $jadwal->tanggal_keberangkatan) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam</label>
                        <input type="time" name="jam_keberangkatan" value="{{ old('jam_keberangkatan', $jadwal->jam_keberangkatan) }}" class="form-control" required>
                    </div>
                </div>

                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="aktif" @selected(old('status', $jadwal->status) === 'aktif')>Aktif</option>
                        <option value="sedang_jalan" @selected(old('status', $jadwal->status) === 'sedang_jalan')>Sedang jalan</option>
                        <option value="tidak_aktif" @selected(old('status', $jadwal->status) === 'tidak_aktif')>Tidak aktif</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: menunggu penumpang di terminal...">{{ old('catatan', $jadwal->catatan) }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('sopir.dashboard') }}#jadwal-saya" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const customInput = document.querySelector('input[name="custom_rute"]');
        const radios = document.querySelectorAll('input[name="rute_pilihan"]');

        const toggleCustomInput = () => {
            const isCustom = document.getElementById('rute-custom').checked;
            customInput.disabled = !isCustom;
        };

        radios.forEach(radio => radio.addEventListener('change', toggleCustomInput));
        toggleCustomInput();
    });
</script>
@endsection
