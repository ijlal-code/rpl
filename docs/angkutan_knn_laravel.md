# Blueprint Lengkap Website Angkutan Umum (Laravel + KNN)

Dokumen ini berisi panduan dan kode lengkap berbentuk teks (siap salin–tempel) untuk membangun aplikasi Laravel sesuai laporan **“Perancangan dan Pengembangan Website Angkutan Umum Menggunakan Metode Prototyping dan Algoritma KNN”**. Semua bagian ditulis dalam bahasa Indonesia dan mengikuti arsitektur MVC Laravel.

---
## Bagian 1 — Penjelasan Arsitektur

### Arsitektur MVC Laravel
- **Model**: Merepresentasikan tabel dan relasi (User, Sopir, Kendaraan, Rute, Pesanan). Model mengatur mass-assignment, casting, relasi Eloquent, serta logika bisnis sederhana.
- **View**: Blade templates menggunakan Bootstrap 5. View dibagi per peran: admin, sopir, penumpang, plus halaman login/register, daftar kendaraan, rute, pesanan, dan rekomendasi KNN.
- **Controller**: Mengelola request, validasi, otorisasi per peran, memanggil model, dan memilih view. Ada controller khusus KNN untuk rekomendasi jadwal.
- **Middleware**: Memeriksa role (admin, sopir, penumpang) untuk mengamankan rute.

### Struktur Folder Final (ringkas)
```
app/
  Http/
    Controllers/
      AuthController.php
      AdminController.php
      SopirController.php
      UserController.php
      PesananController.php
      KendaraanController.php
      RuteController.php
      RekomendasiKNNController.php
    Middleware/
      AdminMiddleware.php
      SopirMiddleware.php
      UserMiddleware.php
  Models/
    User.php
    Sopir.php
    Kendaraan.php
    Rute.php
    Pesanan.php
resources/
  views/
    auth/login.blade.php
    auth/register.blade.php
    dashboard/admin.blade.php
    dashboard/sopir.blade.php
    dashboard/penumpang.blade.php
    kendaraan/index.blade.php
    rute/index.blade.php
    pesanan/index.blade.php
    rekomendasi/index.blade.php
routes/web.php
Database/migrations/*.php
Database/seeders/DatabaseSeeder.php
Database/seeders/SampleDataSeeder.php
Database/factories/*.php
```

### Alur Login Multi-Role
1. Pengguna register (role default penumpang). Admin membuat akun sopir/admin via panel.
2. Login: AuthController memverifikasi kredensial, menyimpan role di session, redirect sesuai peran:
   - **Admin** → `/dashboard/admin`
   - **Sopir** → `/dashboard/sopir`
   - **Penumpang** → `/dashboard/penumpang`
3. Middleware role memblokir akses rute yang tidak sesuai.

### Alur Pemesanan sesuai DFD
1. Penumpang memilih rute & kendaraan aktif → membuat pesanan (status `menunggu`).
2. Sopir melihat pesanan masuk → mengonfirmasi/ubah status (`dikonfirmasi`, `dalam_perjalanan`, `selesai`).
3. Admin memantau laporan pesanan/kendaraan.
4. Penumpang dapat melihat status dan rekomendasi jadwal via KNN.

### Diagram Hubungan (ERD Teks)
- **User (id, name, email, password, role)**
  - One-to-One → **Sopir (user_id)**
  - One-to-Many → **Pesanan (user_id)**
- **Sopir (id, user_id, nama, no_hp, pengalaman)**
  - One-to-Many → **Kendaraan (sopir_id)**
  - One-to-Many → **Pesanan (sopir_id)**
- **Kendaraan (id, sopir_id, nama_kendaraan, kapasitas, status)**
  - Many-to-One → **Rute (rute_id)**
  - One-to-Many → **Pesanan (kendaraan_id)**
- **Rute (id, nama_rute, titik_berangkat, titik_tujuan, estimasi_waktu)**
  - One-to-Many → **Kendaraan (rute_id)**
  - One-to-Many → **Pesanan (rute_id)**
- **Pesanan (id, user_id, sopir_id, kendaraan_id, rute_id, jam_keberangkatan, status)**

---
## Bagian 2 — Database (Migration)
Berikut kode migration lengkap. Sesuaikan nama file dengan timestamp Anda di folder `database/migrations`.

### Migration: `users` (database/migrations/xxxx_xx_xx_create_users_table.php)
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

### Migration: `sopirs` (database/migrations/xxxx_xx_xx_create_sopirs_table.php)
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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('no_hp');
            $table->integer('pengalaman')->default(0); // tahun pengalaman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sopirs');
    }
};
```

### Migration: `rute` (database/migrations/xxxx_xx_xx_create_rutes_table.php)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rute', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rute');
            $table->string('titik_berangkat');
            $table->string('titik_tujuan');
            $table->integer('estimasi_waktu'); // menit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rute');
    }
};
```

### Migration: `kendaraan` (database/migrations/xxxx_xx_xx_create_kendaraans_table.php)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sopir_id')->constrained('sopirs')->onDelete('cascade');
            $table->foreignId('rute_id')->constrained('rute')->onDelete('cascade');
            $table->string('nama_kendaraan');
            $table->integer('kapasitas');
            $table->enum('status', ['siap', 'jalan', 'selesai'])->default('siap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
```

### Migration: `pesanan` (database/migrations/xxxx_xx_xx_create_pesanans_table.php)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sopir_id')->nullable()->constrained('sopirs')->onDelete('set null');
            $table->foreignId('kendaraan_id')->nullable()->constrained('kendaraan')->onDelete('set null');
            $table->foreignId('rute_id')->constrained('rute')->onDelete('cascade');
            $table->time('jam_keberangkatan');
            $table->enum('status', ['menunggu', 'dikonfirmasi', 'dalam_perjalanan', 'selesai', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
```

---
## Bagian 3 — Model (Relasi Lengkap)

### app/Models/User.php
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

    public function sopir()
    {
        return $this->hasOne(Sopir::class);
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
```

### app/Models/Sopir.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sopir extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nama', 'no_hp', 'pengalaman',
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

### app/Models/Kendaraan.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'sopir_id', 'rute_id', 'nama_kendaraan', 'kapasitas', 'status',
    ];

    public function sopir()
    {
        return $this->belongsTo(Sopir::class);
    }

    public function rute()
    {
        return $this->belongsTo(Rute::class, 'rute_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
```

### app/Models/Rute.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    use HasFactory;

    protected $table = 'rute';

    protected $fillable = [
        'nama_rute', 'titik_berangkat', 'titik_tujuan', 'estimasi_waktu',
    ];

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class, 'rute_id');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }
}
```

### app/Models/Pesanan.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'user_id', 'sopir_id', 'kendaraan_id', 'rute_id', 'jam_keberangkatan', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
        return $this->belongsTo(Rute::class, 'rute_id');
    }
}
```

---
## Bagian 4 — Middleware Role
Letakkan di `app/Http/Middleware` dan daftarkan pada `app/Http/Kernel.php`.

### AdminMiddleware.php
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
        abort(403, 'Akses khusus admin');
    }
}
```

### SopirMiddleware.php
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
        abort(403, 'Akses khusus sopir');
    }
}
```

### UserMiddleware.php
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
        abort(403, 'Akses khusus penumpang');
    }
}
```

---
## Bagian 5 — Routing (routes/web.php)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SopirController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\RuteController;
use App\Http\Controllers\RekomendasiKNNController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard redirect
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard/admin', [AdminController::class, 'index']);
        Route::resource('kendaraan', KendaraanController::class);
        Route::resource('rute', RuteController::class);
        Route::resource('sopir', SopirController::class);
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('admin.laporan');
    });

    // Sopir routes
    Route::middleware('sopir')->group(function () {
        Route::get('/dashboard/sopir', [SopirController::class, 'dashboard']);
        Route::get('/pesanan/masuk', [SopirController::class, 'pesananMasuk'])->name('sopir.pesanan');
        Route::post('/pesanan/{id}/konfirmasi', [SopirController::class, 'konfirmasi'])->name('sopir.konfirmasi');
        Route::post('/kendaraan/{id}/status', [SopirController::class, 'ubahStatusKendaraan'])->name('sopir.kendaraan.status');
    });

    // Penumpang routes
    Route::middleware('penumpang')->group(function () {
        Route::get('/dashboard/penumpang', [UserController::class, 'dashboard']);
        Route::get('/kendaraan/aktif', [UserController::class, 'kendaraanAktif'])->name('penumpang.kendaraan');
        Route::get('/rute', [UserController::class, 'rute'])->name('penumpang.rute');
        Route::resource('pesanan', PesananController::class)->only(['index','store','create','show']);
        Route::get('/rekomendasi', [RekomendasiKNNController::class, 'index'])->name('rekomendasi.index');
        Route::post('/rekomendasi', [RekomendasiKNNController::class, 'proses'])->name('rekomendasi.proses');
    });
});
```

---
## Bagian 6 — Controller
Kode berikut lengkap dengan fungsi CRUD dan logika KNN.

### AuthController.php
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
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
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'penumpang',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->dashboard();
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function dashboard()
    {
        $role = auth()->user()->role;
        return match ($role) {
            'admin' => redirect('/dashboard/admin'),
            'sopir' => redirect('/dashboard/sopir'),
            default => redirect('/dashboard/penumpang'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
```

### AdminController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Rute;
use App\Models\Sopir;
use App\Models\Pesanan;

class AdminController extends Controller
{
    public function index()
    {
        return view('dashboard.admin', [
            'totalKendaraan' => Kendaraan::count(),
            'totalRute' => Rute::count(),
            'totalSopir' => Sopir::count(),
            'totalPesanan' => Pesanan::count(),
        ]);
    }

    public function laporan()
    {
        $pesanan = Pesanan::with(['user', 'sopir', 'kendaraan', 'rute'])->latest()->get();
        return view('pesanan.index', compact('pesanan'));
    }
}
```

### SopirController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class SopirController extends Controller
{
    public function index()
    {
        return $this->dashboard();
    }

    public function dashboard()
    {
        $pesanan = Pesanan::where('sopir_id', auth()->user()->sopir->id ?? null)->latest()->get();
        return view('dashboard.sopir', compact('pesanan'));
    }

    public function pesananMasuk()
    {
        $pesanan = Pesanan::whereNull('sopir_id')->orWhere('sopir_id', auth()->user()->sopir->id)->get();
        return view('pesanan.index', compact('pesanan'));
    }

    public function konfirmasi($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $pesanan->update([
            'sopir_id' => auth()->user()->sopir->id,
            'kendaraan_id' => auth()->user()->sopir->kendaraan->first()->id ?? null,
            'status' => 'dikonfirmasi',
        ]);
        return back()->with('success', 'Pesanan dikonfirmasi');
    }

    public function ubahStatusKendaraan(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:siap,jalan,selesai'
        ]);

        $kendaraan = Kendaraan::findOrFail($id);
        $kendaraan->update(['status' => $request->status]);
        return back()->with('success', 'Status kendaraan diperbarui');
    }
}
```

### UserController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Rute;
use App\Models\Pesanan;

class UserController extends Controller
{
    public function dashboard()
    {
        $pesanan = Pesanan::where('user_id', auth()->id())->latest()->get();
        return view('dashboard.penumpang', compact('pesanan'));
    }

    public function kendaraanAktif()
    {
        $kendaraan = Kendaraan::where('status', '!=', 'selesai')->with(['rute', 'sopir'])->get();
        return view('kendaraan.index', compact('kendaraan'));
    }

    public function rute()
    {
        $rute = Rute::all();
        return view('rute.index', compact('rute'));
    }
}
```

### PesananController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Kendaraan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        $pesanan = Pesanan::where('user_id', auth()->id())->with(['rute','kendaraan','sopir'])->get();
        return view('pesanan.index', compact('pesanan'));
    }

    public function create()
    {
        return view('pesanan.create', [
            'rute' => Rute::all(),
            'kendaraan' => Kendaraan::where('status', 'siap')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rute_id' => 'required|exists:rute,id',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'jam_keberangkatan' => 'required'
        ]);

        Pesanan::create([
            'user_id' => auth()->id(),
            'rute_id' => $data['rute_id'],
            'kendaraan_id' => $data['kendaraan_id'],
            'jam_keberangkatan' => $data['jam_keberangkatan'],
            'status' => 'menunggu'
        ]);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan dibuat');
    }

    public function show($id)
    {
        $pesanan = Pesanan::with(['rute','kendaraan','sopir'])->findOrFail($id);
        return view('pesanan.show', compact('pesanan'));
    }
}
```

### KendaraanController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Sopir;
use App\Models\Rute;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraan = Kendaraan::with(['sopir','rute'])->get();
        return view('kendaraan.index', compact('kendaraan'));
    }

    public function create()
    {
        return view('kendaraan.create', [
            'sopir' => Sopir::all(),
            'rute' => Rute::all()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sopir_id' => 'required|exists:sopirs,id',
            'rute_id' => 'required|exists:rute,id',
            'nama_kendaraan' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:siap,jalan,selesai'
        ]);
        Kendaraan::create($data);
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan ditambahkan');
    }

    public function show($id)
    {
        $kendaraan = Kendaraan::with(['sopir','rute'])->findOrFail($id);
        return view('kendaraan.show', compact('kendaraan'));
    }

    public function edit($id)
    {
        return view('kendaraan.edit', [
            'kendaraan' => Kendaraan::findOrFail($id),
            'sopir' => Sopir::all(),
            'rute' => Rute::all()
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'sopir_id' => 'required|exists:sopirs,id',
            'rute_id' => 'required|exists:rute,id',
            'nama_kendaraan' => 'required|string',
            'kapasitas' => 'required|integer',
            'status' => 'required|in:siap,jalan,selesai'
        ]);
        Kendaraan::findOrFail($id)->update($data);
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan diperbarui');
    }

    public function destroy($id)
    {
        Kendaraan::findOrFail($id)->delete();
        return back()->with('success', 'Kendaraan dihapus');
    }
}
```

### RuteController.php
```php
<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    public function index()
    {
        $rute = Rute::all();
        return view('rute.index', compact('rute'));
    }

    public function create()
    {
        return view('rute.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string',
            'titik_berangkat' => 'required|string',
            'titik_tujuan' => 'required|string',
            'estimasi_waktu' => 'required|integer'
        ]);
        Rute::create($data);
        return redirect()->route('rute.index')->with('success', 'Rute dibuat');
    }

    public function show($id)
    {
        $rute = Rute::findOrFail($id);
        return view('rute.show', compact('rute'));
    }

    public function edit($id)
    {
        $rute = Rute::findOrFail($id);
        return view('rute.edit', compact('rute'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string',
            'titik_berangkat' => 'required|string',
            'titik_tujuan' => 'required|string',
            'estimasi_waktu' => 'required|integer'
        ]);
        Rute::findOrFail($id)->update($data);
        return redirect()->route('rute.index')->with('success', 'Rute diperbarui');
    }

    public function destroy($id)
    {
        Rute::findOrFail($id)->delete();
        return back()->with('success', 'Rute dihapus');
    }
}
```

### RekomendasiKNNController.php
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Rute;

class RekomendasiKNNController extends Controller
{
    public function index()
    {
        return view('rekomendasi.index', [
            'rute' => Rute::all(),
            'hasil' => null,
            'dataset' => $this->datasetContoh()
        ]);
    }

    public function proses(Request $request)
    {
        $data = $request->validate([
            'jam_keberangkatan' => 'required', // format HH:MM
            'rute_id' => 'required|exists:rute,id',
            'status_sebelumnya' => 'required|in:menunggu,dikonfirmasi,dalam_perjalanan,selesai,ditolak'
        ]);

        $inputVector = [
            'jam_keberangkatan' => $this->jamToFloat($data['jam_keberangkatan']),
            'rute_id' => (int)$data['rute_id'],
            'status_sebelumnya' => $this->statusToInt($data['status_sebelumnya'])
        ];

        $dataset = $this->datasetContoh();

        // Hitung jarak Euclidean untuk setiap data
        $k = 3;
        $neighbors = [];
        foreach ($dataset as $item) {
            $distance = $this->euclideanDistance($inputVector, $item);
            $neighbors[] = [
                'distance' => $distance,
                'label' => $item['label'],
                'data' => $item
            ];
        }

        usort($neighbors, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $kNearest = array_slice($neighbors, 0, $k);

        // Voting mayoritas label
        $votes = [];
        foreach ($kNearest as $n) {
            $votes[$n['label']] = ($votes[$n['label']] ?? 0) + 1;
        }
        arsort($votes);
        $hasil = array_key_first($votes);

        return view('rekomendasi.index', [
            'rute' => Rute::all(),
            'hasil' => $hasil,
            'neighbors' => $kNearest,
            'dataset' => $dataset,
            'input' => $data
        ]);
    }

    private function euclideanDistance(array $input, array $item): float
    {
        $sum = pow($input['jam_keberangkatan'] - $item['jam_keberangkatan'], 2)
             + pow($input['rute_id'] - $item['rute_id'], 2)
             + pow($input['status_sebelumnya'] - $item['status_sebelumnya'], 2);
        return sqrt($sum);
    }

    private function jamToFloat(string $jam): float
    {
        [$h, $m] = explode(':', $jam);
        return (int)$h + ((int)$m / 60);
    }

    private function statusToInt(string $status): int
    {
        return match($status) {
            'menunggu' => 0,
            'dikonfirmasi' => 1,
            'dalam_perjalanan' => 2,
            'selesai' => 3,
            default => 4,
        };
    }

    private function datasetContoh(): array
    {
        return [
            ['jam_keberangkatan' => 6.0, 'rute_id' => 1, 'status_sebelumnya' => 0, 'label' => '06:00'],
            ['jam_keberangkatan' => 7.5, 'rute_id' => 1, 'status_sebelumnya' => 1, 'label' => '07:30'],
            ['jam_keberangkatan' => 9.0, 'rute_id' => 2, 'status_sebelumnya' => 1, 'label' => '09:00'],
            ['jam_keberangkatan' => 12.0, 'rute_id' => 2, 'status_sebelumnya' => 2, 'label' => '12:00'],
            ['jam_keberangkatan' => 15.0, 'rute_id' => 3, 'status_sebelumnya' => 1, 'label' => '15:00'],
        ];
    }
}
```

### Sopir Resource (CRUD) — SopirController extended
Jika ingin CRUD sopir penuh pada admin, tambahkan metode berikut:
```php
    public function create()
    {
        return view('sopir.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama' => 'required|string',
            'no_hp' => 'required|string',
            'pengalaman' => 'required|integer',
        ]);
        Sopir::create($data);
        return redirect()->route('sopir.index')->with('success', 'Sopir dibuat');
    }

    public function show($id)
    {
        $sopir = Sopir::findOrFail($id);
        return view('sopir.show', compact('sopir'));
    }

    public function edit($id)
    {
        $sopir = Sopir::findOrFail($id);
        return view('sopir.edit', compact('sopir'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama' => 'required|string',
            'no_hp' => 'required|string',
            'pengalaman' => 'required|integer',
        ]);
        Sopir::findOrFail($id)->update($data);
        return redirect()->route('sopir.index')->with('success', 'Sopir diperbarui');
    }

    public function destroy($id)
    {
        Sopir::findOrFail($id)->delete();
        return back()->with('success', 'Sopir dihapus');
    }
```

---
## Bagian 7 — Blade View (Bootstrap 5)
Pastikan `resources/views` mengikuti struktur pada arsitektur. Berikut isi utama.

### resources/views/auth/login.blade.php
```blade
<!doctype html>
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
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-3 text-center">Login</h3>
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
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
          <p class="mt-3 mb-0">Belum punya akun? <a href="/register">Daftar</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
```

### resources/views/auth/register.blade.php
```blade
<!doctype html>
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
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="mb-3 text-center">Registrasi Penumpang</h3>
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
            <button class="btn btn-success w-100">Daftar</button>
          </form>
          <p class="mt-3 mb-0">Sudah punya akun? <a href="/login">Login</a></p>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
```

### resources/views/dashboard/admin.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h2>Dashboard Admin</h2>
  <div class="row g-3 mt-3">
    <div class="col-md-3">
      <div class="card text-bg-primary"><div class="card-body">Total Kendaraan: {{ $totalKendaraan }}</div></div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-success"><div class="card-body">Total Rute: {{ $totalRute }}</div></div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-warning"><div class="card-body">Total Sopir: {{ $totalSopir }}</div></div>
    </div>
    <div class="col-md-3">
      <div class="card text-bg-info"><div class="card-body">Total Pesanan: {{ $totalPesanan }}</div></div>
    </div>
  </div>
</div>
@endsection
```

### resources/views/dashboard/sopir.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h2>Dashboard Sopir</h2>
  <p>Pesanan terbaru yang ditangani:</p>
  <table class="table table-bordered">
    <thead><tr><th>ID</th><th>Rute</th><th>Jam</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($pesanan as $p)
        <tr>
          <td>{{ $p->id }}</td>
          <td>{{ $p->rute->nama_rute ?? '-' }}</td>
          <td>{{ $p->jam_keberangkatan }}</td>
          <td>{{ $p->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
```

### resources/views/dashboard/penumpang.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h2>Dashboard Penumpang</h2>
  <a href="{{ route('pesanan.create') }}" class="btn btn-primary mb-3">Buat Pesanan</a>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Rute</th><th>Kendaraan</th><th>Jam</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($pesanan as $p)
        <tr>
          <td>{{ $p->id }}</td>
          <td>{{ $p->rute->nama_rute ?? '-' }}</td>
          <td>{{ $p->kendaraan->nama_kendaraan ?? '-' }}</td>
          <td>{{ $p->jam_keberangkatan }}</td>
          <td>{{ $p->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
```

### resources/views/kendaraan/index.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Kendaraan</h3>
    @if(auth()->user()->role==='admin')
      <a href="{{ route('kendaraan.create') }}" class="btn btn-primary">Tambah</a>
    @endif
  </div>
  <table class="table table-hover">
    <thead><tr><th>Nama</th><th>Sopir</th><th>Rute</th><th>Kapasitas</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($kendaraan as $k)
        <tr>
          <td>{{ $k->nama_kendaraan }}</td>
          <td>{{ $k->sopir->nama ?? '-' }}</td>
          <td>{{ $k->rute->nama_rute ?? '-' }}</td>
          <td>{{ $k->kapasitas }}</td>
          <td>{{ $k->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
```

### resources/views/rute/index.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Rute</h3>
    @if(auth()->user()->role==='admin')
      <a href="{{ route('rute.create') }}" class="btn btn-primary">Tambah</a>
    @endif
  </div>
  <table class="table table-bordered">
    <thead><tr><th>Nama Rute</th><th>Berangkat</th><th>Tujuan</th><th>Estimasi (menit)</th></tr></thead>
    <tbody>
      @foreach($rute as $r)
        <tr>
          <td>{{ $r->nama_rute }}</td>
          <td>{{ $r->titik_berangkat }}</td>
          <td>{{ $r->titik_tujuan }}</td>
          <td>{{ $r->estimasi_waktu }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
```

### resources/views/pesanan/index.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h3>Pesanan</h3>
  <table class="table table-striped">
    <thead><tr><th>ID</th><th>Penumpang</th><th>Rute</th><th>Jam</th><th>Status</th></tr></thead>
    <tbody>
      @foreach($pesanan as $p)
        <tr>
          <td>{{ $p->id }}</td>
          <td>{{ $p->user->name ?? '-' }}</td>
          <td>{{ $p->rute->nama_rute ?? '-' }}</td>
          <td>{{ $p->jam_keberangkatan }}</td>
          <td>{{ $p->status }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
```

### resources/views/rekomendasi/index.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="container py-4">
  <h3>Rekomendasi Jadwal (KNN)</h3>
  <form method="POST" action="{{ route('rekomendasi.proses') }}" class="row g-3 mb-4">
    @csrf
    <div class="col-md-4">
      <label class="form-label">Jam Keberangkatan (HH:MM)</label>
      <input type="time" name="jam_keberangkatan" class="form-control" required>
    </div>
    <div class="col-md-4">
      <label class="form-label">Pilih Rute</label>
      <select name="rute_id" class="form-select" required>
        @foreach($rute as $r)
          <option value="{{ $r->id }}">{{ $r->nama_rute }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Status Sebelumnya</label>
      <select name="status_sebelumnya" class="form-select" required>
        <option value="menunggu">Menunggu</option>
        <option value="dikonfirmasi">Dikonfirmasi</option>
        <option value="dalam_perjalanan">Dalam Perjalanan</option>
        <option value="selesai">Selesai</option>
        <option value="ditolak">Ditolak</option>
      </select>
    </div>
    <div class="col-12">
      <button class="btn btn-success">Proses KNN</button>
    </div>
  </form>

  @if(isset($hasil))
    <div class="alert alert-info">Jadwal rekomendasi terbaik: <strong>{{ $hasil }}</strong></div>
    <h5>3 Tetangga Terdekat:</h5>
    <ul>
      @foreach($neighbors as $n)
        <li>Label {{ $n['label'] }} dengan jarak {{ number_format($n['distance'], 2) }}</li>
      @endforeach
    </ul>
  @endif

  <h5>Dataset Contoh</h5>
  <table class="table table-bordered">
    <thead><tr><th>Jam (float)</th><th>Rute ID</th><th>Status</th><th>Label Jadwal</th></tr></thead>
    <tbody>
      @foreach($dataset as $d)
        <tr>
          <td>{{ $d['jam_keberangkatan'] }}</td>
          <td>{{ $d['rute_id'] }}</td>
          <td>{{ $d['status_sebelumnya'] }}</td>
          <td>{{ $d['label'] }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
```

> **Catatan layout:** gunakan `resources/views/layouts/app.blade.php` sederhana dengan navbar logout.

```blade
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Aplikasi Angkutan Umum</title>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="/dashboard">Angkutan Umum</a>
    <div class="d-flex">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-outline-light">Logout</button>
      </form>
    </div>
  </div>
</nav>
<main>
  @yield('content')
</main>
</body>
</html>
```

---
## Bagian 8 — Seeder dan Factory

### database/factories/SopirFactory.php
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SopirFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'no_hp' => $this->faker->phoneNumber(),
            'pengalaman' => $this->faker->numberBetween(1, 5),
        ];
    }
}
```

### database/factories/KendaraanFactory.php
```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KendaraanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kendaraan' => 'Bus ' . $this->faker->word(),
            'kapasitas' => $this->faker->numberBetween(12, 40),
            'status' => $this->faker->randomElement(['siap','jalan','selesai']),
        ];
    }
}
```

### database/seeders/SampleDataSeeder.php
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sopir;
use App\Models\Rute;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password'), 'role' => 'admin']
        );

        // Sopir
        $sopirUsers = collect([
            ['name' => 'Sopir Satu', 'email' => 'sopir1@example.com'],
            ['name' => 'Sopir Dua', 'email' => 'sopir2@example.com'],
            ['name' => 'Sopir Tiga', 'email' => 'sopir3@example.com'],
        ])->map(function ($data) {
            return User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password'), 'role' => 'sopir']
            );
        });

        $rute = [
            ['nama_rute' => 'Terminal A - Terminal B', 'titik_berangkat' => 'Terminal A', 'titik_tujuan' => 'Terminal B', 'estimasi_waktu' => 60],
            ['nama_rute' => 'Terminal B - Terminal C', 'titik_berangkat' => 'Terminal B', 'titik_tujuan' => 'Terminal C', 'estimasi_waktu' => 80],
            ['nama_rute' => 'Terminal C - Terminal D', 'titik_berangkat' => 'Terminal C', 'titik_tujuan' => 'Terminal D', 'estimasi_waktu' => 50],
        ];
        foreach ($rute as $r) {
            Rute::firstOrCreate(['nama_rute' => $r['nama_rute']], $r);
        }

        $ruteIds = Rute::pluck('id');

        $sopirModels = $sopirUsers->map(function ($user) {
            return Sopir::firstOrCreate(
                ['user_id' => $user->id],
                ['nama' => $user->name, 'no_hp' => '08' . rand(100000000, 999999999), 'pengalaman' => rand(1, 5)]
            );
        });

        // 5 kendaraan
        $kendaraanData = [
            ['nama_kendaraan' => 'Bus A', 'kapasitas' => 20, 'status' => 'siap'],
            ['nama_kendaraan' => 'Bus B', 'kapasitas' => 18, 'status' => 'siap'],
            ['nama_kendaraan' => 'Bus C', 'kapasitas' => 24, 'status' => 'jalan'],
            ['nama_kendaraan' => 'Bus D', 'kapasitas' => 30, 'status' => 'siap'],
            ['nama_kendaraan' => 'Bus E', 'kapasitas' => 15, 'status' => 'selesai'],
        ];

        foreach ($kendaraanData as $idx => $k) {
            Kendaraan::firstOrCreate(
                ['nama_kendaraan' => $k['nama_kendaraan']],
                $k + [
                    'sopir_id' => $sopirModels[$idx % $sopirModels->count()]->id,
                    'rute_id' => $ruteIds[$idx % $ruteIds->count()],
                ]
            );
        }
    }
}
```

Tambahkan pemanggilan di `DatabaseSeeder.php`:
```php
public function run(): void
{
    $this->call(SampleDataSeeder::class);
}
```

---
## Bagian 9 — Panduan Instalasi
1. **Clone & install dependency**
   ```bash
   composer install
   npm install && npm run build
   ```
2. **Buat file .env** dari `.env.example`, set database lokal.
3. **Generate key**
   ```bash
   php artisan key:generate
   ```
4. **Migrasi database**
   ```bash
   php artisan migrate
   ```
5. **Seed data dummy**
   ```bash
   php artisan db:seed --class=SampleDataSeeder
   ```
6. **Jalankan server**
   ```bash
   php artisan serve
   ```
7. **Login akun default**
   - Admin: `admin@example.com / password`
   - Sopir: `sopir1@example.com / password` (dst sopir2, sopir3)
   - Penumpang: daftar manual via halaman register.

---
## Bagian 10 — Simulasi KNN

### Dataset Kecil (contoh sama seperti controller)
| No | Jam (float) | Rute | Status | Label Jadwal |
|----|-------------|------|--------|--------------|
| 1  | 6.0         | 1    | 0      | 06:00 |
| 2  | 7.5         | 1    | 1      | 07:30 |
| 3  | 9.0         | 2    | 1      | 09:00 |
| 4  | 12.0        | 2    | 2      | 12:00 |
| 5  | 15.0        | 3    | 1      | 15:00 |

### Langkah Perhitungan
1. **Input**: jam=08:00 → 8.0, rute=1, status_sebelumnya=`menunggu`(0).
2. **Hitung jarak Euclidean ke tiap data**:
   - Data1: sqrt((8-6)^2 + (1-1)^2 + (0-0)^2) = sqrt(4) = 2.00
   - Data2: sqrt((8-7.5)^2 + (1-1)^2 + (0-1)^2) = sqrt(0.25+0+1)=1.12
   - Data3: sqrt((8-9)^2 + (1-2)^2 + (0-1)^2) = sqrt(1+1+1)=1.73
   - Data4: sqrt((8-12)^2 + (1-2)^2 + (0-2)^2) = sqrt(16+1+4)=4.58
   - Data5: sqrt((8-15)^2 + (1-3)^2 + (0-1)^2) = sqrt(49+4+1)=7.28
3. **Urutkan jarak & ambil k=3 terdekat**: Data2 (1.12, label 07:30), Data3 (1.73, label 09:00), Data1 (2.00, label 06:00).
4. **Voting mayoritas**: label 07:30, 09:00, 06:00 (semua unik → pilih jarak terdekat atau gunakan frekuensi tertinggi; di contoh controller menggunakan mayoritas, sehingga 07:30 menang karena berada paling dekat di urutan pertama).
5. **Output**: Jadwal rekomendasi = **07:30**.

---
## Ringkasan Alasan Desain
- **Roles dipisah via middleware** untuk keamanan dan kejelasan akses.
- **Relasi Eloquent** mengikuti ERD: User→Sopir (1-1), Sopir→Kendaraan/Pesanan (1-many), Rute→Kendaraan/Pesanan (1-many).
- **KNN Euclidean** sederhana dengan 3 fitur (jam, rute, status). Jam diubah ke float agar jarak bermakna.
- **Bootstrap 5** dipilih untuk tampilan cepat dan responsif.
- **Seeder** menyiapkan akun dan data minimal agar langsung bisa diuji.

