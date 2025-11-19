<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Pesanan;
use App\Models\Rute;
use App\Models\Sopir;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('dashboard.penumpang', [
            'kendaraan' => Kendaraan::with('sopir')->where('status', 'siap')->get(),
            'rute' => Rute::all(),
            'pesanan' => Pesanan::with(['rute', 'kendaraan'])->where('user_id', auth()->id())->latest()->get(),
        ]);
    }

    public function buatPesanan(Request $request)
    {
        $data = $request->validate([
            'rute_id' => 'required|exists:rutes,id',
            'kendaraan_id' => 'nullable|exists:kendaraans,id',
            'tanggal_keberangkatan' => 'required|date',
            'jam_keberangkatan' => 'required',
            'catatan' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();

        if ($data['kendaraan_id']) {
            $kendaraan = Kendaraan::find($data['kendaraan_id']);
            $data['sopir_id'] = $kendaraan?->sopir_id;
        }

        Pesanan::create($data);

        return redirect()->route('penumpang.pesanan')->with('success', 'Pesanan berhasil dibuat.');
    }

    public function pesanan()
    {
        return view('pesanan.index', [
            'pesanan' => Pesanan::with(['rute', 'kendaraan', 'sopir'])->where('user_id', auth()->id())->get(),
        ]);
    }
}
