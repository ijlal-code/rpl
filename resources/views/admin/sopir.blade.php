@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Kelola Sopir</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row g-4">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Kaitkan User sebagai Sopir</h5>
                    <form method="POST" action="{{ route('admin.sopir.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="" disabled selected>Pilih user</option>
                                @foreach($calon as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Sopir</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengalaman (tahun)</label>
                            <input type="number" name="pengalaman" class="form-control" min="0" value="0">
                        </div>
                        <button class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Daftar Sopir</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead><tr><th>Nama</th><th>Email</th><th>No HP</th><th>Pengalaman</th></tr></thead>
                            <tbody>
                                @forelse($sopir as $item)
                                    <tr>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->user->email }}</td>
                                        <td>{{ $item->no_hp }}</td>
                                        <td>{{ $item->pengalaman }} tahun</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center">Belum ada sopir.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
