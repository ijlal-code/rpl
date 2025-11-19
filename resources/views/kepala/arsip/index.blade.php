@extends('layouts.app')

@section('content')
<div class="py-5" style="min-height: 100vh; background: linear-gradient(135deg, #1f4037, #99f2c8);">
    <div class="container">
        {{-- Alert Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Header & Tombol Kembali --}}
        <div class="d-flex justify-content-between align-items-center mb-4 text-white">
            <div>
                <h2 class="fw-bold">🚍 Jadwal & Posisi Angkutan</h2>
                <p class="mb-0">Cari dan pantau jadwal angkutan secara real-time</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light fw-bold">
                <i class="bi bi-arrow-left-circle"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Form Pencarian --}}
        <form action="{{ route('kepala.arsip.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama angkutan/jadwal...">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary fw-semibold">Cari</button>
                <a href="{{ route('kepala.arsip.index') }}" class="btn btn-secondary fw-semibold">Reset</a>
            </div>
        </form>

        {{-- Tabel Arsip --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered bg-white shadow-sm rounded overflow-hidden">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Angkutan</th>
                        <th>Rute</th>
                        <th>Sopir</th>
                        <th>Tgl Keberangkatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($arsip as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->judul_arsip }}</td>
                            <td>{{ $item->kategori->nama_kategori ?? 'Rute Tidak Ada' }}</td>
                            <td>{{ $item->user->name ?? 'Sopir Tidak Dikenal' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_upload)->format('d-m-Y') }}</td>
                            <td class="text-center">
                            @php $role = auth()->user()->role; @endphp
<a href="{{ route('arsip.view', ['role' => $role, 'id' => $item->id]) }}" 
   target="_blank" 
   class="btn btn-sm btn-info mb-1">
    <i class="bi bi-pin-map-fill"></i> Lihat Posisi Realtime
</a>

                                @if (auth()->user()->role === 'kepala')
                                @php $role = auth()->user()->role; @endphp
<a href="{{ route($role . '.arsip.download', $item->id) }}" class="btn btn-sm btn-success mb-1">
    <i class="bi bi-clock-history"></i> Lihat Estimasi Waktu (ETA)
</a>
                                @endif

                                @if (auth()->user()->role === 'sekretaris' || auth()->user()->role === 'admin')
                                    <a href="{{ route('arsip.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <form action="{{ route('arsip.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus arsip ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger mb-1">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada Jadwal Angkutan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection