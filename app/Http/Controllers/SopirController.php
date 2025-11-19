<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class SopirController extends Controller
{
    public function dashboard()
    {
        $pesanan = Pesanan::with(['penumpang', 'rute', 'kendaraan'])
            ->where('sopir_id', auth()->user()->sopir->id ?? null)
            ->latest()
            ->get();

        return view('dashboard.sopir', [
            'pesanan' => $pesanan,
            'kendaraan' => Kendaraan::where('sopir_id', auth()->user()->sopir->id ?? null)->get(),
        ]);
    }

    public function konfirmasi(Pesanan $pesanan)
    {
        $pesanan->update(['status' => 'dikonfirmasi']);

        return back()->with('success', 'Pesanan berhasil dikonfirmasi.');
    }

    public function ubahStatusKendaraan(Kendaraan $kendaraan, Request $request)
    {
        $request->validate([
            'status' => 'required|in:siap,jalan,selesai',
        ]);

        $kendaraan->update(['status' => $request->status]);

        return back()->with('success', 'Status kendaraan diperbarui.');
    }
}
