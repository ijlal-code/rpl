@extends('layouts.app')

@section('content')
<div class="container mt-5" style="max-width: 520px;">
    <h2 class="mb-4">Daftar Penumpang</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('regis') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            <small class="text-muted">Akun baru otomatis berperan sebagai penumpang.</small>
        </div>
        <button class="btn btn-success">Daftar</button>
    </form>
</div>
@endsection
