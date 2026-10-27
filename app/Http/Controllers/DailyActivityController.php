<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyActivityController extends Controller
{
    public function index(Request $request)
    {
        $date = $this->resolveDate($request);

        $completedOnDate = fn ($q) => $q
            ->where('status', 'done')
            ->whereDate('completed_at', $date);

        $students = User::query()
            ->whereHas('tickets', $completedOnDate)
            ->with([
                'tickets' => fn ($q) => $completedOnDate($q)->orderBy('completed_at'),
                'dailyActivities' => fn ($q) => $q->whereDate('activity_date', $date),
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(fn ($w) => $w
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('nim', 'like', "%{$s}%")
                    ->orWhereHas('tickets', fn ($t) => $t->where('code', 'like', "%{$s}%")));
            })
            ->when($request->filled('division'), fn ($q) => $q->where('division', $request->division))
            ->when($request->logbook === 'belum', fn ($q) => $q->whereDoesntHave(
                'dailyActivities', fn ($a) => $a->whereDate('activity_date', $date)
            ))
            ->when($request->logbook === 'sudah', fn ($q) => $q->whereHas(
                'dailyActivities', fn ($a) => $a->whereDate('activity_date', $date)
            ))
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        $students->getCollection()->each(function ($student) {
            $start = $student->tickets->pluck('started_at')->filter()->map(fn ($d) => Carbon::parse($d))->min();
            $end   = $student->tickets->pluck('completed_at')->filter()->map(fn ($d) => Carbon::parse($d))->max();

            $student->work_start   = $start;
            $student->work_end     = $end;
            $student->work_minutes = ($start && $end) ? $start->diffInMinutes($end) : 0;
        });

        return view('admin.DailyActivity.index', [
            'user'      => $request->user(),
            'date'      => $date,
            'prevDate'  => $date->copy()->subDay()->toDateString(),
            'nextDate'  => $date->copy()->addDay()->toDateString(),
            'students'  => $students,
            'divisions' => User::whereNotNull('division')->distinct()->orderBy('division')->pluck('division'),
            'stats'     => $this->stats($date),
        ]);
    }

    public function show(Request $request, User $user)
    {
        $date = $this->resolveDate($request);

        $tickets = Ticket::where('user_id', $user->id)
            ->where('status', 'done')
            ->whereDate('completed_at', $date)
            ->orderBy('completed_at')
            ->get();

        $activity = DailyActivity::where('user_id', $user->id)
            ->whereDate('activity_date', $date)
            ->first();

        return view('admin.DailyActivity.show', compact('user', 'date', 'tickets', 'activity'));
    }

    public function store(Request $request, User $user)
    {
        $data = $request->validate([
            'activity_date' => ['required', 'date'],
            'description'   => ['required', 'string', 'max:5000'],
        ]);

        DailyActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_date' => $data['activity_date']],
            ['description' => $data['description']],
        );

        return redirect()
            ->route('admin.daily-activity.show', [$user, 'date' => $data['activity_date']])
            ->with('success', 'Catatan harian disimpan.');
    }

    private function resolveDate(Request $request): Carbon
    {
        try {
            return Carbon::parse($request->input('date', today()))->startOfDay();
        } catch (\Throwable) {
            return today();
        }
    }

    private function stats(Carbon $date): array
    {
        $tickets = Ticket::where('status', 'done')
            ->whereDate('completed_at', $date)
            ->get(['user_id', 'started_at', 'completed_at']);

        $perUser = $tickets->groupBy('user_id');

        $avgMinutes = $perUser->map(function ($rows) {
            $start = $rows->pluck('started_at')->filter()->map(fn ($d) => Carbon::parse($d))->min();
            $end   = $rows->pluck('completed_at')->filter()->map(fn ($d) => Carbon::parse($d))->max();

            return ($start && $end) ? $start->diffInMinutes($end) : null;
        })->filter()->avg() ?? 0;

        $logbookFilled = DailyActivity::whereDate('activity_date', $date)
            ->whereIn('user_id', $perUser->keys())
            ->count();

        return [
            'total_students'  => User::whereNotNull('nim')->count(),
            'active_students' => $perUser->count(),
            'total_tickets'   => $tickets->count(),
            'avg_hours'       => round($avgMinutes / 60, 1),
            'logbook_filled'  => $logbookFilled,
            'logbook_missing' => $perUser->count() - $logbookFilled,
        ];
    }
}