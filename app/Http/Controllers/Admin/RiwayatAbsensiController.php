<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class RiwayatAbsensiController extends Controller
{
    private const STATUS_LABELS = [
        'hadir' => 'Tepat Waktu',
        'telat' => 'Terlambat',
        'sakit' => 'Sakit',
        'izin'  => 'Izin',
    ];

    public function index(Request $request)
    {
        $sampaiRules = ['nullable', 'date'];
        if ($request->filled('dari')) {
            $sampaiRules[] = 'after_or_equal:dari';
        }

        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status'  => ['nullable', 'in:' . implode(',', array_keys(self::STATUS_LABELS))],
            'dari'    => ['nullable', 'date'],
            'sampai'  => $sampaiRules,
        ]);

        $dari    = $filters['dari']   ?? now()->startOfMonth()->toDateString();
        $sampai  = $filters['sampai'] ?? now()->toDateString();
        $status  = $filters['status'] ?? null;
        $keyword = $filters['keyword'] ?? null;

        $base = Attendance::query()
            ->whereBetween('attendance_date', [$dari, $sampai])
            ->when($keyword, function ($q) use ($keyword) {
                $q->whereHas('user', function ($u) use ($keyword) {
                    $u->where(function ($w) use ($keyword) {
                        $w->where('name', 'like', "%{$keyword}%")
                          ->orWhere('nim', 'like', "%{$keyword}%");
                    });
                });
            });

        $rekap = (clone $base)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $attendances = (clone $base)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->with('user.group')
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in')
            ->paginate(20)
            ->withQueryString();

        return view('admin.riwayat-absensi.index', [
            'attendances'   => $attendances,
            'rekap'         => $rekap,
            'statusOptions' => self::STATUS_LABELS,
            'dari'          => $dari,
            'sampai'        => $sampai,
        ]);
    }
}