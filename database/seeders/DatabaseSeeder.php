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
        $admin = User::create([
            'name' => 'Admin Transport',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0811111111',
        ]);

        $penumpang = User::create([
            'name' => 'Penumpang Demo',
            'email' => 'penumpang@example.com',
            'password' => Hash::make('password'),
            'role' => 'penumpang',
            'phone' => '0822222222',
        ]);

        $sopirUsers = collect([
            ['name' => 'Sopir Satu', 'email' => 'sopir1@example.com', 'telepon' => '0812345671', 'nomor_sim' => 'SIM123'],
            ['name' => 'Sopir Dua', 'email' => 'sopir2@example.com', 'telepon' => '0812345672', 'nomor_sim' => 'SIM124'],
            ['name' => 'Sopir Tiga', 'email' => 'sopir3@example.com', 'telepon' => '0812345673', 'nomor_sim' => 'SIM125'],
        ])->map(function ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'sopir',
                'phone' => $data['telepon'],
            ]);

            return Sopir::create([
                'user_id' => $user->id,
                'nama' => $data['name'],
                'nomor_sim' => $data['nomor_sim'],
                'telepon' => $data['telepon'],
                'pengalaman' => 'Berpengalaman 3 tahun',
            ]);
        });

        $rute = collect([
            ['nama_rute' => 'Kota A - Kota B', 'asal' => 'Kota A', 'tujuan' => 'Kota B', 'jarak_km' => 120, 'perkiraan_waktu' => '02:30'],
            ['nama_rute' => 'Kota B - Kota C', 'asal' => 'Kota B', 'tujuan' => 'Kota C', 'jarak_km' => 90, 'perkiraan_waktu' => '02:00'],
            ['nama_rute' => 'Kota C - Kota A', 'asal' => 'Kota C', 'tujuan' => 'Kota A', 'jarak_km' => 150, 'perkiraan_waktu' => '03:00'],
        ])->map(fn ($item) => Rute::create($item));

        $kendaraan = [
            ['nama' => 'Elf 01', 'plat_nomor' => 'DD 1001 AA', 'jenis' => 'Minibus', 'kapasitas' => 12, 'status' => 'siap', 'sopir_id' => $sopirUsers[0]->id ?? null],
            ['nama' => 'Elf 02', 'plat_nomor' => 'DD 1002 AA', 'jenis' => 'Minibus', 'kapasitas' => 12, 'status' => 'siap', 'sopir_id' => $sopirUsers[1]->id ?? null],
            ['nama' => 'Bus 01', 'plat_nomor' => 'DD 2001 BB', 'jenis' => 'Bus', 'kapasitas' => 40, 'status' => 'jalan', 'sopir_id' => $sopirUsers[2]->id ?? null],
            ['nama' => 'Sedan 01', 'plat_nomor' => 'DD 3001 CC', 'jenis' => 'Sedan', 'kapasitas' => 4, 'status' => 'siap', 'sopir_id' => $sopirUsers[0]->id ?? null],
            ['nama' => 'SUV 01', 'plat_nomor' => 'DD 4001 DD', 'jenis' => 'SUV', 'kapasitas' => 6, 'status' => 'selesai', 'sopir_id' => $sopirUsers[1]->id ?? null],
        ];

        foreach ($kendaraan as $item) {
            Kendaraan::create($item);
        }

        Pesanan::create([
            'user_id' => $penumpang->id,
            'sopir_id' => $sopirUsers[0]->id ?? null,
            'kendaraan_id' => 1,
            'rute_id' => $rute[0]->id,
            'tanggal_keberangkatan' => now()->toDateString(),
            'jam_keberangkatan' => '08:00',
            'status' => 'dikonfirmasi',
            'catatan' => 'Seat depan',
        ]);
    }
}
