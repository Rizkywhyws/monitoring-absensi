<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Profil PESERTA: menampilkan data resmi (read-only) dan mengedit data pribadi (teks).
 */
class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('group');

        $fmt = fn ($d) => $d ? Carbon::parse($d)->translatedFormat('d M Y') : null;

        $mulai   = $fmt($user->periode_mulai);
        $selesai = $fmt($user->periode_selesai);

        // Data yang diatur admin: hanya ditampilkan
        $official = [
            'Email'               => $user->email,
            'Periode PKL'         => ($mulai && $selesai) ? "{$mulai} – {$selesai}" : null,
            'Divisi / Kelompok'   => $user->group?->name,
            'Pembimbing lapangan' => $user->mentor,
        ];

        return view('user.profil', compact('user', 'official'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'nim'              => [
                'required', 'string', 'max:30', 'regex:/^[A-Za-z0-9]+$/',
                Rule::unique('users', 'nim')->ignore($request->user()->id),
            ],
            'asal_universitas' => ['nullable', 'string', 'max:150'],
            'jurusan'          => ['nullable', 'string', 'max:100'],
            'no_hp'            => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'alamat'           => ['nullable', 'string', 'max:500'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nim.required'  => 'NIM / NISN wajib diisi.',
            'nim.regex'     => 'NIM / NISN hanya boleh berisi huruf dan angka, tanpa spasi.',
            'nim.unique'    => 'NIM / NISN ini sudah dipakai peserta lain.',
            'no_hp.regex'   => 'Nomor HP hanya boleh berisi angka, spasi, tanda + atau -.',
        ]);

        $request->user()->forceFill($data)->save();

        return redirect()
            ->route('profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}