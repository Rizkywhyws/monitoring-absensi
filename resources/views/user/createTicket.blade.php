@extends('layouts.app')

@section('title', 'Buat Tiket Baru')
@section('breadcrumb', 'Tiket / Buat Tiket Baru')

@section('content')
    @php
        $initials = \Illuminate\Support\Str::upper(
            collect(explode(' ', trim(auth()->user()->name)))
                ->map(fn($word) => mb_substr($word, 0, 1))
                ->take(2)
                ->implode(''),
        );

        $summaryCards = [
            [
                'key' => 'to_do',
                'label' => 'To Do',
                'icon' => 'assignment',
                'tone' => 'bg-surface-container-high text-on-surface-variant',
            ],
            [
                'key' => 'on_progress',
                'label' => 'On Progress',
                'icon' => 'play_arrow',
                'tone' => 'bg-amber-100 text-tertiary',
            ],
            [
                'key' => 'selesai',
                'label' => 'Selesai',
                'icon' => 'check_circle',
                'tone' => 'bg-primary/10 text-primary',
            ],
        ];

        $flow = [
            ['icon' => 'assignment', 'title' => 'To Do', 'desc' => 'Tiket baru tersimpan dan menunggu dikerjakan.'],
            ['icon' => 'play_arrow', 'title' => 'On Progress', 'desc' => 'Tekan "Mulai" saat pekerjaan dimulai.'],
            ['icon' => 'check_circle', 'title' => 'Selesai', 'desc' => 'Tekan "Selesai" setelah pekerjaan beres.'],
            ['icon' => 'event_note', 'title' => 'Daily Activity', 'desc' => 'Tiket selesai otomatis masuk rekap harian.'],
        ];
    @endphp

    <div class="flex flex-col w-full gap-space-lg">
        <nav aria-label="Breadcrumb"
            class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant">
            <a class="hover:text-primary transition-colors" href="{{ route('dashboard') }}">Presensi &amp; Penugasan</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('tickets') }}">Tiket</a>
            <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
            <span class="font-semibold text-primary">Buat Tiket Baru</span>
        </nav>

        {{-- Header --}}
        <div class="space-y-1">
            <div class="flex items-center gap-space-xs flex-wrap">
                <span
                    class="px-2 py-0.5 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm uppercase tracking-wider">Tiket
                    Baru</span>
                <span
                    class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-mono">#{{ $nextNumber }}</span>
                <span class="text-outline-variant text-body-sm">•</span>
                <span class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface-variant font-medium">
                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">
                Buat Tiket Penugasan Baru
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                Tuliskan judul pekerjaan untuk mencatat instruksi kerja lapangan, aktivasi, atau pemeliharaan jaringan
                fiber optic.
            </p>
        </div>

        {{-- Pengingat tiket aktif --}}
        @if (isset($activeTickets) && $activeTickets->isNotEmpty())
            <div class="rounded-xl bg-amber-50 p-space-md flex flex-col sm:flex-row sm:items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/70 text-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">pending_actions</span>
                </div>
                <div class="flex-1 min-w-0 space-y-1.5">
                    <p class="font-label-lg text-label-lg text-tertiary font-semibold">
                        Kamu masih punya {{ $activeTickets->count() }} tiket On Progress
                    </p>
                    <ul class="space-y-1">
                        @foreach ($activeTickets as $active)
                            <li class="font-body-sm text-body-sm text-on-surface truncate">
                                <span
                                    class="font-mono text-[11px] text-on-surface-variant">#{{ $active->ticket_number }}</span>
                                {{ $active->title }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <a href="{{ route('tickets') }}"
                    class="inline-flex items-center gap-1 self-start font-label-sm text-label-sm font-semibold text-tertiary hover:underline shrink-0">
                    Selesaikan dulu
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        @endif

        <form id="ticket-form" method="POST" action="{{ route('ticket.store') }}"
            class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
            @csrf

            {{-- Kolom form --}}
            <div class="lg:col-span-7 xl:col-span-8">
                <section class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>

                    <div class="p-space-xl space-y-space-xl">

                        {{-- Judul --}}
                        <div class="space-y-space-md">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">edit_note</span>
                                </div>
                                <div>
                                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Judul
                                        Pekerjaan</h2>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Sebut jenis
                                        pekerjaan, lokasi, dan perangkat yang dikerjakan agar mudah dikenali di
                                        rekap Daily Activity.</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-md text-label-md text-on-surface font-medium"
                                        for="ticket-title">Judul Pekerjaan / Tiket Penugasan <span
                                            class="text-error">*</span></label>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant"><span
                                            id="title-char-count">0</span>/100</span>
                                </div>

                                <div class="relative">
                                    <span
                                        class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[22px]">confirmation_number</span>
                                    <input id="ticket-title" name="title" type="text" maxlength="100" required
                                        autofocus autocomplete="off"
                                        class="w-full h-14 pl-12 pr-4 rounded-lg bg-surface-container-low text-on-surface placeholder:text-outline font-body-md text-body-md focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary outline-none transition-all shadow-sm @error('title') ring-2 ring-error @enderror"
                                        placeholder="Contoh: Splicing kabel feeder ODP Jl. Mastrip, 12 core"
                                        value="{{ old('title') }}" />
                                </div>

                                <div class="w-full h-1 rounded-full bg-surface-container overflow-hidden">
                                    <div id="title-bar" class="h-full rounded-full bg-primary transition-all duration-200"
                                        style="width: 0%"></div>
                                </div>

                                @error('title')
                                    <p class="font-body-sm text-body-sm text-error flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Ringkasan tiket --}}
                        <div class="space-y-space-md pt-space-lg border-t border-surface-container">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-11 h-11 rounded-xl bg-secondary-fixed/50 text-secondary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[24px]">insights</span>
                                </div>
                                <div>
                                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-semibold">Ringkasan
                                        Tiket</h2>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">Posisi tiketmu saat ini
                                        sebelum membuat yang baru.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($summaryCards as $card)
                                    <div class="rounded-lg bg-surface-container-low px-3.5 py-3 flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full {{ $card['tone'] }} flex items-center justify-center shrink-0">
                                            <span
                                                class="material-symbols-outlined text-[20px]">{{ $card['icon'] }}</span>
                                        </div>
                                        <div class="min-w-0">
                                            <p
                                                class="font-headline-sm text-headline-sm text-on-surface font-bold leading-tight">
                                                {{ $summary[$card['key']] ?? 0 }}</p>
                                            <p class="font-label-sm text-label-sm text-on-surface-variant truncate">
                                                {{ $card['label'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div
                            class="flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-3 pt-space-md border-t border-surface-container">
                            <a href="{{ route('tickets') }}"
                                class="inline-flex items-center justify-center gap-2 h-11 px-5 rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                                <span>Batal</span>
                            </a>
                            <button id="btn-submit-ticket" type="submit"
                                class="inline-flex items-center justify-center gap-2 h-11 px-6 rounded-lg bg-primary-container hover:bg-primary disabled:opacity-60 disabled:pointer-events-none text-on-primary font-label-lg text-label-lg font-bold transition-all shadow-sm hover:shadow-md active:scale-[0.98]">
                                <span class="js-label">Terbitkan &amp; Tugaskan Tiket</span>
                                <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                            </button>
                        </div>

                    </div>
                </section>
            </div>

            {{-- Kolom samping --}}
            <div class="lg:col-span-5 xl:col-span-4 space-y-space-lg">

                {{-- Pratinjau --}}
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg space-y-space-md">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-on-surface-variant font-medium">Pratinjau
                            tiket</span>
                        <span
                            class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm font-semibold">To
                            Do</span>
                    </div>

                    <div class="rounded-lg bg-surface-container-low p-space-md space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span
                                class="font-label-lg text-label-lg font-bold text-primary tracking-tight">#{{ $nextNumber }}</span>
                        </div>
                        <p id="preview-title"
                            class="font-headline-sm text-headline-sm font-semibold break-words text-outline"
                            data-empty="Judul tiket akan tampil di sini">Judul tiket akan tampil di sini</p>

                        <div class="flex items-center gap-2.5 pt-2 border-t border-surface-container">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-container/20 text-primary font-bold text-xs flex items-center justify-center shrink-0">
                                {{ $initials }}</div>
                            <div class="flex flex-col min-w-0">
                                <span
                                    class="font-label-md text-label-md text-on-surface font-semibold leading-tight truncate">{{ auth()->user()->name }}</span>
                                <span class="font-body-sm text-[11px] text-on-surface-variant">Peserta PKL
                                    · {{ now()->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Alur --}}
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg space-y-space-md">
                    <span class="font-label-md text-label-md text-on-surface-variant font-medium block">Alur
                        tiket</span>

                    <ol>
                        @foreach ($flow as $i => $step)
                            <li class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full {{ $i === 0 ? 'bg-primary text-on-primary' : 'bg-primary/10 text-primary' }} flex items-center justify-center shrink-0">
                                        <span
                                            class="material-symbols-outlined text-[18px]">{{ $step['icon'] }}</span>
                                    </div>
                                    @if (!$loop->last)
                                        <div class="w-px flex-1 min-h-[20px] bg-surface-container-high my-1"></div>
                                    @endif
                                </div>
                                <div class="{{ $loop->last ? '' : 'pb-4' }}">
                                    <p class="font-label-md text-label-md text-on-surface font-semibold leading-8">
                                        {{ $step['title'] }}</p>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant -mt-1">
                                        {{ $step['desc'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('extra-scripts')
    <script>
        (function() {
            const form = document.getElementById('ticket-form');
            const titleInput = document.getElementById('ticket-title');
            const charCount = document.getElementById('title-char-count');
            const bar = document.getElementById('title-bar');
            const previewTitle = document.getElementById('preview-title');
            const max = Number(titleInput.getAttribute('maxlength')) || 100;

            function syncTitle() {
                const value = titleInput.value;
                charCount.textContent = value.length;
                bar.style.width = Math.min(100, (value.length / max) * 100) + '%';
                bar.classList.toggle('bg-error', value.length >= max);
                bar.classList.toggle('bg-primary', value.length < max);

                const text = value.trim();
                previewTitle.textContent = text !== '' ? text : previewTitle.dataset.empty;
                previewTitle.classList.toggle('text-outline', text === '');
                previewTitle.classList.toggle('text-on-surface', text !== '');
            }

            titleInput.addEventListener('input', syncTitle);
            syncTitle();

            form.addEventListener('submit', function() {
                const button = document.getElementById('btn-submit-ticket');
                button.disabled = true;
                button.querySelector('.js-label').textContent = 'Menyimpan...';
            });
        })();
    </script>
@endsection