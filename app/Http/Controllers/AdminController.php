<?php

namespace App\Http\Controllers;

use App\Models\Sopir;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function sopir()
    {
        return view('admin.sopir', [
            'sopir' => Sopir::with('user')->get(),
            'calon' => User::where('role', 'sopir')->get(),
        ]);
    }

    public function storeSopir(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'nama' => 'required|string|max:255',
            'no_hp' => 'required|string|max:30',
            'pengalaman' => 'nullable|integer|min:0',
        ]);

        $user = User::findOrFail($data['user_id']);
        if ($user->role !== 'sopir') {
            $user->update(['role' => 'sopir']);
        }

        Sopir::updateOrCreate(
            ['user_id' => $data['user_id']],
            [
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'],
                'pengalaman' => $data['pengalaman'] ?? 0,
            ]
        );

        return redirect()->route('admin.sopir')->with('success', 'Data sopir disimpan.');
    }
}
