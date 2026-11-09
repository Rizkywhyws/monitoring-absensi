<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        $official = [
            'Email login'      => $user->email,
            'Peran'            => $user->role === 'admin' ? 'Administrator' : ucfirst((string) $user->role),
            'Status akun'      => $user->status === 'active' ? 'Aktif' : 'Nonaktif',
            'Terdaftar sejak'  => $user->created_at?->translatedFormat('d M Y'),
        ];

        return view('admin.profil', compact('user', 'official'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'nomor_telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{6,20}$/'],
        ], [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'nomor_telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, tanda + - atau tanda kurung.',
        ]);

        $request->user()->forceFill($data)->save();

        return redirect()
            ->route('admin.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}