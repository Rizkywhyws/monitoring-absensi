<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $search = trim((string) $request->query('q', ''));
        $term = ltrim($search, '#');

        $scope = fn() => Ticket::query()
            ->when($user->role !== 'admin', fn($query) => $query->where('user_id', $user->id));

        $tickets = $scope()
            ->with('user:id,name')
            ->when($term !== '', function ($query) use ($term) {
                $like = '%' . addcslashes($term, '%_\\') . '%';

                $query->where(function ($query) use ($like) {
                    $query->where('ticket_number', 'like', $like)
                        ->orWhere('title', 'like', $like)
                        ->orWhereHas('user', fn($creator) => $creator->where('name', 'like', $like));
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => $scope()->count(),
            'today' => $scope()->whereDate('created_at', today())->count(),
        ];

        return view('user.daftartTicket', compact('tickets', 'stats', 'search'));
    }

    public function create()
    {
        $nextNumber = Ticket::generateNumber();
        session(['pending_ticket_number' => $nextNumber]);

        $userId = auth()->id();

        $counts = Ticket::where('user_id', $userId)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $summary = [
            'to_do'       => (int) ($counts['to_do'] ?? 0),
            'on_progress' => (int) ($counts['on_progress'] ?? 0),
            'selesai'     => (int) collect(Ticket::FINISHED)->sum(fn($s) => $counts[$s] ?? 0),
        ];

        $activeTickets = Ticket::where('user_id', $userId)
            ->where('status', 'on_progress')
            ->latest()
            ->get(['id', 'ticket_number', 'title']);

        return view('user.createTicket', compact('nextNumber', 'activeTickets', 'summary'));
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        $number = session()->pull('pending_ticket_number');
        if (! $number || Ticket::where('ticket_number', $number)->exists()) {
            $number = Ticket::generateNumber();
        }

        $ticket = Ticket::create([
            'ticket_number' => $number,
            'user_id'       => $request->user()->id,
            'title'         => $data['title'],
            'category'      => 'Umum',
            'status'        => 'to_do',
        ]);

        return redirect()
            ->route('tickets')
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil dibuat.");
    }

    private function layoutData(Request $request): array
    {
        $name = $request->user()->name;

        return [
            'userName' => $name,
            'initials' => Str::upper(
                collect(explode(' ', trim($name)))
                    ->map(fn($word) => mb_substr($word, 0, 1))
                    ->take(2)
                    ->implode('')
            ),
        ];
    }

    public function show(Request $request, Ticket $ticket)
    {
        abort_unless(
            $request->user()->role === 'admin' || $ticket->user_id === $request->user()->id,
            403
        );

        $ticket->load('user:id,name');

        return view('user.detailTicket', compact('ticket') + $this->layoutData($request));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $isAdmin = $request->user()->role === 'admin';

        abort_unless($isAdmin || $ticket->user_id === $request->user()->id, 403);

        if (! $isAdmin && in_array($ticket->status, Ticket::FINISHED, true)) {
            return back()->withErrors(['status' => 'Tiket yang sudah selesai tidak bisa diedit.']);
        }

        $ticket->update([
            'title' => $request->validated()['title'],
        ]);

        return redirect()
            ->route('tickets')
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil diperbarui.");
    }

    public function start(Request $request, Ticket $ticket): RedirectResponse
    {
        $affected = Ticket::query()
            ->whereKey($ticket->id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['to_do', 'hold', 'revision'])
            ->update([
                'status'     => 'on_progress',
                'started_at' => $ticket->started_at ?? now(),
            ]);

        return $affected
            ? back()->with('success', "Tiket #{$ticket->ticket_number} dimulai.")
            : back()->withErrors(['status' => 'Tiket tidak bisa dimulai dari status saat ini.']);
    }

    public function complete(Request $request, Ticket $ticket): RedirectResponse
    {
        $affected = Ticket::query()
            ->whereKey($ticket->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'on_progress')
            ->update([
                'status'       => 'done',
                'completed_at' => now(),
            ]);

        return $affected
            ? back()->with('success', "Tiket #{$ticket->ticket_number} selesai dan masuk Daily Activity.")
            : back()->withErrors(['status' => 'Mulai tiket terlebih dahulu sebelum menyelesaikannya.']);
    }
}
