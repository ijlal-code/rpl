@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Pesanan</h1>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Penumpang</th>
            <th>Rute</th>
            <th>Kendaraan</th>
            <th>Jadwal</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pesanan as $item)
            <tr>
                <td>{{ $item->penumpang->name ?? '-' }}</td>
                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                <td>{{ $item->kendaraan->nama ?? '-' }}</td>
                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                <td>{{ $item->status }}</td>
                <td>
                    <form method="POST" action="{{ route('pesanan.status', $item) }}" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select form-select-sm">
                            <option value="menunggu">Menunggu</option>
                            <option value="dikonfirmasi">Dikonfirmasi</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                        <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
