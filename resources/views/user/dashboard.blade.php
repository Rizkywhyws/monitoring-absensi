@extends('layouts.app')

@section('title', 'Dashboard - Sistem Manajemen PKL PLN Icon Plus')

@section('breadcrumb', 'Dashboard')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <section
        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl shadow-sm">
        <div class="flex flex-col gap-space-2xs">
            <div class="flex items-center gap-space-xs">
                <span
                    class="font-label-sm text-label-sm uppercase tracking-widest text-primary font-bold">Ringkasan
                    Mahasiswa PKL</span>
                <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">{{ $kampus ?? 'PLN Icon Plus KP Jember' }}</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Selamat datang,
                {{ \Illuminate\Support\Str::of($userName ?? 'Peserta')->before(' ') }} 👋</h1>
            <p
                class="font-body-md text-body-md text-on-surface-variant flex items-center gap-space-2xs flex-wrap">
                <span class="font-semibold text-on-surface">{{ $today ?? now()->translatedFormat('l, d F Y') }}</span>
                <span>•</span>
                <span>Semangat berkarya dan selalu utamakan keselamatan kerja (K3) di lingkungan PLN Icon
                    Plus Jember</span>
            </p>
        </div>
    </section>
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-space-md">
        <div
            class="lg:col-span-7 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-md">
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-space-sm pb-space-sm border-b border-surface-container">
                <div class="flex items-center gap-space-2xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px] text-primary">badge</span>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider font-bold">Identitas
                        Resmi Peserta PKL</span>
                </div>
                <div class="flex items-center gap-space-xs">
                    <span
                        class="px-space-xs py-1 rounded-full bg-primary-container text-on-primary-container font-label-sm text-label-sm font-semibold">
                        Divisi: {{ $divisi ?? '-' }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-space-md items-start md:items-center">
                <div class="relative shrink-0">
                    <img alt="Foto Profil Mahasiswa {{ $userName ?? 'Peserta PKL' }}"
                        class="w-24 h-24 rounded-full object-cover shadow-sm bg-surface-container"
                        src="https://lh3.googleusercontent.com/aida/AEtjO1XqCy6QiwsxhGKymn_BEvHBjZIgfF4SIvAuS7ZPUoxWv41O7Dz2GgseftuWi4esTira4FZvkNrQLYyWUBJ9EUsSh-mMXG37P8VsNdYOGLIeYBmc5fbroFb6mS6R6Yl3XlCqs4Y842tVdq5C_ohR-mI-gR-aPIeWIGAdZkWGDbvVjsfAbwWOZ-mrIjrwbBsanvjDCTLBSai48IEJbWhP0AQgyR7GBIpQQLuk2vreuz4M7Kyy-hWkt2stWw" />
                    <span
                        class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                    </span>
                </div>
                <div class="flex flex-col gap-space-2xs min-w-0 flex-1">
                    <div class="flex items-center gap-space-xs flex-wrap">
                        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">{{ $userName ?? 'Nama Peserta' }}</h2>
                        <span
                            class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container text-on-surface-variant font-mono">NIM:
                            {{ $nim ?? '-' }}</span>
                    </div>
                    <div class="text-on-surface-variant font-body-sm text-body-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-secondary">school</span>
                        <span class="font-medium text-on-surface">{{ $kampus ?? '-' }}</span>
                    </div>
                    <div class="text-on-surface-variant font-body-sm text-body-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-tertiary">date_range</span>
                        <span>Periode PKL: <span class="font-semibold text-on-surface">{{ $periodeMagang ?? '-' }}</span></span>
                    </div>
                </div>
            </div>
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-space-sm p-space-sm rounded-lg bg-surface-container-low">
                <div class="flex items-center gap-space-xs">
                    <div
                        class="w-8 h-8 rounded-lg bg-surface-container-lowest flex items-center justify-center text-primary shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">supervisor_account</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Pembimbing
                            Lapangan</span>
                        <span
                            class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ $pembimbing ?? '-' }}</span>
                        <span class="font-label-sm text-label-sm text-outline truncate">Spv. Pemeliharaan
                            &amp; Operasi</span>
                    </div>
                </div>
                <div class="flex items-center gap-space-xs">
                    <div
                        class="w-8 h-8 rounded-lg bg-surface-container-lowest flex items-center justify-center text-secondary shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Kelompok &amp;
                            Penugasan</span>
                        <span
                            class="font-label-md text-label-md text-on-surface font-semibold truncate">Divisi:
                            {{ $divisi ?? '-' }}</span>
                        <span class="font-label-sm text-label-sm text-outline truncate">Ditentukan oleh
                            Admin PLN</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-span-5 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col justify-between gap-space-md"
            id="absensi-card">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-[20px] text-tertiary">search_off</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Presensi Hari
                        Ini</h2>
                </div>
                <span
                    class="px-space-xs py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant font-label-sm text-label-sm font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-tertiary animate-ping"></span> Belum Check-in
                </span>
            </div>
            <div class="grid grid-cols-2 gap-space-xs">
                <div class="p-space-xs rounded-lg bg-surface-container-low flex flex-col gap-0.5">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Jam Kerja
                        Normal</span>
                    <span class="font-label-md text-label-md text-on-surface font-bold">08:00 – 17:00
                        WIB</span>
                    <span class="font-label-sm text-label-sm text-outline">Toleransi s/d 08:15</span>
                </div>
                <div class="p-space-xs rounded-lg bg-surface-container-low flex flex-col gap-0.5">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Lokasi Target</span>
                    <span class="font-label-md text-label-md text-on-surface font-bold truncate">PLN Icon
                        Plus Jember</span>
                    <span class="font-label-sm text-label-sm text-primary font-semibold">Radius Kantor
                        100m</span>
                </div>
            </div>
            <div
                class="flex items-center justify-between p-space-xs rounded-lg bg-primary-fixed/20 text-on-primary-fixed-variant">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[18px]">near_me</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm font-bold text-on-primary-fixed">GPS
                            Terkunci: Radius 42m</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Sesuai perimeter
                            geofence kantor</span>
                    </div>
                </div>
                <span
                    class="px-2 py-0.5 rounded bg-surface-container-lowest text-primary font-label-sm text-label-sm font-bold shadow-xs">Akurat</span>
            </div>
            <div
                class="flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm px-1">
                <span>Check-in Terakhir:</span>
                <span class="font-medium text-on-surface">Kemarin (04 Sep), 07:55 WIB • Hadir</span>
            </div>
            <a href="{{ route('absensi') }}"
                class="w-full py-3 px-space-md rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-space-xs">
                <span class="material-symbols-outlined text-[20px]">photo_camera_front</span>
                <span>Ambil Presensi Sekarang (Check-in)</span>
            </a>
        </div>
    </section>

    {{-- ================= 3 Kartu Statistik: Tiket, Hadir, Tidak Hadir ================= --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
        {{-- Total Tiket --}}
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Total
                    Tiket</span>
                <span
                    class="w-8 h-8 rounded-lg bg-secondary-fixed text-on-secondary-fixed-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">confirmation_number</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold text-on-surface">{{ $totalTiket ?? 0 }}</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm font-bold">
                    {{ $tiketSelesai ?? 0 }} Selesai
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-outline">Seluruh tiket penugasan kamu</span>
        </div>

        {{-- Total Absen Hadir --}}
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Total
                    Absen Hadir</span>
                <span
                    class="w-8 h-8 rounded-lg bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold text-primary">{{ $totalHadir ?? 0 }}</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-bold">
                    {{ $totalTelat ?? 0 }} Telat
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-outline">Sudah check-in (termasuk yang telat)</span>
        </div>

        {{-- Total Tidak Hadir --}}
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Total
                    Tidak Hadir</span>
                <span
                    class="w-8 h-8 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">event_busy</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold {{ ($totalTidakHadir ?? 0) > 0 ? 'text-error' : 'text-on-surface' }}">{{ $totalTidakHadir ?? 0 }}</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm font-bold">
                    Hari Kerja
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-outline">Hari kerja tanpa catatan absensi</span>
        </div>
    </section>

    {{-- ================= Rekap Riwayat Absensi ================= --}}
    @php
        $riwayatAbsensi = $riwayatAbsensi ?? collect();

        $statusMap = [
            'hadir' => ['label' => 'Hadir', 'class' => 'bg-primary-fixed/40 text-on-primary-fixed-variant', 'icon' => 'check_circle'],
            'telat' => ['label' => 'Telat', 'class' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',  'icon' => 'schedule'],
        ];

        $rekapHadir = $riwayatAbsensi->where('status', 'hadir')->count();
        $rekapTelat = $riwayatAbsensi->where('status', 'telat')->count();
        $rekapAll   = $riwayatAbsensi->count();
    @endphp

    <section class="grid grid-cols-1 gap-space-md">
        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                <div class="flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Rekap Riwayat Absensi</h2>
                    <span class="font-label-sm text-label-sm text-outline">(7 hari terakhir)</span>
                </div>
                <a class="font-label-md text-label-md text-primary hover:text-primary-container font-bold flex items-center gap-1 transition-colors"
                    href="{{ route('absensi') }}">
                    Lihat Semua
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-3 gap-space-xs">
                <div class="p-space-xs rounded-lg bg-surface-container-low flex items-center justify-between">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Tepat Waktu</span>
                    <span class="font-label-lg text-label-lg font-bold text-primary">{{ $rekapHadir }}</span>
                </div>
                <div class="p-space-xs rounded-lg bg-surface-container-low flex items-center justify-between">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Telat</span>
                    <span class="font-label-lg text-label-lg font-bold {{ $rekapTelat > 0 ? 'text-tertiary' : 'text-on-surface' }}">{{ $rekapTelat }}</span>
                </div>
                <div class="p-space-xs rounded-lg bg-surface-container-low flex items-center justify-between">
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Total Tercatat</span>
                    <span class="font-label-lg text-label-lg font-bold text-on-surface">{{ $rekapAll }}</span>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto rounded-xl">
                <table class="w-full text-left border-collapse min-w-[640px]">
                    <thead>
                        <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                            <th class="px-space-sm py-space-xs font-bold">Tanggal</th>
                            <th class="px-space-sm py-space-xs font-bold">Check-in</th>
                            <th class="px-space-sm py-space-xs font-bold">Check-out</th>
                            <th class="px-space-sm py-space-xs font-bold">Durasi</th>
                            <th class="px-space-sm py-space-xs font-bold">Jarak dari Kantor</th>
                            <th class="px-space-sm py-space-xs font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-sm text-body-sm text-on-surface">
                        @forelse ($riwayatAbsensi as $absen)
                            @php
                                $status  = $statusMap[$absen->status] ?? ['label' => ucfirst($absen->status), 'class' => 'bg-surface-container text-on-surface-variant', 'icon' => 'help'];
                                $tanggal = \Illuminate\Support\Carbon::parse($absen->attendance_date);
                                $in      = $absen->check_in  ? \Illuminate\Support\Carbon::parse($absen->check_in)  : null;
                                $out     = $absen->check_out ? \Illuminate\Support\Carbon::parse($absen->check_out) : null;
                                $durasi  = ($in && $out) ? $in->diff($out)->format('%hj %im') : '-';
                            @endphp
                            <tr class="border-b border-surface-container hover:bg-surface-container-low transition-colors">
                                <td class="px-space-sm py-space-xs">
                                    <div class="flex flex-col">
                                        <span class="font-semibold">{{ $tanggal->translatedFormat('d M Y') }}</span>
                                        <span class="font-label-sm text-label-sm text-outline">{{ $tanggal->translatedFormat('l') }}</span>
                                    </div>
                                </td>
                                <td class="px-space-sm py-space-xs font-mono">{{ $in ? $in->format('H:i') . ' WIB' : '-' }}</td>
                                <td class="px-space-sm py-space-xs font-mono">{{ $out ? $out->format('H:i') . ' WIB' : 'Belum check-out' }}</td>
                                <td class="px-space-sm py-space-xs">{{ $durasi }}</td>
                                <td class="px-space-sm py-space-xs">
                                    <span class="flex items-center gap-1 text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[14px]">near_me</span>
                                        {{ !is_null($absen->check_in_distance) ? number_format($absen->check_in_distance, 0) . ' m' : '-' }}
                                    </span>
                                </td>
                                <td class="px-space-sm py-space-xs">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-label-sm text-label-sm font-bold {{ $status['class'] }}">
                                        <span class="material-symbols-outlined text-[14px]">{{ $status['icon'] }}</span>
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-space-sm py-space-lg text-center text-outline">
                                    Belum ada riwayat absensi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection