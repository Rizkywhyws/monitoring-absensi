@extends('layouts.app')

@section('title', 'Daily Activity')
@section('breadcrumb', 'Daily Activity')

@section('content')
@php
    $fmt = fn ($m) => $m === null ? '-' : intdiv($m, 60) . 'j ' . str_pad($m % 60, 2, '0', STR_PAD_LEFT) . 'm';
@endphp

<div class="flex flex-col gap-space-lg">

    {{-- Judul --}}
    <div>
        <p class="font-label-sm text-label-sm uppercase tracking-wider text-primary">Logbook harian</p>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Daily Activity Saya</h1>
        <p class="mt-1 max-w-2xl font-body-md text-body-md text-on-surface-variant">
            Tiket yang kamu selesaikan setiap hari, jam kerjanya, dan catatan logbook untuk mentor.
        </p>
    </div>

    {{-- Ringkasan --}}
    <div class="grid gap-gutter-desktop sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-surface-container-lowest p-space-md shadow-sm">
            <p class="font-label-md text-label-md text-on-surface-variant">Tiket selesai</p>
            <p class="mt-1 font-stat-counter text-stat-counter text-on-surface">
                {{ $tickets->count() }} <span class="font-label-lg text-label-lg text-on-surface-variant">Tiket</span>
            </p>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">{{ $date->translatedFormat('l, d M Y') }}</p>
        </div>

        <div class="rounded-xl bg-surface-container-lowest p-space-md shadow-sm">
            <p class="font-label-md text-label-md text-on-surface-variant">Jam kerja</p>
            <p class="mt-1 font-stat-counter text-stat-counter text-on-surface">{{ $fmt($workMinutes) }}</p>
            <p class="mt-1 font-body-sm text-body-sm text-primary">
                @if ($workStart && $workEnd)
                    {{ $workStart->format('H:i') }} – {{ $workEnd->format('H:i') }} WIB
                @else
                    Standar operasional 8 jam kerja
                @endif
            </p>
        </div>

        <div class="rounded-xl bg-surface-container-lowest p-space-md shadow-sm">
            <p class="font-label-md text-label-md text-on-surface-variant">Logbook hari ini</p>
            @if ($activity)
                <p class="mt-1 font-stat-counter text-stat-counter text-primary">Sudah diisi</p>
                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Bisa kamu perbarui kapan saja</p>
            @elseif ($tickets->isNotEmpty())
                <p class="mt-1 font-stat-counter text-stat-counter text-error">Belum diisi</p>
                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Isi catatan di bagian kanan</p>
            @else
                <p class="mt-1 font-stat-counter text-stat-counter text-on-surface-variant">-</p>
                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Belum ada tiket selesai</p>
            @endif
        </div>

        <div class="rounded-xl bg-surface-container-lowest p-space-md shadow-sm">
            <p class="font-label-md text-label-md text-on-surface-variant">7 hari terakhir</p>
            <p class="mt-1 font-stat-counter text-stat-counter text-on-surface">
                {{ $weekTotal }} <span class="font-label-lg text-label-lg text-on-surface-variant">Tiket</span>
            </p>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">Selesai sampai tanggal terpilih</p>
        </div>
    </div>

    {{-- Filter tanggal --}}
    <form method="GET" class="flex flex-wrap items-center gap-space-sm rounded-xl bg-surface-container-lowest p-space-md shadow-sm">
        <div class="flex items-center gap-1 rounded-lg bg-surface-container-low px-space-xs py-1.5">
            <a href="{{ route('daily-activity.index', ['date' => $prevDate]) }}"
               class="px-2 text-on-surface-variant hover:text-on-surface" aria-label="Hari sebelumnya">&lsaquo;</a>

            <input type="date" name="date" value="{{ $date->toDateString() }}"
                   max="{{ today()->toDateString() }}"
                   onchange="this.form.submit()"
                   class="border-0 bg-transparent font-label-lg text-label-lg text-on-surface focus:ring-0">

            @if ($nextDate)
                <a href="{{ route('daily-activity.index', ['date' => $nextDate]) }}"
                   class="px-2 text-on-surface-variant hover:text-on-surface" aria-label="Hari berikutnya">&rsaquo;</a>
            @else
                <span class="px-2 text-outline-variant" aria-hidden="true">&rsaquo;</span>
            @endif
        </div>

        @unless ($isToday)
            <a href="{{ route('daily-activity.index') }}"
               class="rounded-lg bg-primary-fixed px-space-sm py-2 font-label-lg text-label-lg text-on-primary-fixed hover:opacity-90">
                Hari ini
            </a>
        @endunless
    </form>

    <div class="grid gap-space-lg xl:grid-cols-3">

        {{-- Daftar tiket --}}
        <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between bg-surface-container-low px-space-lg py-space-md">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Tiket yang kamu selesaikan</h2>
                <span class="rounded-full bg-surface-container-high px-space-sm py-0.5 font-label-md text-label-md text-on-surface-variant">
                    {{ $tickets->count() }} tiket
                </span>
            </div>

            @forelse ($tickets as $ticket)
                @php
                    $isHigh = in_array(strtolower((string) $ticket->priority), ['high', 'tinggi', 'urgent'], true);
                @endphp
                <div class="flex items-start justify-between gap-space-md border-t border-surface-container px-space-lg py-space-md first:border-t-0">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-space-xs">
                            <span class="rounded bg-surface-container px-1.5 py-0.5 font-label-md text-label-md text-on-surface">
                                {{ $ticket->code ?: $ticket->ticket_number }}
                            </span>
                            @if ($isHigh)
                                <span class="rounded bg-error-container px-1.5 py-0.5 font-label-md text-label-md text-on-error-container">High</span>
                            @endif
                            @if ($ticket->category)
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $ticket->category }}</span>
                            @endif
                        </div>
                        <p class="mt-1 font-body-md text-body-md text-on-surface">{{ $ticket->title }}</p>
                    </div>

                    <div class="shrink-0 text-right">
                        <p class="font-label-lg text-label-lg text-on-surface">{{ $fmt($ticket->duration_minutes) }}</p>
                        @if ($ticket->start_at && $ticket->end_at)
                            <p class="font-body-sm text-body-sm text-on-surface-variant">
                                {{ $ticket->start_at->format('H:i') }} – {{ $ticket->end_at->format('H:i') }}
                            </p>
                        @elseif ($ticket->end_at)
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Selesai {{ $ticket->end_at->format('H:i') }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-space-lg py-space-3xl text-center font-body-md text-body-md text-on-surface-variant">
                    Belum ada tiket yang selesai pada {{ $date->translatedFormat('d F Y') }}.
                    <br>Coba pilih tanggal lain atau lihat rekap 7 hari di samping.
                </div>
            @endforelse
        </div>

        {{-- Kolom kanan: catatan + rekap --}}
        <div class="flex flex-col gap-space-lg">

            {{-- Catatan harian --}}
            <div class="rounded-xl bg-surface-container-lowest p-space-lg shadow-sm">
                <h2 class="font-headline-sm text-headline-sm text-on-surface">Catatan harian</h2>

                @if ($tickets->isEmpty())
                    <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">
                        Catatan bisa diisi setelah ada tiket yang selesai pada tanggal ini.
                    </p>
                @else
                    <form method="POST" action="{{ route('daily-activity.store') }}" class="mt-space-sm">
                        @csrf
                        <input type="hidden" name="activity_date" value="{{ $date->toDateString() }}">

                        <textarea name="description" rows="6"
                                  class="w-full rounded-lg border-outline-variant bg-surface-container-low font-body-md text-body-md text-on-surface focus:border-primary focus:ring-primary"
                                  placeholder="Ringkas pekerjaan, kendala, dan hasil hari ini…">{{ old('description', $activity?->description) }}</textarea>

                        @error('description')
                            <p class="mt-1 font-body-sm text-body-sm text-error">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                class="mt-space-sm w-full rounded-lg bg-primary px-space-md py-2.5 font-label-lg text-label-lg text-on-primary transition-colors hover:bg-primary-container">
                            {{ $activity ? 'Perbarui catatan' : 'Simpan catatan' }}
                        </button>
                    </form>
                @endif
            </div>

            {{-- Rekap 7 hari --}}
            <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm">
                <div class="bg-surface-container-low px-space-lg py-space-md">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Rekap 7 hari</h2>
                </div>

                <ul>
                    @foreach ($recap as $row)
                        <li class="border-t border-surface-container first:border-t-0">
                            <a href="{{ route('daily-activity.index', ['date' => $row['date']]) }}"
                               class="flex items-center justify-between px-space-lg py-space-sm transition-colors hover:bg-surface-container-low {{ $row['active'] ? 'bg-primary-fixed/40' : '' }}">
                                <span class="font-label-lg text-label-lg text-on-surface">{{ $row['label'] }}</span>

                                <span class="flex items-center gap-space-xs">
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $row['count'] }} tiket</span>

                                    @if ($row['count'] === 0)
                                        <span class="rounded-full bg-surface-container px-2 py-0.5 font-label-sm text-label-sm text-on-surface-variant">-</span>
                                    @elseif ($row['logged'])
                                        <span class="rounded-full bg-primary-fixed px-2 py-0.5 font-label-sm text-label-sm text-on-primary-fixed">Terisi</span>
                                    @else
                                        <span class="rounded-full bg-tertiary-fixed px-2 py-0.5 font-label-sm text-label-sm text-on-tertiary-fixed">Belum</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection