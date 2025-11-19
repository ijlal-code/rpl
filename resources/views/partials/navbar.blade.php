<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ auth()->check() ? route('dashboard') : url('/') }}">MandarMove</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('rute.index') }}">Rute</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('kendaraan.index') }}">Kendaraan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.sopir') }}">Sopir</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.pesanan') }}">Pesanan</a></li>
                    @elseif(auth()->user()->role === 'sopir')
                        <li class="nav-item"><a class="nav-link" href="{{ route('sopir.pesanan') }}">Pesanan Saya</a></li>
                    @elseif(auth()->user()->role === 'penumpang')
                        <li class="nav-item"><a class="nav-link" href="{{ route('pesanan.index') }}">Pesanan</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('rekomendasi.index') }}">Rekomendasi</a></li>
                    @endif
                @endauth
            </ul>
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item">
                        <span class="nav-link text-white">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-link nav-link" type="submit">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
