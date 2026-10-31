<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DailyActivity;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Daily Activity versi PESERTA: hanya menampilkan data akun yang sedang login.
 */
class DailyActivityController extends Controller
{
    /** Nilai kolom tickets.status untuk tiket yang sudah selesai. Ubah di sini kalau beda. */
    private const DONE_STATUS = 'done';

    public function index(Request $request)
    {
        $user = $request->user();
        $date = $this->resolveDate($request);

        // Tiket milik user ini yang selesai pada tanggal terpilih
        $tickets = Ticket::where('user_id', $user->id)
            ->where('status', self::DONE_STATUS)
            ->whereDate('completed_at', $date)
            ->orderBy('completed_at')
            ->get()
            ->each(function ($t) {
                $t->start_at = $t->started_at ? Carbon::parse($t->started_at) : null;
                $t->end_at   = $t->completed_at ? Carbon::parse($t->completed_at) : null;
                $t->duration_minutes = ($t->start_at && $t->end_at)
                    ? (int) round($t->start_at->diffInMinutes($t->end_at))
                    : null;
            });

        // Jam kerja hari itu: tiket pertama mulai -> tiket terakhir selesai
        $workStart = $tickets->pluck('start_at')->filter()->min();
        $workEnd   = $tickets->pluck('end_at')->filter()->max();
        $workMinutes = ($workStart && $workEnd)
            ? (int) round($workStart->diffInMinutes($workEnd))
            : null;

        $activity = DailyActivity::where('user_id', $user->id)
            ->whereDate('activity_date', $date)
            ->first();

        // Rekap 7 hari yang berakhir di tanggal terpilih
        $rangeStart = $date->copy()->subDays(6)->startOfDay();
        $rangeEnd   = $date->copy()->endOfDay();

        $countPerDay = Ticket::where('user_id', $user->id)
            ->where('status', self::DONE_STATUS)
            ->whereBetween('completed_at', [$rangeStart, $rangeEnd])
            ->get(['completed_at'])
            ->groupBy(fn ($t) => Carbon::parse($t->completed_at)->toDateString())
            ->map->count();

        $loggedDays = DailyActivity::where('user_id', $user->id)
            ->whereBetween('activity_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->pluck('activity_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->flip();

        $recap = collect(range(0, 6))->map(function ($i) use ($date, $countPerDay, $loggedDays) {
            $d   = $date->copy()->subDays($i);
            $key = $d->toDateString();

            return [
                'date'   => $key,
                'label'  => $d->translatedFormat('D, d M'),
                'count'  => $countPerDay->get($key, 0),
                'logged' => $loggedDays->has($key),
                'active' => $i === 0,
            ];
        });

        return view('user.daily-activity', [
            'date'        => $date,
            'isToday'     => $date->isSameDay(today()),
            'prevDate'    => $date->copy()->subDay()->toDateString(),
            'nextDate'    => $date->isSameDay(today()) ? null : $date->copy()->addDay()->toDateString(),
            'tickets'     => $tickets,
            'workStart'   => $workStart,
            'workEnd'     => $workEnd,
            'workMinutes' => $workMinutes,
            'activity'    => $activity,
            'recap'       => $recap,
            'weekTotal'   => $countPerDay->sum(),
        ]);
    }

    /**
     * Simpan / perbarui catatan harian milik user yang sedang login.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'activity_date' => ['required', 'date', 'before_or_equal:today'],
            'description'   => ['required', 'string', 'max:5000'],
        ]);

        $hasTickets = Ticket::where('user_id', $user->id)
            ->where('status', self::DONE_STATUS)
            ->whereDate('completed_at', $data['activity_date'])
            ->exists();

        if (! $hasTickets) {
            return back()
                ->withErrors(['description' => 'Belum ada tiket selesai pada tanggal ini, jadi catatan belum bisa disimpan.'])
                ->withInput();
        }

        DailyActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_date' => $data['activity_date']],
            ['description' => $data['description']],
        );

        return redirect()
            ->route('daily-activity.index', ['date' => $data['activity_date']])
            ->with('success', 'Catatan harian disimpan.');
    }

    /** Baca ?date=...; kalau kosong/salah pakai hari ini; tidak boleh melebihi hari ini. */
    private function resolveDate(Request $request): Carbon
    {
        try {
            $date = Carbon::parse($request->input('date', today()))->startOfDay();
        } catch (\Throwable) {
            $date = today();
        }

        return $date->gt(today()) ? today() : $date;
    }
}