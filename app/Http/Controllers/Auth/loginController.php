<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cek status akun (opsional tapi disarankan)
            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'message' => 'Akun Anda tidak aktif. Hubungi admin.',
                ], 403);
            }

            $redirectUrl = $user->role === 'admin'
                ? route('admin.dashboard')
                : route('dashboard');

            return response()->json([
                'success'  => true,
                'redirect' => $redirectUrl,
            ]);
        }

        return response()->json([
            'message' => 'Email atau kata sandi tidak sesuai.',
        ], 422);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}