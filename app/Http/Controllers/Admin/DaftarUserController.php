<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DaftarUserController extends Controller
{
    private const ROLES_PESERTA = ['user', 'peserta_pkl'];
    private const ROLE_BARU = 'user';
    private const UNITS = ['KP Jember', 'KP Probolinggo'];

    public function index(Request $request)
    {
        $today = today();

        $query = User::whereIn('role', self::ROLES_PESERTA);

        if ($request->filled('keyword')) {
            $like = '%' . addcslashes(trim($request->keyword), '%_\\') . '%';

            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('nim', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('status', 'active')
                    ->where(function ($q) use ($today) {
                        $q->whereNull('periode_selesai')
                            ->orWhereDate('periode_selesai', '>=', $today);
                    });
            } elseif ($request->status === 'nonaktif') {
                $query->where(function ($q) use ($today) {
                    $q->where('status', 'inactive')
                        ->orWhereDate('periode_selesai', '<', $today);
                });
            }
        }

        if ($request->filled('divisi')) {
            $like = '%' . addcslashes(trim($request->divisi), '%_\\') . '%';
            $query->where('division', 'like', $like);
        }

        if (
            $request->filled('periode') &&
            preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $request->periode)
        ) {
            $awal = Carbon::createFromFormat('!Y-m', $request->periode)->startOfMonth();
            $akhir = $awal->copy()->endOfMonth();

            $query->whereBetween('periode_mulai', [
                $awal->toDateString(),
                $akhir->toDateString(),
            ]);
        }

        $users = $query
            ->latest('created_at')
            ->paginate(6)
            ->withQueryString();

        // [nama divisi => jumlah peserta], dipakai untuk filter dan saran.
        $divisions = $this->divisionCounts();

        $periodeOptions = User::whereIn('role', self::ROLES_PESERTA)
            ->whereNotNull('periode_mulai')
            ->selectRaw("DATE_FORMAT(periode_mulai, '%Y-%m') as ym, COUNT(*) as total")
            ->groupByRaw("DATE_FORMAT(periode_mulai, '%Y-%m')")
            ->orderByDesc('ym')
            ->pluck('total', 'ym');

        return view('admin.daftaruser', compact('users', 'divisions', 'periodeOptions'));
    }

    public function tambah()
    {
        $divisions = $this->divisionCounts()->keys();

        return view('admin.tambahuser', compact('divisions'));
    }

    public function simpan(Request $request)
    {
        $request->merge([
            'emailPribadi' => strtolower(trim((string) $request->input('emailPribadi'))),
        ]);

        $validated = $request->validate([
            'namaLengkap'     => ['required', 'string', 'max:150'],
            'nimMahasiswa'    => ['required', 'string', 'max:30', 'unique:users,nim'],
            'asalUniversitas' => ['required', 'string', 'max:150'],
            'emailPribadi'    => ['required', 'email', 'max:150', 'unique:users,email_pribadi', 'unique:users,email'],
            'nomorTelepon'    => [
                'required', 'string', 'max:25',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! preg_match('/^\+62\d{8,13}$/', $this->normalizePhone($value))) {
                        $fail('Nomor telepon tidak valid (8–13 digit setelah +62).');
                    }
                },
            ],
            'kelompokDivisi'  => ['required', 'string', 'max:100'],
            'mentor'          => ['required', 'string', 'max:150'],
            'password'        => ['required', 'string', 'min:6', 'max:100'],
            'tanggalMulai'    => ['required', 'date'],
            'tanggalSelesai'  => ['required', 'date', 'after_or_equal:tanggalMulai'],
            'unitRayon'       => ['required', Rule::in(self::UNITS)],
        ], [
            'nimMahasiswa.unique'           => 'NIM ini sudah terdaftar di sistem.',
            'emailPribadi.unique'           => 'Email ini sudah terdaftar di sistem.',
            'tanggalSelesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'kelompokDivisi.required'       => 'Divisi wajib diisi.',
            'mentor.required'               => 'Nama Mentor wajib diisi.',
            'password.required'             => 'Password wajib diisi.',
            'password.min'                  => 'Password minimal 6 karakter.',
            'unitRayon.in'                  => 'Unit / Rayon tidak valid.',
        ]);

        $nomorTelepon = $this->normalizePhone($validated['nomorTelepon']);
        $emailLogin = $validated['emailPribadi'];

        $status = Carbon::parse($validated['tanggalSelesai'])->lt(today())
            ? 'inactive'
            : 'active';

        User::create([
            'name'             => $validated['namaLengkap'],
            'nim'              => $validated['nimMahasiswa'],
            'asal_universitas' => $validated['asalUniversitas'],
            'nomor_telepon'    => $nomorTelepon,
            'no_hp'            => $nomorTelepon,
            'division'         => $this->canonicalDivision($validated['kelompokDivisi']),
            'email_pribadi'    => $emailLogin,
            'email'            => $emailLogin,
            'periode_mulai'    => $validated['tanggalMulai'],
            'periode_selesai'  => $validated['tanggalSelesai'],
            'unit_rayon'       => $validated['unitRayon'],
            'mentor'           => $validated['mentor'],
            'password'         => Hash::make($validated['password']),
            'role'             => self::ROLE_BARU,
            'status'           => $status,
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'User berhasil ditambahkan.')
            ->with('kredensial', [
                'email_login' => $emailLogin,
            ]);
    }

    public function detail($id)
    {
        $user = $this->findPeserta($id);

        return view('admin.detail-user', compact('user'));
    }

    public function edit($id)
    {
        $user = $this->findPeserta($id);
        $divisions = $this->divisionCounts()->keys();

        return view('admin.edit-user', compact('user', 'divisions'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->findPeserta($id);

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'nim'             => ['required', 'string', 'max:30', Rule::unique('users', 'nim')->ignore($user->id)],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'mentor'          => ['nullable', 'string', 'max:150'],
            'division'        => ['nullable', 'string', 'max:100'],
            'periode_mulai'   => ['nullable', 'date'],
            'periode_selesai' => ['nullable', 'date', 'after_or_equal:periode_mulai'],
            'status'          => ['required', 'in:active,inactive'],
            'password'        => ['nullable', 'string', 'min:6', 'max:100'],
        ]);

        $status = $validated['status'];

        if (
            !empty($validated['periode_selesai']) &&
            Carbon::parse($validated['periode_selesai'])->lt(today())
        ) {
            $status = 'inactive';
        }

        $data = [
            'name'            => $validated['name'],
            'nim'             => $validated['nim'],
            'email'           => $validated['email'],
            'mentor'          => $validated['mentor'] ?? null,
            'division'        => filled($validated['division'] ?? null)
                ? $this->canonicalDivision($validated['division'])
                : null,
            'periode_mulai'   => $validated['periode_mulai'] ?? null,
            'periode_selesai' => $validated['periode_selesai'] ?? null,
            'status'          => $status,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function ubahKelompok(Request $request, $id)
    {
        $user = $this->findPeserta($id);

        $validated = $request->validate([
            'division' => ['required', 'string', 'max:100'],
        ]);

        $user->update([
            'division' => $this->canonicalDivision($validated['division']),
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Divisi user berhasil diubah.');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = $this->findPeserta($id);

        $validated = $request->validate([
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Password user berhasil direset.');
    }

    public function nonaktifkan($id)
    {
        $this->findPeserta($id)->update(['status' => 'inactive']);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Akun user berhasil dinonaktifkan.');
    }

    public function aktifkan($id)
    {
        $user = $this->findPeserta($id);

        if ($user->periode_selesai && $user->periode_selesai->lt(today())) {
            $user->update(['status' => 'inactive']);

            return redirect()
                ->route('admin.daftar-user')
                ->with('error', 'User tidak dapat diaktifkan karena periode PKL sudah berakhir.');
        }

        $user->update(['status' => 'active']);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Akun user berhasil diaktifkan kembali.');
    }

    public function hapus($id)
    {
        $this->findPeserta($id)->delete();

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'User berhasil dihapus.');
    }

    private function findPeserta($id): User
    {
        return User::whereIn('role', self::ROLES_PESERTA)->findOrFail($id);
    }

    private function divisionCounts()
    {
        return User::whereIn('role', self::ROLES_PESERTA)
            ->whereNotNull('division')
            ->where('division', '!=', '')
            ->selectRaw('division, COUNT(*) as total')
            ->groupBy('division')
            ->orderBy('division')
            ->pluck('total', 'division');
    }

    /**
     * Rapikan spasi, lalu pakai ejaan yang sudah ada di DB bila cocok
     * (collation *_ci tidak membedakan huruf besar/kecil).
     */
    private function canonicalDivision(string $input): string
    {
        $name = preg_replace('/\s+/', ' ', trim($input));

        return User::whereIn('role', self::ROLES_PESERTA)
            ->where('division', $name)
            ->value('division') ?? $name;
    }

    private function normalizePhone(string $input): string
    {
        $digits = preg_replace('/\D/', '', $input);
        $digits = preg_replace('/^(62)+/', '', $digits);
        $digits = ltrim($digits, '0');

        return '+62' . $digits;
    }
}