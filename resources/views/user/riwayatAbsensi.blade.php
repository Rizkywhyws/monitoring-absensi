@extends('layouts.app')
@section('title', 'Riwayat Absensi - Sistem Manajemen PKL PLN Icon Plus')
@section('breadcrumb')
    Presensi &amp; Kehadiran <span class="material-symbols-outlined text-[14px] align-middle">chevron_right</span>
    <span class="text-primary font-label-md">Riwayat Absensi</span>
@endsection
@section('extra-styles')
    <style>
        .detail-card {
            animation: detail-in .28s cubic-bezier(.2, .9, .3, 1.1) both;
        }

        @keyframes detail-in {
            from {
                opacity: 0;
                transform: translateY(16px) scale(.96);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .detail-card {
                animation-duration: .01ms !important;
            }
        }
    </style>
@endsection
@section('content')
    @php
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $fmtDate = fn($d) => $dayNames[$d->dayOfWeek] .
            ', ' .
            $d->format('d') .
            ' ' .
            $months[$d->month] .
            ' ' .
            $d->year;
        $mapUrl = fn($lat, $lng) => $lat !== null && $lng !== null
            ? "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}"
            : null;     
        $summary = array_merge(
            ['total' => 0, 'hadir' => 0, 'telat' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0],
            $summary ?? [],
        );
        $alphaDates = collect($alphaDates ?? []);
        $alphaCount = $summary['alpha'] ?? $alphaDates->count();
        $showAlphaOnly = $status === 'alpha';
        $badge = [
            'hadir' => ['Tepat Waktu', 'bg-primary-fixed/30 text-on-primary-fixed-variant'],
            'telat' => ['Terlambat', 'bg-error-container text-on-error-container'],
            'sakit' => ['Sakit', 'bg-secondary-fixed text-on-secondary-fixed-variant'],
            'izin' => ['Izin', 'bg-surface-container-high text-on-surface'],
        ];
        $rows = $attendances
            ->getCollection()
            ->filter(fn($a) => in_array($a->status, ['hadir', 'telat'], true))
            ->mapWithKeys(
                fn($a) => [
                    $a->id => [
                        'date' => $fmtDate($a->attendance_date),
                        'status' => $a->status,
                        'in_time' => $a->check_in?->format('H:i:s'),
                        'in_distance' => $a->check_in_distance !== null ? round($a->check_in_distance) : null,
                        'in_accuracy' => $a->check_in_accuracy !== null ? round($a->check_in_accuracy) : null,
                        'in_photo' => $a->check_in_photo ? route('absensi.photo', [$a, 'in']) : null,
                        'in_map' => $mapUrl($a->check_in_latitude, $a->check_in_longitude),
                    ],
                ],
            );
        $onTimePercent = $summary['total'] > 0 ? round(($summary['hadir'] / $summary['total']) * 100) : 0;
        $selectCls =
            'h-10 px-space-sm rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-sm border border-outline-variant focus:outline-none focus:ring-2 focus:ring-primary/40';
    @endphp
    <div class="flex flex-col gap-space-xl">
        {{-- ================= HEADER + FILTER ================= --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-space-md">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-surface">Riwayat Absensi</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Rekap kehadiran Anda selama
                    {{ $months[$month] }} {{ $year }}.</p>
            </div>
            <form method="GET" action="{{ route('absensi.riwayat') }}" class="flex flex-wrap items-center gap-space-sm">
                <select name="bulan" onchange="this.form.submit()" class="{{ $selectCls }}">
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" @selected($month === $num)>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="tahun" onchange="this.form.submit()" class="{{ $selectCls }}">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="this.form.submit()" class="{{ $selectCls }}">
                    <option value="">Semua Status</option>
                    <option value="hadir" @selected($status === 'hadir')>Tepat Waktu</option>
                    <option value="telat" @selected($status === 'telat')>Terlambat</option>
                    <option value="sakit" @selected($status === 'sakit')>Sakit</option>
                    <option value="izin" @selected($status === 'izin')>Izin</option>
                    <option value="alpha" @selected($status === 'alpha')>Alpha</option>
                </select>
                <a href="{{ route('absensi.riwayat') }}"
                    class="h-10 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    <span>Bulan Ini</span>
                </a>
            </form>
        </div>
        {{-- ================= RINGKASAN ================= --}}
        <div class="flex flex-col gap-space-md">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-space-md">
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">event_available</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Total
                            Kehadiran</span>
                        <span
                            class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['total'] }}
                            <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                    </div>
                </div>
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                    <div
                        class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">check_circle</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Tepat
                            Waktu</span>
                        <span
                            class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['hadir'] }}
                            <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $onTimePercent }}% dari total
                            kehadiran</span>
                    </div>
                </div>
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                    <div class="w-12 h-12 rounded-xl bg-error/10 text-error flex items-center justify-center shrink-0"><span
                            class="material-symbols-outlined text-[26px]">alarm</span></div>
                    <div class="flex flex-col min-w-0">
                        <span
                            class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Terlambat</span>
                        <span
                            class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['telat'] }}
                            <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                    </div>
                </div>
            </div>
            {{-- Tidak hadir: Sakit, Izin, Alpha --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                    <div
                        class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">sick</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span
                            class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Sakit</span>
                        <span
                            class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['sakit'] }}
                            <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                    </div>
                </div>
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[26px]">event_busy</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span
                            class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Izin</span>
                        <span
                            class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['izin'] }}
                            <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                    </div>
                </div>
                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                    <div class="w-12 h-12 rounded-xl bg-error/10 text-error flex items-center justify-center shrink-0"><span
                            class="material-symbols-outlined text-[26px]">person_off</span></div>
                    <div class="flex flex-col min-w-0">
                        <span
                            class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Alpha</span>
                        <span
                            class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $alphaCount }}
                            <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">tanpa keterangan</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- ================= DAFTAR ALPHA ================= --}}
        @if ($alphaDates->isNotEmpty() && ($status === '' || $status === null || $showAlphaOnly))
            <div class="rounded-xl bg-error-container/50 p-space-lg flex flex-col gap-space-sm">
                <div class="flex items-center gap-space-xs text-on-error-container">
                    <span class="material-symbols-outlined text-[22px]">person_off</span>
                    <span class="font-headline-sm text-headline-sm">{{ $alphaDates->count() }} hari tanpa keterangan
                        (alpha)</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Hari kerja tanpa Check-in dan tanpa izin/sakit
                    yang disetujui. Jika ini keliru, hubungi admin.</p>
                <div class="flex flex-wrap gap-space-xs">
                    @foreach ($alphaDates as $d)
                        <span
                            class="px-space-xs py-1 rounded-full bg-surface-container-lowest text-on-error-container font-label-md text-label-md">{{ $fmtDate($d) }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        {{-- ================= TABEL RIWAYAT ================= --}}
        @unless ($showAlphaOnly)
            <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
                <div class="flex items-center justify-between gap-space-sm p-space-lg">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                        <span class="font-headline-sm text-headline-sm text-on-surface">Riwayat {{ $months[$month] }}
                            {{ $year }}</span>
                    </div>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $attendances->total() }} catatan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left">
                        <thead>
                            <tr
                                class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                                <th class="px-space-lg py-space-sm">Tanggal</th>
                                <th class="px-space-md py-space-sm">Check-in</th>
                                <th class="px-space-md py-space-sm">Jarak</th>
                                <th class="px-space-md py-space-sm">Status</th>
                                <th class="px-space-lg py-space-sm text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($attendances as $a)
                                @php
                                    $isLeave = in_array($a->status, ['sakit', 'izin'], true);
                                    [$badgeText, $badgeCls] = $badge[$a->status] ?? [
                                        ucfirst($a->status),
                                        'bg-surface-container-high text-on-surface',
                                    ];
                                @endphp
                                <tr
                                    class="border-t border-surface-variant/40 hover:bg-surface-container-low/60 transition-colors">
                                    <td class="px-space-lg py-space-md"><span
                                            class="font-label-lg text-label-lg text-on-surface font-semibold">{{ $fmtDate($a->attendance_date) }}</span>
                                    </td>
                                    @if ($isLeave)
                                        <td colspan="2"
                                            class="px-space-md py-space-md font-body-md text-body-md text-on-surface-variant">
                                            <span class="flex items-center gap-1.5"><span
                                                    class="material-symbols-outlined text-[18px]">event_available</span>Tidak
                                                masuk, {{ $a->status === 'sakit' ? 'sakit' : 'izin' }} disetujui admin</span>
                                        </td>
                                    @else
                                        <td class="px-space-md py-space-md font-body-md text-body-md text-on-surface">
                                            <span class="flex items-center gap-1"><span
                                                    class="material-symbols-outlined text-primary text-[16px]">login</span>{{ $a->check_in?->format('H:i') ?? '–' }}</span>
                                        </td>
                                        <td class="px-space-md py-space-md font-body-md text-body-md text-on-surface-variant">
                                            {{ $a->check_in_distance !== null ? round($a->check_in_distance) . ' m' : '–' }}
                                        </td>
                                    @endif
                                    <td class="px-space-md py-space-md">
                                        <span
                                            class="px-2 py-0.5 rounded-full {{ $badgeCls }} font-label-sm text-[11px]">{{ $badgeText }}</span>
                                    </td>
                                    <td class="px-space-lg py-space-md text-right">
                                        @if ($isLeave)
                                            <span class="text-on-surface-variant">–</span>
                                        @else
                                            <button type="button" data-detail="{{ $a->id }}"
                                                class="inline-flex items-center gap-1 h-9 px-space-sm rounded-lg bg-primary/10 text-primary font-label-md text-label-md hover:bg-primary/20 transition-colors">
                                                <span
                                                    class="material-symbols-outlined text-[18px]">photo_camera</span><span>Detail</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-space-lg py-space-3xl">
                                        <div class="flex flex-col items-center gap-2 text-center">
                                            <span class="material-symbols-outlined text-[48px] text-outline">event_busy</span>
                                            <p class="font-headline-sm text-headline-sm text-on-surface">Belum ada catatan
                                                absensi</p>
                                            <p class="font-body-md text-body-md text-on-surface-variant">Tidak ada data untuk
                                                {{ $months[$month] }} {{ $year }} @if ($status)
                                                    dengan status yang dipilih
                                                @endif.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($attendances->hasPages())
                    <div
                        class="flex flex-col sm:flex-row items-center justify-between gap-space-sm p-space-lg border-t border-surface-variant/40">
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Menampilkan
                            {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }} dari {{ $attendances->total() }}
                            catatan</span>
                        <div class="flex items-center gap-space-xs">
                            @if ($attendances->onFirstPage())
                                <span
                                    class="h-9 px-space-md rounded-lg bg-surface-container-high text-outline font-label-md text-label-md flex items-center gap-1 opacity-60 cursor-not-allowed"><span
                                        class="material-symbols-outlined text-[18px]">chevron_left</span> Sebelumnya</span>
                            @else
                                <a href="{{ $attendances->previousPageUrl() }}"
                                    class="h-9 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1"><span
                                        class="material-symbols-outlined text-[18px]">chevron_left</span> Sebelumnya</a>
                            @endif
                            <span
                                class="px-space-sm font-label-md text-label-md text-on-surface">{{ $attendances->currentPage() }}
                                / {{ $attendances->lastPage() }}</span>
                            @if ($attendances->hasMorePages())
                                <a href="{{ $attendances->nextPageUrl() }}"
                                    class="h-9 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1">Berikutnya
                                    <span class="material-symbols-outlined text-[18px]">chevron_right</span></a>
                            @else
                                <span
                                    class="h-9 px-space-md rounded-lg bg-surface-container-high text-outline font-label-md text-label-md flex items-center gap-1 opacity-60 cursor-not-allowed">Berikutnya
                                    <span class="material-symbols-outlined text-[18px]">chevron_right</span></span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endunless
        @if ($showAlphaOnly && $alphaDates->isEmpty())
            <div
                class="rounded-xl bg-surface-container-lowest shadow-sm p-space-3xl flex flex-col items-center gap-2 text-center">
                <span class="material-symbols-outlined text-[48px] text-outline">task_alt</span>
                <p class="font-headline-sm text-headline-sm text-on-surface">Tidak ada alpha</p>
                <p class="font-body-md text-body-md text-on-surface-variant">Tidak ada hari tanpa keterangan pada
                    {{ $months[$month] }} {{ $year }}.</p>
            </div>
        @endif
    </div>
    {{-- ================= MODAL DETAIL CHECK-IN ================= --}}
    <div id="detail-modal"
        style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.45); backdrop-filter:blur(4px);"
        role="dialog" aria-modal="true">
        <div
            class="detail-card w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl flex flex-col gap-space-md">
            <div class="flex items-start justify-between gap-space-sm">
                <div class="flex flex-col gap-1">
                    <span class="font-headline-md text-headline-md text-on-surface" id="d-date">–</span>
                    <span id="d-status" class="self-start px-2 py-0.5 rounded-full font-label-sm text-[11px]">–</span>
                </div>
                <button type="button" id="detail-close"
                    class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors"
                    aria-label="Tutup">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-space-sm p-space-md rounded-xl bg-surface-container-low">
                <span class="font-label-lg text-label-lg font-semibold text-on-surface flex items-center gap-1.5"><span
                        class="material-symbols-outlined text-[18px] text-primary">login</span>Check-in</span>
                <a id="d-in-photo-link" href="#" target="_blank" rel="noopener"
                    class="relative block w-full max-w-md mx-auto rounded-lg overflow-hidden bg-inverse-surface"
                    style="aspect-ratio:4/3;">
                    <img id="d-in-photo" alt="Foto Check-in" class="w-full h-full object-cover" style="display:none;">
                    <span id="d-in-nophoto"
                        class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-inverse-on-surface/70 font-body-sm text-body-sm"><span
                            class="material-symbols-outlined text-[32px]">no_photography</span><span>Tidak ada
                            foto</span></span>
                </a>
                <div class="flex flex-col gap-1.5 font-body-sm text-body-sm">
                    <div class="flex justify-between gap-2"><span class="text-on-surface-variant">Waktu</span><span
                            class="font-label-md text-label-md text-on-surface" id="d-in-time">–</span></div>
                    <div class="flex justify-between gap-2"><span class="text-on-surface-variant">Jarak ke
                            kantor</span><span class="font-label-md text-label-md text-on-surface"
                            id="d-in-distance">–</span></div>
                    <div class="flex justify-between gap-2"><span class="text-on-surface-variant">Akurasi GPS</span><span
                            class="font-label-md text-label-md text-on-surface" id="d-in-accuracy">–</span></div>
                </div>
                <a id="d-in-map" href="#" target="_blank" rel="noopener" style="display:none;"
                    class="h-9 rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">pin_drop</span><span>Lihat Lokasi di Peta</span>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('extra-scripts')
    <script>
        (function() {
            const ROWS = @json($rows);
            const $ = (id) => document.getElementById(id);
            const modal = $('detail-modal');
            const STATUS = {
                hadir: {
                    text: 'Tepat Waktu',
                    cls: 'bg-primary-fixed/30 text-on-primary-fixed-variant'
                },
                telat: {
                    text: 'Terlambat',
                    cls: 'bg-error-container text-on-error-container'
                },
            };

            function openDetail(id) {
                const row = ROWS[id];
                if (!row) return;
                $('d-date').textContent = row.date;
                const meta = STATUS[row.status] || STATUS.hadir;
                const status = $('d-status');
                status.textContent = meta.text;
                status.className = `self-start px-2 py-0.5 rounded-full font-label-sm text-[11px] ${meta.cls}`;
                const photo = $('d-in-photo');
                const noPhoto = $('d-in-nophoto');
                const photoLink = $('d-in-photo-link');
                if (row.in_photo) {
                    photo.src = row.in_photo;
                    photo.style.display = 'block';
                    noPhoto.style.display = 'none';
                    photoLink.href = row.in_photo;
                    photoLink.style.pointerEvents = 'auto';
                } else {
                    photo.removeAttribute('src');
                    photo.style.display = 'none';
                    noPhoto.style.display = 'flex';
                    photoLink.removeAttribute('href');
                    photoLink.style.pointerEvents = 'none';
                }
                $('d-in-time').textContent = row.in_time ? `${row.in_time} WIB` : '–';
                $('d-in-distance').textContent = row.in_distance !== null ? `${row.in_distance} meter` : '–';
                $('d-in-accuracy').textContent = row.in_accuracy !== null ? `±${row.in_accuracy} meter` : '–';
                const mapLink = $('d-in-map');
                if (row.in_map) {
                    mapLink.href = row.in_map;
                    mapLink.style.display = 'flex';
                } else {
                    mapLink.style.display = 'none';
                    mapLink.removeAttribute('href');
                }
                modal.style.display = 'flex';
            }

            function closeDetail() {
                modal.style.display = 'none';
                $('d-in-photo').removeAttribute('src');
            }
            document.querySelectorAll('[data-detail]').forEach((button) => {
                button.addEventListener('click', () => openDetail(button.dataset.detail));
            });
            $('detail-close').addEventListener('click', closeDetail);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeDetail();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.style.display === 'flex') closeDetail();
            });
        })();
    </script>
@endsection
