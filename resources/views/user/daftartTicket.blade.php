@extends('layouts.app')

@section('title', 'Daftar Tiket Penugasan')
@section('breadcrumb', 'Tiket / Daftar Tiket Penugasan')

@section('extra-styles')
    <style>
        .modal-backdrop.is-open {
            animation: modal-fade-in .2s ease-out both;
        }
        .modal-backdrop.is-open .modal-panel {
            animation: modal-panel-in .28s cubic-bezier(.16, 1, .3, 1) both;
        }
        .modal-backdrop.is-closing {
            animation: modal-fade-out .16s ease-in both;
        }

        .modal-backdrop.is-closing .modal-panel {
            animation: modal-panel-out .16s ease-in both;
        }

        @keyframes modal-fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes modal-fade-out {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        @keyframes modal-panel-in {
            from { opacity: 0; transform: translateY(24px) scale(.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes modal-panel-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(12px) scale(.97); }
        }

        /* Animasi centang */
        .check-wrap.is-playing {
            animation: check-pop .45s cubic-bezier(.34, 1.56, .64, 1) both;
        }
        .check-wrap.is-playing .check-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            animation: check-stroke .55s cubic-bezier(.65, 0, .45, 1) forwards;
        }
        .check-wrap.is-playing .check-mark {
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: check-stroke .35s .5s cubic-bezier(.65, 0, .45, 1) forwards;
        }

        @keyframes check-stroke {
            to { stroke-dashoffset: 0; }
        }

        @keyframes check-pop {
            0% { transform: scale(.6); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .modal-backdrop,
            .modal-backdrop .modal-panel,
            .check-wrap.is-playing,
            .check-wrap.is-playing .check-circle,
            .check-wrap.is-playing .check-mark {
                animation: none !important;
                stroke-dashoffset: 0 !important;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isAdmin = auth()->user()->role === 'admin';

        $percentOf = fn($value) => $stats['total'] > 0 ? round(($value / $stats['total']) * 100) : 0;

        $initialsOf = fn($name) => \Illuminate\Support\Str::upper(
            collect(explode(' ', trim($name)))
                ->map(fn($word) => mb_substr($word, 0, 1))
                ->take(2)
                ->implode(''),
        );

        // Label + warna badge per status. Satu sumber untuk seluruh view.
        $statusMeta = [
            'to_do'       => ['label' => 'To Do',       'class' => 'bg-surface-container-high text-on-surface-variant'],
            'on_progress' => ['label' => 'On Progress', 'class' => 'bg-amber-50 text-tertiary'],
            'hold'        => ['label' => 'Hold',        'class' => 'bg-surface-container-high text-on-surface-variant'],
            'revision'    => ['label' => 'Revisi',      'class' => 'bg-error-container text-on-error-container'],
            'done'        => ['label' => 'Selesai',     'class' => 'bg-primary/10 text-primary'],
            'verified'    => ['label' => 'Terverifikasi', 'class' => 'bg-primary/10 text-primary'],
            'cancelled'   => ['label' => 'Dibatalkan',  'class' => 'bg-surface-container-high text-on-surface-variant'],
        ];

        $cardClass =
            'relative overflow-hidden rounded-xl bg-surface-container-lowest p-5 shadow-sm flex flex-col justify-between gap-4 group hover:shadow-md transition-shadow';

        $ticketsData = $tickets->mapWithKeys(function ($ticket) use ($initialsOf, $isAdmin, $statusMeta) {
            $isFinished = in_array($ticket->status, \App\Models\Ticket::FINISHED, true);
            $isOwner = $ticket->user_id === auth()->id();

            return [
                $ticket->id => [
                    'number' => $ticket->ticket_number,
                    'title' => $ticket->title,
                    'creator' => $ticket->user->name ?? '-',
                    'initials' => $initialsOf($ticket->user->name ?? '-'),
                    'createdAt' => $ticket->created_at->translatedFormat('d M Y, H:i'),
                    'status' => $statusMeta[$ticket->status]['label'] ?? $ticket->status,
                    'canEdit' => $isAdmin || ($isOwner && ! $isFinished),
                ],
            ];
        });
    @endphp

    <div class="flex flex-col w-full gap-space-lg">

        @if (session('success'))
            <div class="flex items-center gap-3 rounded-xl bg-primary/10 text-primary px-4 py-3 font-label-lg text-label-lg">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-center gap-3 rounded-xl bg-error-container text-on-error-container px-4 py-3 font-label-lg text-label-lg">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <nav aria-label="Breadcrumb"
            class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
            <a class="hover:text-primary transition-colors" href="{{ route('dashboard') }}">Presensi &amp; Penugasan</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('tickets') }}">Tiket</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <span class="font-semibold text-primary">Daftar Tiket Penugasan</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
            <div class="space-y-1">
                <div class="flex items-center gap-space-xs">
                    <span
                        class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm uppercase tracking-wider">Tiket
                        Lapangan</span>
                    <span class="text-outline-variant text-body-sm">•</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Kantor Perwakilan
                        Jember</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Tiket Penugasan
                </h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                    Daftar instruksi kerja teknis, pemasangan, dan pemeliharaan kabel fiber optik serta perangkat
                    Iconnet. Tiket yang diterbitkan dari formulir akan tampil di sini.
                </p>
            </div>
            <div class="flex items-center gap-space-sm self-start lg:self-center">
                <a href="{{ route('ticket.create') }}"
                    class="inline-flex items-center gap-2 h-[42px] px-5 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm hover:bg-primary transition-all active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Buat Ticket</span>
                </a>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
            <div class="{{ $cardClass }}">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Total Penugasan</span>
                    <div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[22px]">confirmation_number</span>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="font-stat-counter text-stat-counter text-on-surface">{{ $stats['total'] }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant ml-1">Tiket</span>
                    </div>
                    <span
                        class="px-2 py-1 rounded font-label-sm text-label-sm bg-surface-container-low text-on-surface-variant font-semibold">Semua
                        Waktu</span>
                </div>
                <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <div class="{{ $cardClass }}">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Dibuat Hari Ini</span>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-tertiary-container">
                        <span class="material-symbols-outlined text-[22px]">today</span>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="font-stat-counter text-stat-counter text-on-surface">{{ $stats['today'] }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant ml-1">Tiket Baru</span>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded font-label-sm text-label-sm bg-amber-50 text-tertiary font-semibold">{{ now()->translatedFormat('d M') }}</span>
                </div>
                <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                    <div class="bg-tertiary-container h-full rounded-full"
                        style="width: {{ $percentOf($stats['today']) }}%"></div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('tickets') }}"
            class="rounded-xl bg-surface-container-lowest shadow-sm p-4 flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <span
                    class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input name="q" type="text" value="{{ $search }}" autocomplete="off"
                    class="w-full h-10 pl-11 pr-4 bg-surface-container-low text-on-surface placeholder:text-outline font-body-sm text-body-sm rounded-lg border-0 focus:outline-none focus:ring-2 focus:ring-primary-container transition-all"
                    placeholder="Cari nomor tiket, judul pekerjaan, atau nama pembuat..." />
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                    class="h-10 px-5 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm hover:bg-primary transition-all active:scale-[0.98]">
                    Cari
                </button>
                @if ($search !== '')
                    <a href="{{ route('tickets') }}"
                        class="inline-flex items-center h-10 px-4 rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                            <th class="py-3.5 px-4 font-semibold w-44">Nomor Tiket</th>
                            <th class="py-3.5 px-4 font-semibold min-w-[320px]">Detail Pekerjaan</th>
                            <th class="py-3.5 px-4 font-semibold min-w-[180px]">Pembuat</th>
                            <th class="py-3.5 px-4 font-semibold text-right w-64">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        @forelse ($tickets as $ticket)
                            @php
                                $meta = $statusMeta[$ticket->status] ?? ['label' => $ticket->status, 'class' => 'bg-surface-container-high text-on-surface-variant'];
                                $isOwner = $ticket->user_id === auth()->id();
                                $isFinished = in_array($ticket->status, \App\Models\Ticket::FINISHED, true);
                                $canStart = $isOwner && in_array($ticket->status, ['to_do', 'hold', 'revision'], true);
                                $canComplete = $isOwner && $ticket->status === 'on_progress';
                                $canEdit = $isAdmin || ($isOwner && ! $isFinished);
                            @endphp
                            <tr class="hover:bg-surface-container-low/60 transition-colors group">
                                <td class="py-4 px-4 align-top">
                                    <div class="flex flex-col gap-1">
                                        <button type="button" onclick="openTicketModal({{ $ticket->id }})"
                                            class="text-left font-label-lg text-label-lg font-bold text-primary hover:underline tracking-tight flex items-center gap-1">
                                            #{{ $ticket->ticket_number }}
                                        </button>
                                        <span class="font-body-sm text-[11px] text-on-surface-variant">
                                            {{ $ticket->created_at->translatedFormat('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <div class="font-headline-sm text-headline-sm text-on-surface font-semibold">
                                            {{ $ticket->title }}
                                        </div>
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold {{ $meta['class'] }}">
                                            {{ $meta['label'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-primary-container/20 text-primary font-bold text-xs flex items-center justify-center shrink-0">
                                            {{ $initialsOf($ticket->user->name ?? '-') }}</div>
                                        <div class="flex flex-col">
                                            <span
                                                class="font-label-md text-label-md text-on-surface font-semibold leading-tight">{{ $ticket->user->name ?? '-' }}</span>
                                            <span
                                                class="font-body-sm text-[11px] text-on-surface-variant leading-none mt-0.5">Peserta
                                                PKL</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 align-top text-right">
                                    <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                        @if ($canStart)
                                            <form method="POST" action="{{ route('ticket.start', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 h-[34px] px-3.5 rounded-lg bg-amber-50 text-tertiary font-label-sm text-label-sm font-semibold hover:bg-amber-100 transition-all">
                                                    <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                                                    <span>Mulai</span>
                                                </button>
                                            </form>
                                        @elseif ($canComplete)
                                            <form id="complete-form-{{ $ticket->id }}" method="POST"
                                                action="{{ route('ticket.complete', $ticket) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" onclick="openCompleteModal({{ $ticket->id }})"
                                                    class="inline-flex items-center gap-1 h-[34px] px-3.5 rounded-lg bg-primary text-on-primary font-label-sm text-label-sm font-semibold hover:bg-primary-container transition-all">
                                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                                    <span>Selesai</span>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" onclick="openTicketModal({{ $ticket->id }})"
                                            class="inline-flex items-center gap-1 h-[34px] px-3.5 rounded-lg bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold hover:bg-surface-container-high transition-all">
                                            <span>Detail</span>
                                            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                        </button>

                                        @if ($canEdit)
                                            <button type="button" onclick="openEditModal({{ $ticket->id }})"
                                                class="inline-flex items-center gap-1 h-[34px] px-3.5 rounded-lg bg-primary/10 text-primary font-label-sm text-label-sm font-semibold hover:bg-primary/20 transition-all">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                <span>Edit</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 px-4 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <span
                                            class="material-symbols-outlined text-[40px] text-outline-variant">confirmation_number</span>
                                        @if ($search !== '')
                                            <span class="font-headline-sm text-headline-sm text-on-surface">Tiket tidak
                                                ditemukan</span>
                                            <span class="font-body-sm text-body-sm text-on-surface-variant">Tidak ada
                                                tiket yang cocok dengan "{{ $search }}".</span>
                                        @else
                                            <span class="font-headline-sm text-headline-sm text-on-surface">Belum ada
                                                tiket penugasan</span>
                                            <span class="font-body-sm text-body-sm text-on-surface-variant">Tiket yang
                                                kamu buat akan muncul di sini.</span>
                                            <a href="{{ route('ticket.create') }}"
                                                class="mt-2 inline-flex items-center gap-1 h-[34px] px-3.5 rounded-lg bg-primary-container text-on-primary font-label-sm text-label-sm font-semibold hover:bg-primary transition-all">
                                                <span class="material-symbols-outlined text-[16px]">add</span>
                                                <span>Buat Tiket Pertama</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tickets->total() > 0)
                <div
                    class="px-5 py-3.5 bg-surface-container-lowest border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="font-body-sm text-body-sm text-on-surface-variant">
                        Menampilkan <span class="font-semibold text-on-surface">{{ $tickets->firstItem() }} -
                            {{ $tickets->lastItem() }}</span> dari <span
                            class="font-semibold text-on-surface">{{ $tickets->total() }}</span> tiket penugasan
                    </div>
                    @if ($tickets->hasPages())
                        <div>{{ $tickets->links() }}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Detail --}}
    <div id="ticket-modal-backdrop"
        class="modal-backdrop fixed inset-0 z-50 bg-inverse-surface/50 hidden items-center justify-center p-4"
        onclick="if (event.target === this) closeTicketModal()">
        <div class="modal-panel w-full max-w-lg max-h-[85vh] overflow-y-auto rounded-xl bg-surface-container-lowest shadow-xl">
            <div
                class="sticky top-0 bg-surface-container-lowest flex items-start justify-between gap-space-md p-space-lg border-b border-surface-container">
                <div class="space-y-1 min-w-0">
                    <span id="modal-number"
                        class="inline-block px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-mono"></span>
                    <h2 id="modal-title" class="font-headline-sm text-headline-sm text-on-surface font-bold"></h2>
                </div>
                <button type="button" onclick="closeTicketModal()" title="Tutup"
                    class="w-9 h-9 rounded-lg bg-surface-container text-on-surface-variant hover:bg-surface-container-high flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="p-space-lg">
                <div class="flex items-center gap-2.5">
                    <div id="modal-initials"
                        class="w-9 h-9 rounded-full bg-primary-container/20 text-primary font-bold text-sm flex items-center justify-center shrink-0">
                    </div>
                    <div class="flex flex-col">
                        <span id="modal-creator"
                            class="font-label-md text-label-md text-on-surface font-semibold leading-tight"></span>
                        <span id="modal-date" class="font-body-sm text-[11px] text-on-surface-variant"></span>
                    </div>
                    <span id="modal-status"
                        class="ml-auto px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="edit-modal-backdrop"
        class="modal-backdrop fixed inset-0 z-50 bg-inverse-surface/50 hidden items-center justify-center p-4"
        onclick="if (event.target === this) closeEditModal()">
        <div class="modal-panel w-full max-w-lg max-h-[85vh] overflow-y-auto rounded-xl bg-surface-container-lowest shadow-xl">
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')

                <div
                    class="sticky top-0 bg-surface-container-lowest flex items-start justify-between gap-space-md p-space-lg border-b border-surface-container">
                    <div class="space-y-1 min-w-0">
                        <span id="edit-modal-number"
                            class="inline-block px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-mono"></span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Edit Tiket</h2>
                    </div>
                    <button type="button" onclick="closeEditModal()" title="Tutup"
                        class="w-9 h-9 rounded-lg bg-surface-container text-on-surface-variant hover:bg-surface-container-high flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-space-lg">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="font-label-md text-label-md text-on-surface font-medium"
                                for="edit-title">Judul Pekerjaan / Tiket Penugasan</label>
                            <span class="font-label-sm text-label-sm text-on-surface-variant"><span
                                    id="edit-title-count">0</span>/100 karakter</span>
                        </div>
                        <input id="edit-title" name="title" type="text" maxlength="100" required
                            class="w-full h-11 px-space-sm rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary outline-none transition-all shadow-sm" />
                    </div>
                </div>

                <div
                    class="sticky bottom-0 bg-surface-container-lowest p-space-lg border-t border-surface-container flex items-center justify-end gap-space-sm">
                    <button type="button" onclick="closeEditModal()"
                        class="h-10 px-4 rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="h-10 px-5 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm hover:bg-primary transition-all active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konfirmasi Selesai --}}
    <div id="complete-modal-backdrop"
        class="modal-backdrop fixed inset-0 z-50 bg-inverse-surface/50 hidden items-center justify-center p-4"
        onclick="if (event.target === this) closeCompleteModal()">
        <div class="modal-panel w-full max-w-sm rounded-xl bg-surface-container-lowest shadow-xl overflow-hidden">

            {{-- Tahap 1: konfirmasi --}}
            <div id="complete-confirm-state" class="p-space-xl text-center space-y-space-md">
                <div
                    class="w-16 h-16 mx-auto rounded-full bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[34px]">help</span>
                </div>
                <div class="space-y-1.5">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Selesaikan tiket ini?</h2>
                    <p id="complete-ticket-label"
                        class="font-label-md text-label-md text-on-surface font-semibold break-words"></p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        Tiket akan ditandai selesai dan otomatis masuk ke rekap Daily Activity.
                    </p>
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <button type="button" onclick="closeCompleteModal()"
                        class="flex-1 h-11 rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all">
                        Batal
                    </button>
                    <button type="button" id="complete-confirm-btn" onclick="confirmComplete()"
                        class="flex-1 h-11 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg font-bold shadow-sm hover:bg-primary transition-all active:scale-[0.98]">
                        Ya, selesaikan
                    </button>
                </div>
            </div>

            {{-- Tahap 2: animasi centang --}}
            <div id="complete-success-state" class="hidden p-space-xl text-center space-y-space-md">
                <div id="check-wrap" class="check-wrap w-20 h-20 mx-auto">
                    <svg viewBox="0 0 52 52" class="w-full h-full" fill="none" stroke-width="3"
                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle class="check-circle" cx="26" cy="26" r="25" stroke="#047857" />
                        <path class="check-mark" d="M14 27 l8 8 l16 -16" stroke="#047857" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Tiket selesai!</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Menyimpan dan mengalihkan...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script>
        const ticketsData = @json($ticketsData);
        const ticketUpdateBaseUrl = @json(url('/tickets'));
        const MODAL_CLOSE_MS = 160;
        const CHECK_ANIMATION_MS = 1100;

        function showModal(el) {
            clearTimeout(el._closeTimer);
            el.classList.remove('hidden', 'is-closing');
            el.classList.add('flex', 'is-open');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(el) {
            if (el.classList.contains('hidden') || el.classList.contains('is-closing')) return;
            el.classList.remove('is-open');
            el.classList.add('is-closing');
            el._closeTimer = setTimeout(function() {
                el.classList.remove('flex', 'is-closing');
                el.classList.add('hidden');
                document.body.style.overflow = '';
            }, MODAL_CLOSE_MS);
        }

        /* ---------- Modal detail ---------- */
        const modalBackdrop = document.getElementById('ticket-modal-backdrop');
        const modalNumber = document.getElementById('modal-number');
        const modalTitle = document.getElementById('modal-title');
        const modalStatus = document.getElementById('modal-status');
        const modalInitials = document.getElementById('modal-initials');
        const modalCreator = document.getElementById('modal-creator');
        const modalDate = document.getElementById('modal-date');

        function openTicketModal(ticketId) {
            const ticket = ticketsData[ticketId];
            if (!ticket) return;

            modalNumber.textContent = '#' + ticket.number;
            modalTitle.textContent = ticket.title;
            modalStatus.textContent = ticket.status;
            modalInitials.textContent = ticket.initials;
            modalCreator.textContent = ticket.creator;
            modalDate.textContent = ticket.createdAt;

            showModal(modalBackdrop);
        }

        function closeTicketModal() {
            hideModal(modalBackdrop);
        }

        /* ---------- Modal edit ---------- */
        const editBackdrop = document.getElementById('edit-modal-backdrop');
        const editForm = document.getElementById('edit-form');
        const editNumber = document.getElementById('edit-modal-number');
        const editTitleInput = document.getElementById('edit-title');
        const editTitleCount = document.getElementById('edit-title-count');

        function openEditModal(ticketId) {
            const ticket = ticketsData[ticketId];
            if (!ticket || !ticket.canEdit) return;

            editForm.action = `${ticketUpdateBaseUrl}/${ticketId}`;
            editForm.reset();

            editNumber.textContent = '#' + ticket.number;
            editTitleInput.value = ticket.title;
            editTitleCount.textContent = ticket.title.length;

            showModal(editBackdrop);
        }

        function closeEditModal() {
            hideModal(editBackdrop);
        }

        editTitleInput.addEventListener('input', function() {
            editTitleCount.textContent = editTitleInput.value.length;
        });

        /* ---------- Modal konfirmasi selesai ---------- */
        const completeBackdrop = document.getElementById('complete-modal-backdrop');
        const completeConfirmState = document.getElementById('complete-confirm-state');
        const completeSuccessState = document.getElementById('complete-success-state');
        const completeLabel = document.getElementById('complete-ticket-label');
        const completeConfirmBtn = document.getElementById('complete-confirm-btn');
        const checkWrap = document.getElementById('check-wrap');

        let completeTicketId = null;
        let completeSubmitting = false;

        function openCompleteModal(ticketId) {
            const ticket = ticketsData[ticketId];
            if (!ticket) return;

            completeTicketId = ticketId;
            completeSubmitting = false;

            completeLabel.textContent = '#' + ticket.number + ' · ' + ticket.title;
            completeConfirmBtn.disabled = false;
            checkWrap.classList.remove('is-playing');
            completeSuccessState.classList.add('hidden');
            completeConfirmState.classList.remove('hidden');

            showModal(completeBackdrop);
        }

        function closeCompleteModal() {
            // Saat animasi berjalan dan form sedang dikirim, modal tidak boleh ditutup.
            if (completeSubmitting) return;
            hideModal(completeBackdrop);
        }

        function confirmComplete() {
            if (completeSubmitting || completeTicketId === null) return;

            const form = document.getElementById('complete-form-' + completeTicketId);
            if (!form) return;

            completeSubmitting = true;
            completeConfirmBtn.disabled = true;

            completeConfirmState.classList.add('hidden');
            completeSuccessState.classList.remove('hidden');

            // Paksa reflow supaya animasi centang selalu diputar ulang dari awal.
            checkWrap.classList.remove('is-playing');
            void checkWrap.offsetWidth;
            checkWrap.classList.add('is-playing');

            setTimeout(function() {
                form.submit();
            }, CHECK_ANIMATION_MS);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key !== 'Escape') return;
            closeTicketModal();
            closeEditModal();
            closeCompleteModal();
        });
    </script>
@endsection