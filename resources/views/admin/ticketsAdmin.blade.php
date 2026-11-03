@extends('layouts.app')

@section('title', 'Monitoring Tiket')
@section('breadcrumb', 'Ticket & Penugasan / Monitoring Tiket')

@section('content')
@php
    $fmtMinutes = fn ($m) => $m === null ? '-' : intdiv($m, 60) . 'j ' . str_pad($m % 60, 2, '0', STR_PAD_LEFT) . 'm';

    $statusMeta = [
        'to_do'       => ['To Do',         'bg-surface-container-high text-on-surface-variant'],
        'on_progress' => ['On Progress',   'bg-secondary-fixed text-on-secondary-fixed'],
        'hold'        => ['Hold',          'bg-error-container text-on-error-container'],
        'revision'    => ['Revisi',        'bg-tertiary-fixed text-on-tertiary-fixed'],
        'done'        => ['Selesai (Menunggu Verifikasi)', 'bg-tertiary-fixed text-on-tertiary-fixed'],
        'verified'    => ['Terverifikasi', 'bg-primary-fixed text-on-primary-fixed'],
        'cancelled'   => ['Dibatalkan',    'bg-surface-container-high text-on-surface-variant'],
    ];

    $kpis = [
        ['Total Ticket',      $total,                       'text-on-surface'],
        ['To Do',             $counts['to_do'] ?? 0,        'text-on-surface'],
        ['On Progress',       $counts['on_progress'] ?? 0,  'text-secondary'],
        ['Butuh Verifikasi',  $counts['done'] ?? 0,         'text-tertiary'],
        ['Hold',              $counts['hold'] ?? 0,         'text-error'],
        ['Revision',          $counts['revision'] ?? 0,     'text-on-surface'],
        ['Verified',          $counts['verified'] ?? 0,     'text-primary'],
    ];

    $tabs = [
        'semua'      => ['Semua Ticket', $total],
        'verifikasi' => ['Butuh Verifikasi', $counts['done'] ?? 0],
        'berjalan'   => ['Sedang Dikerjakan', $counts['on_progress'] ?? 0],
        'kendala'    => ['Ada Kendala', ($counts['hold'] ?? 0) + ($counts['revision'] ?? 0)],
    ];
    $activeTab = $filters['tab'] ?? 'semua';
@endphp

<div class="flex flex-col w-full gap-space-lg">

    @if ($errors->any())
        <div class="flex items-center gap-3 rounded-xl bg-error-container text-on-error-container px-4 py-3 font-label-lg text-label-lg">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="flex flex-col gap-space-2xs">
        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Monitoring &amp; Verifikasi Ticket PKL</h1>
        <p class="font-body-sm text-body-sm text-on-surface-variant max-w-3xl">
            Pantau tiket penugasan peserta, verifikasi hasil pekerjaan, atau kembalikan untuk revisi.
            Tiket yang diselesaikan peserta langsung tampil di Daily Activity.
        </p>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-space-sm">
        @foreach ($kpis as [$label, $value, $color])
            <div class="flex flex-col p-space-md rounded-xl bg-surface-container-lowest shadow-sm">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">{{ $label }}</span>
                <span class="mt-space-xs font-stat-counter text-stat-counter font-bold leading-none {{ $color }}">{{ $value }}</span>
            </div>
        @endforeach
    </div>

    {{-- Tab + filter --}}
    <div class="flex flex-col gap-space-sm p-space-md rounded-xl bg-surface-container-lowest shadow-sm">
        <div class="flex items-center gap-space-2xs overflow-x-auto pb-space-xs">
            @foreach ($tabs as $key => [$label, $count])
                <a href="{{ request()->fullUrlWithQuery(['tab' => $key, 'status' => null, 'page' => null]) }}"
                   class="flex items-center gap-space-xs px-space-md py-space-xs rounded-lg font-label-md text-label-md whitespace-nowrap transition-colors
                          {{ $activeTab === $key ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container font-semibold' }}">
                    <span>{{ $label }}</span>
                    <span class="px-space-2xs py-0.5 rounded-full bg-surface-container-high text-on-surface font-label-sm text-label-sm">{{ $count }}</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.tickets.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-space-sm">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <input name="q" type="text" value="{{ $filters['q'] ?? '' }}" autocomplete="off"
                   placeholder="Cari nomor tiket, judul, atau nama peserta..."
                   class="md:col-span-4 w-full px-space-md py-space-xs rounded-lg bg-surface-container-low text-on-surface placeholder:text-outline font-body-sm text-body-sm border-0 focus:ring-2 focus:ring-primary/20">

            <select name="division" onchange="this.form.submit()" class="md:col-span-2 rounded-lg bg-surface-container-low font-body-sm text-body-sm border-0">
                <option value="">Semua Divisi</option>
                @foreach ($divisions as $division)
                    <option value="{{ $division }}" @selected(($filters['division'] ?? '') === $division)>{{ $division }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="md:col-span-2 rounded-lg bg-surface-container-low font-body-sm text-body-sm border-0">
                <option value="">Semua Status</option>
                @foreach ($statusMeta as $key => [$label])
                    <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <input name="date" type="date" value="{{ $filters['date'] ?? '' }}" onchange="this.form.submit()"
                   class="md:col-span-2 rounded-lg bg-surface-container-low font-body-sm text-body-sm border-0">

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="px-space-md py-space-xs rounded-lg bg-primary text-on-primary font-label-md text-label-md">Cari</button>
            </div>
        </form>

        @if (request()->hasAny(['q', 'status', 'division', 'date']))
            <a href="{{ route('admin.tickets.index', ['tab' => $activeTab]) }}" class="text-sm text-on-surface-variant underline w-max">Reset filter</a>
        @endif
    </div>

    {{-- Tabel --}}
    <div class="flex flex-col bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-body-sm text-body-sm">
                <thead class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                    <tr>
                        <th class="py-space-sm px-space-md">Tiket</th>
                        <th class="py-space-sm px-space-md min-w-[280px]">Pekerjaan</th>
                        <th class="py-space-sm px-space-md min-w-[200px]">Peserta &amp; Divisi</th>
                        <th class="py-space-sm px-space-md min-w-[220px]">Status</th>
                        <th class="py-space-sm px-space-md">Waktu Kerja</th>
                        <th class="py-space-sm px-space-md text-right min-w-[220px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                @forelse ($tickets as $ticket)
                    @php
                        [$sLabel, $sClass] = $statusMeta[$ticket->status] ?? [$ticket->status, $statusMeta['to_do'][1]];
                        $isDone = $ticket->status === 'done';
                        $divisionName = $ticket->user?->division ?: ($ticket->user?->group?->name ?: '-');
                    @endphp
                    <tr class="align-top hover:bg-surface-container-low transition-colors {{ $isDone ? 'bg-tertiary-fixed/10' : '' }}">
                        <td class="py-space-md px-space-md">
                            <span class="font-label-md text-label-md font-bold text-primary font-mono bg-primary-fixed/40 px-space-xs py-0.5 rounded">
                                {{ $ticket->ticket_number }}
                            </span>
                            <p class="mt-1 text-[11px] text-on-surface-variant">{{ $ticket->created_at->translatedFormat('d M Y, H:i') }}</p>
                        </td>

                        <td class="py-space-md px-space-md">
                            <p class="font-headline-sm text-headline-sm text-on-surface font-semibold leading-snug">{{ $ticket->title }}</p>
                            <p class="mt-1 line-clamp-2 max-w-md text-on-surface-variant">{{ \Illuminate\Support\Str::limit($ticket->description, 140) }}</p>
                            @if ($ticket->attachments_count > 0)
                                <span class="mt-1 inline-flex items-center gap-1 text-label-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">attach_file</span>{{ $ticket->attachments_count }} lampiran
                                </span>
                            @endif
                        </td>

                        <td class="py-space-md px-space-md">
                            <p class="font-label-md text-label-md text-on-surface font-semibold">{{ $ticket->user->name ?? '-' }}</p>
                            <p class="text-primary font-medium">{{ $divisionName }}</p>
                            @if ($ticket->user?->posko)
                                <p class="text-[11px] text-on-surface-variant">{{ $ticket->user->posko }}</p>
                            @endif
                        </td>

                        <td class="py-space-md px-space-md">
                            <span class="inline-block px-space-sm py-1 rounded-full font-label-sm text-label-sm font-bold {{ $sClass }}">{{ $sLabel }}</span>

                            @if ($ticket->status === 'revision' && $ticket->revision_note)
                                <p class="mt-1 text-error font-medium">Catatan: {{ $ticket->revision_note }}</p>
                            @elseif ($ticket->status === 'verified' && $ticket->verified_at)
                                <p class="mt-1 text-primary font-medium">
                                    {{ $ticket->verifier->name ?? 'Admin' }} · {{ $ticket->verified_at->translatedFormat('d M, H:i') }}
                                </p>
                            @endif
                        </td>

                        <td class="py-space-md px-space-md whitespace-nowrap">
                            <p class="font-label-md text-label-md text-on-surface font-semibold">{{ $fmtMinutes($ticket->duration_minutes) }}</p>
                            @if ($ticket->started_at)
                                <p class="text-[11px] text-on-surface-variant">Mulai {{ $ticket->started_at->format('d M, H:i') }}</p>
                            @endif
                            @if ($ticket->completed_at && in_array($ticket->status, \App\Models\Ticket::FINISHED, true))
                                <p class="text-[11px] text-on-surface-variant">Selesai {{ $ticket->completed_at->format('d M, H:i') }}</p>
                            @endif
                        </td>

                        <td class="py-space-md px-space-md text-right">
                            <div class="inline-flex flex-wrap items-center justify-end gap-space-xs">
                                @if ($isDone)
                                    <form method="POST" action="{{ route('admin.tickets.verify', $ticket) }}"
                                          onsubmit="return confirm('Verifikasi tiket {{ $ticket->ticket_number }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="flex items-center gap-1 px-space-sm py-1 rounded-md bg-primary hover:bg-primary-container text-on-primary font-label-sm text-label-sm font-bold shadow-sm transition-all">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>Verifikasi
                                        </button>
                                    </form>
                                    <button type="button"
                                            data-url="{{ route('admin.tickets.revision', $ticket) }}"
                                            data-number="{{ $ticket->ticket_number }}"
                                            onclick="openRevision(this)"
                                            class="flex items-center gap-1 px-space-sm py-1 rounded-md bg-surface-container hover:bg-surface-container-high text-on-surface font-label-sm text-label-sm font-semibold transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">replay</span>Revisi
                                    </button>
                                @endif
                                <a href="{{ route('ticket.show', $ticket) }}" title="Detail tiket"
                                   class="p-1 rounded hover:bg-surface-container text-on-surface-variant hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 px-space-md text-center text-on-surface-variant">
                            Tidak ada tiket yang cocok dengan filter ini.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-space-md gap-space-sm border-t border-surface-container">
                <div class="text-on-surface-variant font-body-sm text-body-sm">
                    Menampilkan <span class="font-bold text-on-surface">{{ $tickets->firstItem() }} - {{ $tickets->lastItem() }}</span>
                    dari <span class="font-bold text-on-surface">{{ $tickets->total() }}</span> tiket
                </div>
                @if ($tickets->hasPages())
                    <div>{{ $tickets->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</div>

<dialog id="revision-dialog" class="w-full max-w-md rounded-xl p-0 shadow-xl backdrop:bg-black/40">
    <form id="revision-form" method="POST" class="p-space-lg space-y-space-md">
        @csrf
        @method('PATCH')
        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">
            Minta revisi <span id="revision-number" class="font-mono text-primary"></span>
        </h2>
        <div class="space-y-1.5">
            <label for="revision-note" class="font-label-md text-label-md text-on-surface font-medium">Catatan untuk peserta</label>
            <textarea id="revision-note" name="revision_note" rows="4" required minlength="5" maxlength="1000"
                      placeholder="Contoh: foto hasil pekerjaan kurang jelas, lengkapi dokumentasi."
                      class="w-full rounded-lg bg-surface-container-low font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary resize-none"></textarea>
        </div>
        <div class="flex justify-end gap-space-sm">
            <button type="button" onclick="document.getElementById('revision-dialog').close()"
                    class="px-space-md py-space-xs rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg">Batal</button>
            <button type="submit"
                    class="px-space-md py-space-xs rounded-lg bg-primary text-on-primary font-label-lg text-label-lg">Kirim revisi</button>
        </div>
    </form>
</dialog>
@endsection

@section('extra-scripts')
<script>
    function openRevision(btn) {
        const dialog = document.getElementById('revision-dialog');
        document.getElementById('revision-form').action = btn.dataset.url;
        document.getElementById('revision-number').textContent = '#' + btn.dataset.number;
        document.getElementById('revision-note').value = '';
        dialog.showModal();
    }
</script>
@endsection