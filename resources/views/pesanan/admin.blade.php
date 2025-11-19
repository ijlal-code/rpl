@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Semua Pesanan</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Penumpang</th><th>Rute</th><th>Kendaraan</th><th>Jam</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($pesanan as $item)
                    <tr>
                        <td>{{ $item->penumpang->name }}</td>
                        <td>{{ $item->rute->nama_rute }}</td>
                        <td>{{ $item->kendaraan?->nama_kendaraan ?? '-' }}</td>
                        <td>{{ $item->jam_keberangkatan }}</td>
                        <td>{{ ucfirst(str_replace('_',' ', $item->status)) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.pesanan.update', $item) }}" class="d-flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm">
                                    @foreach(['menunggu','dikonfirmasi','dalam_perjalanan','selesai'] as $status)
                                        <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Belum ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
