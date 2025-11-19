@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <h2 class="mb-3">Edit Kendaraan</h2>
    <form method="POST" action="{{ route('kendaraan.update', $kendaraan) }}">
        @csrf
        @method('PUT')
        @include('kendaraan.partials.form', ['kendaraan' => $kendaraan])
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
