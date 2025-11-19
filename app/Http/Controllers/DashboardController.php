<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Sopir;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $data = [
                'totalSopir' => Sopir::count(),
                'totalRute' => Rute::count(),
                'totalKendaraan' => Kendaraan::count(),
                'totalPesanan' => Pesanan::count(),
            ];
            return view('dashboard.admin', $data);
        }

        if ($user->role === 'sopir') {
            $sopir = $user->sopir;
            $pesanan = $sopir
                ? Pesanan::with(['penumpang', 'rute', 'kendaraan'])
                    ->where('sopir_id', $sopir->id)
                    ->latest()
                    ->get()
                : collect();

            return view('dashboard.sopir', [
                'sopir' => $sopir,
                'pesanan' => $pesanan,
            ]);
        }

        $rute = Rute::with('kendaraans')->get();
        $pesanan = $user->pesanan()->with(['rute', 'kendaraan'])->latest()->get();

        return view('dashboard.penumpang', [
            'routes' => $rute,
            'pesanan' => $pesanan,
        ]);
    }
}
