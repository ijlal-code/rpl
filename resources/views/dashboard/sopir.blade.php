@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Dashboard Sopir</h1>
    <h5 class="mb-2">Pesanan Masuk</h5>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Penumpang</th>
            <th>Rute</th>
            <th>Waktu</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pesanan as $item)
            <tr>
                <td>{{ $item->penumpang->name ?? '-' }}</td>
                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                <td>{{ $item->status }}</td>
                <td>
                    @if($item->status === 'menunggu')
                        <form method="POST" action="{{ route('sopir.pesanan.konfirmasi', $item) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success">Konfirmasi</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h5 class="mt-4">Kendaraan Saya</h5>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Nama</th>
            <th>Plat</th>
            <th>Status</th>
            <th>Ubah</th>
        </tr>
        </thead>
        <tbody>
        @foreach($kendaraan as $item)
            <tr>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->plat_nomor }}</td>
                <td><span class="badge text-bg-info">{{ $item->status }}</span></td>
                <td>
                    <form method="POST" action="{{ route('sopir.kendaraan.status', $item) }}" class="d-flex gap-2">
                        @csrf
                        <select name="status" class="form-select form-select-sm">
                            <option value="siap">Siap</option>
                            <option value="jalan">Jalan</option>
                            <option value="selesai">Selesai</option>
                        </select>
                        <button class="btn btn-sm btn-primary" type="submit">Update</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
