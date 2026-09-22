@extends('layouts.app')

@section('title', 'Daftar Tiket Penugasan')
@section('breadcrumb', 'Tiket / Daftar Tiket Penugasan')

@section('content')
    @php
        $percentOf = fn($value) => $stats['total'] > 0 ? round(($value / $stats['total']) * 100) : 0;

        $initialsOf = fn($name) => \Illuminate\Support\Str::upper(
            collect(explode(' ', trim($name)))
                ->map(fn($word) => mb_substr($word, 0, 1))
                ->take(2)
                ->implode(''),
        );

        $formatSize = fn($bytes) => $bytes >= 1048576
            ? number_format($bytes / 1048576, 1) . ' MB'
            : max(1, round($bytes / 1024)) . ' KB';

        $cardClass =
            'relative overflow-hidden rounded-xl bg-surface-container-lowest p-5 shadow-sm flex flex-col justify-between gap-4 group hover:shadow-md transition-shadow';

        // Data setiap tiket disiapkan di sini supaya modal detail tidak perlu request baru saat dibuka.
        $ticketsData = $tickets->mapWithKeys(function ($ticket) use ($initialsOf, $formatSize) {
            return [
                $ticket->id => [
                    'number' => $ticket->ticket_number,
                    'title' => $ticket->title,
                    'description' => $ticket->description,
                    'creator' => $ticket->user->name ?? '-',
                    'initials' => $initialsOf($ticket->user->name ?? '-'),
                    'createdAt' => $ticket->created_at->translatedFormat('d M Y, H:i'),
                    'attachments' => $ticket->attachments->map(fn($attachment) => [
                        'name' => $attachment->file_name,
                        'size' => $formatSize($attachment->file_size),
                        'isImage' => \Illuminate\Support\Str::startsWith($attachment->file_type, 'image/'),
                        'url' => $attachment->url,
                    ]),
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

        <nav aria-label="Breadcrumb"
            class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
            <a class="hover:text-primary transition-colors" href="{{ route('dashboard') }}">Presensi &amp; Penugasan</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('tickets') }}">Tiket</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <span class="font-semibold text-primary">Daftar Tiket Penugasan</span>
        </nav>

        {{-- Header halaman --}}
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
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Tiket Penugasan Lapangan
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
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

            <div class="{{ $cardClass }}">
                <div class="flex items-center justify-between">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium">Memiliki Lampiran</span>
                    <div class="w-10 h-10 rounded-lg bg-secondary-fixed/50 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[22px]">attach_file</span>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <div>
                        <span
                            class="font-stat-counter text-stat-counter text-on-surface">{{ $stats['with_attachment'] }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant ml-1">Tiket</span>
                    </div>
                    <span
                        class="px-2 py-0.5 rounded font-label-sm text-label-sm bg-secondary-fixed/40 text-on-secondary-container font-semibold">SPK
                        / SOP</span>
                </div>
                <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                    <div class="bg-secondary h-full rounded-full"
                        style="width: {{ $percentOf($stats['with_attachment']) }}%"></div>
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

        {{-- Tabel tiket --}}
        <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                            <th class="py-3.5 px-4 font-semibold w-44">Nomor Tiket</th>
                            <th class="py-3.5 px-4 font-semibold min-w-[320px]">Detail Pekerjaan</th>
                            <th class="py-3.5 px-4 font-semibold min-w-[180px]">Pembuat</th>
                            <th class="py-3.5 px-4 font-semibold w-32">Lampiran</th>
                            <th class="py-3.5 px-4 font-semibold text-right w-28">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        @forelse ($tickets as $ticket)
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
                                        <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-2 max-w-xl">
                                            {{ \Illuminate\Support\Str::limit($ticket->description, 140) }}
                                        </p>
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
                                <td class="py-4 px-4 align-top">
                                    @if ($ticket->attachments_count > 0)
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">attach_file</span>
                                            {{ $ticket->attachments_count }} File
                                        </span>
                                    @else
                                        <span class="font-body-sm text-body-sm text-outline">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 align-top text-right">
                                    <button type="button" onclick="openTicketModal({{ $ticket->id }})"
                                        class="inline-flex items-center gap-1 h-[34px] px-3.5 rounded-lg bg-surface-container text-on-surface font-label-sm text-label-sm font-semibold hover:bg-surface-container-high transition-all">
                                        <span>Detail</span>
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 px-4 text-center">
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

    {{-- ===================== MODAL DETAIL TIKET ===================== --}}
    <div id="ticket-modal-backdrop"
        class="fixed inset-0 z-50 bg-inverse-surface/50 hidden items-center justify-center p-4"
        onclick="if (event.target === this) closeTicketModal()">
        <div class="w-full max-w-2xl max-h-[85vh] overflow-y-auto rounded-xl bg-surface-container-lowest shadow-xl">
            {{-- Header modal --}}
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

            {{-- Isi modal --}}
            <div class="p-space-lg space-y-space-lg">
                <div class="flex items-center gap-2.5">
                    <div id="modal-initials"
                        class="w-9 h-9 rounded-full bg-primary-container/20 text-primary font-bold text-sm flex items-center justify-center shrink-0">
                    </div>
                    <div class="flex flex-col">
                        <span id="modal-creator"
                            class="font-label-md text-label-md text-on-surface font-semibold leading-tight"></span>
                        <span id="modal-date" class="font-body-sm text-[11px] text-on-surface-variant"></span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <span class="font-label-md text-label-md text-on-surface block font-medium">Deskripsi Rincian
                        Pekerjaan &amp; SOP</span>
                    <div id="modal-description"
                        class="p-space-sm rounded-lg bg-surface-container-low font-body-md text-body-md text-on-surface whitespace-pre-line">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <span class="font-label-md text-label-md text-on-surface block font-medium">Lampiran Dokumen SOP /
                        SPK</span>
                    <div id="modal-attachments" class="space-y-2"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script>
        // Data semua tiket di halaman ini, dipakai modal supaya tidak perlu request baru.
        const ticketsData = @json($ticketsData);

        const modalBackdrop = document.getElementById('ticket-modal-backdrop');
        const modalNumber = document.getElementById('modal-number');
        const modalTitle = document.getElementById('modal-title');
        const modalInitials = document.getElementById('modal-initials');
        const modalCreator = document.getElementById('modal-creator');
        const modalDate = document.getElementById('modal-date');
        const modalDescription = document.getElementById('modal-description');
        const modalAttachments = document.getElementById('modal-attachments');

        function openTicketModal(ticketId) {
            const ticket = ticketsData[ticketId];
            if (!ticket) return;

            modalNumber.textContent = '#' + ticket.number;
            modalTitle.textContent = ticket.title;
            modalInitials.textContent = ticket.initials;
            modalCreator.textContent = ticket.creator;
            modalDate.textContent = ticket.createdAt;
            modalDescription.textContent = ticket.description;

            modalAttachments.innerHTML = '';
            if (ticket.attachments.length === 0) {
                modalAttachments.innerHTML =
                    '<p class="font-body-sm text-body-sm text-on-surface-variant">Tidak ada lampiran pada tiket ini.</p>';
            } else {
                ticket.attachments.forEach(function(file) {
                    const row = document.createElement('div');
                    row.className =
                        'p-space-sm rounded-lg bg-surface-container-low flex items-center justify-between gap-space-sm';
                    row.innerHTML = `
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[22px]">${file.isImage ? 'image' : 'picture_as_pdf'}</span>
                            </div>
                            <div class="min-w-0">
                                <span class="font-label-md text-label-md text-on-surface font-semibold block truncate">${file.name}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">${file.size}</span>
                            </div>
                        </div>
                        <a href="${file.url}" target="_blank" rel="noopener" title="Lihat file"
                            class="w-8 h-8 rounded-lg bg-surface-container-high text-on-surface-variant hover:text-primary flex items-center justify-center transition-all shrink-0">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                    `;
                    modalAttachments.appendChild(row);
                });
            }

            modalBackdrop.classList.remove('hidden');
            modalBackdrop.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeTicketModal() {
            modalBackdrop.classList.add('hidden');
            modalBackdrop.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeTicketModal();
        });
    </script>
@endsection