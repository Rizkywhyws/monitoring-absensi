<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
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
            ->with('user:id,name', 'attachments')
            ->withCount('attachments')
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($query) use ($term) {
                    $query->where('ticket_number', 'like', "%{$term}%")
                        ->orWhere('title', 'like', "%{$term}%")
                        ->orWhereHas('user', fn($creator) => $creator->where('name', 'like', "%{$term}%"));
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => $scope()->count(),
            'today' => $scope()->whereDate('created_at', today())->count(),
            'with_attachment' => $scope()->has('attachments')->count(),
        ];

        return view('user.daftartTicket', compact('tickets', 'stats', 'search'));
    }

    public function create()
    {
        $nextNumber = sprintf('TKT-%05d', (Ticket::max('id') ?? 0) + 1);

        return view('user.createTicket', compact('nextNumber'));
    }

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();

        $ticket = DB::transaction(function () use ($request, $data) {
            $ticket = Ticket::create([
                'ticket_number' => 'TMP-' . Str::random(20),
                'user_id' => $request->user()->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => 'Umum',
                'priority' => 'medium',
                'status' => 'to_do',
            ]);

            $ticket->update([
                'ticket_number' => sprintf('TKT-%05d', $ticket->id),
            ]);

            foreach ($request->file('attachments', []) as $file) {
                $ticket->attachments()->create([
                    'file_name' => Str::limit($file->getClientOriginalName(), 255, ''),
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'file_path' => $file->store('ticket-attachments', 'public'),
                ]);
            }

            return $ticket;
        });

        return redirect()
            ->route('tickets')
            ->with('success', "Tiket #{$ticket->ticket_number} berhasil dibuat.");
    }

    /** Nama dan inisial user yang login, dipakai sidebar & header di layouts.app. */
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

        $ticket->load(['user:id,name', 'attachments']);

        return view('user.detailTicket', compact('ticket') + $this->layoutData($request));
    }
}
