<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Rute;
use App\Models\Sopir;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index()
    {
        $kendaraan = Kendaraan::with(['sopir.user', 'rute'])->latest()->get();
        return view('kendaraan.index', compact('kendaraan'));
    }

    public function create()
    {
        return view('kendaraan.create', [
            'sopir' => Sopir::with('user')->get(),
            'rute' => Rute::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sopir_id' => 'required|exists:sopirs,id',
            'rute_id' => 'required|exists:rute,id',
            'nama_kendaraan' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:siap,jalan,selesai',
        ]);

        Kendaraan::create($data);

        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Kendaraan $kendaraan)
    {
        return view('kendaraan.edit', [
            'kendaraan' => $kendaraan,
            'sopir' => Sopir::with('user')->get(),
            'rute' => Rute::all(),
        ]);
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $data = $request->validate([
            'sopir_id' => 'required|exists:sopirs,id',
            'rute_id' => 'required|exists:rute,id',
            'nama_kendaraan' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:siap,jalan,selesai',
        ]);

        $kendaraan->update($data);

        return redirect()->route('kendaraan.index')->with('success', 'Data kendaraan diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();
        return redirect()->route('kendaraan.index')->with('success', 'Kendaraan dihapus.');
    }
}
