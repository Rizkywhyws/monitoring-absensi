<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class RiwayatAbsensiController extends Controller
{
    private const STATUS_LABELS = [
        'hadir' => 'Tepat Waktu',
        'telat' => 'Terlambat',
        'sakit' => 'Sakit',
        'izin'  => 'Izin',
    ];

    /** Menampilkan riwayat absensi admin. */
    public function index(Request $request)
    {
        $filters = $this->resolveDateFilters($request);
        $base = $this->baseQuery($filters);

        // Rekap mengikuti rentang tanggal dan keyword, bukan filter status tabel.
        $rekap = (clone $base)
            ->selectRaw('attendances.status, COUNT(*) as total')
            ->groupBy('attendances.status')
            ->pluck('total', 'status');

        $attendances = (clone $base)
            ->when(
                $filters['status'] !== null && $filters['status'] !== '',
                fn(Builder $query) => $query->where('attendances.status', $filters['status'])
            )
            ->leftJoin(
                'attendance_settings as office_settings',
                'office_settings.id',
                '=',
                'attendances.attendance_setting_id'
            )
            ->select('attendances.*', 'office_settings.name as office_name')
            ->with('user')
            ->orderByDesc('attendances.attendance_date')
            ->orderByDesc('attendances.check_in')
            ->paginate(20)
            ->withQueryString();

        return view('admin.riwayat-absensi.index', [
            'attendances'   => $attendances,
            'rekap'         => $rekap,
            'statusOptions' => self::STATUS_LABELS,
            'dari'          => $filters['dari'],
            'sampai'        => $filters['sampai'],
        ]);
    }

    /**
     * Mengekspor seluruh data untuk rentang bulan yang dipilih melalui modal.
     * Rentang mencakup seluruh hari dari awal bulan pertama sampai akhir bulan terakhir.
     */
    public function exportPdf(Request $request)
    {
        // Nama field ini harus sama dengan input pada modal Blade:
        // bulan_dari dan bulan_sampai.
        $validated = $request->validate([
            'bulan_dari'   => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'bulan_sampai' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'keyword'      => ['nullable', 'string', 'max:100'],
            'status'       => ['nullable', 'in:' . implode(',', array_keys(self::STATUS_LABELS))],
        ]);

        $startMonth = Carbon::createFromFormat('!Y-m', $validated['bulan_dari'])->startOfMonth();
        $endMonth = Carbon::createFromFormat('!Y-m', $validated['bulan_sampai'])->startOfMonth();

        if ($endMonth->lt($startMonth)) {
            throw ValidationException::withMessages([
                'bulan_sampai' => 'Bulan akhir harus sama dengan atau setelah bulan awal.',
            ]);
        }

        $filters = [
            'dari'    => $startMonth->toDateString(),
            'sampai'  => $endMonth->copy()->endOfMonth()->toDateString(),
            'status'  => $validated['status'] ?? null,
            'keyword' => $validated['keyword'] ?? null,
        ];

        $base = $this->baseQuery($filters);

        // Rekap menghitung seluruh status dalam rentang tanggal dan keyword terpilih.
        $rekap = (clone $base)
            ->selectRaw('attendances.status, COUNT(*) as total')
            ->groupBy('attendances.status')
            ->pluck('total', 'status');

        // Ambil semua data tanpa pagination agar PDF tidak hanya berisi 20 baris.
        $attendances = (clone $base)
            ->when(
                $filters['status'] !== null && $filters['status'] !== '',
                fn(Builder $query) => $query->where('attendances.status', $filters['status'])
            )
            ->leftJoin(
                'attendance_settings as office_settings',
                'office_settings.id',
                '=',
                'attendances.attendance_setting_id'
            )
            ->select('attendances.*', 'office_settings.name as office_name')
            ->with('user')
            ->orderBy('attendances.attendance_date')
            ->orderBy('attendances.check_in')
            ->get();

        $fileName = sprintf(
            'rekap-absensi-%s-sampai-%s.pdf',
            $startMonth->format('Y-m'),
            $endMonth->format('Y-m')
        );

        return Pdf::loadView('admin.riwayat-absensi.pdf', [
            'attendances'   => $attendances,
            'rekap'         => $rekap,
            'statusOptions' => self::STATUS_LABELS,
            'statusLabel'   => $filters['status'] !== null && $filters['status'] !== ''
                ? self::STATUS_LABELS[$filters['status']]
                : 'Semua Status',
            'keyword'       => $filters['keyword'],
            'dari'          => $filters['dari'],
            'sampai'        => $filters['sampai'],
        ])
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }

    /** Filter tanggal untuk halaman riwayat admin. */
    private function resolveDateFilters(Request $request): array
    {
        $sampaiRules = ['nullable', 'date'];

        if ($request->filled('dari')) {
            $sampaiRules[] = 'after_or_equal:dari';
        }

        $validated = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status'  => ['nullable', 'in:' . implode(',', array_keys(self::STATUS_LABELS))],
            'dari'    => ['nullable', 'date'],
            'sampai'  => $sampaiRules,
        ]);

        $dari = $validated['dari'] ?? now()->startOfMonth()->toDateString();
        $sampai = $validated['sampai'] ?? now()->toDateString();

        // Validasi ulang setelah nilai default diterapkan.
        if (Carbon::parse($sampai)->lt(Carbon::parse($dari))) {
            throw ValidationException::withMessages([
                'sampai' => 'Tanggal sampai harus sama dengan atau setelah tanggal dari.',
            ]);
        }

        return [
            'dari'    => Carbon::parse($dari)->toDateString(),
            'sampai'  => Carbon::parse($sampai)->toDateString(),
            'status'  => $validated['status'] ?? null,
            'keyword' => $validated['keyword'] ?? null,
        ];
    }

    /** Query dasar yang dipakai halaman dan ekspor PDF. */
    private function baseQuery(array $filters): Builder
    {
        $query = Attendance::query()
            ->whereBetween('attendances.attendance_date', [
                $filters['dari'],
                $filters['sampai'],
            ]);

        $keyword = $filters['keyword'] ?? null;

        if ($keyword !== null && $keyword !== '') {
            $query->whereHas('user', function (Builder $userQuery) use ($keyword) {
                $userQuery->where(function (Builder $searchQuery) use ($keyword) {
                    $searchQuery->where('name', 'like', "%{$keyword}%")
                        ->orWhere('nim', 'like', "%{$keyword}%");
                });
            });
        }

        return $query;
    }
}
