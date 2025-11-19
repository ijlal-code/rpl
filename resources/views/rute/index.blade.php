@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Rute</h2>
        <a class="btn btn-primary" href="{{ route('rute.create') }}">Tambah Rute</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Nama</th><th>Berangkat</th><th>Tujuan</th><th>Estimasi (menit)</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($rute as $item)
                    <tr>
                        <td>{{ $item->nama_rute }}</td>
                        <td>{{ $item->titik_berangkat }}</td>
                        <td>{{ $item->titik_tujuan }}</td>
                        <td>{{ $item->estimasi_waktu }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('rute.edit', $item) }}">Edit</a>
                            <form action="{{ route('rute.destroy', $item) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus rute?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">Belum ada rute.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
