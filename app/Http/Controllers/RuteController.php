<?php

namespace App\Http\Controllers;

use App\Models\Rute;
use Illuminate\Http\Request;

class RuteController extends Controller
{
    public function index()
    {
        return view('rute.index', ['rute' => Rute::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string|max:255',
            'asal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'jarak_km' => 'nullable|numeric',
            'perkiraan_waktu' => 'nullable',
        ]);

        Rute::create($data);

        return back()->with('success', 'Rute disimpan.');
    }

    public function update(Rute $rute, Request $request)
    {
        $data = $request->validate([
            'nama_rute' => 'required|string|max:255',
            'asal' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'jarak_km' => 'nullable|numeric',
            'perkiraan_waktu' => 'nullable',
        ]);

        $rute->update($data);

        return back()->with('success', 'Rute diperbarui.');
    }

    public function destroy(Rute $rute)
    {
        $rute->delete();

        return back()->with('success', 'Rute dihapus.');
    }
}
