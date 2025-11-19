<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Transportasi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('kendaraan.index') }}">Kendaraan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('rute.index') }}">Rute</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('pesanan.index') }}">Pesanan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.laporan') }}">Laporan</a></li>
                    @elseif(auth()->user()->role === 'sopir')
                        <li class="nav-item"><a class="nav-link" href="{{ route('sopir.dashboard') }}">Dashboard</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('penumpang.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('rekomendasi.index') }}">Rekomendasi</a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item"><span class="nav-link text-white">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span></li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-link nav-link" type="submit">Logout</button>
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
