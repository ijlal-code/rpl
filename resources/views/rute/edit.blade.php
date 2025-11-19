@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <h2 class="mb-3">Edit Rute</h2>
    <form method="POST" action="{{ route('rute.update', $rute) }}">
        @csrf
        @method('PUT')
        @include('rute.partials.form', ['rute' => $rute])
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
