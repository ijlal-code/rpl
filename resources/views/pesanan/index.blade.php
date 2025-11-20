@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1>Pesanan</h1>
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

    @php $role = auth()->user()->role ?? null; @endphp

    <table class="table table-hover">
        <thead>
        <tr>
            <th>Penumpang</th>
            <th>Sopir</th>
            <th>Rute</th>
            <th>Jadwal</th>
            <th>Status</th>
            <th>Alasan Pembatalan</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($pesanan as $item)
            <tr>
                <td>{{ $item->penumpang->name ?? '-' }}</td>
                <td>{{ $item->sopir->nama ?? $item->jadwal->sopir->nama ?? '-' }}</td>
                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                <td>{{ ucfirst($item->status) }}</td>
                <td>{{ $item->alasan_pembatalan ?? '-' }}</td>
                <td>
                    @if($role === 'penumpang')
                        @if(in_array($item->status, ['dibatalkan', 'selesai']))
                            <span class="text-muted">Tidak ada aksi</span>
                        @else
                            <form method="POST" action="{{ route('penumpang.pesanan.batalkan', $item) }}" class="d-flex flex-column gap-2">
                                @csrf
                                <select name="alasan" class="form-select form-select-sm alasan-select" data-target="#alasan-lain-{{ $item->id }}">
                                    <option value="perubahan_rencana">Perubahan rencana perjalanan</option>
                                    <option value="menemukan_transportasi_lain">Menemukan transportasi lain</option>
                                    <option value="kesalahan_pemesanan">Kesalahan pemesanan</option>
                                    <option value="lainnya">Alasan lainnya</option>
                                </select>
                                <input type="text" name="alasan_lain" id="alasan-lain-{{ $item->id }}" class="form-control form-control-sm alasan-lain" placeholder="Tuliskan alasan lain" style="display: none;">
                                <button class="btn btn-sm btn-danger" type="submit">Batalkan Pesanan</button>
                            </form>
                        @endif
                    @elseif($role === 'admin')
                        <form method="POST" action="{{ route('pesanan.status', $item) }}" class="d-flex gap-2">
                            @csrf
                            <select name="status" class="form-select form-select-sm">
                                <option value="menunggu" @selected($item->status === 'menunggu')>Menunggu</option>
                                <option value="dikonfirmasi" @selected($item->status === 'dikonfirmasi')>Dikonfirmasi</option>
                                <option value="selesai" @selected($item->status === 'selesai')>Selesai</option>
                                <option value="dibatalkan" @selected($item->status === 'dibatalkan')>Dibatalkan</option>
                            </select>
                            <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                        </form>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.alasan-select').forEach(select => {
            const target = document.querySelector(select.dataset.target);

            const toggleInput = () => {
                if (!target) return;
                target.style.display = select.value === 'lainnya' ? 'block' : 'none';
            };

            select.addEventListener('change', toggleInput);
            toggleInput();
        });
    });
</script>
@endsection
