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

    public function batalkanPesanan(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->user_id !== auth()->id()) {
            abort(403);
        }

        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return back()->withErrors(['pesanan' => 'Pesanan ini tidak dapat dibatalkan.']);
        }

        $data = $request->validate([
            'alasan' => 'required|in:perubahan_rencana,menemukan_transportasi_lain,kesalahan_pemesanan,lainnya',
            'alasan_lain' => 'required_if:alasan,lainnya|nullable|string|max:255',
        ]);

        $alasan = [
            'perubahan_rencana' => 'Perubahan rencana perjalanan',
            'menemukan_transportasi_lain' => 'Menemukan transportasi lain',
            'kesalahan_pemesanan' => 'Kesalahan saat pemesanan',
            'lainnya' => trim($data['alasan_lain'] ?? 'Alasan lain'),
        ][$data['alasan']];

        $pesanan->update([
            'status' => 'dibatalkan',
            'alasan_pembatalan' => $alasan,
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
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
