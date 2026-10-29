<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class RiwayatAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status'  => ['nullable', 'string', 'max:30'],
            'dari'    => ['nullable', 'date'],
            'sampai'  => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $dari   = $data['dari']   ?? now()->startOfMonth()->toDateString();
        $sampai = $data['sampai'] ?? now()->toDateString();

        $query = Attendance::query()
            ->with('user.group')
            ->betweenDates($dari, $sampai)
            ->when($data['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($data['keyword'] ?? null, function ($q, $k) {
                $q->whereHas('user', fn($u) => $u
                    ->where('name', 'like', "%{$k}%")
                    ->orWhere('nim', 'like', "%{$k}%"));
            });

        $rekap = $query->clone()
            ->reorder()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $attendances = $query
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in')
            ->paginate(20)
            ->withQueryString();

        return view('admin.riwayat-absensi.index', [
            'attendances'   => $attendances,
            'rekap'         => $rekap,
            'statusOptions' => Attendance::STATUS_LABELS,
            'dari'          => $dari,
            'sampai'        => $sampai,
        ]);
    }
}