# Blueprint Aplikasi "Perancangan dan Pengembangan Website Angkutan Umum" (Laravel)

Dokumen ini menyajikan penjelasan arsitektur, rancangan database, potongan kode lengkap (migration, model, middleware, routing, controller, Blade view, seeder/factory), serta simulasi algoritma KNN sesuai laporan "Perancangan dan Pengembangan Website Angkutan Umum Menggunakan Metode Prototyping dan Algoritma KNN". Semua contoh ditulis agar siap _copy–paste_ ke proyek Laravel 10.x dan menggunakan Bootstrap 5.

## Bagian 1 — Penjelasan Arsitektur

### Arsitektur MVC Laravel
- **Model**: Membungkus tabel `users`, `sopirs`, `kendaraans`, `rutes`, `pesanans` dengan relasi Eloquent untuk mengurangi _query_ manual.
- **View**: Blade template Bootstrap 5 untuk login/registrasi, dashboard per‐role, manajemen kendaraan/rute/pesanan, serta halaman rekomendasi KNN.
- **Controller**: Memisahkan logika autentikasi, CRUD admin, alur pesanan, dan modul rekomendasi KNN.
- **Middleware**: `AdminMiddleware`, `SopirMiddleware`, `UserMiddleware` memaksa user masuk sesuai peran.
- **Route**: Grup prefix dan middleware untuk memetakan alur DFD: registrasi/login → dashboard → CRUD/pemesanan → KNN.

### Struktur Folder Final (ringkas)
```
app/
  Http/
    Controllers/ (AuthController, AdminController, SopirController, UserController,
                  PesananController, KendaraanController, RuteController, RekomendasiKNNController)
    Middleware/ (AdminMiddleware.php, SopirMiddleware.php, UserMiddleware.php)
  Models/ (User.php, Sopir.php, Kendaraan.php, Rute.php, Pesanan.php)
database/
  migrations/ (create_users_table.php, create_sopirs_table.php, create_kendaraans_table.php,
               create_rutes_table.php, create_pesanans_table.php)
  seeders/ (DatabaseSeeder.php, SopirSeeder.php, KendaraanSeeder.php, RuteSeeder.php)
  factories/ (SopirFactory.php, KendaraanFactory.php, RuteFactory.php)
resources/views/
  auth/login.blade.php
  auth/register.blade.php
  dashboard/{admin,sopir,penumpang}.blade.php
  kendaraan/index.blade.php
  rute/index.blade.php
  pesanan/index.blade.php
  rekomendasi/index.blade.php
routes/web.php
```

### Alur Login Multi‐Role
1. User registrasi memilih `role` (`admin`, `sopir`, `penumpang`).
2. Setelah login, middleware memeriksa `auth()->user()->role`:
   - `admin` → `/dashboard/admin`
   - `sopir` → `/dashboard/sopir`
   - `penumpang` → `/dashboard/penumpang`
3. Route grup terproteksi memastikan setiap role hanya mengakses menu relevan.

### Alur Pemesanan (mengikuti DFD)
1. Penumpang membuka daftar kendaraan/rute aktif.
2. Mengisi form pemesanan (pilih rute, kendaraan, jadwal berangkat).
3. Data tersimpan di `pesanans` dengan status `menunggu`.
4. Sopir melihat pesanan masuk, mengonfirmasi atau menolak, serta memperbarui status kendaraan (`siap`, `jalan`, `selesai`).
5. Admin memonitor laporan pesanan dan performa kendaraan.

### Diagram Hubungan (ERD teks)
- **User** (id, name, email, password, role) 1..* —< Pesanan (penumpang)
- **Sopir** (id, user_id, nama, telepon, alamat, status) 1 —< Kendaraan
- **Kendaraan** (id, sopir_id, nama_kendaraan, plat, kapasitas, status) 1 —< Pesanan
- **Rute** (id, nama_rute, asal, tujuan, jarak_km, estimasi_waktu) 1 —< Pesanan
- **Pesanan** (id, user_id, sopir_id, kendaraan_id, rute_id, jadwal, status, catatan)

## Bagian 2 — Migration
Salin setiap file ke `database/migrations/` sesuai timestamp Laravel.

### 1. `create_users_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'sopir', 'penumpang'])->default('penumpang');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### 2. `create_sopirs_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sopirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama');
            $table->string('telepon');
            $table->string('alamat');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sopirs');
    }
};
```

### 3. `create_kendaraans_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sopir_id')->constrained('sopirs')->cascadeOnDelete();
            $table->string('nama_kendaraan');
            $table->string('plat');
            $table->integer('kapasitas');
            $table->enum('status', ['siap', 'jalan', 'selesai'])->default('siap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
```

### 4. `create_rutes_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rutes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rute');
            $table->string('asal');
            $table->string('tujuan');
            $table->double('jarak_km');
            $table->integer('estimasi_waktu'); // menit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutes');
    }
};
```

### 5. `create_pesanans_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sopir_id')->nullable()->constrained('sopirs')->nullOnDelete();
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraans')->nullOnDelete();
            $table->foreignId('rute_id')->constrained('rutes')->cascadeOnDelete();
            $table->dateTime('jadwal');
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'ditolak', 'selesai'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
```

## Bagian 3 — Model & Relasi
### `app/Models/User.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function sopir()
    {
        return $this->hasOne(Sopir::class);
    }
}
```

### `app/Models/Sopir.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sopir extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama', 'telepon', 'alamat', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
```

### `app/Models/Kendaraan.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sopir_id', 'nama_kendaraan', 'plat', 'kapasitas', 'status',
    ];

    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
```

### `app/Models/Rute.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_rute', 'asal', 'tujuan', 'jarak_km', 'estimasi_waktu',
    ];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
```

### `app/Models/Pesanan.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'sopir_id', 'kendaraan_id', 'rute_id', 'jadwal', 'status', 'catatan',
    ];

    protected $casts = [
        'jadwal' => 'datetime',
    ];

    public function penumpang()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }
}
```

## Bagian 4 — Middleware Role
Tempatkan di `app/Http/Middleware/` dan daftarkan di `app/Http/Kernel.php`.

### `AdminMiddleware.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Akses admin diperlukan.');
    }
}
```

### `SopirMiddleware.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SopirMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'sopir') {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Akses sopir diperlukan.');
    }
}
```

### `UserMiddleware.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'penumpang') {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Akses penumpang diperlukan.');
    }
}
```

## Bagian 5 — Routing (`routes/web.php`)
```php
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RekomendasiKNNController;
use App\Http\Controllers\RuteController;
use App\Http\Controllers\SopirController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('dashboard.admin');
    Route::resource('/kendaraan', KendaraanController::class)->except(['show']);
    Route::resource('/rute', RuteController::class)->except(['show']);
    Route::resource('/sopir', SopirController::class)->except(['show']);
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('admin.laporan');
});

Route::middleware(['auth', 'sopir'])->group(function () {
    Route::get('/dashboard/sopir', [SopirController::class, 'dashboard'])->name('dashboard.sopir');
    Route::get('/pesanan/masuk', [SopirController::class, 'pesananMasuk'])->name('sopir.pesanan');
    Route::post('/pesanan/{pesanan}/konfirmasi', [SopirController::class, 'konfirmasi'])->name('sopir.konfirmasi');
    Route::post('/kendaraan/{kendaraan}/status', [SopirController::class, 'ubahStatusKendaraan'])->name('sopir.kendaraan.status');
});

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/dashboard/penumpang', [UserController::class, 'dashboard'])->name('dashboard.penumpang');
    Route::get('/kendaraan/aktif', [UserController::class, 'kendaraanAktif'])->name('penumpang.kendaraan');
    Route::get('/rute', [UserController::class, 'rute'])->name('penumpang.rute');
    Route::resource('/pesanan', PesananController::class)->only(['index', 'create', 'store']);
    Route::get('/rekomendasi', [RekomendasiKNNController::class, 'index'])->name('rekomendasi.index');
    Route::post('/rekomendasi/hitung', [RekomendasiKNNController::class, 'hitung'])->name('rekomendasi.hitung');
});
```

## Bagian 6 — Controller Lengkap

> Catatan: Pada contoh ini validasi dibuat sederhana. Sesuaikan kebutuhan produksi.

### `AuthController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        return back()->withErrors(['email' => 'Kredensial salah.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,sopir,penumpang',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        Auth::login($user);
        return $this->redirectByRole($user->role);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->route('dashboard.admin'),
            'sopir' => redirect()->route('dashboard.sopir'),
            default => redirect()->route('dashboard.penumpang'),
        };
    }
}
```

### `AdminController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Sopir;

class AdminController extends Controller
{
    public function index()
    {
        return view('dashboard.admin', [
            'totalPesanan' => Pesanan::count(),
            'totalKendaraan' => Kendaraan::count(),
            'totalSopir' => Sopir::count(),
            'totalRute' => Rute::count(),
        ]);
    }

    public function laporan()
    {
        return view('pesanan.index', [
            'pesanans' => Pesanan::with(['penumpang', 'sopir', 'kendaraan', 'rute'])->latest()->get(),
        ]);
    }
}
```

### `SopirController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class SopirController extends Controller
{
    public function dashboard()
    {
        $sopirId = auth()->user()->sopir?->id;
        return view('dashboard.sopir', [
            'pesananCount' => Pesanan::where('sopir_id', $sopirId)->count(),
            'kendaraan' => Kendaraan::where('sopir_id', $sopirId)->get(),
        ]);
    }

    public function pesananMasuk()
    {
        $sopirId = auth()->user()->sopir?->id;
        $pesanan = Pesanan::whereNull('sopir_id')->orWhere('sopir_id', $sopirId)
            ->with(['penumpang', 'rute', 'kendaraan'])
            ->orderByDesc('created_at')
            ->get();
        return view('pesanan.index', ['pesanans' => $pesanan]);
    }

    public function konfirmasi(Pesanan $pesanan, Request $request)
    {
        $data = $request->validate([
            'status' => 'required|in:dikonfirmasi,ditolak,selesai',
            'kendaraan_id' => 'nullable|exists:kendaraans,id',
        ]);

        $pesanan->update([
            'status' => $data['status'],
            'sopir_id' => auth()->user()->sopir->id,
            'kendaraan_id' => $data['kendaraan_id'],
        ]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function ubahStatusKendaraan(Kendaraan $kendaraan, Request $request)
    {
        $request->validate(['status' => 'required|in:siap,jalan,selesai']);
        $kendaraan->update(['status' => $request->status]);
        return back()->with('success', 'Status kendaraan diubah.');
    }
}
```

### `UserController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Rute;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('dashboard.penumpang', [
            'kendaraanAktif' => Kendaraan::where('status', 'siap')->count(),
            'totalRute' => Rute::count(),
        ]);
    }

    public function kendaraanAktif()
    {
        $kendaraan = Kendaraan::with('sopir')->where('status', 'siap')->get();
        return view('kendaraan.index', ['kendaraan' => $kendaraan]);
    }

    public function rute()
    {
        return view('rute.index', ['rutes' => Rute::all()]);
    }
}
```

### `PesananController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::with(['rute', 'kendaraan', 'sopir'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();
        return view('pesanan.index', ['pesanans' => $pesanan]);
    }

    public function create()
    {
        return view('pesanan.create', [
            'rutes' => Rute::all(),
            'kendaraan' => Kendaraan::where('status', 'siap')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rute_id' => 'required|exists:rutes,id',
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'jadwal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        Pesanan::create([
            'user_id' => auth()->id(),
            'rute_id' => $data['rute_id'],
            'kendaraan_id' => $data['kendaraan_id'],
            'jadwal' => $data['jadwal'],
            'status' => 'menunggu',
            'catatan' => $data['catatan'] ?? null,
        ]);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan dikirim.');
    }
}
```

### `KendaraanController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Sopir;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index()
    {
        return view('kendaraan.index', [
            'kendaraan' => Kendaraan::with('sopir')->get(),
            'sopirs' => Sopir::all(),
        ]);
    }

    public function create() { /* opsional jika ingin halaman khusus */ }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sopir_id' => 'required|exists:sopirs,id',
            'nama_kendaraan' => 'required|string',
            'plat' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:siap,jalan,selesai',
        ]);
        Kendaraan::create($data);
        return back()->with('success', 'Kendaraan ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan)
    {
        return view('kendaraan.edit', ['kendaraan' => $kendaraan, 'sopirs' => Sopir::all()]);
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $data = $request->validate([
            'sopir_id' => 'required|exists:sopirs,id',
            'nama_kendaraan' => 'required|string',
            'plat' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:siap,jalan,selesai',
        ]);
        $kendaraan->update($data);
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();
        return back()->with('success', 'Kendaraan dihapus.');
    }
}
```

### `RuteController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    public function index()
    {
        return view('rute.index', ['rutes' => Rute::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string',
            'asal' => 'required|string',
            'tujuan' => 'required|string',
            'jarak_km' => 'required|numeric',
            'estimasi_waktu' => 'required|integer',
        ]);
        Rute::create($data);
        return back()->with('success', 'Rute ditambahkan.');
    }

    public function edit(Rute $rute)
    {
        return view('rute.edit', ['rute' => $rute]);
    }

    public function update(Request $request, Rute $rute)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string',
            'asal' => 'required|string',
            'tujuan' => 'required|string',
            'jarak_km' => 'required|numeric',
            'estimasi_waktu' => 'required|integer',
        ]);
        $rute->update($data);
        return redirect()->route('rute.index')->with('success', 'Rute diperbarui.');
    }

    public function destroy(Rute $rute)
    {
        $rute->delete();
        return back()->with('success', 'Rute dihapus.');
    }
}
```

### `RekomendasiKNNController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Rute;
use Illuminate\Http\Request;

class RekomendasiKNNController extends Controller
{
    /**
     * Dataset sederhana: setiap baris = [jam_keberangkatan (menit 0-1440), rute_id, status_kendaraan]
     * status_kendaraan: 0=siap, 1=jalan, 2=selesai
     */
    private array $dataset = [
        ['jadwal' => 480, 'rute_id' => 1, 'status' => 0, 'label' => '08:00'],
        ['jadwal' => 540, 'rute_id' => 1, 'status' => 1, 'label' => '09:00'],
        ['jadwal' => 600, 'rute_id' => 2, 'status' => 0, 'label' => '10:00'],
        ['jadwal' => 660, 'rute_id' => 2, 'status' => 1, 'label' => '11:00'],
        ['jadwal' => 720, 'rute_id' => 3, 'status' => 0, 'label' => '12:00'],
    ];

    public function index()
    {
        return view('rekomendasi.index', [
            'rutes' => Rute::all(),
            'kendaraanSiap' => Kendaraan::where('status', 'siap')->get(),
            'hasil' => null,
        ]);
    }

    public function hitung(Request $request)
    {
        $data = $request->validate([
            'jadwal' => 'required|date_format:H:i',
            'rute_id' => 'required|integer',
            'status' => 'required|in:0,1,2',
            'k' => 'required|integer|min:1',
        ]);

        $jadwalMenit = $this->jamKeMenit($data['jadwal']);
        $jarak = [];

        foreach ($this->dataset as $baris) {
            $d = $this->euclidean([
                $jadwalMenit,
                (int) $data['rute_id'],
                (int) $data['status'],
            ], [
                $baris['jadwal'],
                (int) $baris['rute_id'],
                (int) $baris['status'],
            ]);
            $jarak[] = [
                'label' => $baris['label'],
                'distance' => $d,
            ];
        }

        usort($jarak, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $k = min($data['k'], count($jarak));
        $tetangga = array_slice($jarak, 0, $k);

        $freq = [];
        foreach ($tetangga as $t) {
            $freq[$t['label']] = ($freq[$t['label']] ?? 0) + 1;
        }
        arsort($freq);
        $rekomendasi = array_key_first($freq);

        return view('rekomendasi.index', [
            'rutes' => Rute::all(),
            'kendaraanSiap' => Kendaraan::where('status', 'siap')->get(),
            'hasil' => [
                'input' => $data,
                'tetangga' => $tetangga,
                'rekomendasi' => $rekomendasi,
            ],
        ]);
    }

    private function euclidean(array $a, array $b): float
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }

    private function jamKeMenit(string $jam): int
    {
        [$h, $m] = explode(':', $jam);
        return ((int)$h * 60) + (int)$m;
    }
}
```

## Bagian 7 — Blade View (Bootstrap 5)
Semua view disederhanakan namun lengkap; sesuaikan `@extends` jika memakai layout.

### `resources/views/auth/login.blade.php`
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Login</h4>
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif
                    <form method="POST" action="/login">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">Masuk</button>
                    </form>
                    <p class="text-center mt-3">Belum punya akun? <a href="/register">Register</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
```

### `resources/views/auth/register.blade.php`
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register</title>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="mb-4 text-center">Registrasi</h4>
                    <form method="POST" action="/register">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="penumpang">Penumpang</option>
                                <option value="sopir">Sopir</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button class="btn btn-success w-100">Daftar</button>
                    </form>
                    <p class="text-center mt-3">Sudah punya akun? <a href="/login">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
```

### `resources/views/dashboard/admin.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Dashboard Admin</h3>
    <div class="row g-3 mt-2">
        <div class="col-md-3"><div class="card"><div class="card-body">Total Pesanan: {{ $totalPesanan }}</div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">Total Kendaraan: {{ $totalKendaraan }}</div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">Total Sopir: {{ $totalSopir }}</div></div></div>
        <div class="col-md-3"><div class="card"><div class="card-body">Total Rute: {{ $totalRute }}</div></div></div>
    </div>
    <div class="mt-4">
        <a href="/kendaraan" class="btn btn-primary">Kelola Kendaraan</a>
        <a href="/rute" class="btn btn-secondary">Kelola Rute</a>
        <a href="/sopir" class="btn btn-warning">Kelola Sopir</a>
        <a href="/laporan" class="btn btn-success">Laporan Pesanan</a>
    </div>
</div>
@endsection
```

### `resources/views/dashboard/sopir.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Dashboard Sopir</h3>
    <p>Total pesanan Anda: {{ $pesananCount }}</p>
    <h5>Kendaraan yang dikelola</h5>
    <ul class="list-group">
        @forelse($kendaraan as $k)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $k->nama_kendaraan }} ({{ $k->plat }})
                <span class="badge bg-info">{{ $k->status }}</span>
            </li>
        @empty
            <li class="list-group-item">Belum ada kendaraan.</li>
        @endforelse
    </ul>
</div>
@endsection
```

### `resources/views/dashboard/penumpang.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Dashboard Penumpang</h3>
    <div class="alert alert-info">Kendaraan siap: {{ $kendaraanAktif }}, Total rute: {{ $totalRute }}</div>
    <a href="/pesanan/create" class="btn btn-primary">Buat Pesanan</a>
    <a href="/rekomendasi" class="btn btn-success">Rekomendasi Jadwal KNN</a>
</div>
@endsection
```

### `resources/views/kendaraan/index.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Data Kendaraan</h3>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <form class="row g-2" method="POST" action="{{ route('kendaraan.store') }}">
        @csrf
        <div class="col-md-2"><input class="form-control" name="nama_kendaraan" placeholder="Nama" required></div>
        <div class="col-md-2"><input class="form-control" name="plat" placeholder="Plat" required></div>
        <div class="col-md-2"><input class="form-control" type="number" name="kapasitas" placeholder="Kapasitas" required></div>
        <div class="col-md-2">
            <select class="form-select" name="sopir_id">
                @foreach($sopirs as $s)
                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" name="status">
                <option value="siap">Siap</option>
                <option value="jalan">Jalan</option>
                <option value="selesai">Selesai</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Tambah</button></div>
    </form>
    <table class="table mt-3 table-bordered">
        <thead><tr><th>Nama</th><th>Plat</th><th>Kapasitas</th><th>Sopir</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($kendaraan as $k)
            <tr>
                <td>{{ $k->nama_kendaraan }}</td>
                <td>{{ $k->plat }}</td>
                <td>{{ $k->kapasitas }}</td>
                <td>{{ $k->sopir->nama ?? '-' }}</td>
                <td>{{ $k->status }}</td>
                <td>
                    <form method="POST" action="{{ route('kendaraan.destroy', $k) }}" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Belum ada data</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
```

### `resources/views/rute/index.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Data Rute</h3>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <form class="row g-2" method="POST" action="{{ route('rute.store') }}">
        @csrf
        <div class="col-md-3"><input class="form-control" name="nama_rute" placeholder="Nama" required></div>
        <div class="col-md-2"><input class="form-control" name="asal" placeholder="Asal" required></div>
        <div class="col-md-2"><input class="form-control" name="tujuan" placeholder="Tujuan" required></div>
        <div class="col-md-2"><input class="form-control" type="number" step="0.1" name="jarak_km" placeholder="Jarak" required></div>
        <div class="col-md-2"><input class="form-control" type="number" name="estimasi_waktu" placeholder="Menit" required></div>
        <div class="col-md-1"><button class="btn btn-primary w-100">Tambah</button></div>
    </form>
    <table class="table mt-3 table-bordered">
        <thead><tr><th>Nama</th><th>Asal</th><th>Tujuan</th><th>Jarak</th><th>Estimasi</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($rutes as $r)
            <tr>
                <td>{{ $r->nama_rute }}</td>
                <td>{{ $r->asal }}</td>
                <td>{{ $r->tujuan }}</td>
                <td>{{ $r->jarak_km }} km</td>
                <td>{{ $r->estimasi_waktu }} menit</td>
                <td>
                    <form method="POST" action="{{ route('rute.destroy', $r) }}" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Belum ada data</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
```

### `resources/views/pesanan/index.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Daftar Pesanan</h3>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <table class="table table-bordered">
        <thead><tr><th>Rute</th><th>Kendaraan</th><th>Jadwal</th><th>Status</th><th>Catatan</th></tr></thead>
        <tbody>
        @forelse($pesanans as $p)
            <tr>
                <td>{{ $p->rute->nama_rute ?? '-' }}</td>
                <td>{{ $p->kendaraan->nama_kendaraan ?? '-' }}</td>
                <td>{{ $p->jadwal->format('d-m-Y H:i') }}</td>
                <td>{{ $p->status }}</td>
                <td>{{ $p->catatan }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Belum ada pesanan.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
```

### `resources/views/pesanan/create.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Buat Pesanan</h3>
    <form method="POST" action="{{ route('pesanan.store') }}" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Rute</label>
            <select name="rute_id" class="form-select" required>
                @foreach($rutes as $r)
                    <option value="{{ $r->id }}">{{ $r->nama_rute }} ({{ $r->asal }}-{{ $r->tujuan }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Kendaraan</label>
            <select name="kendaraan_id" class="form-select" required>
                @foreach($kendaraan as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kendaraan }} - {{ $k->plat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Jadwal</label>
            <input type="datetime-local" name="jadwal" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea name="catatan" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Kirim Pesanan</button>
        </div>
    </form>
</div>
@endsection
```

### `resources/views/rekomendasi/index.blade.php`
```blade
@extends('layout')
@section('content')
<div class="container py-4">
    <h3>Rekomendasi Jadwal (KNN)</h3>
    <form method="POST" action="{{ route('rekomendasi.hitung') }}" class="row g-3 mb-4">
        @csrf
        <div class="col-md-3">
            <label class="form-label">Jam Keberangkatan</label>
            <input type="time" name="jadwal" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Rute</label>
            <select name="rute_id" class="form-select" required>
                @foreach($rutes as $r)
                    <option value="{{ $r->id }}">{{ $r->nama_rute }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Status Kendaraan</label>
            <select name="status" class="form-select" required>
                <option value="0">Siap</option>
                <option value="1">Jalan</option>
                <option value="2">Selesai</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Nilai K</label>
            <input type="number" name="k" class="form-control" value="3" min="1" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button class="btn btn-success w-100">Hitung</button>
        </div>
    </form>

    @if($hasil)
        <div class="alert alert-info">Rekomendasi jadwal terbaik: <strong>{{ $hasil['rekomendasi'] }}</strong></div>
        <h5>Detail Tetangga Terdekat:</h5>
        <ul class="list-group">
            @foreach($hasil['tetangga'] as $t)
                <li class="list-group-item d-flex justify-content-between">
                    Label: {{ $t['label'] }} <span>Jarak: {{ number_format($t['distance'], 2) }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
```

## Bagian 8 — Seeder dan Factory

### Factory Contoh
`database/factories/SopirFactory.php`
```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SopirFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama' => $this->faker->name(),
            'telepon' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'status' => 'aktif',
        ];
    }
}
```

`database/factories/KendaraanFactory.php`
```php
<?php

namespace Database\Factories;

use App\Models\Sopir;
use Illuminate\Database\Eloquent\Factories\Factory;

class KendaraanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sopir_id' => Sopir::factory(),
            'nama_kendaraan' => $this->faker->word().' Bus',
            'plat' => strtoupper($this->faker->bothify('##-####')),
            'kapasitas' => $this->faker->numberBetween(10, 40),
            'status' => 'siap',
        ];
    }
}
```

`database/factories/RuteFactory.php`
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RuteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_rute' => 'Rute '.$this->faker->unique()->city(),
            'asal' => $this->faker->city(),
            'tujuan' => $this->faker->city(),
            'jarak_km' => $this->faker->numberBetween(5, 50),
            'estimasi_waktu' => $this->faker->numberBetween(20, 180),
        ];
    }
}
```

### Seeder Contoh
`database/seeders/DatabaseSeeder.php`
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SopirSeeder::class,
            KendaraanSeeder::class,
            RuteSeeder::class,
        ]);
    }
}
```

`database/seeders/SopirSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\Sopir;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SopirSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $user = User::create([
                'name' => 'Sopir '.$i,
                'email' => 'sopir'.$i.'@mail.com',
                'password' => Hash::make('password'),
                'role' => 'sopir',
            ]);

            Sopir::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'telepon' => '08123'.$i.'000',
                'alamat' => 'Alamat Sopir '.$i,
                'status' => 'aktif',
            ]);
        }
    }
}
```

`database/seeders/KendaraanSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Sopir;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $sopirs = Sopir::all();
        $data = [
            ['nama_kendaraan' => 'Bus Kota', 'plat' => 'DD-1234-AA', 'kapasitas' => 30],
            ['nama_kendaraan' => 'Minibus', 'plat' => 'DD-2345-BB', 'kapasitas' => 18],
            ['nama_kendaraan' => 'Elf', 'plat' => 'DD-3456-CC', 'kapasitas' => 14],
            ['nama_kendaraan' => 'Hiace', 'plat' => 'DD-4567-DD', 'kapasitas' => 12],
            ['nama_kendaraan' => 'Sedan', 'plat' => 'DD-5678-EE', 'kapasitas' => 4],
        ];

        foreach ($data as $index => $d) {
            Kendaraan::create([
                'sopir_id' => $sopirs[$index % $sopirs->count()]->id,
                'nama_kendaraan' => $d['nama_kendaraan'],
                'plat' => $d['plat'],
                'kapasitas' => $d['kapasitas'],
                'status' => 'siap',
            ]);
        }
    }
}
```

`database/seeders/RuteSeeder.php`
```php
<?php

namespace Database\Seeders;

use App\Models\Rute;
use Illuminate\Database\Seeder;

class RuteSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_rute' => 'Kota A - Kota B', 'asal' => 'Kota A', 'tujuan' => 'Kota B', 'jarak_km' => 25, 'estimasi_waktu' => 60],
            ['nama_rute' => 'Kota B - Kota C', 'asal' => 'Kota B', 'tujuan' => 'Kota C', 'jarak_km' => 40, 'estimasi_waktu' => 90],
            ['nama_rute' => 'Kota C - Kota D', 'asal' => 'Kota C', 'tujuan' => 'Kota D', 'jarak_km' => 35, 'estimasi_waktu' => 75],
        ];
        foreach ($data as $d) {
            Rute::create($d);
        }
    }
}
```

## Bagian 9 — Panduan Instalasi & Menjalankan
1. **Clone & install**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
2. **Atur database** di `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
3. **Migrasi**
   ```bash
   php artisan migrate
   ```
4. **Seeding dummy**
   ```bash
   php artisan db:seed
   ```
5. **Jalankan server**
   ```bash
   php artisan serve
   ```
6. **Login**
   - Admin contoh: buat manual melalui register (role admin) atau tinker.
   - Sopir: `sopir1@mail.com` / password `password` (dari seeder).
   - Penumpang: registrasi langsung via halaman register.

## Bagian 10 — Simulasi KNN (Dataset Mini)
Dataset (jam → menit):

| Baris | Jadwal (menit) | Rute | Status | Label Jam |
|------|----------------|------|--------|-----------|
| 1    | 480            | 1    | 0      | 08:00     |
| 2    | 540            | 1    | 1      | 09:00     |
| 3    | 600            | 2    | 0      | 10:00     |
| 4    | 660            | 2    | 1      | 11:00     |
| 5    | 720            | 3    | 0      | 12:00     |

Misal input penumpang: `jadwal=09:30 (570 menit)`, `rute=1`, `status=0`, `k=3`.

Hitung jarak Euclidean:
- Ke baris 1: sqrt((570-480)^2 + (1-1)^2 + (0-0)^2) = 90
- Ke baris 2: sqrt((570-540)^2 + (1-1)^2 + (0-1)^2) = 30.02
- Ke baris 3: sqrt((570-600)^2 + (1-2)^2 + (0-0)^2) = 30.02
- Ke baris 4: sqrt((570-660)^2 + (1-2)^2 + (0-1)^2) = 90.01
- Ke baris 5: sqrt((570-720)^2 + (1-3)^2 + (0-0)^2) = 150.01

Tetangga terdekat (k=3): baris 2 (09:00), baris 3 (10:00), baris 1 (08:00).

Voting label terbanyak → rekomendasi **09:00** (jarak paling kecil juga mendukung keputusan).
