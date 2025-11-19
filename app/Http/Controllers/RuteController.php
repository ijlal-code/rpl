<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    public function index()
    {
        $rute = Rute::latest()->get();
        return view('rute.index', compact('rute'));
    }

    public function create()
    {
        return view('rute.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string|max:255',
            'titik_berangkat' => 'required|string|max:255',
            'titik_tujuan' => 'required|string|max:255',
            'estimasi_waktu' => 'required|integer|min:1',
        ]);

        Rute::create($data);

        return redirect()->route('rute.index')->with('success', 'Rute berhasil ditambahkan.');
    }

    public function edit(Rute $rute)
    {
        return view('rute.edit', compact('rute'));
    }

    public function update(Request $request, Rute $rute)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string|max:255',
            'titik_berangkat' => 'required|string|max:255',
            'titik_tujuan' => 'required|string|max:255',
            'estimasi_waktu' => 'required|integer|min:1',
        ]);

        $rute->update($data);

        return redirect()->route('rute.index')->with('success', 'Rute berhasil diperbarui.');
    }

    public function destroy(Rute $rute)
    {
        $rute->delete();
        return redirect()->route('rute.index')->with('success', 'Rute berhasil dihapus.');
    }
}
