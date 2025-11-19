<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\JadwalSopir;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        $jadwal = JadwalSopir::with(['sopir.user', 'rute'])
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->get();

        $rekomendasi = $this->buildRekomendasi(auth()->id());

        return view('dashboard.penumpang', [
            'jadwal' => $jadwal,
            'rekomendasi' => $rekomendasi,
            'pesanan' => Pesanan::with(['rute', 'jadwal.sopir.user'])
                ->where('user_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }

    public function buatPesanan(Request $request)
    {
        $data = $request->validate([
            'jadwal_id' => 'required|exists:jadwal_sopirs,id',
            'catatan' => 'nullable|string',
        ]);

        $jadwal = JadwalSopir::with('sopir')->findOrFail($data['jadwal_id']);

        if ($jadwal->status !== 'aktif') {
            return back()->withErrors(['jadwal_id' => 'Jadwal ini tidak tersedia untuk dipesan.']);
        }

        Pesanan::create([
            'user_id' => auth()->id(),
            'sopir_id' => $jadwal->sopir_id,
            'jadwal_id' => $jadwal->id,
            'rute_id' => $jadwal->rute_id,
            'tanggal_keberangkatan' => $jadwal->tanggal_keberangkatan,
            'jam_keberangkatan' => $jadwal->jam_keberangkatan,
            'status' => 'menunggu',
            'catatan' => $data['catatan'] ?? null,
        ]);

        return redirect()->route('penumpang.pesanan')->with('success', 'Pesanan berhasil dibuat.');
    }

    public function pesanan()
    {
        return view('pesanan.index', [
            'pesanan' => Pesanan::with(['rute', 'kendaraan', 'sopir', 'jadwal'])
                ->where('user_id', auth()->id())
                ->get(),
        ]);
    }

    private function buildRekomendasi(int $userId)
    {
        $riwayat = Pesanan::select('rute_id', 'jam_keberangkatan')
            ->selectRaw('count(*) as total')
            ->where('user_id', $userId)
            ->groupBy('rute_id', 'jam_keberangkatan')
            ->orderByDesc('total')
            ->take(3)
            ->get();

        if ($riwayat->isEmpty()) {
            return JadwalSopir::with(['sopir.user', 'rute'])
                ->where('status', 'aktif')
                ->orderBy('tanggal_keberangkatan')
                ->orderBy('jam_keberangkatan')
                ->take(3)
                ->get();
        }

        $jadwal = JadwalSopir::with(['sopir.user', 'rute'])
            ->where('status', 'aktif')
            ->where(function ($query) use ($riwayat) {
                foreach ($riwayat as $preferensi) {
                    $query->orWhere(function ($sub) use ($preferensi) {
                        $sub->where('rute_id', $preferensi->rute_id)
                            ->where('jam_keberangkatan', $preferensi->jam_keberangkatan);
                    });
                }
            })
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->get();

        return $jadwal->isNotEmpty() ? $jadwal : JadwalSopir::with(['sopir.user', 'rute'])
            ->where('status', 'aktif')
            ->orderBy('tanggal_keberangkatan')
            ->orderBy('jam_keberangkatan')
            ->take(3)
            ->get();
    }
}
