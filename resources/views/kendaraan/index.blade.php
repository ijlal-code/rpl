@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Kendaraan</h2>
        <a class="btn btn-primary" href="{{ route('kendaraan.create') }}">Tambah Kendaraan</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Nama</th><th>Sopir</th><th>Rute</th><th>Kapasitas</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($kendaraan as $item)
                    <tr>
                        <td>{{ $item->nama_kendaraan }}</td>
                        <td>{{ $item->sopir?->nama }}</td>
                        <td>{{ $item->rute->nama_rute }}</td>
                        <td>{{ $item->kapasitas }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('kendaraan.edit', $item) }}">Edit</a>
                            <form action="{{ route('kendaraan.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kendaraan?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Belum ada kendaraan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
