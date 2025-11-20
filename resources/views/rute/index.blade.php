@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <h1 class="h3 mb-3">Rute</h1>
            <div class="table-responsive shadow-sm rounded-3 bg-white">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Jarak</th>
                        <th>Estimasi</th>
                        <th class="text-nowrap">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rute as $item)
                        <tr>
                            <td>{{ $item->nama_rute }}</td>
                            <td>{{ $item->asal }}</td>
                            <td>{{ $item->tujuan }}</td>
                            <td>{{ $item->jarak_km }} km</td>
                            <td>{{ $item->perkiraan_waktu }}</td>
                            <td>
                                <form method="POST" action="{{ route('rute.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus rute?')">
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
                <div class="card-header">Tambah Rute</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rute.store') }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Nama Rute</label>
                            <input type="text" name="nama_rute" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Asal</label>
                            <input type="text" name="asal" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Tujuan</label>
                            <input type="text" name="tujuan" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Jarak (km)</label>
                            <input type="number" step="0.1" name="jarak_km" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perkiraan Waktu (HH:MM)</label>
                            <input type="time" name="perkiraan_waktu" class="form-control">
                        </div>
                        <button class="btn btn-primary w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
