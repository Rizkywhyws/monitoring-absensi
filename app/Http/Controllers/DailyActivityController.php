<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyActivityController extends Controller
{
    public function index(Request $request)
    {
        $date = $this->resolveDate($request);
        $dayStart = $date->copy()->startOfDay();
        $dayEnd = $date->copy()->endOfDay();

        $finishedOnDate = fn($q) => $this->scopeFinishedOn($q, $dayStart, $dayEnd);
        $loggedOnDate = fn($q) => $q->whereDate('activity_date', $date->toDateString());

        $search = trim((string) $request->input('search', ''));
        $like = '%' . addcslashes($search, '%_\\') . '%';

        $students = User::query()
            ->whereHas('tickets', $finishedOnDate)
            ->with([
                'tickets' => fn($q) => $finishedOnDate($q)
                    ->with(['attachments', 'verifier'])
                    ->orderBy('completed_at'),
                'dailyActivities' => $loggedOnDate,
            ])
            ->when($search !== '', function ($q) use ($like) {
                $q->where(fn($w) => $w
                    ->where('name', 'like', $like)
                    ->orWhere('nim', 'like', $like)
                    ->orWhereHas('tickets', fn($t) => $t->where('code', 'like', $like)));
            })
            ->when($request->filled('division'), fn($q) => $q->where('division', $request->input('division')))
            ->when($request->input('logbook') === 'belum', fn($q) => $q->whereDoesntHave('dailyActivities', $loggedOnDate))
            ->when($request->input('logbook') === 'sudah', fn($q) => $q->whereHas('dailyActivities', $loggedOnDate))
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString();

        $students->getCollection()->each(function (User $student) {
            $start = $student->tickets->pluck('started_at')->filter()->min();
            $end = $student->tickets->pluck('completed_at')->filter()->max();

            $student->work_start = $start;
            $student->work_end = $end;
            $student->work_minutes = ($start && $end)
                ? (int) round($start->diffInMinutes($end, true))
                : null;
        });

        $today = today();

        return view('admin.DailyActivity.index', [
            'user'      => $request->user(),
            'date'      => $date,
            'prevDate'  => $date->copy()->subDay()->toDateString(),
            'nextDate'  => $date->lt($today) ? $date->copy()->addDay()->toDateString() : null,
            'isToday'   => $date->isSameDay($today),
            'students'  => $students,
            'divisions' => User::whereNotNull('division')->distinct()->orderBy('division')->pluck('division'),
            'stats'     => $this->stats($dayStart, $dayEnd, $date),
        ]);
    }

    public function show(Request $request, User $user)
    {
        $date = $this->resolveDate($request);

        $tickets = $this->scopeFinishedOn(
            Ticket::query()->where('user_id', $user->id),
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        )
            ->orderBy('completed_at')
            ->get();

        $activity = DailyActivity::where('user_id', $user->id)
            ->whereDate('activity_date', $date->toDateString())
            ->first();

        $tickets = $this->scopeFinishedOn(
            Ticket::query()->where('user_id', $user->id),
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        )
            ->with(['attachments', 'verifier'])
            ->orderBy('completed_at')
            ->get();

        return view('admin.DailyActivity.show', compact('user', 'date', 'tickets', 'activity'));
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'activity_date' => ['required', 'date', 'before_or_equal:today'],
            'description'   => ['required', 'string', 'max:5000'],
        ]);

        $date = Carbon::parse($data['activity_date'])->startOfDay();

        $hasFinishedTicket = $this->scopeFinishedOn(
            Ticket::query()->where('user_id', $user->id),
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay()
        )->exists();

        if (! $hasFinishedTicket) {
            return back()
                ->withErrors(['description' => 'Peserta belum memiliki tiket selesai pada tanggal ini.'])
                ->withInput();
        }

        DailyActivity::updateOrCreate(
            ['user_id' => $user->id, 'activity_date' => $date->toDateString()],
            ['description' => $data['description']],
        );

        return redirect()
            ->route('admin.daily-activity.show', [$user, 'date' => $date->toDateString()])
            ->with('success', 'Catatan harian disimpan.');
    }

    private function scopeFinishedOn($query, Carbon $start, Carbon $end)
    {
        return $query
            ->whereIn('status', Ticket::FINISHED)
            ->whereBetween('completed_at', [$start, $end]);
    }

    private function resolveDate(Request $request): Carbon
    {
        try {
            $date = Carbon::parse($request->input('date', today()))->startOfDay();
        } catch (\Throwable) {
            return today();
        }

        return $date->gt(today()) ? today() : $date;
    }

    private function stats(Carbon $dayStart, Carbon $dayEnd, Carbon $date): array
    {
        $perUser = $this->scopeFinishedOn(Ticket::query(), $dayStart, $dayEnd)
            ->groupBy('user_id')
            ->get([
                'user_id',
                DB::raw('COUNT(*) as ticket_count'),
                DB::raw('MIN(started_at) as first_start'),
                DB::raw('MAX(completed_at) as last_end'),
            ]);

        $minutes = $perUser
            ->filter(fn($r) => $r->first_start && $r->last_end)
            ->map(fn($r) => max(0, Carbon::parse($r->first_start)->diffInMinutes(Carbon::parse($r->last_end), true)));

        $activeIds = $perUser->pluck('user_id');

        $logbookFilled = DailyActivity::whereDate('activity_date', $date->toDateString())
            ->whereIn('user_id', $activeIds)
            ->count();

        return [
            'total_students'  => User::whereNotNull('nim')->count(),
            'active_students' => $activeIds->count(),
            'total_tickets'   => (int) $perUser->sum('ticket_count'),
            'avg_hours'       => round(($minutes->avg() ?? 0) / 60, 1),
            'logbook_filled'  => $logbookFilled,
            'logbook_missing' => $activeIds->count() - $logbookFilled,
        ];
    }
}
