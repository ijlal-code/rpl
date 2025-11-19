<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Sopir;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin MandarMove',
            'email' => 'admin@mandarmove.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $sopirUser = User::factory()->create([
            'name' => 'Sopir Percobaan',
            'email' => 'sopir@mandarmove.test',
            'password' => Hash::make('password'),
            'role' => 'sopir',
        ]);

        $penumpang = User::factory()->create([
            'name' => 'Penumpang Demo',
            'email' => 'penumpang@mandarmove.test',
            'password' => Hash::make('password'),
            'role' => 'penumpang',
        ]);

        $sopir = Sopir::create([
            'user_id' => $sopirUser->id,
            'nama' => 'Pak Sopir',
            'no_hp' => '081234567890',
            'pengalaman' => 5,
        ]);

        $ruteMandar = Rute::create([
            'nama_rute' => 'Campus - Kota',
            'titik_berangkat' => 'Kampus UNSULBAR',
            'titik_tujuan' => 'Terminal Kota',
            'estimasi_waktu' => 45,
        ]);

        $rutePantai = Rute::create([
            'nama_rute' => 'Terminal - Pantai',
            'titik_berangkat' => 'Terminal Kota',
            'titik_tujuan' => 'Pantai Manakarra',
            'estimasi_waktu' => 30,
        ]);

        $kendaraan = Kendaraan::create([
            'sopir_id' => $sopir->id,
            'rute_id' => $ruteMandar->id,
            'nama_kendaraan' => 'Minibus Biru',
            'kapasitas' => 12,
            'status' => 'siap',
        ]);

        Pesanan::create([
            'user_id' => $penumpang->id,
            'sopir_id' => $sopir->id,
            'kendaraan_id' => $kendaraan->id,
            'rute_id' => $ruteMandar->id,
            'jam_keberangkatan' => '07:30',
            'status' => 'selesai',
        ]);

        Pesanan::create([
            'user_id' => $penumpang->id,
            'sopir_id' => $sopir->id,
            'kendaraan_id' => $kendaraan->id,
            'rute_id' => $rutePantai->id,
            'jam_keberangkatan' => '10:15',
            'status' => 'dikonfirmasi',
        ]);
    }
}
