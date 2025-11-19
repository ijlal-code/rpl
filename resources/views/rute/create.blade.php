@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <h2 class="mb-3">Tambah Rute</h2>
    <form method="POST" action="{{ route('rute.store') }}">
        @csrf
        @include('rute.partials.form', ['rute' => null])
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
