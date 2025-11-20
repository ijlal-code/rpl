<?php

namespace App\Http\Controllers;

use App\Models\JadwalSopir;
use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        return view('pesanan.index', [
            'pesanan' => Pesanan::with(['penumpang', 'sopir', 'kendaraan', 'rute', 'jadwal'])->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('pesanan.index', [
            'pesanan' => Pesanan::with(['penumpang', 'sopir', 'kendaraan', 'rute', 'jadwal'])->get(),
            'formMode' => 'create',
            'rute' => Rute::all(),
            'kendaraan' => Kendaraan::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'rute_id' => 'required_without:jadwal_id|nullable|exists:rutes,id',
            'kendaraan_id' => 'nullable|exists:kendaraans,id',
            'jadwal_id' => 'nullable|exists:jadwal_sopirs,id',
            'tanggal_keberangkatan' => 'required_without:jadwal_id|nullable|date',
            'jam_keberangkatan' => 'required_without:jadwal_id|nullable',
            'status' => 'required|in:menunggu,dikonfirmasi,selesai,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        if ($data['kendaraan_id']) {
            $kendaraan = Kendaraan::find($data['kendaraan_id']);
            $data['sopir_id'] = $kendaraan?->sopir_id;
        }

        if (!empty($data['jadwal_id'])) {
            $jadwal = JadwalSopir::with('sopir')->find($data['jadwal_id']);
            if ($jadwal) {
                $data['sopir_id'] = $jadwal->sopir_id;
                $data['rute_id'] = $jadwal->rute_id;
                $data['tanggal_keberangkatan'] = $jadwal->tanggal_keberangkatan;
                $data['jam_keberangkatan'] = $jadwal->jam_keberangkatan;
            }
        }

        if (empty($data['rute_id']) || empty($data['tanggal_keberangkatan']) || empty($data['jam_keberangkatan'])) {
            return back()->withErrors(['rute_id' => 'Rute dan jadwal keberangkatan wajib diisi.']);
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
