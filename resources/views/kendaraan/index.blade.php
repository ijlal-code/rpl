@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Kendaraan</h1>
    </div>
    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <div class="table-responsive shadow-sm rounded-3 bg-white">
                <table class="table table-bordered mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Plat</th>
                        <th>Jenis</th>
                        <th>Kapasitas</th>
                        <th>Sopir</th>
                        <th>Status</th>
                        <th class="text-nowrap">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($kendaraan as $item)
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->plat_nomor }}</td>
                            <td>{{ $item->jenis }}</td>
                            <td>{{ $item->kapasitas }}</td>
                            <td>{{ $item->sopir->nama ?? '-' }}</td>
                            <td>{{ $item->status }}</td>
                            <td>
                                <form method="POST" action="{{ route('kendaraan.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus kendaraan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header">Tambah Kendaraan</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kendaraan.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Plat Nomor</label>
                            <input type="text" name="plat_nomor" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Jenis</label>
                            <input type="text" name="jenis" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Kapasitas</label>
                            <input type="number" name="kapasitas" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Sopir</label>
                            <select name="sopir_id" class="form-select">
                                <option value="">Belum ditetapkan</option>
                                @foreach($sopir as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="siap">Siap</option>
                                <option value="jalan">Jalan</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                        <button class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
