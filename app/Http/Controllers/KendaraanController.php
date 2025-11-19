<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Sopir;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index()
    {
        return view('kendaraan.index', [
            'kendaraan' => Kendaraan::with('sopir')->get(),
            'sopir' => Sopir::with('user')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:50',
            'jenis' => 'required|string',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:siap,jalan,selesai',
            'sopir_id' => 'nullable|exists:sopirs,id',
        ]);

        Kendaraan::create($data);

        return back()->with('success', 'Kendaraan berhasil disimpan.');
    }

    public function update(Kendaraan $kendaraan, Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:50',
            'jenis' => 'required|string',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:siap,jalan,selesai',
            'sopir_id' => 'nullable|exists:sopirs,id',
        ]);

        $kendaraan->update($data);

        return back()->with('success', 'Kendaraan diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        $kendaraan->delete();

        return back()->with('success', 'Kendaraan dihapus.');
    }
}
