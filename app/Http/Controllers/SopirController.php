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
        $sopirId = $this->getSopirId();

        return view('dashboard.sopir', $this->dataSopir($sopirId));
    }

    public function jadwal()
    {
        $sopirId = $this->getSopirId();

        return view('sopir.jadwal.index', $this->dataSopir($sopirId));
    }

    public function pesanan()
    {
        $sopirId = $this->getSopirId();

        return view('sopir.pesanan.index', $this->dataSopir($sopirId));
    }

    public function konfirmasi(Pesanan $pesanan)
    {
        $sopirId = $this->getSopirId();

        if ($pesanan->sopir_id && $pesanan->sopir_id !== $sopirId) {
            abort(403);
        }

        $pesanan->update(['status' => 'dikonfirmasi']);

        return back()->with('success', 'Pesanan berhasil dikonfirmasi.');
    }

    public function selesaikan(Pesanan $pesanan)
    {
        $sopirId = $this->getSopirId();

        if ($pesanan->sopir_id !== $sopirId) {
            abort(403);
        }

        $pesanan->update(['status' => 'selesai']);

        return back()->with('success', 'Pesanan telah ditandai selesai.');
    }

    public function editJadwal(JadwalSopir $jadwal)
    {
        $sopirId = $this->getSopirId();

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        return view('sopir.jadwal.edit', [
            'jadwal' => $jadwal->load('rute'),
        ]);
    }

    public function simpanJadwal(Request $request)
    {
        $data = $request->validate([
            'rute_pilihan' => 'required|in:majene_polewali,polewali_majene,custom',
            'custom_rute' => 'required_if:rute_pilihan,custom|nullable|string|max:255',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'status' => 'required|in:aktif,sedang_jalan,tidak_aktif',
            'catatan' => 'nullable|string',
        ]);

        $sopirId = $this->getSopirId();

        if (!$sopirId) {
            return back()->withErrors(['jadwal' => 'Sopir tidak ditemukan.']);
        }

        $rute = $this->resolveRute($data['rute_pilihan'], $data['custom_rute'] ?? null);

        JadwalSopir::create([
            'sopir_id' => $sopirId,
            'rute_id' => $rute->id,
            'tanggal_keberangkatan' => $data['tanggal_keberangkatan'],
            'jam_keberangkatan' => $data['jam_keberangkatan'],
            'status' => $data['status'],
            'catatan' => $data['catatan'],
        ]);

        return back()->with('success', 'Jadwal keberangkatan tersimpan.');
    }

    public function perbaruiJadwal(JadwalSopir $jadwal, Request $request)
    {
        $data = $request->validate([
            'rute_pilihan' => 'sometimes|required|in:majene_polewali,polewali_majene,custom',
            'custom_rute' => 'required_if:rute_pilihan,custom|nullable|string|max:255',
            'tanggal_keberangkatan' => 'sometimes|required|date',
            'jam_keberangkatan' => 'sometimes|required',
            'status' => 'required|in:aktif,sedang_jalan,tidak_aktif',
            'catatan' => 'nullable|string',
        ]);

        $sopirId = $this->getSopirId();

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        if (isset($data['rute_pilihan'])) {
            $rute = $this->resolveRute($data['rute_pilihan'], $data['custom_rute'] ?? null);
            $jadwal->rute_id = $rute->id;
        }

        if (isset($data['tanggal_keberangkatan'])) {
            $jadwal->tanggal_keberangkatan = $data['tanggal_keberangkatan'];
        }

        if (isset($data['jam_keberangkatan'])) {
            $jadwal->jam_keberangkatan = $data['jam_keberangkatan'];
        }

        $jadwal->status = $data['status'];
        $jadwal->catatan = $data['catatan'] ?? null;
        $jadwal->save();

        return redirect()->route('sopir.jadwal.index')->with('success', 'Jadwal diperbarui.');
    }

    public function hapusJadwal(JadwalSopir $jadwal)
    {
        $sopirId = $this->getSopirId();

        if ($jadwal->sopir_id !== $sopirId) {
            abort(403);
        }

        $jadwal->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    private function resolveRute(string $pilihan, ?string $customRute): Rute
    {
        return match ($pilihan) {
            'majene_polewali' => Rute::firstOrCreate(
                ['nama_rute' => 'Majene - Polewali'],
                ['asal' => 'Majene', 'tujuan' => 'Polewali']
            ),
            'polewali_majene' => Rute::firstOrCreate(
                ['nama_rute' => 'Polewali - Majene'],
                ['asal' => 'Polewali', 'tujuan' => 'Majene']
            ),
            'custom' => $this->buatRuteCustom($customRute),
        };
    }

    private function buatRuteCustom(?string $input): Rute
    {
        $input = trim($input ?? '');

        if ($input === '') {
            abort(422, 'Rute khusus harus diisi.');
        }

        [$asal, $tujuan] = array_pad(array_map('trim', explode('-', $input, 2)), 2, null);

        return Rute::firstOrCreate(
            ['nama_rute' => $input],
            [
                'asal' => $asal ?: $input,
                'tujuan' => $tujuan ?: ($asal ?: $input),
            ]
        );
    }

    private function dataSopir(?int $sopirId): array
    {
        return [
            'pesanan' => $this->pesananUntukSopir($sopirId),
            'jadwal' => $this->jadwalUntukSopir($sopirId),
        ];
    }

    private function pesananUntukSopir(?int $sopirId)
    {
        return Pesanan::with(['penumpang', 'rute', 'kendaraan', 'jadwal'])
            ->where('sopir_id', $sopirId)
            ->latest()
            ->get();
    }

    private function jadwalUntukSopir(?int $sopirId)
    {
        return JadwalSopir::with('rute')
            ->where('sopir_id', $sopirId)
            ->orderByDesc('tanggal_keberangkatan')
            ->orderByDesc('jam_keberangkatan')
            ->get();
    }

    private function getSopirId(): ?int
    {
        return auth()->user()->sopir->id ?? null;
    }
}
