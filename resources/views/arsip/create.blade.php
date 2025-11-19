@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded-4">
        <div class="card-header bg-success text-white rounded-top-4 d-flex justify-content-between align-items-center">
            <h4>➕ Input Jadwal Angkutan</h4>
                <a href="{{ route('sekretaris.kategori.create') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-plus-circle"></i> Buat Rute Baru
                </a>
        </div>
        <div class="card-body">

        {{-- Alert jika kategori belum tersedia --}}
            @if ($kategori->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Belum ada **Data Rute** tersedia (sebagai pengganti Kategori). Silakan buat Rute terlebih dahulu.
                </div>
            @endif

            <form action="{{ route('sekretaris.arsip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
    <label for="judul_arsip" class="form-label">Nama Angkutan / Judul Jadwal</label>
    <input type="text" class="form-control @error('judul_arsip') is-invalid @enderror" id="judul_arsip" name="judul_arsip" value="{{ old('judul_arsip') }}" required>
    @error('judul_arsip')
    <div class="invalid-feedback">
        Nama Angkutan/Jadwal sudah dipakai.
    </div>
@enderror
</div>


                <div class="mb-3">
                    <label for="kategori_id" class="form-label">Rute (sebagai pengganti Kategori)</label>
                    <select class="form-select" name="kategori_id" id="kategori_id" required>
                        <option value="">-- Pilih Rute --</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal_upload" class="form-label">Tanggal Keberangkatan</label>
                    <input type="date" class="form-control" id="tanggal_upload" name="tanggal_upload" required>
                </div>

                {{-- PERUBAHAN: Ganti Upload File menjadi Jam Keberangkatan --}}
                <div class="mb-3">
                    <label for="jam_keberangkatan" class="form-label">Jam Keberangkatan</label>
                    <input type="time" class="form-control" id="jam_keberangkatan" name="jam_keberangkatan" required>
                </div>
                
                {{-- Field file_arsip dihapus dari formulir untuk Jam Keberangkatan --}}

                <button type="submit" class="btn btn-success">💾 Simpan Jadwal</button>
                <a href="{{ route('sekretaris.arsip.index') }}" class="btn btn-secondary">↩️ Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection