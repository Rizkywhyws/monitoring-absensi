@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('breadcrumb', 'Admin Workspace › Riwayat Absensi')
@section('content')
    @php
        $statusStyle = [
            'hadir' => 'bg-primary-fixed text-on-primary-fixed',
            'telat' => 'bg-error-container text-on-error-container',
            'sakit' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
            'izin' => 'bg-surface-container-high text-on-surface',
        ];
        $pgBase = 'px-space-sm py-space-xs rounded-lg border border-outline-variant font-label-lg text-label-lg';
    @endphp
    <div class="flex flex-col w-full gap-space-lg">
        <div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Riwayat Absensi</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Rekap kehadiran peserta PKL PLN Icon Plus
                ({{ \Carbon\Carbon::parse($dari)->locale('id')->translatedFormat('d M Y') }} –
                {{ \Carbon\Carbon::parse($sampai)->locale('id')->translatedFormat('d M Y') }})
            </p>
        </div>
        @if ($errors->any())
            <div
                class="px-space-md py-space-sm rounded-xl bg-error-container text-on-error-container font-body-md text-body-md">
                {{ $errors->first() }}
            </div>
        @endif
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-space-md">
            <div class="rounded-2xl bg-surface-container-lowest shadow-sm p-space-md">
                <p class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Total Absensi</p>
                <p class="font-stat-counter text-stat-counter text-on-surface">{{ $rekap->sum() }}</p>
            </div>
            @foreach ($statusOptions as $key => $label)
                <div class="rounded-2xl bg-surface-container-lowest shadow-sm p-space-md">
                    <p class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">
                        {{ $label }}</p>
                    <p class="font-stat-counter text-stat-counter text-on-surface">{{ $rekap[$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>
        <div class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
            <div class="p-space-lg bg-surface-bright">
                <form id="attendance-filter-form" action="{{ route('admin.riwayat-absensi') }}" method="GET"
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
                        class="px-space-md py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors">Filter</button>
                    <button type="button" id="btn-open-export-modal"
                        class="inline-flex items-center gap-1.5 px-space-md py-3 rounded-xl bg-primary-fixed text-on-primary-fixed font-label-lg text-label-lg font-semibold hover:opacity-90 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                        Export PDF
                    </button>
                    <a href="{{ route('admin.riwayat-absensi') }}"
                        class="px-space-md py-3 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg font-semibold hover:bg-surface-container-high transition-colors">Reset</a>
                </form>
                <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">Pilih tombol Export PDF untuk menentukan
                    rentang bulan laporan yang ingin diunduh.</p>
            </div>
            {{-- ================= TABEL ================= --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr
                            class="bg-surface-container-low text-left text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
                            @foreach (['Peserta', 'Tanggal', 'Check-in', 'Kantor', 'Status'] as $th)
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
                                        @if ($a->user?->division)
                                            · {{ $a->user->division }}
                                        @endif
                                    </div>
                                </td>
                                {{-- Tanggal --}}
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    {{ $tanggal }}</td>
                                @if (in_array($a->status, ['sakit', 'izin'], true))
                                    <td colspan="2"
                                        class="px-space-md py-space-sm border-b border-surface-container text-on-surface-variant">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[18px]">event_available</span>
                                            Tidak masuk — {{ $a->status === 'sakit' ? 'sakit' : 'izin' }} disetujui admin
                                        </span>
                                    </td>
                                @else
                                    @php
                                        $waktu = $a->check_in;
                                        $adaFoto = $a->check_in_photo;
                                        $jarak = $a->check_in_distance;
                                        $lat = $a->check_in_latitude;
                                        $lng = $a->check_in_longitude;
                                        $type = 'in';
                                    @endphp
                                    {{-- Check-in --}}
                                    <td class="px-space-md py-space-sm border-b border-surface-container">
                                        @if ($waktu)
                                            <div class="flex items-center gap-space-sm">
                                                @if ($adaFoto)
                                                    <button type="button"
                                                        class="photo-thumb shrink-0 rounded-lg overflow-hidden"
                                                        data-photo="{{ route('absensi.photo', [$a, $type]) }}"
                                                        data-title="{{ $a->user?->name }} — Check-in"
                                                        data-caption="{{ $tanggal }} · {{ $waktu->format('H:i:s') }} WIB"
                                                        title="Lihat foto">
                                                        <img src="{{ route('absensi.photo', [$a, $type]) }}"
                                                            alt="Foto Check-in" loading="lazy"
                                                            class="w-12 h-12 object-cover bg-surface-container hover:opacity-80 transition-opacity">
                                                    </button>
                                                @else
                                                    <div
                                                        class="w-12 h-12 shrink-0 rounded-lg bg-surface-container flex items-center justify-center text-outline">
                                                        <span
                                                            class="material-symbols-outlined text-[22px]">no_photography</span>
                                                    </div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span class="font-semibold">{{ $waktu->format('H:i') }}</span>
                                                    @if (!is_null($jarak))
                                                        <span
                                                            class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">{{ number_format((float) $jarak, 0, ',', '.') }}
                                                            m dari kantor</span>
                                                    @endif
                                                    @if (!is_null($lat) && !is_null($lng))
                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $lat }},{{ $lng }}"
                                                            target="_blank" rel="noopener"
                                                            class="font-label-md text-label-md text-primary hover:underline">Lihat
                                                            lokasi</a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                    {{-- Kantor --}}
                                    <td class="px-space-md py-space-sm border-b border-surface-container">
                                        {{ $a->office_name ?? '-' }}
                                    </td>
                                @endif
                                {{-- Status --}}
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <span
                                        class="inline-flex items-center gap-1 px-space-xs py-1 rounded-full {{ $statusStyle[$a->status] ?? 'bg-surface-container text-on-surface' }} font-label-md text-label-md font-bold whitespace-nowrap">
                                        ● {{ $statusOptions[$a->status] ?? ucfirst($a->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-space-md py-space-2xl text-center text-on-surface-variant font-body-md text-body-md">
                                    Tidak ada data absensi pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- ================= PAGINATION ================= --}}
            @if ($attendances->hasPages())
                <div
                    class="px-space-lg py-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-md text-body-md text-on-surface-variant">
                    <div>Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari
                        {{ $attendances->total() }}</div>
                    <div class="flex gap-1.5">
                        @if ($attendances->onFirstPage())
                            <span class="{{ $pgBase }} opacity-50">‹</span>
                        @else
                            <a href="{{ $attendances->previousPageUrl() }}"
                                class="{{ $pgBase }} hover:bg-surface-container">‹</a>
                        @endif
                        @foreach ($attendances->getUrlRange(max(1, $attendances->currentPage() - 2), min($attendances->lastPage(), $attendances->currentPage() + 2)) as $page => $url)
                            @if ($page == $attendances->currentPage())
                                <span
                                    class="{{ $pgBase }} bg-primary text-on-primary border-primary">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                    class="{{ $pgBase }} hover:bg-surface-container">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if ($attendances->hasMorePages())
                            <a href="{{ $attendances->nextPageUrl() }}"
                                class="{{ $pgBase }} hover:bg-surface-container">›</a>
                        @else
                            <span class="{{ $pgBase }} opacity-50">›</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    {{-- ================= MODAL EXPORT PDF ================= --}}
    <div id="export-modal"
        style="display:none; position:fixed; inset:0; z-index:70; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.55); backdrop-filter:blur(4px);"
        role="dialog" aria-modal="true" aria-labelledby="export-modal-title">
        <div class="w-full max-w-md rounded-2xl bg-surface-container-lowest shadow-2xl overflow-hidden">
            <div class="flex items-start justify-between gap-space-sm p-space-lg border-b border-surface-container">
                <div class="flex items-start gap-space-sm">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">picture_as_pdf</span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <h2 id="export-modal-title" class="font-headline-sm text-headline-sm text-on-surface">Export
                            Laporan Absensi</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih rentang bulan yang ingin
                            direkap.</p>
                    </div>
                </div>
                <button type="button" id="export-modal-close" aria-label="Tutup"
                    class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>
            <form id="export-pdf-form" action="{{ route('admin.riwayat-absensi.pdf') }}" method="GET"
                class="p-space-lg flex flex-col gap-space-md">
                <input type="hidden" name="keyword" id="export-keyword" value="{{ request('keyword') }}">
                <input type="hidden" name="status" id="export-status" value="{{ request('status') }}">
                <div class="flex flex-col gap-1.5">
                    <label for="export-bulan-dari" class="font-label-md text-label-md text-on-surface">Dari bulan</label>
                    <input type="month" id="export-bulan-dari" name="bulan_dari" required
                        value="{{ \Carbon\Carbon::parse($dari)->format('Y-m') }}"
                        class="w-full bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary/40">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="export-bulan-sampai" class="font-label-md text-label-md text-on-surface">Sampai
                        bulan</label>
                    <input type="month" id="export-bulan-sampai" name="bulan_sampai" required
                        value="{{ \Carbon\Carbon::parse($sampai)->format('Y-m') }}"
                        class="w-full bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary/40">
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Laporan mencakup seluruh tanggal dari awal
                    bulan pertama sampai akhir bulan terakhir.</p>
                <p id="export-range-error" class="hidden font-body-sm text-body-sm text-error" role="alert">Bulan akhir
                    tidak boleh lebih awal daripada bulan awal.</p>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-space-sm pt-space-xs">
                    <button type="button" id="export-modal-cancel"
                        class="h-11 px-space-md rounded-xl bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors">Batal</button>
                    <button type="submit"
                        class="h-11 px-space-md rounded-xl bg-primary text-on-primary font-label-md text-label-md font-semibold hover:bg-primary-container transition-colors inline-flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        Unduh PDF
                    </button>
                </div>
            </form>
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
            const exportModal = $('export-modal');
            const exportForm = $('export-pdf-form');

            function openExportModal() {
                const filterForm = $('attendance-filter-form');
                $('export-keyword').value = filterForm?.querySelector('[name="keyword"]')?.value || '';
                $('export-status').value = filterForm?.querySelector('[name="status"]')?.value || '';
                $('export-range-error').classList.add('hidden');
                exportModal.style.display = 'flex';
                $('export-bulan-dari').focus();
            }

            function closeExportModal() {
                exportModal.style.display = 'none';
            }

            $('btn-open-export-modal').addEventListener('click', openExportModal);
            $('export-modal-close').addEventListener('click', closeExportModal);
            $('export-modal-cancel').addEventListener('click', closeExportModal);
            exportModal.addEventListener('click', (e) => {
                if (e.target === exportModal) closeExportModal();
            });

            exportForm.addEventListener('submit', (e) => {
                const startMonth = $('export-bulan-dari').value;
                const endMonth = $('export-bulan-sampai').value;
                const error = $('export-range-error');

                if (!startMonth || !endMonth || endMonth < startMonth) {
                    e.preventDefault();
                    error.classList.remove('hidden');
                    return;
                }

                error.classList.add('hidden');
                closeExportModal();
            });

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
            modal.addEventListener('click', (e) => {
                if (e.target === modal) close();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && exportModal.style.display === 'flex') closeExportModal();
                if (e.key === 'Escape' && modal.style.display === 'flex') close();
            });
        })();
    </script>
@endsection
