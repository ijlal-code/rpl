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
        <div class="col-sm-10 col-md-6 col-lg-5">
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
    </div>
</div>
@endsection
