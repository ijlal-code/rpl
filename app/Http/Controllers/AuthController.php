<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sopir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:penumpang,sopir',
            'phone' => 'nullable|string|max:20',
            'telepon' => 'required_if:role,sopir|nullable|string|max:20',
            'nomor_sim' => 'required_if:role,sopir|nullable|string|max:50',
            'pengalaman' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['telepon'] ?? $data['phone'] ?? null,
        ]);

        if ($user->role === 'sopir') {
            Sopir::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'nomor_sim' => $data['nomor_sim'],
                'telepon' => $data['telepon'],
                'pengalaman' => $data['pengalaman'] ?? null,
            ]);
        }

        return redirect()->route('login')->with('status', 'Registrasi berhasil, silakan login.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Kredensial tidak valid'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
