<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index()
    {
        $pesanan = Auth::user()
            ->pesanan()
            ->with(['rute', 'kendaraan'])
            ->latest()
            ->get();

        return view('pesanan.index', [
            'pesanan' => $pesanan,
        ]);
    }

    public function adminIndex()
    {
        $pesanan = Pesanan::with(['penumpang', 'sopir.user', 'kendaraan', 'rute'])->latest()->get();
        return view('pesanan.admin', compact('pesanan'));
    }

    public function sopirIndex()
    {
        $sopir = Auth::user()->sopir;
        $pesanan = Pesanan::with(['penumpang', 'rute', 'kendaraan'])
            ->where('sopir_id', optional($sopir)->id)
            ->latest()
            ->get();

        return view('pesanan.sopir', compact('pesanan'));
    }

    public function create()
    {
        return view('pesanan.create', [
            'rute' => Rute::all(),
            'kendaraan' => Kendaraan::with(['rute', 'sopir'])->where('status', '!=', 'selesai')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rute_id' => 'required|exists:rute,id',
            'kendaraan_id' => 'nullable|exists:kendaraan,id',
            'jam_keberangkatan' => 'required|date_format:H:i',
        ]);

        $kendaraan = null;
        if (!empty($data['kendaraan_id'])) {
            $kendaraan = Kendaraan::find($data['kendaraan_id']);
        }

        Pesanan::create([
            'user_id' => Auth::id(),
            'sopir_id' => $kendaraan?->sopir_id,
            'kendaraan_id' => $kendaraan?->id,
            'rute_id' => $data['rute_id'],
            'jam_keberangkatan' => $data['jam_keberangkatan'],
            'status' => 'menunggu',
        ]);

        return redirect()->route('pesanan.index')->with('success', 'Pesanan dibuat, menunggu konfirmasi sopir.');
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,dalam_perjalanan,selesai',
        ]);

        $pesanan->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
