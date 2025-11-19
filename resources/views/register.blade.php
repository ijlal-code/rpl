<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Daftar Akun</title>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Buat Akun</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="text-muted small">Pilih peran kamu sebelum membuat akun baru.</p>
                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Daftar sebagai</label>
                            <select name="role" class="form-select" required id="role-select">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih peran</option>
                                <option value="penumpang" {{ old('role') === 'penumpang' ? 'selected' : '' }}>Penumpang</option>
                                <option value="sopir" {{ old('role') === 'sopir' ? 'selected' : '' }}>Sopir</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="tel" name="telepon" class="form-control" value="{{ old('telepon') }}" placeholder="Contoh: 08xx" required>
                            <small class="text-muted">Wajib untuk sopir, opsional untuk penumpang.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3 sopir-field" style="display: none;">
                            <label class="form-label">Nomor SIM</label>
                            <input type="text" name="nomor_sim" class="form-control" value="{{ old('nomor_sim') }}" placeholder="Masukkan nomor SIM sopir">
                        </div>
                        <div class="mb-3 sopir-field" style="display: none;">
                            <label class="form-label">Pengalaman Mengemudi (opsional)</label>
                            <input type="text" name="pengalaman" class="form-control" value="{{ old('pengalaman') }}" placeholder="Contoh: 3 tahun perjalanan antar kota">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button class="btn btn-success w-100" type="submit">Daftar</button>
                    </form>
                    <p class="mt-3 text-center">Sudah punya akun? <a href="{{ route('login') }}">Kembali ke login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const roleSelect = document.getElementById('role-select');
        const sopirFields = document.querySelectorAll('.sopir-field');
        const teleponInput = document.querySelector('input[name="telepon"]');

        const toggleSopirFields = () => {
            const isSopir = roleSelect.value === 'sopir';
            sopirFields.forEach(field => field.style.display = isSopir ? 'block' : 'none');
            teleponInput.required = isSopir;
        };

        roleSelect.addEventListener('change', toggleSopirFields);
        toggleSopirFields();
    });
</script>
</body>
</html>
