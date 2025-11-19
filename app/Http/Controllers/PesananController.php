<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Sopir;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        return view('pesanan.index', [
            'pesanan' => Pesanan::with(['penumpang', 'sopir', 'kendaraan', 'rute'])->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('pesanan.index', [
            'pesanan' => Pesanan::with(['penumpang', 'sopir', 'kendaraan', 'rute'])->get(),
            'formMode' => 'create',
            'rute' => Rute::all(),
            'kendaraan' => Kendaraan::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'rute_id' => 'required|exists:rutes,id',
            'kendaraan_id' => 'nullable|exists:kendaraans,id',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'status' => 'required|in:menunggu,dikonfirmasi,selesai,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        if ($data['kendaraan_id']) {
            $kendaraan = Kendaraan::find($data['kendaraan_id']);
            $data['sopir_id'] = $kendaraan?->sopir_id;
        }

        Pesanan::create($data);

        return back()->with('success', 'Pesanan berhasil disimpan.');
    }

    public function updateStatus(Pesanan $pesanan, Request $request)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,selesai,dibatalkan',
        ]);

        $pesanan->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
