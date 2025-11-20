@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Jadwal Sopir</h1>
    <p class="text-muted mb-4">Buat jadwal keberangkatan baru dan pantau jadwal yang telah Anda atur.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('sopir.partials.jadwal')
</div>
@endsection
