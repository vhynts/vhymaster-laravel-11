<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;

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
            // Update last_login dengan Carbon setelah login berhasil
            $user->last_login = Carbon::now();
            $user->save();

            return redirect()->intended('dashboard');
        } else {
            return redirect()->route('login')->with('error', 'Email atau password salah.');
        }
    }




    public function logout(Request $request)
    {
        $user = Auth::user();

        // Remove the user's online status from cache
        Cache::forget('user-is-online-' . $user->id);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
