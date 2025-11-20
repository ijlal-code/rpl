@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Dashboard Penumpang</h1>
    <p class="text-muted mb-4">Pantau jadwal angkutan umum Mobil Majene dan pesan sopir yang sedang aktif.</p>

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

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Rekomendasi Jadwal Anda</div>
                <div class="card-body">
                    @if($rekomendasi->isEmpty())
                        <p class="text-muted mb-0">Belum ada histori perjalanan yang bisa dianalisis. Pantau jadwal sopir aktif di bawah untuk melakukan pemesanan.</p>
                    @else
                        <form method="POST" action="{{ route('penumpang.pesan') }}" class="d-flex flex-column gap-3">
                            @csrf
                            <div>
                                <label class="form-label">Pilih jadwal</label>
                                <select name="jadwal_id" class="form-select" required>
                                    @foreach($rekomendasi as $item)
                                        <option value="{{ $item->id }}" @selected(old('jadwal_id') == $item->id)>
                                            {{ $item->rute->nama_rute ?? '-' }} • {{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }} • Sopir {{ $item->sopir->nama ?? $item->sopir->user->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Catatan untuk sopir (opsional)</label>
                                <input type="text" name="catatan" class="form-control" value="{{ old('catatan') }}" placeholder="Contoh: jemput di depan pasar">
                            </div>
                            <button class="btn btn-primary" type="submit">Pesan Jadwal Rekomendasi</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Ringkasan Aktivitas Sopir</div>
                <div class="card-body">
                    @php
                        $ringkasan = $jadwal->groupBy('status')->map->count();
                        $statusLabel = [
                            'aktif' => 'Aktif menerima pesanan',
                            'sedang_jalan' => 'Sedang berjalan',
                            'tidak_aktif' => 'Tidak aktif',
                        ];
                        $badgeClass = [
                            'aktif' => 'success',
                            'sedang_jalan' => 'warning',
                            'tidak_aktif' => 'secondary',
                        ];
                    @endphp
                    <div class="row text-center g-3">
                        @foreach($statusLabel as $status => $label)
                            <div class="col-4">
                                <div class="border rounded p-3 h-100">
                                    <p class="text-uppercase small mb-1">{{ $label }}</p>
                                    <span class="display-6 d-block">{{ $ringkasan[$status] ?? 0 }}</span>
                                    <span class="badge text-bg-{{ $badgeClass[$status] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Semua Jadwal Sopir</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Sopir</th>
                        <th>Rute</th>
                        <th>Waktu Berangkat</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($jadwal as $item)
                        @php
                            $badge = $badgeClass[$item->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>{{ $item->sopir->nama ?? $item->sopir->user->name ?? '-' }}</td>
                            <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                            <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                            <td><span class="badge text-bg-{{ $badge }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                            <td>
                                @if($item->status === 'aktif')
                                    <form method="POST" action="{{ route('penumpang.pesan') }}" class="d-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="jadwal_id" value="{{ $item->id }}">
                                        <input type="text" name="catatan" class="form-control form-control-sm" placeholder="Catatan">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Pesan</button>
                                    </form>
                                @else
                                    <span class="text-muted">Tidak tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Belum ada jadwal sopir yang terdaftar.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @php
        $statusBadgePesanan = [
            'menunggu' => 'warning',
            'dikonfirmasi' => 'primary',
            'selesai' => 'success',
            'dibatalkan' => 'secondary',
        ];
    @endphp

    <div class="card">
        <div class="card-header">Riwayat Pesanan</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Rute</th>
                        <th>Sopir</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($pesanan as $item)
                        <tr>
                            <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                            <td>{{ $item->jadwal->sopir->nama ?? $item->sopir->nama ?? '-' }}</td>
                            <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                            <td><span class="badge text-bg-{{ $statusBadgePesanan[$item->status] ?? 'secondary' }} text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">Belum ada pesanan yang tercatat.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
