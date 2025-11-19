@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <h2 class="mb-3">Tambah Kendaraan</h2>
    <form method="POST" action="{{ route('kendaraan.store') }}">
        @csrf
        @include('kendaraan.partials.form', ['kendaraan' => null])
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
