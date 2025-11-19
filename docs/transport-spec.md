# Sistem Pemesanan Transportasi Multi-Role

## Bagian 1 — Penjelasan Arsitektur
- **Arsitektur MVC Laravel**: Model menangani relasi database (`app/Models`), Controller menangani logika HTTP (`app/Http/Controllers`), View memakai Blade (`resources/views`). Middleware role di `app/Middleware` membatasi akses. Routing utama di `routes/web.php`.
- **Struktur folder final (ringkas)**
  - `app/Models`: `User`, `Sopir`, `Kendaraan`, `Rute`, `Pesanan`
  - `app/Http/Controllers`: `AuthController`, `AdminController`, `SopirController`, `UserController`, `PesananController`, `KendaraanController`, `RuteController`, `RekomendasiKNNController`
  - `app/Middleware`: `AdminMiddleware`, `SopirMiddleware`, `UserMiddleware`
  - `database/migrations`: tabel `users`, `sopirs`, `kendaraans`, `rutes`, `pesanans`
  - `resources/views`: login/register, dashboard per-role, CRUD kendaraan/rute/pesanan, rekomendasi KNN
- **Alur login multi-role**: `AuthController@login` memvalidasi kredensial, lalu redirect `dashboard` yang akan memilih view berdasarkan `auth()->user()->role` (admin, sopir, penumpang). Middleware `admin`, `sopir`, `penumpang` memastikan hanya role tepat yang bisa mengakses grup route.
- **Alur pemesanan sesuai DFD**: Penumpang membuat pesanan (`UserController@buatPesanan` ➜ `Pesanan` disimpan). Admin/Sopir memantau (`PesananController`, `SopirController`). Status diperbarui (`updateStatus`, `konfirmasi`). Rekomendasi jadwal menggunakan KNN via `RekomendasiKNNController` sebelum pemesanan.
- **Diagram hubungan berdasarkan ERD (teks)**: `User(1) — (N) Pesanan`; `Pesanan(N) — (1) Rute`; `Pesanan(N) — (1) Kendaraan`; `Kendaraan(N) — (1) Sopir`; `User(1) — (1) Sopir`.

## Bagian 2 — Database (migrations)
Semua kode lengkap ada di file:
- `database/migrations/2025_05_04_152616_create_users.php`
- `database/migrations/2025_05_10_000001_create_sopirs_table.php`
- `database/migrations/2025_05_10_000002_create_kendaraans_table.php`
- `database/migrations/2025_05_10_000003_create_rutes_table.php`
- `database/migrations/2025_05_10_000004_create_pesanans_table.php`

## Bagian 3 — Model
Kode lengkap dengan relasi berada di:
- `app/Models/User.php`
- `app/Models/Sopir.php`
- `app/Models/Kendaraan.php`
- `app/Models/Rute.php`
- `app/Models/Pesanan.php`

## Bagian 4 — Middleware Role
- `app/Middleware/AdminMiddleware.php`
- `app/Middleware/SopirMiddleware.php`
- `app/Middleware/UserMiddleware.php`

## Bagian 5 — Routing (web.php)
Routing dan grup middleware lengkap ada di `routes/web.php` untuk login, dashboard per-role, CRUD kendaraan/rute/pesanan, serta rekomendasi KNN.

## Bagian 6 — Controller
- `AuthController` (login, register, logout)
- `AdminController` (dashboard + laporan)
- `SopirController` (pesanan masuk, konfirmasi, status kendaraan)
- `UserController` (dashboard penumpang, buat pesanan, lihat status)
- `PesananController` (CRUD status/admin)
- `KendaraanController` (CRUD kendaraan)
- `RuteController` (CRUD rute)
- `RekomendasiKNNController` (perhitungan KNN Euclidean)

## Bagian 7 — Blade View
Semua view ada di `resources/views`: `login.blade.php`, `register.blade.php`, `dashboard/admin.blade.php`, `dashboard/sopir.blade.php`, `dashboard/penumpang.blade.php`, `kendaraan/index.blade.php`, `rute/index.blade.php`, `pesanan/index.blade.php`, `rekomendasi/index.blade.php`. Seluruhnya memakai Bootstrap 5 dan menampilkan logika sesuai peran.

## Bagian 8 — Seeder dan Factory
`database/seeders/DatabaseSeeder.php` menyiapkan 5 kendaraan, 3 rute, 3 sopir (beserta akun user). `database/factories/UserFactory.php` menambahkan role & phone default untuk dummy user.

## Bagian 9 — Panduan Instalasi
1. `composer install`
2. Salin `.env` dan atur koneksi DB.
3. `php artisan migrate`
4. `php artisan db:seed`
5. Jalankan server: `php artisan serve`
6. Login: admin (`admin@example.com`/`password`), sopir (`sopir1@example.com` dst), penumpang (`penumpang@example.com`).

## Bagian 10 — Simulasi KNN
Dataset kecil dapat dilihat di `RekomendasiKNNController::buildDataset()` ketika histori kosong. Contoh perhitungan (input: 09:00, rute 2, status selesai, k=3):
1. Vektor input = `[540, 2, 2]`.
2. Hitung jarak Euclidean ke setiap data: `jarak = sqrt((x1-a1)^2 + (x2-a2)^2 + (x3-a3)^2)`.
3. Ambil 3 jarak terdekat, misal mendapatkan label `09:30` paling sering.
4. Rekomendasi jadwal = `09:30`.

Semua kode lengkap tersedia di path di atas tanpa ada yang dipotong.
