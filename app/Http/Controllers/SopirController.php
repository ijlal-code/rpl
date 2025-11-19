<?php

namespace App\Http\Controllers;

use App\Models\JadwalSopir;
use App\Models\Pesanan;
use App\Models\Rute;
use Illuminate\Http\Request;

class SopirController extends Controller
{
    public function dashboard()
    {
        $sopirId = auth()->user()->sopir->id ?? null;

        $pesanan = Pesanan::with(['penumpang', 'rute', 'kendaraan', 'jadwal'])
            ->where('sopir_id', $sopirId)
            ->latest()
            ->get();

        return view('dashboard.sopir', [
            'pesanan' => $pesanan,
            'jadwal' => JadwalSopir::with('rute')
                ->where('sopir_id', $sopirId)
                ->orderByDesc('tanggal_keberangkatan')
                ->orderByDesc('jam_keberangkatan')
                ->get(),
            'rute' => Rute::all(),
        ]);
    }

    public function konfirmasi(Pesanan $pesanan)
    {
        $pesanan->update(['status' => 'dikonfirmasi']);

        return back()->with('success', 'Pesanan berhasil dikonfirmasi.');
    }

    public function simpanJadwal(Request $request)
    {
        $request->validate([
            'rute_id' => 'required|exists:rutes,id',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'status' => 'required|in:aktif,sedang_jalan,tidak_aktif',
            'catatan' => 'nullable|string',
        ]);

        $sopirId = auth()->user()->sopir->id ?? null;

        if (!$sopirId) {
            return back()->withErrors(['jadwal' => 'Sopir tidak ditemukan.']);
        }

        JadwalSopir::create([
            'sopir_id' => $sopirId,
            'rute_id' => $request->rute_id,
            'tanggal_keberangkatan' => $request->tanggal_keberangkatan,
            'jam_keberangkatan' => $request->jam_keberangkatan,
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Jadwal keberangkatan tersimpan.');
    }

    public function ubahStatusJadwal(JadwalSopir $jadwal, Request $request)
    {
        $request->validate([
            'status' => 'required|in:aktif,sedang_jalan,tidak_aktif',
        ]);

        $sopirId = auth()->user()->sopir->id ?? null;

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        $jadwal->update(['status' => $request->status]);

        return back()->with('success', 'Status jadwal diperbarui.');
    }
}
