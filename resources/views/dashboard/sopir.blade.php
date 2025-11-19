@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Dashboard Sopir</h2>
    @if(!$sopir)
        <div class="alert alert-warning">Akun belum dikaitkan dengan data sopir. Hubungi admin.</div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Pesanan Aktif</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Penumpang</th><th>Rute</th><th>Jam</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($pesanan as $item)
                            <tr>
                                <td>{{ $item->penumpang->name }}</td>
                                <td>{{ $item->rute->nama_rute }}</td>
                                <td>{{ $item->jam_keberangkatan }}</td>
                                <td>
                                    <form method="POST" action="{{ route('sopir.pesanan.update', $item) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach(['menunggu','dikonfirmasi','dalam_perjalanan','selesai'] as $status)
                                                <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">Belum ada pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
