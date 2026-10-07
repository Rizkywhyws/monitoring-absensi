@extends('layouts.App')

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
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Ticket
                    Aktif</span>
                <span
                    class="w-8 h-8 rounded-lg bg-secondary-fixed text-on-secondary-fixed-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">assignment_late</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold text-on-surface">2</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm font-bold">
                    Sedang Dikerjakan
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-outline">Target penyelesaian hari ini</span>
        </div>
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Ticket
                    Selesai</span>
                <span
                    class="w-8 h-8 rounded-lg bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold text-primary">18</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-bold">
                    Bulan Ini
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-outline">Tervalidasi mentor &amp; supervisor</span>
        </div>
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Ticket
                    Revisi</span>
                <span
                    class="w-8 h-8 rounded-lg bg-tertiary-fixed text-on-tertiary-fixed-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">rate_review</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold text-tertiary">1</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-bold">
                    Perlu Cek Segera
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-error font-medium truncate">Butuh foto ulang
                dokumentasi drop core</span>
        </div>
        <div
            class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col justify-between gap-space-xs">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-semibold">Aktivitas
                    Tercatat</span>
                <span
                    class="w-8 h-8 rounded-lg bg-surface-container-highest text-on-surface flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">history_edu</span>
                </span>
            </div>
            <div class="flex items-baseline justify-between pt-1">
                <span class="font-stat-counter text-stat-counter font-bold text-on-surface">14</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm font-bold">
                    Pekan Ini
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-outline">Sinkron otomatis dengan logbook</span>
        </div>
    </section>
    <section class="grid grid-cols-1 gap-space-md">
        <div
            class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-space-xs">
                    <span
                        class="material-symbols-outlined text-primary text-[20px]">confirmation_number</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Tiket Penugasan
                        Terbaru</h2>
                </div>
                <a class="font-label-md text-label-md text-primary hover:text-primary-container font-bold flex items-center gap-1 transition-colors"
                    href="#">
                    Lihat Semua (20)
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-space-xs">
                <div
                    class="p-space-sm rounded-xl bg-surface-bright hover:bg-surface-container-low transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                    <div class="flex items-start gap-space-sm min-w-0">
                        <div
                            class="w-9 h-9 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">build</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-space-xs flex-wrap">
                                <span
                                    class="font-label-sm text-label-sm font-bold text-on-surface font-mono">TKT-00025</span>
                                <span
                                    class="px-2 py-0.5 rounded text-xs bg-surface-container font-medium text-on-surface-variant">Maintenance</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-primary-fixed/40 text-on-primary-fixed-variant font-label-sm text-label-sm font-bold">Verified</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface font-medium truncate pt-1">
                                Menyiapkan material gangguan untuk tim Servpo IKR</p>
                            <span class="font-label-sm text-label-sm text-outline">Tanggal: 05 Sep 2026 •
                                09:15 WIB</span>
                        </div>
                    </div>
                    <a class="sm:self-center shrink-0 px-space-xs py-1 rounded bg-surface-container-lowest font-label-sm text-label-sm text-primary font-bold shadow-xs hover:bg-primary hover:text-on-primary transition-all flex items-center gap-1 justify-center"
                        href="#">
                        Detail <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    </a>
                </div>
                <div
                    class="p-space-sm rounded-xl bg-surface-bright hover:bg-surface-container-low transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                    <div class="flex items-start gap-space-sm min-w-0">
                        <div
                            class="w-9 h-9 rounded-lg bg-secondary-fixed text-on-secondary-fixed-variant flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">hub</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-space-xs flex-wrap">
                                <span
                                    class="font-label-sm text-label-sm font-bold text-on-surface font-mono">TKT-00026</span>
                                <span
                                    class="px-2 py-0.5 rounded text-xs bg-surface-container font-medium text-on-surface-variant">Network</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-label-sm font-bold">On
                                    Progress</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface font-medium truncate pt-1">
                                Pengecekan terminasi Optical Distribution Cabinet (ODC)</p>
                            <span class="font-label-sm text-label-sm text-outline">Tanggal: 04 Sep 2026 •
                                13:45 WIB</span>
                        </div>
                    </div>
                    <a class="sm:self-center shrink-0 px-space-xs py-1 rounded bg-surface-container-lowest font-label-sm text-label-sm text-primary font-bold shadow-xs hover:bg-primary hover:text-on-primary transition-all flex items-center gap-1 justify-center"
                        href="#">
                        Detail <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    </a>
                </div>
                <div
                    class="p-space-sm rounded-xl bg-error-container/20 hover:bg-error-container/30 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                    <div class="flex items-start gap-space-sm min-w-0">
                        <div
                            class="w-9 h-9 rounded-lg bg-error-container text-on-error-container flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-space-xs flex-wrap">
                                <span
                                    class="font-label-sm text-label-sm font-bold text-on-surface font-mono">TKT-00027</span>
                                <span
                                    class="px-2 py-0.5 rounded text-xs bg-surface-container font-medium text-on-surface-variant">Installation</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-error text-on-error font-label-sm text-label-sm font-bold">Revision</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface font-medium truncate pt-1">
                                Dokumentasi perapian kabel Drop Core Pelanggan</p>
                            <span
                                class="font-label-sm text-label-sm text-error font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                Catatan Mentor: Lampiran foto buram, unggah ulang foto label OTB
                            </span>
                        </div>
                    </div>
                    <a class="sm:self-center shrink-0 px-space-sm py-1 rounded bg-error text-on-error font-label-sm text-label-sm font-bold shadow-xs hover:bg-on-error-container transition-all flex items-center gap-1 justify-center"
                        href="#">
                        Perbaiki <span class="material-symbols-outlined text-[14px]">edit</span>
                    </a>
                </div>
                <div
                    class="p-space-sm rounded-xl bg-surface-bright hover:bg-surface-container-low transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs">
                    <div class="flex items-start gap-space-sm min-w-0">
                        <div
                            class="w-9 h-9 rounded-lg bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">settings_ethernet</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <div class="flex items-center gap-space-xs flex-wrap">
                                <span
                                    class="font-label-sm text-label-sm font-bold text-on-surface font-mono">TKT-00024</span>
                                <span
                                    class="px-2 py-0.5 rounded text-xs bg-surface-container font-medium text-on-surface-variant">Maintenance</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-bold">Done</span>
                            </div>
                            <p class="font-body-sm text-body-sm text-on-surface font-medium truncate pt-1">
                                Pengukuran redaman kabel fiber optik OTB Segmen Barat</p>
                            <span class="font-label-sm text-label-sm text-outline">Tanggal: 03 Sep 2026 •
                                15:20 WIB</span>
                        </div>
                    </div>
                    <a class="sm:self-center shrink-0 px-space-xs py-1 rounded bg-surface-container-lowest font-label-sm text-label-sm text-primary font-bold shadow-xs hover:bg-primary hover:text-on-primary transition-all flex items-center gap-1 justify-center"
                        href="#">
                        Detail <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection