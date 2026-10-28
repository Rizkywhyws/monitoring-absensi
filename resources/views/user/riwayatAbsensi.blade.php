@extends('layouts.App')

@section('title', 'Riwayat Absensi - Sistem Manajemen PKL PLN Icon Plus')

@section('breadcrumb')
    Presensi &amp; Kehadiran <span class="material-symbols-outlined text-[14px] align-middle">chevron_right</span>
    <span class="text-primary font-label-md">Riwayat Absensi</span>
@endsection

@section('extra-styles')
    <style>
        .detail-card { animation: detail-in .28s cubic-bezier(.2, .9, .3, 1.1) both; }
        @keyframes detail-in {
            from { opacity: 0; transform: translateY(16px) scale(.96); }
            to { opacity: 1; transform: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            .detail-card { animation-duration: .01ms !important; }
        }
    </style>
@endsection

@section('content')
    @php
        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $fmtDate = fn ($d) => $dayNames[$d->dayOfWeek] . ', ' . $d->format('d') . ' ' . $months[$d->month] . ' ' . $d->year;

        $fmtDuration = function (?int $m) {
            if ($m === null) {
                return '–';
            }
            return intdiv($m, 60) . ' j ' . str_pad((string) ($m % 60), 2, '0', STR_PAD_LEFT) . ' m';
        };

        $mapUrl = fn ($lat, $lng) => $lat !== null && $lng !== null
            ? "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lng}#map=18/{$lat}/{$lng}"
            : null;

        // Data untuk modal detail (dipakai JavaScript)
        $rows = $attendances->getCollection()->mapWithKeys(fn ($a) => [
            $a->id => [
                'date'         => $fmtDate($a->attendance_date),
                'status'       => $a->status,
                'in_time'      => $a->check_in?->format('H:i:s'),
                'out_time'     => $a->check_out?->format('H:i:s'),
                'in_distance'  => $a->check_in_distance !== null ? round($a->check_in_distance) : null,
                'out_distance' => $a->check_out_distance !== null ? round($a->check_out_distance) : null,
                'in_accuracy'  => $a->check_in_accuracy !== null ? round($a->check_in_accuracy) : null,
                'out_accuracy' => $a->check_out_accuracy !== null ? round($a->check_out_accuracy) : null,
                'in_photo'     => $a->check_in_photo ? route('absensi.photo', [$a, 'in']) : null,
                'out_photo'    => $a->check_out_photo ? route('absensi.photo', [$a, 'out']) : null,
                'in_map'       => $mapUrl($a->check_in_latitude, $a->check_in_longitude),
                'out_map'      => $mapUrl($a->check_out_latitude, $a->check_out_longitude),
            ],
        ]);

        $onTimePercent = $summary['total'] > 0 ? round($summary['hadir'] / $summary['total'] * 100) : 0;
    @endphp

    <div class="flex flex-col gap-space-xl">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-space-md">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-surface">Riwayat Absensi</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Rekap kehadiran Anda selama {{ $months[$month] }} {{ $year }}.
                </p>
            </div>

            <form method="GET" action="{{ route('absensi.riwayat') }}" class="flex flex-wrap items-center gap-space-sm">
                <select name="bulan" onchange="this.form.submit()"
                    class="h-10 px-space-sm rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-sm border border-outline-variant focus:outline-none focus:ring-2 focus:ring-primary/40">
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" @selected($month === $num)>{{ $name }}</option>
                    @endforeach
                </select>

                <select name="tahun" onchange="this.form.submit()"
                    class="h-10 px-space-sm rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-sm border border-outline-variant focus:outline-none focus:ring-2 focus:ring-primary/40">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()"
                    class="h-10 px-space-sm rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-sm border border-outline-variant focus:outline-none focus:ring-2 focus:ring-primary/40">
                    <option value="">Semua Status</option>
                    <option value="hadir" @selected($status === 'hadir')>Tepat Waktu</option>
                    <option value="telat" @selected($status === 'telat')>Terlambat</option>
                </select>

                <a href="{{ route('absensi.riwayat') }}"
                    class="h-10 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    <span>Bulan Ini</span>
                </a>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
            <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">event_available</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Total Kehadiran</span>
                    <span class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['total'] }}
                        <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">check_circle</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Tepat Waktu</span>
                    <span class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['hadir'] }}
                        <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $onTimePercent }}% dari total kehadiran</span>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-error/10 text-error flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">alarm</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Terlambat</span>
                    <span class="font-stat-counter text-[28px] leading-tight font-bold text-on-surface">{{ $summary['telat'] }}
                        <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">hari</span></span>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-lg flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[26px]">timer</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Rata-rata Durasi Kerja</span>
                    <span class="font-stat-counter text-[24px] leading-tight font-bold text-on-surface">{{ $fmtDuration($summary['avg_minutes']) }}</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">dari hari yang sudah check-out</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-space-sm p-space-lg">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface">
                        Riwayat {{ $months[$month] }} {{ $year }}
                    </span>
                </div>
                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $attendances->total() }} catatan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead>
                        <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                            <th class="px-space-lg py-space-sm">Tanggal</th>
                            <th class="px-space-md py-space-sm">Check-in</th>
                            <th class="px-space-md py-space-sm">Check-out</th>
                            <th class="px-space-md py-space-sm">Durasi</th>
                            <th class="px-space-md py-space-sm">Jarak</th>
                            <th class="px-space-md py-space-sm">Status</th>
                            <th class="px-space-lg py-space-sm text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $a)
                            @php
                                $minutes = ($a->check_in && $a->check_out)
                                    ? intdiv($a->check_out->getTimestamp() - $a->check_in->getTimestamp(), 60)
                                    : null;
                            @endphp
                            <tr class="border-t border-surface-variant/40 hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-space-lg py-space-md">
                                    <span class="font-label-lg text-label-lg text-on-surface font-semibold">{{ $fmtDate($a->attendance_date) }}</span>
                                </td>
                                <td class="px-space-md py-space-md font-body-md text-body-md text-on-surface">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-primary text-[16px]">login</span>
                                        {{ $a->check_in?->format('H:i') ?? '–' }}
                                    </span>
                                </td>
                                <td class="px-space-md py-space-md font-body-md text-body-md text-on-surface">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-secondary text-[16px]">logout</span>
                                        {{ $a->check_out?->format('H:i') ?? '–' }}
                                    </span>
                                </td>
                                <td class="px-space-md py-space-md font-body-md text-body-md text-on-surface-variant">
                                    {{ $fmtDuration($minutes) }}
                                </td>
                                <td class="px-space-md py-space-md font-body-md text-body-md text-on-surface-variant">
                                    {{ $a->check_in_distance !== null ? round($a->check_in_distance) . ' m' : '–' }}
                                </td>
                                <td class="px-space-md py-space-md">
                                    <div class="flex flex-col items-start gap-1">
                                        @if ($a->status === 'hadir')
                                            <span class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[11px]">Tepat Waktu</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-[11px]">Terlambat</span>
                                        @endif

                                        @if (! $a->check_out)
                                            @if ($a->attendance_date->isToday())
                                                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-[11px]">Belum check-out</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-[11px]">Tidak check-out</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="px-space-lg py-space-md text-right">
                                    <button type="button" data-detail="{{ $a->id }}"
                                        class="inline-flex items-center gap-1 h-9 px-space-sm rounded-lg bg-primary/10 text-primary font-label-md text-label-md hover:bg-primary/20 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                                        <span>Detail</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-space-lg py-space-3xl">
                                    <div class="flex flex-col items-center gap-2 text-center">
                                        <span class="material-symbols-outlined text-[48px] text-outline">event_busy</span>
                                        <p class="font-headline-sm text-headline-sm text-on-surface">Belum ada catatan absensi</p>
                                        <p class="font-body-md text-body-md text-on-surface-variant">
                                            Tidak ada data untuk {{ $months[$month] }} {{ $year }}
                                            @if ($status) dengan status yang dipilih @endif.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($attendances->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between gap-space-sm p-space-lg border-t border-surface-variant/40">
                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                        Menampilkan {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }} dari {{ $attendances->total() }} catatan
                    </span>

                    <div class="flex items-center gap-space-xs">
                        @if ($attendances->onFirstPage())
                            <span class="h-9 px-space-md rounded-lg bg-surface-container-high text-outline font-label-md text-label-md flex items-center gap-1 opacity-60 cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span> Sebelumnya
                            </span>
                        @else
                            <a href="{{ $attendances->previousPageUrl() }}"
                                class="h-9 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span> Sebelumnya
                            </a>
                        @endif

                        <span class="px-space-sm font-label-md text-label-md text-on-surface">
                            {{ $attendances->currentPage() }} / {{ $attendances->lastPage() }}
                        </span>

                        @if ($attendances->hasMorePages())
                            <a href="{{ $attendances->nextPageUrl() }}"
                                class="h-9 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1">
                                Berikutnya <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </a>
                        @else
                            <span class="h-9 px-space-md rounded-lg bg-surface-container-high text-outline font-label-md text-label-md flex items-center gap-1 opacity-60 cursor-not-allowed">
                                Berikutnya <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div id="detail-modal"
        style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.45); backdrop-filter:blur(4px);"
        role="dialog" aria-modal="true">
        <div class="detail-card w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl flex flex-col gap-space-md">
            <div class="flex items-start justify-between gap-space-sm">
                <div class="flex flex-col gap-1">
                    <span class="font-headline-md text-headline-md text-on-surface" id="d-date">–</span>
                    <span id="d-status" class="self-start px-2 py-0.5 rounded-full font-label-sm text-[11px]">–</span>
                </div>
                <button type="button" id="detail-close"
                    class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                @foreach (['in' => 'Check-in', 'out' => 'Check-out'] as $k => $label)
                    <div class="flex flex-col gap-space-sm p-space-md rounded-xl bg-surface-container-low">
                        <span class="font-label-lg text-label-lg font-semibold text-on-surface flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px] {{ $k === 'in' ? 'text-primary' : 'text-secondary' }}">{{ $k === 'in' ? 'login' : 'logout' }}</span>
                            {{ $label }}
                        </span>

                        <a id="d-{{ $k }}-photo-link" href="#" target="_blank" rel="noopener"
                            class="relative block rounded-lg overflow-hidden bg-inverse-surface" style="aspect-ratio:4/3;">
                            <img id="d-{{ $k }}-photo" alt="Foto {{ $label }}" class="w-full h-full object-cover" style="display:none;">
                            <span id="d-{{ $k }}-nophoto"
                                class="absolute inset-0 flex flex-col items-center justify-center gap-1 text-inverse-on-surface/70 font-body-sm text-body-sm">
                                <span class="material-symbols-outlined text-[32px]">no_photography</span>
                                <span>Tidak ada foto</span>
                            </span>
                        </a>

                        <div class="flex flex-col gap-1.5 font-body-sm text-body-sm">
                            <div class="flex justify-between gap-2">
                                <span class="text-on-surface-variant">Waktu</span>
                                <span class="font-label-md text-label-md text-on-surface" id="d-{{ $k }}-time">–</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-on-surface-variant">Jarak ke kantor</span>
                                <span class="font-label-md text-label-md text-on-surface" id="d-{{ $k }}-distance">–</span>
                            </div>
                            <div class="flex justify-between gap-2">
                                <span class="text-on-surface-variant">Akurasi GPS</span>
                                <span class="font-label-md text-label-md text-on-surface" id="d-{{ $k }}-accuracy">–</span>
                            </div>
                        </div>

                        <a id="d-{{ $k }}-map" href="#" target="_blank" rel="noopener" style="display:none;"
                            class="h-9 rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">pin_drop</span>
                            <span>Lihat Lokasi di Peta</span>
                        </a>
                    </div>
                @endforeach
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
                hadir: { text: 'Tepat Waktu', cls: 'bg-primary-fixed/30 text-on-primary-fixed-variant' },
                telat: { text: 'Terlambat', cls: 'bg-error-container text-on-error-container' },
            };

            function fillSide(k, r) {
                const photo = $(`d-${k}-photo`);
                const noPhoto = $(`d-${k}-nophoto`);
                const link = $(`d-${k}-photo-link`);
                const mapLink = $(`d-${k}-map`);

                if (r[`${k}_photo`]) {
                    photo.src = r[`${k}_photo`];
                    photo.style.display = 'block';
                    noPhoto.style.display = 'none';
                    link.href = r[`${k}_photo`];
                    link.style.pointerEvents = 'auto';
                } else {
                    photo.removeAttribute('src');
                    photo.style.display = 'none';
                    noPhoto.style.display = 'flex';
                    link.removeAttribute('href');
                    link.style.pointerEvents = 'none';
                }

                $(`d-${k}-time`).textContent = r[`${k}_time`] ? `${r[`${k}_time`]} WIB` : '–';
                $(`d-${k}-distance`).textContent = r[`${k}_distance`] !== null ? `${r[`${k}_distance`]} meter` : '–';
                $(`d-${k}-accuracy`).textContent = r[`${k}_accuracy`] !== null ? `±${r[`${k}_accuracy`]} meter` : '–';

                if (r[`${k}_map`]) {
                    mapLink.href = r[`${k}_map`];
                    mapLink.style.display = 'flex';
                } else {
                    mapLink.style.display = 'none';
                }
            }

            function openDetail(id) {
                const r = ROWS[id];
                if (!r) return;

                $('d-date').textContent = r.date;
                const meta = STATUS[r.status] || STATUS.hadir;
                const st = $('d-status');
                st.textContent = meta.text;
                st.className = `self-start px-2 py-0.5 rounded-full font-label-sm text-[11px] ${meta.cls}`;

                fillSide('in', r);
                fillSide('out', r);

                modal.style.display = 'flex';
            }

            function closeDetail() {
                modal.style.display = 'none';
                ['in', 'out'].forEach((k) => $(`d-${k}-photo`).removeAttribute('src'));
            }

            document.querySelectorAll('[data-detail]').forEach((btn) => {
                btn.addEventListener('click', () => openDetail(btn.dataset.detail));
            });
            $('detail-close').addEventListener('click', closeDetail);
            modal.addEventListener('click', (e) => { if (e.target === modal) closeDetail(); });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') closeDetail();
            });
        })();
    </script>
@endsection