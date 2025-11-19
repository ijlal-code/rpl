@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded-4">
        <div class="card-header bg-warning text-dark rounded-top-4">
            <h4>✏️ Update Jadwal Angkutan & Posisi</h4>
        </div>
        <div class="card-body">
            <form action="{{ route(auth()->user()->role . '.arsip.update', $arsip->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="judul_arsip" class="form-label">Nama Angkutan / Jadwal</label>
                    <input type="text" class="form-control" id="judul_arsip" name="judul_arsip" value="{{ $arsip->judul_arsip }}" required>
                </div>

                <div class="mb-3">
                    <label for="kategori_id" class="form-label">Rute (sebagai pengganti Kategori)</label>
                    <select class="form-select" name="kategori_id" id="kategori_id" required>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" {{ $k->id == $arsip->kategori_id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                        </foreach>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tanggal_upload" class="form-label">Tanggal Keberangkatan</label>
                    <input type="date" class="form-control" id="tanggal_upload" name="tanggal_upload" value="{{ $arsip->tanggal_upload }}" required>
                </div>

                <div class="mb-3">
                    <label for="file_arsip" class="form-label">Ganti Dokumen Pendukung (Opsional)</label>
                    <input type="file" class="form-control" id="file_arsip" name="file_arsip">
                    <small class="form-text text-muted">Abaikan jika tidak ingin mengubah dokumen.</small>
                </div>
                
                {{-- Tambahkan field dummy untuk simulasi update posisi --}}
                <div class="mb-3">
                    <label for="posisi_realtime" class="form-label">Update Posisi Real-Time (Simulasi)</label>
                    <input type="text" class="form-control" id="posisi_realtime" name="posisi_realtime" value="Masukkan Koordinat GPS atau Status Terbaru" required>
                    <small class="form-text text-danger">Di aplikasi nyata, ini akan terisi otomatis dari GPS Sopir.</small>
                </div>

                <button type="submit" class="btn btn-warning">🔄 Update Jadwal</button>
                @php $role = auth()->user()->role; @endphp
                <a href="{{ route($role . '.arsip.index') }}" class="btn btn-secondary">↩️ Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection