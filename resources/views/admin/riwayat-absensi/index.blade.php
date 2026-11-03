@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('breadcrumb', 'Admin Workspace › Riwayat Absensi')

@section('content')
    @php
        // Gaya badge status (kunci = nilai kolom `status` di database)
        $statusStyle = [
            'hadir' => 'bg-primary-fixed text-on-primary-fixed',
            'telat' => 'bg-error-container text-on-error-container',
            'sakit' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
            'izin'  => 'bg-surface-container-high text-on-surface',
        ];

        // Durasi kerja "8 j 05 m" (kosong jika belum check-out)
        $durasi = function ($a) {
            if (!$a->check_in || !$a->check_out) {
                return '-';
            }
            $menit = intdiv($a->check_out->getTimestamp() - $a->check_in->getTimestamp(), 60);
            return intdiv($menit, 60) . ' j ' . str_pad((string) ($menit % 60), 2, '0', STR_PAD_LEFT) . ' m';
        };

        $pgBase = 'px-space-sm py-space-xs rounded-lg border border-outline-variant font-label-lg text-label-lg';
    @endphp

    <div class="flex flex-col w-full gap-space-lg">

        {{-- ================= JUDUL ================= --}}
        <div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Riwayat Absensi</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Rekap kehadiran peserta PKL PLN Icon Plus
                ({{ \Carbon\Carbon::parse($dari)->locale('id')->translatedFormat('d M Y') }} –
                {{ \Carbon\Carbon::parse($sampai)->locale('id')->translatedFormat('d M Y') }})
            </p>
        </div>

        @if ($errors->any())
            <div class="px-space-md py-space-sm rounded-xl bg-error-container text-on-error-container font-body-md text-body-md">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- ================= RINGKASAN ================= --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-space-md">
            <div class="rounded-2xl bg-surface-container-lowest shadow-sm p-space-md">
                <p class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Total Absensi</p>
                <p class="font-stat-counter text-stat-counter text-on-surface">{{ $rekap->sum() }}</p>
            </div>
            @foreach ($statusOptions as $key => $label)
                <div class="rounded-2xl bg-surface-container-lowest shadow-sm p-space-md">
                    <p class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">{{ $label }}</p>
                    <p class="font-stat-counter text-stat-counter text-on-surface">{{ $rekap[$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">

            {{-- ================= FILTER ================= --}}
            <div class="p-space-lg bg-surface-bright">
                <form action="{{ route('admin.riwayat-absensi') }}" method="GET"
                    class="flex flex-wrap items-center gap-space-sm">
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                        placeholder="Cari nama atau NIM..."
                        class="flex-1 min-w-[220px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:bg-surface-container transition-colors">
                    <input type="date" name="dari" value="{{ $dari }}"
                        class="bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                    <input type="date" name="sampai" value="{{ $sampai }}"
                        class="bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                    <select name="status"
                        class="min-w-[160px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-space-md py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.riwayat-absensi') }}"
                        class="px-space-md py-3 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg font-semibold hover:bg-surface-container-high transition-colors">
                        Reset
                    </a>
                </form>
            </div>

            {{-- ================= TABEL ================= --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-left text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
                            @foreach (['Peserta', 'Tanggal', 'Check-in', 'Check-out', 'Durasi', 'Status'] as $th)
                                <th class="px-space-md py-space-sm whitespace-nowrap">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $a)
                            @php
                                $tanggal = $a->attendance_date->locale('id')->translatedFormat('l, d M Y');
                            @endphp
                            <tr class="hover:bg-surface-bright transition-colors font-body-md text-body-md text-on-surface">

                                {{-- Peserta --}}
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <div class="font-semibold">{{ $a->user?->name ?? '-' }}</div>
                                    <div class="font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $a->user?->nim }}
                                        @if ($a->user?->group)
                                            · {{ $a->user->group->name }}
                                        @endif
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    {{ $tanggal }}
                                </td>

                                {{-- Check-in & Check-out (kolom sama, hanya beda awalan kolom database) --}}
                                @if (in_array($a->status, ['sakit', 'izin'], true))
                                    {{-- Izin/sakit yang disetujui: tidak ada check-in/check-out --}}
                                    <td colspan="2" class="px-space-md py-space-sm border-b border-surface-container text-on-surface-variant">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[18px]">event_available</span>
                                            Tidak masuk — {{ $a->status === 'sakit' ? 'sakit' : 'izin' }} disetujui admin
                                        </span>
                                    </td>
                                @else
                                @foreach (['in' => 'check_in', 'out' => 'check_out'] as $type => $col)
                                    @php
                                        $waktu    = $a->{$col};
                                        $adaFoto  = $a->{$col . '_photo'};
                                        $jarak    = $a->{$col . '_distance'};
                                        $lat      = $a->{$col . '_latitude'};
                                        $lng      = $a->{$col . '_longitude'};
                                        $jenis    = $type === 'in' ? 'Check-in' : 'Check-out';
                                    @endphp
                                    <td class="px-space-md py-space-sm border-b border-surface-container">
                                        @if ($waktu)
                                            <div class="flex items-center gap-space-sm">
                                                {{-- Foto: klik untuk memperbesar --}}
                                                @if ($adaFoto)
                                                    <button type="button" class="photo-thumb shrink-0 rounded-lg overflow-hidden"
                                                        data-photo="{{ route('absensi.photo', [$a, $type]) }}"
                                                        data-title="{{ $a->user?->name }} — {{ $jenis }}"
                                                        data-caption="{{ $tanggal }} · {{ $waktu->format('H:i:s') }} WIB"
                                                        title="Lihat foto">
                                                        <img src="{{ route('absensi.photo', [$a, $type]) }}" alt="Foto {{ $jenis }}"
                                                            loading="lazy"
                                                            class="w-12 h-12 object-cover bg-surface-container hover:opacity-80 transition-opacity">
                                                    </button>
                                                @else
                                                    <div class="w-12 h-12 shrink-0 rounded-lg bg-surface-container flex items-center justify-center text-outline">
                                                        <span class="material-symbols-outlined text-[22px]">no_photography</span>
                                                    </div>
                                                @endif

                                                <div class="flex flex-col">
                                                    <span class="font-semibold">{{ $waktu->format('H:i') }}</span>
                                                    @if (!is_null($jarak))
                                                        <span class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">
                                                            {{ number_format((float) $jarak, 0, ',', '.') }} m dari kantor
                                                        </span>
                                                    @endif
                                                    @if ($lat && $lng)
                                                        <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}"
                                                            target="_blank" rel="noopener"
                                                            class="font-label-md text-label-md text-primary hover:underline">
                                                            Lihat lokasi
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                @endforeach
                                @endif

                                {{-- Durasi --}}
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    {{ $durasi($a) }}
                                </td>

                                {{-- Status --}}
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <span class="inline-flex items-center gap-1 px-space-xs py-1 rounded-full {{ $statusStyle[$a->status] ?? 'bg-surface-container text-on-surface' }} font-label-md text-label-md font-bold whitespace-nowrap">
                                        ● {{ $statusOptions[$a->status] ?? ucfirst($a->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-space-md py-space-2xl text-center text-on-surface-variant font-body-md text-body-md">
                                    Tidak ada data absensi pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ================= PAGINATION ================= --}}
            @if ($attendances->hasPages())
                <div class="px-space-lg py-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-md text-body-md text-on-surface-variant">
                    <div>Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }}</div>
                    <div class="flex gap-1.5">
                        @if ($attendances->onFirstPage())
                            <span class="{{ $pgBase }} opacity-50">‹</span>
                        @else
                            <a href="{{ $attendances->previousPageUrl() }}" class="{{ $pgBase }} hover:bg-surface-container">‹</a>
                        @endif

                        @foreach ($attendances->getUrlRange(max(1, $attendances->currentPage() - 2), min($attendances->lastPage(), $attendances->currentPage() + 2)) as $page => $url)
                            @if ($page == $attendances->currentPage())
                                <span class="{{ $pgBase }} bg-primary text-on-primary border-primary">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $pgBase }} hover:bg-surface-container">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($attendances->hasMorePages())
                            <a href="{{ $attendances->nextPageUrl() }}" class="{{ $pgBase }} hover:bg-surface-container">›</a>
                        @else
                            <span class="{{ $pgBase }} opacity-50">›</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ================= LIGHTBOX FOTO ================= --}}
    <div id="photo-modal"
        style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.6); backdrop-filter:blur(4px);"
        role="dialog" aria-modal="true">
        <div class="w-full max-w-lg rounded-2xl bg-surface-container-lowest shadow-2xl overflow-hidden">
            <div class="flex items-start justify-between gap-space-sm p-space-md">
                <div class="flex flex-col min-w-0">
                    <span id="photo-title" class="font-headline-sm text-headline-sm text-on-surface truncate">–</span>
                    <span id="photo-caption" class="font-body-sm text-body-sm text-on-surface-variant">–</span>
                </div>
                <button type="button" id="photo-close" aria-label="Tutup"
                    class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>
            <div class="bg-inverse-surface">
                <img id="photo-img" alt="Foto absensi" class="w-full h-auto max-h-[70vh] object-contain mx-auto">
            </div>
            <div class="p-space-md flex justify-end">
                <a id="photo-open" href="#" target="_blank" rel="noopener"
                    class="px-space-md py-2 rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    <span>Buka ukuran penuh</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script>
        (function() {
            const $ = (id) => document.getElementById(id);
            const modal = $('photo-modal');

            function open(btn) {
                $('photo-title').textContent = btn.dataset.title || '';
                $('photo-caption').textContent = btn.dataset.caption || '';
                $('photo-img').src = btn.dataset.photo;
                $('photo-open').href = btn.dataset.photo;
                modal.style.display = 'flex';
            }

            function close() {
                modal.style.display = 'none';
                $('photo-img').removeAttribute('src');
            }

            document.querySelectorAll('.photo-thumb').forEach((btn) => {
                btn.addEventListener('click', () => open(btn));
            });
            $('photo-close').addEventListener('click', close);
            modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') close();
            });
        })();
    </script>
@endsection