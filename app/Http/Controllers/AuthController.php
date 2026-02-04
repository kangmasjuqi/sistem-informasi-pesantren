<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('login'));
        }

        // Determine if login is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt login
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
            'status' => 'aktif', // Only allow active users
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Update last login
            Auth::user()->updateLastLogin();

            // Redirect based on role
            return $this->redirectBasedOnRole();
        }

        // Login failed
        return back()
            ->withErrors(['login' => 'Username/email atau password salah, atau akun tidak aktif'])
            ->withInput($request->only('login'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout');
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole()
    {
        $user = Auth::user();

        // SuperAdmin & Admin → Dashboard
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang, ' . $user->name);
        }

        // Kepala Sekolah → Dashboard
        if ($user->hasRole('KEPSEK')) {
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang, Kepala Sekolah');
        }

        // Bendahara → Pembayaran
        if ($user->hasRole('BENDAHARA')) {
            return redirect()->intended('/pembayaran')
                ->with('success', 'Selamat datang, Bendahara');
        }

        // Staff TU → Dashboard
        if ($user->hasRole('STAFF_TU')) {
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang, Staff TU');
        }

        // Pengajar → Dashboard
        if ($user->hasRole('PENGAJAR')) {
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang, Ustadz/Ustadzah');
        }

        // Wali Kelas → Dashboard
        if ($user->hasRole('WALIKELAS')) {
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang, Wali Kelas');
        }

        // Santri → Santri Dashboard
        if ($user->hasRole('SANTRI')) {
            return redirect()->intended('/santri/dashboard')
                ->with('success', 'Selamat datang, Santri');
        }

        // Wali Santri → Wali Dashboard
        if ($user->hasRole('WALI')) {
            return redirect()->intended('/wali/dashboard')
                ->with('success', 'Selamat datang, Wali Santri');
        }

        // Default redirect
        return redirect()->intended('/dashboard')
            ->with('success', 'Selamat datang, ' . $user->name);
    }
}