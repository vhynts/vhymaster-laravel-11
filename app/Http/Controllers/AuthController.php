<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('zAuth.login');
    }

    public function loginAuthenticate(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'email-input' => 'required|email',
            // 'password-input' => 'required'
        ], [
            'email-input.required' => 'Email harus diisi.',
            'email-input.email' => 'Format email tidak valid.',
            // 'password-input.required' => 'Password harus diisi.'
        ]);

        $credentials = [
            'email' => $request->input('email-input'),
            'password' => $request->input('password-input')
        ];

        // Periksa apakah pengguna ada dan is_active nya adalah 1
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !$user->is_active) {
            return redirect()->route('login')->with('error', 'Akun tidak aktif / ditemukan.');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        } else {
            return redirect()->route('login')->with('error', 'Email atau password salah.');
        }
    }




    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
