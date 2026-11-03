<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DailyActivity;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $this->resolveDate($request);

        $tickets = $this->finishedOn($user->id, $date)
            ->orderBy('completed_at')
            ->get();

        $workStart = $tickets->pluck('started_at')->filter()->min();
        $workEnd   = $tickets->pluck('completed_at')->filter()->max();
        $workMinutes = ($workStart && $workEnd)
            ? (int) round($workStart->diffInMinutes($workEnd, true))
            : null;

        $activity = DailyActivity::where('user_id', $user->id)
            ->whereDate('activity_date', $date)
            ->first();
        $rangeStart = $date->copy()->subDays(6)->startOfDay();
        $rangeEnd   = $date->copy()->endOfDay();

        $countPerDay = Ticket::where('user_id', $user->id)
            ->whereIn('status', Ticket::FINISHED)
            ->whereBetween('completed_at', [$rangeStart, $rangeEnd])
            ->selectRaw('DATE(completed_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

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
                'count'  => (int) $countPerDay->get($key, 0),
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
            'weekTotal'   => (int) $countPerDay->sum(),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'activity_date' => ['required', 'date', 'before_or_equal:today'],
            'description'   => ['required', 'string', 'max:5000'],
        ]);

        $date = Carbon::parse($data['activity_date'])->startOfDay();

        if (! $this->finishedOn($user->id, $date)->exists()) {
            return back()
                ->withErrors(['description' => 'Belum ada tiket selesai pada tanggal ini, jadi catatan belum bisa disimpan.'])
                ->withInput();
        }

        DailyActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_date' => $date->toDateString()],
            ['description' => $data['description']],
        );

        return redirect()
            ->route('daily-activity.index', ['date' => $date->toDateString()])
            ->with('success', 'Catatan harian disimpan.');
    }

    private function finishedOn(int $userId, Carbon $date)
    {
        return Ticket::query()
            ->where('user_id', $userId)
            ->whereIn('status', Ticket::FINISHED)
            ->whereBetween('completed_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
    }

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