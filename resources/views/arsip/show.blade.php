@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow rounded-4">
                <div class="card-header bg-info text-white rounded-top-4">
                    <h4 class="mb-0"><i class="bi bi-bus-front me-2"></i> Detail Jadwal & Posisi</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">
                            <i class="bi bi-tag-fill text-primary me-2"></i>
                            <strong>Nama Angkutan / Jadwal:</strong> {{ $arsip->judul_arsip }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-map-fill text-warning me-2"></i>
                            <strong>Rute:</strong> {{ $arsip->kategori->nama_kategori }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-person-badge-fill text-success me-2"></i>
                            <strong>Sopir / Operator:</strong> {{ $arsip->user->name }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-calendar-event-fill text-danger me-2"></i>
                            <strong>Tanggal Keberangkatan:</strong> {{ \Carbon\Carbon::parse($arsip->tanggal_upload)->format('d M Y') }}
                        </li>
                        
                        {{-- Simulasi Posisi/ETA --}}
                        <li class="list-group-item bg-light border-start border-4 border-success">
                            <i class="bi bi-geo-alt-fill text-success me-2"></i>
                            <strong>POSISI REAL-TIME:</strong> <span class="fw-bold text-danger">Dalam Perjalanan (Posisi Simulasi: 4.5km dari Majene)</span> 
                        </li>
                         <li class="list-group-item bg-light border-start border-4 border-info">
                            <i class="bi bi-clock-history text-info me-2"></i>
                            <strong>ESTIMASI WAKTU TIBA (ETA):</strong> <span class="fw-bold text-info">25 Menit Lagi</span>
                        </li>
                        {{-- End Simulasi --}}
                        
                        <li class="list-group-item">
                            <i class="bi bi-file-earmark-arrow-down-fill text-info me-2"></i>
                            <strong>Dokumen Pendukung (Opsional):</strong>
                            <div class="mt-2 d-flex gap-2 flex-wrap">
                                @php $role = auth()->user()->role; @endphp
                                <a href="{{ route('arsip.view', ['role' => $role, 'id' => $arsip->id]) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-pin-map"></i> Lihat Detail Posisi (Simulasi)
                                </a>
                                <a href="{{ route($role . '.arsip.download', $arsip->id) }}" class="btn btn-warning btn-sm text-white">
                                    <i class="bi bi-download"></i> Unduh Dokumen Pendukung
                                </a>
                            </div>
                        </li>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route($role . '.arsip.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection