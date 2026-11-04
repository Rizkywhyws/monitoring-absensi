<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load('group:id,name');

        $start = $user->periode_mulai?->copy()->startOfDay();
        $end   = $user->periode_selesai?->copy()->startOfDay();
        $today = today();

        $program   = null;
        $kehadiran = null;
        $hadir     = 0;

        if ($start && $end && $end->gte($start)) {
            $total   = (int) $start->diffInDays($end) + 1;
            $elapsed = $today->lt($start) ? 0 : min($total, (int) $start->diffInDays($today) + 1);

            $program = [
                'total'     => $total,
                'elapsed'   => $elapsed,
                'remaining' => $total - $elapsed,
                'percent'   => (int) round($elapsed / $total * 100),
            ];

            $until     = $today->lt($end) ? $today : $end;
            $hariKerja = 0;

            if ($until->gte($start)) {
                for ($d = $start->copy(); $d->lte($until); $d->addDay()) {
                    if ($d->isWeekday()) {
                        $hariKerja++;
                    }
                }

                $hadir = Attendance::where('user_id', $user->id)
                    ->whereBetween('attendance_date', [$start->toDateString(), $until->toDateString()])
                    ->whereNotNull('check_in')
                    ->count();
            }

            $kehadiran = $hariKerja > 0 ? min(100, (int) round($hadir / $hariKerja * 100)) : null;
        }

        $tiket = Ticket::where('user_id', $user->id)
            ->selectRaw("COUNT(*) AS total,
                         COALESCE(SUM(status IN ('done','verified')), 0) AS selesai,
                         COALESCE(SUM(status = 'verified'), 0) AS verified")
            ->first();

        return view('user.profileUser', [
            'user'      => $user,
            'program'   => $program,
            'kehadiran' => $kehadiran,
            'hadir'     => $hadir,
            'tiket'     => [
                'total'    => (int) ($tiket->total ?? 0),
                'selesai'  => (int) ($tiket->selesai ?? 0),
                'verified' => (int) ($tiket->verified ?? 0),
            ],
            'ip'        => $request->ip(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('profile', [
            'nomor_telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{6,20}$/'],
            'email_pribadi' => ['nullable', 'email:rfc', 'max:150'],
        ]);

        $request->user()->forceFill([
            'nomor_telepon' => $data['nomor_telepon'] ?? null,
            'email_pribadi' => $data['email_pribadi'] ?? null,
        ])->save();

        return redirect()->route('profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        return redirect()->route('profil')->with('success', 'Password berhasil diubah.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validateWithBag('photo', [
            'foto' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto.required' => 'Pilih foto terlebih dahulu.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.mimes'    => 'Format foto harus JPG, PNG, atau WebP.',
            'foto.max'      => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user    = $request->user();
        $oldPath = $user->foto;
        $newPath = $request->file('foto')->store('profile-photos', 'public');

        $user->forceFill(['foto' => $newPath])->save();

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('profil')->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $user    = $request->user();
        $oldPath = $user->foto;

        $user->forceFill(['foto' => null])->save();

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('profil')->with('success', 'Foto profil dihapus.');
    }
}