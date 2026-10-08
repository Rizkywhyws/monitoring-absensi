<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DaftarUserController extends Controller
{
    private const ROLE_PESERTA = 'user';

    public function index(Request $request)
    {
        $today = today();

        $query = User::with('group:id,name')
            ->where('role', self::ROLE_PESERTA);

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('nim', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
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
            $divisi = trim($request->divisi);

            $query->whereHas('group', function ($q) use ($divisi) {
                $q->where('name', 'like', "%{$divisi}%");
            });
        }

        if (
            $request->filled('periode') &&
            preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $request->periode)
        ) {
            $awal = Carbon::createFromFormat('!Y-m', $request->periode)
                ->startOfMonth();

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

        $groups = Group::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $jumlahPeserta = User::where('role', self::ROLE_PESERTA)
            ->whereNotNull('group_id')
            ->select('group_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('group_id')
            ->pluck('total', 'group_id');

        $periodeOptions = User::where('role', self::ROLE_PESERTA)
            ->whereNotNull('periode_mulai')
            ->selectRaw(
                "DATE_FORMAT(periode_mulai, '%Y-%m') as ym, COUNT(*) as total"
            )
            ->groupByRaw("DATE_FORMAT(periode_mulai, '%Y-%m')")
            ->orderByDesc('ym')
            ->pluck('total', 'ym');

        return view('admin.daftaruser', compact(
            'users',
            'groups',
            'jumlahPeserta',
            'periodeOptions'
        ));
    }

    public function tambah()
    {
        $groups = Group::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.tambahuser', compact('groups'));
    }

    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'namaLengkap' => ['required', 'string', 'max:150'],

            'nimMahasiswa' => [
                'required',
                'string',
                'max:30',
                'unique:users,nim',
            ],

            'asalUniversitas' => [
                'required',
                'string',
                'max:150',
            ],

            'emailPribadi' => [
                'required',
                'email',
                'max:150',
                'unique:users,email_pribadi',
                'unique:users,email',
            ],

            'nomorTelepon' => [
                'required',
                'string',
                'max:20',
            ],

            'kelompokDivisi' => [
                'required',
                'string',
                'max:100',
            ],

            'mentor' => [
                'required',
                'string',
                'max:150',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'max:100',
            ],

            'tanggalMulai' => [
                'required',
                'date',
            ],

            'tanggalSelesai' => [
                'required',
                'date',
                'after_or_equal:tanggalMulai',
            ],

            'unitRayon' => [
                'required',
                Rule::in([
                    'KP Jember',
                ]),
            ],
        ], [
            'nimMahasiswa.unique' =>
                'NIM ini sudah terdaftar di sistem.',

            'emailPribadi.unique' =>
                'Email ini sudah terdaftar di sistem.',

            'tanggalSelesai.after_or_equal' =>
                'Tanggal selesai tidak boleh sebelum tanggal mulai.',

            'kelompokDivisi.required' =>
                'Kelompok/Divisi Penugasan wajib diisi.',

            'mentor.required' =>
                'Nama Mentor wajib diisi.',

            'password.required' =>
                'Password wajib diisi.',

            'password.min' =>
                'Password minimal 6 karakter.',
        ]);

        $nomorTelepon = '+62' . ltrim(
            preg_replace('/[^0-9]/', '', $validated['nomorTelepon']),
            '0'
        );

        $emailLogin = strtolower(trim($validated['emailPribadi']));

        DB::transaction(function () use ($validated, $nomorTelepon, $emailLogin) {
            $group = Group::firstOrCreate(
                [
                    'name' => trim($validated['kelompokDivisi']),
                ],
                [
                    'description' => null,
                    'status' => 'active',
                ]
            );

            $status = Carbon::parse($validated['tanggalSelesai'])->lt(today())
                ? 'inactive'
                : 'active';

            User::create([
                'name' => $validated['namaLengkap'],
                'nim' => $validated['nimMahasiswa'],
                'asal_universitas' => $validated['asalUniversitas'],
                'email_pribadi' => $emailLogin,
                'nomor_telepon' => $nomorTelepon,
                'group_id' => $group->id,
                'periode_mulai' => $validated['tanggalMulai'],
                'periode_selesai' => $validated['tanggalSelesai'],
                'unit_rayon' => $validated['unitRayon'],
                'mentor' => $validated['mentor'],
                'email' => $emailLogin,
                'password' => $validated['password'], 
                'role' => self::ROLE_PESERTA,
                'status' => $status,
            ]);
        });

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'User berhasil ditambahkan.')
            ->with('kredensial', [
                'email_login' => $emailLogin,
            ]);
    }

    public function detail($id)
    {
        $user = User::with('group:id,name')
            ->where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        return view('admin.detail-user', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with('group:id,name')
            ->where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        $groups = Group::select('id', 'name')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.edit-user', compact('user', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'nim' => [
                'required',
                'string',
                'max:30',
                'unique:users,nim,' . $id,
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $id,
            ],

            'mentor' => [
                'nullable',
                'string',
                'max:255',
            ],

            'group_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'periode_mulai' => [
                'nullable',
                'date',
            ],

            'periode_selesai' => [
                'nullable',
                'date',
                'after_or_equal:periode_mulai',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'max:100',
            ],
        ]);

        $status = $validated['status'];

        if (
            !empty($validated['periode_selesai']) &&
            Carbon::parse($validated['periode_selesai'])->lt(today())
        ) {
            $status = 'inactive';
        }

        $groupId = null;

        if (!empty($validated['group_name'])) {
            $group = Group::firstOrCreate(
                [
                    'name' => trim($validated['group_name']),
                ],
                [
                    'description' => null,
                    'status' => 'active',
                ]
            );

            $groupId = $group->id;
        }

        $data = [
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'email' => strtolower(trim($validated['email'])),
            'mentor' => $validated['mentor'] ?? null,
            'group_id' => $groupId,
            'periode_mulai' => $validated['periode_mulai'] ?? null,
            'periode_selesai' => $validated['periode_selesai'] ?? null,
            'status' => $status,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password']; 
        }

        $user->update($data);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function ubahKelompok(Request $request, $id)
    {
        $user = User::where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        $validated = $request->validate([
            'group_id' => [
                'required',
                'exists:groups,id',
            ],
        ]);

        $user->update([
            'group_id' => $validated['group_id'],
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Kelompok user berhasil diubah.');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        $validated = $request->validate([
            'password' => [
                'required',
                'min:6',
                'confirmed',
            ],
        ]);

        $user->update([
            'password' => $validated['password'], 
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Password user berhasil direset.');
    }

    public function nonaktifkan($id)
    {
        $user = User::where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        $user->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'Akun user berhasil dinonaktifkan.');
    }

    public function aktifkan($id)
    {
        $user = User::where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        if (
            $user->periode_selesai &&
            $user->periode_selesai->lt(today())
        ) {
            $user->update([
                'status' => 'inactive',
            ]);

            return redirect()
                ->route('admin.daftar-user')
                ->with(
                    'error',
                    'User tidak dapat diaktifkan karena periode PKL sudah berakhir.'
                );
        }

        $user->update([
            'status' => 'active',
        ]);

        return redirect()
            ->route('admin.daftar-user')
            ->with(
                'success',
                'Akun user berhasil diaktifkan kembali.'
            );
    }

    public function hapus($id)
    {
        $user = User::where('role', self::ROLE_PESERTA)
            ->findOrFail($id);

        $user->delete();

        return redirect()
            ->route('admin.daftar-user')
            ->with('success', 'User berhasil dihapus.');
    }
}