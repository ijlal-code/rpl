@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Dashboard Admin</h2>
        <p class="lead">Selamat datang, <span class="text-primary fw-semibold">{{ auth()->user()->name }}</span></p>
        <div class="mx-auto" style="width: 100px;">
            <hr class="border border-primary border-2 opacity-75">
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Card: Kelola Pengguna -->
        <div class="col-md-5">
            <div class="card h-100 border-0 shadow rounded-4">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                    </div>
                    <h5 class="card-title fw-bold">Kelola Data</h5>
                    <p class="card-text text-muted">Tambah, ubah, atau hapus akun pengguna sistem dengan mudah dan cepat.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-right-circle"></i> Masuk
                    </a>
                </div>
            </div>
        </div>

        <!-- Card: Lihat Arsip
        <div class="col-md-5">
            <div class="card h-100 border-0 shadow rounded-4">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-folder-fill fs-1 text-warning"></i>
                    </div>
                    <h5 class="card-title fw-bold">Lihat Arsip</h5>
                    <p class="card-text text-muted">Akses dan kelola seluruh arsip desa dengan antarmuka yang nyaman.</p>
                    <a href="{{ route('admin.arsip.index') }}" class="btn btn-warning text-white">
                        <i class="bi bi-folder2-open"></i> Lihat Arsip
                    </a>
                </div>
            </div>
        </div> -->
    </div>
</div>
@endsection
