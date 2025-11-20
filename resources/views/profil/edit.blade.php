@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Edit Profil</h3>

    <form action="{{ route('profil.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" name="alamat" rows="3" required>{{ old('alamat', auth()->user()->profil->alamat) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="nomor_hp" class="form-label">Nomor HP</label>
            <input type="text" class="form-control" name="nomor_hp" value="{{ old('nomor_hp', auth()->user()->profil->nomor_hp) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('profil.show') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
