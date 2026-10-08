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
        $start = microtime(true);

        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'message' => 'Akun Anda tidak aktif. Hubungi admin.',
                    'time' => round(microtime(true) - $start, 4),
                ], 403);
            }

            $redirectUrl = $user->role === 'admin'
                ? route('admin.dashboard')
                : route('dashboard');

            return response()->json([
                'success'  => true,
                'redirect' => $redirectUrl,
                'time' => round(microtime(true) - $start, 4),
            ]);
        }

        return response()->json([
            'message' => 'Email atau kata sandi tidak sesuai.',
            'time' => round(microtime(true) - $start, 4),
        ], 422);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login');
    }
}
