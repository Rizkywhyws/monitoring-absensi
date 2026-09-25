@extends('layouts.App')

@section('title', 'Absensi - Sistem Manajemen PKL PLN Icon Plus')

@section('breadcrumb')
    Presensi &amp; Kehadiran <span class="material-symbols-outlined text-[14px] align-middle">chevron_right</span>
    <span class="text-primary font-label-md">Absensi</span>
@endsection

@section('extra-styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
    <style>
        #location-map {
            z-index: 0;
        }

        .leaflet-control-attribution {
            font-size: 9px !important;
        }
    </style>
@endsection

@section('content')
<div class="flex flex-col w-full gap-space-xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-sm">
                <span class="font-headline-lg text-headline-lg text-on-surface">Absensi</span>
                <span
                    class="px-space-xs py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm tracking-wide">PORTAL
                    SISWA PKL</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant">Catat kehadiran Anda hari ini
                dengan verifikasi presisi geolokasi terintegrasi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm">
            <div
                class="flex items-center gap-2 px-space-md py-space-xs rounded-xl bg-surface-container-low shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Hari
                        &amp; Tanggal</span>
                    <span class="font-label-md text-label-md text-on-surface">{{ $today ?? now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
            <div
                class="flex items-center gap-2 px-space-md py-space-xs rounded-xl bg-primary/10 shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">work_history</span>
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-primary leading-none">Jadwal
                        Penugasan</span>
                    <span class="font-label-md text-label-md text-primary">Shift Pagi: 08:00 – 17:00
                        WIB</span>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-xl items-start">
        <div class="xl:col-span-7 flex flex-col gap-space-xl">
            <div
                class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm p-space-xl">
                <div
                    class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-primary/5 blur-3xl pointer-events-none">
                </div>
                <div class="relative z-10 flex flex-col gap-space-lg">
                    <div class="flex items-center justify-between gap-space-sm">
                        <div class="flex items-center gap-space-xs">
                            <span
                                class="material-symbols-outlined text-primary text-[22px]">fingerprint</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface">Status Presensi
                                Harian</span>
                        </div>
                        <div
                            class="flex items-center gap-2 px-space-md py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed">
                            <span class="w-2 h-2 rounded-full bg-tertiary animate-ping"></span>
                            <span class="font-label-md text-label-md">Belum Check-in</span>
                        </div>
                    </div>
                    <div
                        class="flex flex-col md:flex-row items-center justify-between gap-space-md p-space-lg rounded-xl bg-surface-container-low">
                        <div class="flex flex-col items-center md:items-start">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                                <span
                                    class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Waktu
                                    Saat Ini</span>
                            </div>
                            <div class="font-stat-counter text-[40px] leading-tight text-on-surface font-bold tracking-tight my-1"
                                id="realtime-clock">
                                07:45:12 <span
                                    class="font-label-lg text-label-lg text-on-surface-variant font-medium">WIB</span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col gap-1 items-end self-stretch justify-center md:pl-space-lg md:border-l md:border-surface-variant/40">
                            <span class="font-label-sm text-label-sm text-on-surface-variant">Tenggat
                                Check-in Tepat Waktu</span>
                            <span class="font-headline-sm text-headline-sm text-primary">08:15:00
                                WIB</span>
                            <span class="font-label-sm text-label-sm text-tertiary">Sisa toleransi: 29
                                Menit 48 Detik</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-space-sm">
                        <button
                            class="group relative w-full h-14 rounded-xl bg-primary text-on-primary font-headline-sm text-headline-sm shadow-md hover:bg-primary-container active:scale-[0.99] transition-all flex items-center justify-center gap-space-sm overflow-hidden"
                            id="btn-checkin" type="button">
                            <span
                                class="material-symbols-outlined text-[24px] group-hover:scale-110 transition-transform">photo_camera_front</span>
                            <span>Check-in Sekarang (Kamera &amp; GPS)</span>
                            <span
                                class="material-symbols-outlined text-[20px] opacity-80 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </button>
                        <div
                            class="flex items-start gap-space-xs px-space-sm py-2 rounded-lg bg-surface-container-low">
                            <span
                                class="material-symbols-outlined text-outline text-[18px] mt-0.5">info</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                Batas toleransi kehadiran adalah pukul <strong>08:15 WIB</strong>. Pastikan
                                perangkat mengaktifkan sensor lokasi presisi tinggi dan Anda berada di dalam
                                radius zona kerja resmi. Browser akan meminta izin akses kamera dan lokasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-lg">
                <div class="flex items-center justify-between flex-wrap gap-space-sm">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[22px]">radar</span>
                        <div class="flex flex-col">
                            <span class="font-headline-sm text-headline-sm text-on-surface">Verifikasi
                                Lokasi &amp; Geofencing</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant" id="gps-status-subtitle">Menunggu izin akses lokasi perangkat</span>
                        </div>
                    </div>
                    <span
                        class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm flex items-center gap-1.5"
                        id="gps-status-badge">
                        <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                        <span id="gps-status-text">Belum Aktif</span>
                    </span>
                </div>
                <div class="relative w-full h-72 rounded-xl overflow-hidden shadow-inner bg-surface-container-low">
                    <div class="w-full h-full" id="location-map"></div>
                    <button
                        class="absolute bottom-3 right-3 z-[400] h-10 px-space-md rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-md hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1.5"
                        id="btn-locate-me" type="button">
                        <span class="material-symbols-outlined text-[18px]">my_location</span>
                        <span>Gunakan Lokasi Saya</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-space-sm">
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                        <div
                            class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">gps_fixed</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-label-sm text-label-sm text-on-surface-variant truncate">Status
                                GPS</span>
                            <span
                                class="font-label-lg text-label-lg text-on-surface font-semibold truncate"
                                id="metric-gps-status">Belum Aktif</span>
                        </div>
                    </div>
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                        <div
                            class="w-10 h-10 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">near_me</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-label-sm text-label-sm text-on-surface-variant truncate">Jarak
                                ke Kantor</span>
                            <span
                                class="font-label-lg text-label-lg text-on-surface font-semibold truncate"
                                id="metric-distance">–</span>
                        </div>
                    </div>
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                        <div
                            class="w-10 h-10 rounded-lg bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">crisis_alert</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span
                                class="font-label-sm text-label-sm text-on-surface-variant truncate">Akurasi
                                GPS</span>
                            <span
                                class="font-label-lg text-label-lg text-on-surface font-semibold truncate"
                                id="metric-accuracy">–</span>
                        </div>
                    </div>
                </div>
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-space-md pt-space-xs">
                    <div class="flex items-start gap-2 max-w-lg">
                        <span
                            class="material-symbols-outlined text-outline text-[20px] mt-0.5 shrink-0">corporate_fare</span>
                        <div class="flex flex-col">
                            <span class="font-label-md text-label-md text-on-surface">Kantor PLN Icon Plus
                                KP Jember</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Jl. Gajah Mada
                                No. 120, Kaliwates, Kec. Kaliwates, Kabupaten Jember, Jawa Timur
                                68131</span>
                        </div>
                    </div>
                    <button
                        class="shrink-0 h-10 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center justify-center gap-1.5"
                        id="btn-refresh-location" type="button">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        <span>Perbarui Lokasi</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="xl:col-span-5 flex flex-col gap-space-xl">
            <div
                class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-low">
                    <div class="w-11 h-11 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold shrink-0">{{ $pembimbingInitials ?? 'HK' }}</div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span
                            class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Mentor
                            Lapangan Penanggung Jawab</span>
                        <span
                            class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $pembimbing ?? '-' }}</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant truncate">Spv.
                            Operasi &amp; Pemeliharaan Jaringan</span>
                    </div>
                    <span class="material-symbols-outlined text-primary text-[20px]">verified_user</span>
                </div>
            </div>
            <div
                class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                        <span class="font-headline-sm text-headline-sm text-on-surface">Riwayat
                            Terakhir</span>
                    </div>
                    <a class="font-label-md text-label-md text-primary hover:underline flex items-center gap-0.5"
                        href="#/riwayat-absensi">
                        <span>Lihat Semua</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <div class="flex flex-col gap-space-sm">
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-lg text-label-lg text-on-surface font-semibold">Kamis,
                                    04 Sep 2026</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[10px]">Tepat
                                    Waktu</span>
                            </div>
                            <button
                                class="text-primary hover:text-primary-container font-label-sm text-label-sm flex items-center gap-0.5"
                                type="button">
                                <span>Detail</span>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                            </button>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant pt-1">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-primary text-[16px]">login</span>
                                    07:55 WIB
                                </span>
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-secondary text-[16px]">logout</span>
                                    17:08 WIB
                                </span>
                            </div>
                            <span
                                class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface">
                                <span
                                    class="material-symbols-outlined text-outline text-[14px]">pin_drop</span>
                                38m dari kantor
                            </span>
                        </div>
                    </div>
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-lg text-label-lg text-on-surface font-semibold">Rabu,
                                    03 Sep 2026</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[10px]">Tepat
                                    Waktu</span>
                            </div>
                            <button
                                class="text-primary hover:text-primary-container font-label-sm text-label-sm flex items-center gap-0.5"
                                type="button">
                                <span>Detail</span>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                            </button>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant pt-1">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-primary text-[16px]">login</span>
                                    07:48 WIB
                                </span>
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-secondary text-[16px]">logout</span>
                                    17:15 WIB
                                </span>
                            </div>
                            <span
                                class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface">
                                <span
                                    class="material-symbols-outlined text-outline text-[14px]">pin_drop</span>
                                45m dari kantor
                            </span>
                        </div>
                    </div>
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-lg text-label-lg text-on-surface font-semibold">Selasa,
                                    02 Sep 2026</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[10px]">Tepat
                                    Waktu</span>
                            </div>
                            <button
                                class="text-primary hover:text-primary-container font-label-sm text-label-sm flex items-center gap-0.5"
                                type="button">
                                <span>Detail</span>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                            </button>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant pt-1">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-primary text-[16px]">login</span>
                                    08:02 WIB
                                </span>
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-secondary text-[16px]">logout</span>
                                    17:00 WIB
                                </span>
                            </div>
                            <span
                                class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface">
                                <span
                                    class="material-symbols-outlined text-outline text-[14px]">pin_drop</span>
                                50m dari kantor
                            </span>
                        </div>
                    </div>
                    <div
                        class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors flex flex-col gap-space-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-label-lg text-label-lg text-on-surface font-semibold">Senin,
                                    01 Sep 2026</span>
                                <span
                                    class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[10px]">Tepat
                                    Waktu</span>
                            </div>
                            <button
                                class="text-primary hover:text-primary-container font-label-sm text-label-sm flex items-center gap-0.5"
                                type="button">
                                <span>Detail</span>
                                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                            </button>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant pt-1">
                            <div class="flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-primary text-[16px]">login</span>
                                    07:50 WIB
                                </span>
                                <span class="flex items-center gap-1">
                                    <span
                                        class="material-symbols-outlined text-secondary text-[16px]">logout</span>
                                    17:05 WIB
                                </span>
                            </div>
                            <span
                                class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface">
                                <span
                                    class="material-symbols-outlined text-outline text-[14px]">pin_drop</span>
                                41m dari kantor
                            </span>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-space-xs p-space-sm rounded-lg bg-surface-container flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
                    <span class="material-symbols-outlined text-secondary text-[18px]">verified</span>
                    <span>Seluruh rekaman log telah divalidasi pembimbing lapangan.</span>
                </div>
            </div>
        </div>
    </div>
    <div class="fixed inset-0 z-50 hidden bg-inverse-surface/40 backdrop-blur-sm flex items-center justify-center p-space-md"
        id="checkin-modal">
        <div
            class="relative w-full max-w-lg rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl flex flex-col gap-space-md">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">center_focus_strong</span>
                    </div>
                    <span class="font-headline-sm text-headline-sm text-on-surface">Verifikasi Wajah &amp;
                        Lokasi</span>
                </div>
                <button
                    class="p-1 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors"
                    id="modal-close" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div
                class="relative w-full h-64 rounded-xl bg-inverse-surface overflow-hidden flex items-center justify-center">
                <video autoplay class="w-full h-full object-cover" id="camera-preview" muted playsinline></video>
                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-inverse-on-surface/70 font-body-sm text-body-sm"
                    id="camera-placeholder">
                    <span class="material-symbols-outlined text-[36px]">videocam</span>
                    <span>Menunggu izin akses kamera…</span>
                </div>
                <canvas class="hidden" id="camera-canvas"></canvas>
                <div
                    class="absolute inset-0 border-4 border-dashed border-primary/60 rounded-xl pointer-events-none m-6">
                </div>
                <div
                    class="absolute bottom-3 px-3 py-1 rounded-full bg-surface-container-lowest/90 backdrop-blur-sm font-label-sm text-label-sm text-on-surface flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                    Posisikan wajah di dalam bingkai
                </div>
            </div>
            <div
                class="flex flex-col gap-1 p-space-sm rounded-lg bg-surface-container-low font-body-sm text-body-sm text-on-surface-variant">
                <div class="flex justify-between">
                    <span>Lokasi Terverifikasi:</span>
                    <span class="font-label-md text-label-md text-on-surface" id="modal-location-text">Mengambil lokasi…</span>
                </div>
                <div class="flex justify-between">
                    <span>Waktu Check-in:</span>
                    <span class="font-label-md text-label-md text-primary" id="modal-time-stamp">07:45:12
                        WIB</span>
                </div>
            </div>
            <div class="flex gap-space-sm pt-2">
                <button
                    class="flex-1 h-11 rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors"
                    id="modal-cancel-btn" type="button">
                    Batal
                </button>
                <button
                    class="flex-1 h-11 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container shadow transition-colors flex items-center justify-center gap-1.5"
                    id="modal-submit-btn" type="button">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Kirim Presensi
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function() {
        const OFFICE_LOCATION = { lat: -8.15429406429342, lng: 113.7025026220336 };
        const OFFICE_RADIUS_METERS = 100;
        const OFFICE_ADDRESS = "Kantor PLN Icon Plus KP Jember, Jl. Gajah Mada No. 120, Kaliwates, Jember";

        const clockElement = document.getElementById('realtime-clock');
        const modalClockElement = document.getElementById('modal-time-stamp');

        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeString = `${hours}:${minutes}:${seconds}`;

            if (clockElement) {
                clockElement.innerHTML =
                    `${timeString} <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">WIB</span>`;
            }
            if (modalClockElement) {
                modalClockElement.innerText = `${timeString} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        const map = L.map('location-map', {
            zoomControl: true,
            attributionControl: true
        }).setView([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const officeMarker = L.marker([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng])
            .addTo(map)
            .bindPopup(`<strong>${OFFICE_ADDRESS}</strong><br>Radius geofence: ${OFFICE_RADIUS_METERS}m`);
        const officeCircle = L.circle([OFFICE_LOCATION.lat, OFFICE_LOCATION.lng], {
            radius: OFFICE_RADIUS_METERS,
            color: '#006857',
            fillColor: '#006857',
            fillOpacity: 0.15,
            weight: 2
        }).addTo(map);

        let userMarker = null;
        let userAccuracyCircle = null;

        function haversineDistanceMeters(lat1, lng1, lat2, lng2) {
            const R = 6371000;
            const toRad = (deg) => (deg * Math.PI) / 180;
            const dLat = toRad(lat2 - lat1);
            const dLng = toRad(lng2 - lng1);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                Math.sin(dLng / 2) * Math.sin(dLng / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        const gpsStatusText = document.getElementById('gps-status-text');
        const gpsStatusBadge = document.getElementById('gps-status-badge');
        const gpsStatusSubtitle = document.getElementById('gps-status-subtitle');
        const metricGpsStatus = document.getElementById('metric-gps-status');
        const metricDistance = document.getElementById('metric-distance');
        const metricAccuracy = document.getElementById('metric-accuracy');
        const modalLocationText = document.getElementById('modal-location-text');

        let lastKnownPosition = null;

        function setGpsUIState(state, message) {
            const badgeColors = {
                idle: 'bg-surface-container-high text-on-surface-variant',
                loading: 'bg-secondary-fixed text-on-secondary-fixed-variant',
                ok: 'bg-primary-fixed/40 text-on-primary-fixed-variant',
                error: 'bg-error-container text-on-error-container'
            };
            gpsStatusBadge.className = `px-space-sm py-1 rounded-full font-label-sm text-label-sm flex items-center gap-1.5 ${badgeColors[state]}`;
            gpsStatusText.textContent = message;
        }

        function locateUser() {
            if (!('geolocation' in navigator)) {
                setGpsUIState('error', 'Tidak Didukung');
                gpsStatusSubtitle.textContent = 'Perangkat/browser ini tidak mendukung geolokasi.';
                metricGpsStatus.textContent = 'Tidak Didukung';
                return;
            }
            setGpsUIState('loading', 'Mencari Lokasi…');
            gpsStatusSubtitle.textContent = 'Meminta izin akses lokasi dari browser…';
            metricGpsStatus.textContent = 'Mencari…';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude, accuracy } = position.coords;
                    lastKnownPosition = { latitude, longitude, accuracy };
                    const distance = haversineDistanceMeters(latitude, longitude, OFFICE_LOCATION.lat, OFFICE_LOCATION.lng);
                    const withinRadius = distance <= OFFICE_RADIUS_METERS;

                    if (userMarker) {
                        userMarker.setLatLng([latitude, longitude]);
                    } else {
                        userMarker = L.marker([latitude, longitude], {
                            title: 'Lokasi Anda'
                        }).addTo(map).bindPopup('Posisi Anda saat ini');
                    }
                    if (userAccuracyCircle) {
                        userAccuracyCircle.setLatLng([latitude, longitude]).setRadius(accuracy);
                    } else {
                        userAccuracyCircle = L.circle([latitude, longitude], {
                            radius: accuracy,
                            color: '#006398',
                            fillColor: '#006398',
                            fillOpacity: 0.12,
                            weight: 1
                        }).addTo(map);
                    }

                    const bounds = L.latLngBounds([
                        [OFFICE_LOCATION.lat, OFFICE_LOCATION.lng],
                        [latitude, longitude]
                    ]);
                    map.fitBounds(bounds, { padding: [40, 40], maxZoom: 17 });

                    setGpsUIState('ok', withinRadius ? 'Terkunci & Valid' : 'Di Luar Radius');
                    gpsStatusSubtitle.textContent = withinRadius
                        ? 'Lokasi Anda berada di dalam radius geofence kantor.'
                        : 'Lokasi Anda berada di luar radius geofence kantor.';
                    metricGpsStatus.textContent = withinRadius ? 'Terkunci & Aktif' : 'Di Luar Radius';
                    metricDistance.textContent = `${Math.round(distance)} meter ${withinRadius ? '(Aman)' : '(Terlalu Jauh)'}`;
                    metricAccuracy.textContent = `±${Math.round(accuracy)} meter`;
                    modalLocationText.textContent = `${OFFICE_ADDRESS.split(',')[0]} (${Math.round(distance)}m)`;
                },
                (error) => {
                    let message = 'Gagal Mengambil Lokasi';
                    if (error.code === error.PERMISSION_DENIED) {
                        message = 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser untuk melanjutkan.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        message = 'Lokasi tidak tersedia saat ini.';
                    } else if (error.code === error.TIMEOUT) {
                        message = 'Waktu permintaan lokasi habis.';
                    }
                    setGpsUIState('error', 'Gagal / Ditolak');
                    gpsStatusSubtitle.textContent = message;
                    metricGpsStatus.textContent = 'Gagal';
                    metricDistance.textContent = '–';
                    metricAccuracy.textContent = '–';
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }

        document.getElementById('btn-locate-me').addEventListener('click', locateUser);
        document.getElementById('btn-refresh-location').addEventListener('click', () => {
            const icon = document.getElementById('btn-refresh-location').querySelector('.material-symbols-outlined');
            if (icon) icon.classList.add('animate-spin');
            locateUser();
            setTimeout(() => { if (icon) icon.classList.remove('animate-spin'); }, 800);
        });

        setTimeout(() => map.invalidateSize(), 200);

        const checkinBtn = document.getElementById('btn-checkin');
        const modal = document.getElementById('checkin-modal');
        const closeBtn = document.getElementById('modal-close');
        const cancelBtn = document.getElementById('modal-cancel-btn');
        const submitBtn = document.getElementById('modal-submit-btn');
        const video = document.getElementById('camera-preview');
        const cameraPlaceholder = document.getElementById('camera-placeholder');
        let mediaStream = null;

        async function startCamera() {
            if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
                cameraPlaceholder.innerHTML =
                    '<span class="material-symbols-outlined text-[36px]">videocam_off</span><span>Kamera tidak didukung di perangkat/browser ini.</span>';
                return;
            }
            try {
                mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                video.srcObject = mediaStream;
                cameraPlaceholder.classList.add('hidden');
            } catch (err) {
                let msg = 'Tidak dapat mengakses kamera.';
                if (err && err.name === 'NotAllowedError') msg = 'Izin kamera ditolak. Aktifkan izin kamera di pengaturan browser.';
                if (err && err.name === 'NotFoundError') msg = 'Tidak ada kamera yang terdeteksi pada perangkat ini.';
                cameraPlaceholder.innerHTML = `<span class="material-symbols-outlined text-[36px]">videocam_off</span><span>${msg}</span>`;
                cameraPlaceholder.classList.remove('hidden');
            }
        }

        function stopCamera() {
            if (mediaStream) {
                mediaStream.getTracks().forEach(track => track.stop());
                mediaStream = null;
            }
            video.srcObject = null;
            cameraPlaceholder.classList.remove('hidden');
            cameraPlaceholder.innerHTML = '<span class="material-symbols-outlined text-[36px]">videocam</span><span>Menunggu izin akses kamera…</span>';
        }

        if (checkinBtn && modal) {
            checkinBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
                startCamera();
                if (!lastKnownPosition) locateUser();
            });
        }

        function closeModal() {
            if (modal) modal.classList.add('hidden');
            stopCamera();
        }

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        if (submitBtn) {
            submitBtn.addEventListener('click', () => {
                submitBtn.innerHTML =
                    '<span class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span> Memproses...';
                setTimeout(() => {
                    alert('Check-in Berhasil! Presensi Anda tercatat di sistem.');
                    closeModal();
                    submitBtn.innerHTML =
                        '<span class="material-symbols-outlined text-[18px]">check_circle</span> Kirim Presensi';
                    if (checkinBtn) {
                        checkinBtn.disabled = true;
                        checkinBtn.classList.add('opacity-60', 'cursor-not-allowed');
                        checkinBtn.innerHTML =
                            '<span class="material-symbols-outlined text-[24px]">task_alt</span> Sudah Check-in Hari Ini';
                    }
                }, 1200);
            });
        }
    })();
</script>
@endsection