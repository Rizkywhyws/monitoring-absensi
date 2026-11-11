<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketAdminController extends Controller
{
    private const TABS = [
        'verifikasi' => ['done'],
        'berjalan'   => ['on_progress'],
        'kendala'    => ['hold', 'revision'],
    ];

    public function index(Request $request)
    {
        $f = $request->validate([
            'q'        => ['nullable', 'string', 'max:100'],
            'tab'      => ['nullable', Rule::in(['semua', ...array_keys(self::TABS)])],
            'status'   => ['nullable', Rule::in(Ticket::STATUSES)],
            'division' => ['nullable', 'string', 'max:100'],
            'date'     => ['nullable', 'date'],
        ]);

        $term = trim($f['q'] ?? '');
        $like = '%' . addcslashes($term, '%_\\') . '%';

        $statuses = isset($f['status'])
            ? [$f['status']]
            : (self::TABS[$f['tab'] ?? ''] ?? null);

        $tickets = Ticket::query()
            ->with([
                'user:id,name,division',
                'verifier:id,name',
            ])
            ->withCount('attachments')
            ->when($statuses, fn ($q) => $q->whereIn('status', $statuses))
            ->when($f['division'] ?? null, fn ($q, $v) => $q->whereHas('user', fn ($u) => $u
                ->where(fn ($w) => $w
                    ->where('division', $v)
                    ->orWhereHas('group', fn ($g) => $g->where('name', $v)))))
            ->when($f['date'] ?? null, function ($q, $v) {
                $d = Carbon::parse($v);
                $q->whereBetween('created_at', [$d->copy()->startOfDay(), $d->copy()->endOfDay()]);
            })
            ->when($term !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('ticket_number', 'like', $like)
                ->orWhere('title', 'like', $like)
                ->orWhere('code', 'like', $like)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like))))
            ->orderByRaw("status = 'done' DESC")   
            ->latest()
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $counts = Ticket::query()
            ->selectRaw('status, COUNT(*) AS total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $divisions = User::query()
            ->whereNotNull('division')->where('division', '!=', '')
            ->distinct()->pluck('division')
            ->filter()->unique()->sort()->values();

        return view('admin.ticketsAdmin', [
            'tickets'   => $tickets,
            'counts'    => $counts,
            'total'     => (int) $counts->sum(),
            'filters'   => $f,
            'divisions' => $divisions,
        ]);
    }

    public function verify(Request $request, Ticket $ticket): RedirectResponse
    {
        $affected = Ticket::query()
            ->whereKey($ticket->id)
            ->where('status', 'done')
            ->update([
                'status'        => 'verified',
                'verified_by'   => $request->user()->id,
                'verified_at'   => now(),
                'revision_note' => null,
            ]);

        return $affected
            ? back()->with('success', "Tiket #{$ticket->ticket_number} terverifikasi.")
            : back()->withErrors(['status' => 'Hanya tiket berstatus Selesai yang bisa diverifikasi.']);
    }

    public function revision(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'revision_note' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $affected = Ticket::query()
            ->whereKey($ticket->id)
            ->where('status', 'done')
            ->update([
                'status'        => 'revision',
                'revision_note' => $data['revision_note'],
                'verified_by'   => null,
                'verified_at'   => null,
            ]);

        return $affected
            ? back()->with('success', "Tiket #{$ticket->ticket_number} dikembalikan untuk revisi.")
            : back()->withErrors(['status' => 'Hanya tiket berstatus Selesai yang bisa dikembalikan.']);
    }
}