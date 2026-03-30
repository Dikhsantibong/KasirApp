<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Percobaan login untuk: ' . $request->email);

            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Cek apakah email terdaftar
            $user = \App\Models\User::where('email', $request->email)->first();
            if (!$user) {
                \Illuminate\Support\Facades\Log::warning('Email tidak ditemukan: ' . $request->email);
                return back()->withErrors(['email' => 'E-mail tidak ditemukan.'])->onlyInput('email');
            }

            // Cek Password secara manual
            if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                \Illuminate\Support\Facades\Log::warning('Password salah untuk: ' . $request->email);
                return back()->withErrors(['password' => 'Password salah.'])->onlyInput('email');
            }

            // Lanjut ke Autentikasi Laravel
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                \Illuminate\Support\Facades\Log::info('Login Berhasil! Mengalihkan ke dashboard.');
                return redirect()->intended('/dashboard');
            }

            \Illuminate\Support\Facades\Log::error('Auth::attempt gagal tanpa alasan jelas.');
            return back()->withErrors(['email' => 'Gagal login.'])->onlyInput('email');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error Sistem: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Sistem Error: ' . $e->getMessage()])->onlyInput('email');
        }
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
