@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-1">Pesanan Masuk</h1>
    <p class="text-muted mb-4">Lihat dan tindak lanjuti pesanan dari penumpang untuk jadwal Anda.</p>

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

    @include('sopir.partials.pesanan')
</div>
@endsection
