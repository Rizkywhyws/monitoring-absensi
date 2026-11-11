@extends('layouts.app')

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
            height: 100%;
            min-height: 288px;
        }

        #btn-locate-me {
            z-index: 1000;
        }

        .leaflet-control-attribution {
            font-size: 9px !important;
        }

        button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        /* ===== Indikator langkah ===== */
        .wizard-steps {
            display: flex;
            align-items: flex-start;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 92px;
        }

        .step-dot {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            background: #e3e6ea;
            color: #6b7280;
            transition: all .35s ease;
        }

        .step-dot.active {
            background: #006857;
            color: #fff;
            box-shadow: 0 0 0 6px rgba(0, 104, 87, .16);
        }

        .step-dot.done {
            background: #006857;
            color: #fff;
        }

        .step-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-align: center;
            transition: color .3s;
        }

        .step-label.active,
        .step-label.done {
            color: #006857;
        }

        .step-line {
            flex: 1;
            height: 3px;
            background: #e3e6ea;
            border-radius: 2px;
            margin: 17px 6px 0;
            position: relative;
            overflow: hidden;
        }

        .step-line::after {
            content: '';
            position: absolute;
            inset: 0;
            background: #006857;
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .45s ease;
        }

        .step-line.done::after {
            transform: scaleX(1);
        }

        .step-panel {
            animation: panel-in .35s ease both;
        }

        /* ===== Kamera ===== */
        .shutter-flash {
            position: absolute;
            inset: 0;
            background: #fff;
            opacity: 0;
            pointer-events: none;
        }

        .shutter-flash.go {
            animation: shutter .4s ease-out;
        }

        /* ===== Hasil presensi ===== */
        .result-card {
            position: relative;
            overflow: hidden;
            animation: result-card-in .4s cubic-bezier(.2, .9, .3, 1.15) both;
        }

        .result-icon {
            position: relative;
            width: 96px;
            height: 96px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent, #006857);
        }

        .result-svg {
            width: 96px;
            height: 96px;
            stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            animation: result-pop .55s cubic-bezier(.34, 1.56, .64, 1) both;
        }

        .result-fill {
            fill: currentColor;
            stroke: none;
            opacity: 0;
            animation: result-fill .5s ease-out .6s forwards;
        }

        .result-circle {
            stroke-dasharray: 151;
            stroke-dashoffset: 151;
            animation: result-draw .6s ease-out .1s forwards;
        }

        .result-mark {
            stroke-width: 3.5;
            stroke-dasharray: 40;
            stroke-dashoffset: 40;
            animation: result-draw .4s ease-out .65s forwards;
        }

        .result-mark.is-x {
            stroke-dasharray: 60;
            stroke-dashoffset: 60;
        }

        .result-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: currentColor;
            opacity: 0;
            animation: result-ring 1.3s ease-out .6s;
        }

        .result-card.is-error .result-svg {
            animation: result-shake .55s ease .15s both;
        }

        .result-card.is-closing {
            animation: result-card-out .25s ease forwards;
        }

        .result-row {
            animation: result-row-in .4s ease-out both;
        }

        .confetti-piece {
            position: absolute;
            left: 50%;
            top: 80px;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            opacity: 0;
            animation: confetti-burst 1s ease-out .6s forwards;
        }

        @keyframes panel-in {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes shutter {
            0% {
                opacity: .9;
            }

            100% {
                opacity: 0;
            }
        }

        @keyframes result-card-in {
            from {
                opacity: 0;
                transform: translateY(24px) scale(.92);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes result-pop {
            0% {
                transform: scale(.6);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes result-draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes result-fill {
            to {
                opacity: .12;
            }
        }

        @keyframes result-ring {
            0% {
                opacity: .35;
                transform: scale(.9);
            }

            100% {
                opacity: 0;
                transform: scale(1.9);
            }
        }

        @keyframes result-shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-8px);
            }

            40% {
                transform: translateX(8px);
            }

            60% {
                transform: translateX(-5px);
            }

            80% {
                transform: translateX(5px);
            }
        }

        @keyframes result-row-in {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes confetti-burst {
            0% {
                opacity: 1;
                transform: translate(0, 0) rotate(0) scale(1);
            }

            100% {
                opacity: 0;
                transform: translate(var(--dx), var(--dy)) rotate(var(--rot)) scale(.5);
            }
        }

        @keyframes result-card-out {
            to {
                opacity: 0;
                transform: translateY(8px) scale(.97);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .step-panel,
            .step-dot,
            .step-line::after,
            .shutter-flash,
            .result-card,
            .result-svg,
            .result-fill,
            .result-circle,
            .result-mark,
            .result-ring,
            .result-row,
            .confetti-piece {
                animation-duration: .01ms !important;
                animation-delay: 0s !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
@endsection

@section('content')
    @php
        // Pengajuan izin/sakit yang disetujui admin tercatat sebagai attendance berstatus sakit/izin.
        $onLeave = $todayAttendance && in_array($todayAttendance->status, ['sakit', 'izin'], true);
        $leaveLabel = $onLeave ? ucfirst($todayAttendance->status) : null;

        // Hanya check-in: begitu tercatat hadir/telat, presensi hari ini selesai.
        $checkedIn = $todayAttendance && !$onLeave;
        $isLate = $checkedIn && $todayAttendance->status === 'telat';

        $sideBorder = 'md:pl-space-lg md:border-l md:border-surface-variant/40';
    @endphp

    <div class="flex flex-col gap-space-xl">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col gap-space-md">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-surface">Absensi Hari Ini</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    @if ($onLeave)
                        Hari ini Anda tercatat {{ strtolower($leaveLabel) }} (pengajuan telah disetujui admin).
                    @elseif ($checkedIn)
                        Kehadiran Anda hari ini sudah tercatat. Tidak perlu absensi lagi.
                    @else
                        Ikuti 3 langkah berikut: verifikasi lokasi, ambil swafoto, lalu konfirmasi absensi.
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-space-sm">
                <div class="flex items-center gap-2 px-space-md py-space-xs rounded-xl bg-surface-container-low shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Hari &amp;
                            Tanggal</span>
                        <span
                            class="font-label-md text-label-md text-on-surface">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-space-md py-space-xs rounded-xl bg-primary/10 shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">work_history</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-primary leading-none">Batas Tepat Waktu</span>
                        <span class="font-label-md text-label-md text-primary">Check-in sampai
                            {{ $lateAfter->format('H:i') }} WIB</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= BANNER STATUS ================= --}}
        @if ($onLeave)
            <div
                class="flex items-start gap-space-sm p-space-lg rounded-xl bg-secondary-fixed text-on-secondary-fixed-variant">
                <span class="material-symbols-outlined text-[28px]">event_available</span>
                <div class="flex flex-col gap-1">
                    <span class="font-headline-sm text-headline-sm">{{ $leaveLabel }} disetujui</span>
                    <span class="font-body-md text-body-md">
                        Hari ini tercatat sebagai {{ strtolower($leaveLabel) }}, jadi tidak perlu absensi.
                        Semoga urusan Anda lancar.
                    </span>
                </div>
            </div>
        @elseif (!$checkedIn && $pendingLeave)
            <div class="flex items-start gap-space-sm p-space-lg rounded-xl bg-tertiary-fixed text-on-tertiary-fixed">
                <span class="material-symbols-outlined text-[28px]">hourglass_top</span>
                <div class="flex flex-col gap-1">
                    <span class="font-headline-sm text-headline-sm">
                        Pengajuan {{ strtolower($pendingLeave->typeLabel()) }} menunggu persetujuan
                    </span>
                    <span class="font-body-md text-body-md">
                        Pengajuan Anda belum disetujui admin. Jika Anda tetap masuk, silakan lakukan absensi seperti biasa.
                    </span>
                    <a href="{{ route('izin') }}" class="self-start font-label-lg text-label-lg underline">Lihat
                        pengajuan</a>
                </div>
            </div>
        @elseif (!$checkedIn)
            <a href="{{ route('izin') }}"
                class="self-start inline-flex items-center gap-1.5 px-space-md py-2 rounded-xl bg-surface-container-low text-primary font-label-md text-label-md hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-[18px]">sick</span>
                Tidak bisa masuk? Ajukan izin / sakit
            </a>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-xl items-start">

            {{-- ================= KOLOM KIRI ================= --}}
            <div class="xl:col-span-7 flex flex-col gap-space-xl">

                {{-- ---------- Kartu status presensi ---------- --}}
                <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm p-space-xl">
                    <div
                        class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-primary/5 blur-3xl pointer-events-none">
                    </div>

                    <div class="relative z-10 flex flex-col gap-space-lg">
                        <div class="flex items-center justify-between gap-space-sm">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[22px]">fingerprint</span>
                                <span class="font-headline-sm text-headline-sm text-on-surface">Status Presensi
                                    Harian</span>
                            </div>

                            @if ($onLeave)
                                <div
                                    class="flex items-center gap-2 px-space-md py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant">
                                    <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                    <span class="font-label-md text-label-md">{{ $leaveLabel }}</span>
                                </div>
                            @elseif ($checkedIn)
                                <div
                                    class="flex items-center gap-2 px-space-md py-1 rounded-full {{ $isLate ? 'bg-error-container text-on-error-container' : 'bg-primary-fixed/40 text-on-primary-fixed-variant' }}">
                                    <span class="w-2 h-2 rounded-full {{ $isLate ? 'bg-error' : 'bg-primary' }}"></span>
                                    <span class="font-label-md text-label-md">{{ $isLate ? 'Terlambat' : 'Hadir' }}</span>
                                </div>
                            @else
                                <div
                                    class="flex items-center gap-2 px-space-md py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed">
                                    <span class="w-2 h-2 rounded-full bg-tertiary animate-ping"></span>
                                    <span class="font-label-md text-label-md">Belum Absen</span>
                                </div>
                            @endif
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
                                <div id="realtime-clock"
                                    class="font-stat-counter text-[40px] leading-tight text-on-surface font-bold tracking-tight my-1">
                                    --:--:-- <span
                                        class="font-label-lg text-label-lg text-on-surface-variant font-medium">WIB</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1 items-end self-stretch justify-center {{ $sideBorder }}">
                                @if ($onLeave)
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Status Hari Ini</span>
                                    <span class="font-headline-sm text-headline-sm text-primary">{{ $leaveLabel }}
                                        disetujui</span>
                                @elseif ($checkedIn)
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Waktu Absensi</span>
                                    <span
                                        class="font-headline-sm text-headline-sm text-primary">{{ $todayAttendance->check_in->format('H:i:s') }}
                                        WIB</span>
                                    @if (!is_null($todayAttendance->check_in_distance))
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">
                                            {{ round($todayAttendance->check_in_distance) }} m dari kantor
                                        </span>
                                    @endif
                                @else
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Tenggat Tepat
                                        Waktu</span>
                                    <span
                                        class="font-headline-sm text-headline-sm text-primary">{{ $lateAfter->format('H:i:s') }}
                                        WIB</span>
                                    <span class="font-label-sm text-label-sm text-tertiary" id="late-countdown">–</span>
                                @endif
                            </div>
                        </div>

                        @if ($checkedIn)
                            @if ($todayAttendance->check_in_photo)
                                <div class="flex flex-col gap-1">
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Foto Absensi</span>
                                    <img src="{{ route('absensi.photo', [$todayAttendance, 'in']) }}" alt="Foto absensi"
                                        class="w-full max-w-[240px] object-cover rounded-xl shadow-sm"
                                        style="aspect-ratio:3/4;">
                                </div>
                            @endif

                            <div
                                class="w-full h-14 rounded-xl bg-primary-fixed/30 text-on-primary-fixed-variant font-headline-sm text-headline-sm flex items-center justify-center gap-space-sm">
                                <span class="material-symbols-outlined text-[24px]">task_alt</span>
                                <span>Presensi Hari Ini Selesai</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ================= WIZARD 3 LANGKAH ================= --}}
                @unless ($checkedIn || $onLeave)
                    <div id="wizard"
                        class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-lg">

                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[22px]">login</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface">Absensi Kehadiran</span>
                        </div>

                        {{-- Indikator langkah --}}
                        <div class="wizard-steps">
                            <div class="step-item">
                                <div class="step-dot active" id="dot-1">1</div>
                                <span class="step-label active" id="label-1">Verifikasi Lokasi</span>
                            </div>
                            <div class="step-line" id="line-1"></div>
                            <div class="step-item">
                                <div class="step-dot" id="dot-2">2</div>
                                <span class="step-label" id="label-2">Ambil Swafoto</span>
                            </div>
                            <div class="step-line" id="line-2"></div>
                            <div class="step-item">
                                <div class="step-dot" id="dot-3">3</div>
                                <span class="step-label" id="label-3">Konfirmasi</span>
                            </div>
                        </div>

                        {{-- ========== LANGKAH 1: VERIFIKASI LOKASI ========== --}}
                        <div class="step-panel flex flex-col gap-space-lg" id="panel-1">
                            <div class="flex items-center justify-between flex-wrap gap-space-sm">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-primary text-[22px]">radar</span>
                                    <div class="flex flex-col">
                                        <span class="font-headline-sm text-headline-sm text-on-surface">Langkah 1: Verifikasi
                                            Lokasi</span>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant"
                                            id="gps-status-subtitle">
                                            Menunggu izin akses lokasi perangkat
                                        </span>
                                    </div>
                                </div>
                                <span id="gps-status-badge"
                                    class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                                    <span id="gps-status-text">Belum Aktif</span>
                                </span>
                            </div>

                            <div class="relative w-full rounded-xl overflow-hidden shadow-inner bg-surface-container-low"
                                style="height:288px;">
                                <div class="w-full h-full" id="location-map"></div>
                                <button type="button" id="btn-locate-me"
                                    class="absolute bottom-3 right-3 z-[400] h-10 px-space-md rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-md hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px]">my_location</span>
                                    <span>Gunakan Lokasi Saya</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-space-sm">
                                @foreach ([['icon' => 'gps_fixed', 'tone' => 'bg-primary/10 text-primary', 'label' => 'Status GPS', 'id' => 'metric-gps-status', 'initial' => 'Belum Aktif'], ['icon' => 'near_me', 'tone' => 'bg-secondary/10 text-secondary', 'label' => 'Jarak ke Kantor', 'id' => 'metric-distance', 'initial' => '–'], ['icon' => 'crisis_alert', 'tone' => 'bg-tertiary/10 text-tertiary', 'label' => 'Akurasi GPS', 'id' => 'metric-accuracy', 'initial' => '–']] as $metric)
                                    <div class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                                        <div
                                            class="w-10 h-10 rounded-lg {{ $metric['tone'] }} flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[22px]">{{ $metric['icon'] }}</span>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span
                                                class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $metric['label'] }}</span>
                                            <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate"
                                                id="{{ $metric['id'] }}">{{ $metric['initial'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                                <div class="flex items-start gap-2 max-w-lg">
                                    <span
                                        class="material-symbols-outlined text-outline text-[20px] mt-0.5 shrink-0">corporate_fare</span>
                                    <div class="flex flex-col gap-1">
                                        @forelse ($offices as $office)
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-label-md text-label-md text-on-surface">{{ $office['name'] }}</span>
                                                <span class="font-body-sm text-body-sm text-on-surface-variant">
                                                    Radius presensi {{ round($office['radius']) }} meter
                                                </span>
                                            </div>
                                        @empty
                                            <span class="font-label-md text-label-md text-on-surface">Lokasi kantor belum
                                                dikonfigurasi</span>
                                        @endforelse

                                        @if ($offices->isNotEmpty())
                                            <span class="font-body-sm text-body-sm text-on-surface-variant">
                                                Akurasi GPS maks. ±{{ $maxAccuracy }} m
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" id="btn-refresh-location"
                                    class="shrink-0 h-10 px-space-md rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-variant transition-colors flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                                    <span>Perbarui Lokasi</span>
                                </button>
                            </div>

                            <button type="button" id="btn-to-2" disabled
                                class="w-full h-12 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container active:scale-[0.99] transition-all flex items-center justify-center gap-space-xs">
                                <span class="material-symbols-outlined text-[22px]">photo_camera_front</span>
                                <span>Lanjut: Ambil Swafoto</span>
                                <span class="material-symbols-outlined text-[20px] opacity-80">arrow_forward</span>
                            </button>
                        </div>

                        {{-- ========== LANGKAH 2: AMBIL SWAFOTO ========== --}}
                        <div class="step-panel flex flex-col gap-space-lg" id="panel-2" style="display:none;">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[22px]">photo_camera_front</span>
                                <div class="flex flex-col">
                                    <span class="font-headline-sm text-headline-sm text-on-surface">Langkah 2: Ambil
                                        Swafoto</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                                        Pastikan seluruh wajah terlihat jelas dan pencahayaan cukup.
                                    </span>
                                </div>
                            </div>

                            <div class="relative w-full rounded-xl overflow-hidden bg-inverse-surface"
                                style="width:100%; aspect-ratio:3/4; max-width:480px; margin:0 auto;">
                                <video id="camera-preview" autoplay muted playsinline
                                    class="w-full h-full object-cover"></video>
                                <img id="captured-photo" alt="Hasil swafoto"
                                    class="w-full h-full object-cover absolute inset-0" style="display:none;">

                                <div id="camera-placeholder"
                                    class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-inverse-on-surface/70 font-body-sm text-body-sm text-center p-4">
                                    <span class="material-symbols-outlined text-[36px]">videocam</span>
                                    <span>Menunggu izin akses kamera…</span>
                                </div>

                                <div id="face-guide"
                                    style="position:absolute; left:50%; top:46%; width:46%; height:68%; transform:translate(-50%,-50%); border:3px dashed rgba(255,255,255,.85); border-radius:50%; pointer-events:none;">
                                </div>

                                <button type="button" id="btn-flip-camera" title="Balik kamera (mirror)"
                                    class="absolute top-3 right-3 h-9 px-3 rounded-full bg-surface-container-lowest/90 backdrop-blur-sm text-on-surface font-label-sm text-label-sm shadow-sm hover:bg-surface-container-high transition-colors flex items-center gap-1.5"
                                    style="z-index:5;">
                                    <span class="material-symbols-outlined text-[16px]">flip</span>
                                    <span>Balik Kamera</span>
                                </button>

                                <div id="camera-hint"
                                    class="absolute bottom-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-surface-container-lowest/90 backdrop-blur-sm font-label-sm text-label-sm text-on-surface flex items-center gap-1.5 shadow-sm whitespace-nowrap">
                                    <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                                    Posisikan wajah di dalam bingkai
                                </div>

                                <div id="shutter-flash" class="shutter-flash"></div>
                            </div>
                            <canvas id="camera-canvas" class="hidden"></canvas>

                            <div class="flex gap-space-sm">
                                <button type="button" id="btn-back-1"
                                    class="flex-1 h-12 rounded-xl bg-surface-container-high text-on-surface font-label-lg text-label-lg hover:bg-surface-variant transition-colors flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                                    <span>Kembali</span>
                                </button>
                                <button type="button" id="btn-capture" disabled
                                    class="flex-[2] h-12 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container active:scale-[0.99] transition-all flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[22px]">photo_camera</span>
                                    <span>Ambil Foto</span>
                                </button>
                                <button type="button" id="btn-retake" style="display:none;"
                                    class="flex-1 h-12 rounded-xl bg-surface-container-high text-on-surface font-label-lg text-label-lg hover:bg-surface-variant transition-colors items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[20px]">replay</span>
                                    <span>Foto Ulang</span>
                                </button>
                                <button type="button" id="btn-to-3" style="display:none;"
                                    class="flex-[2] h-12 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container active:scale-[0.99] transition-all items-center justify-center gap-1.5">
                                    <span>Lanjut ke Konfirmasi</span>
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </button>
                            </div>
                        </div>

                        {{-- ========== LANGKAH 3: KONFIRMASI ========== --}}
                        <div class="step-panel flex flex-col gap-space-lg" id="panel-3" style="display:none;">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[22px]">fact_check</span>
                                <div class="flex flex-col">
                                    <span class="font-headline-sm text-headline-sm text-on-surface">Langkah 3: Konfirmasi
                                        Absensi</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Periksa kembali data
                                        berikut sebelum dikirim.</span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-space-md">
                                <img id="confirm-photo" alt="Swafoto" class="w-full max-w-[240px] shadow-sm"
                                    style="aspect-ratio:3/4; object-fit:cover; border-radius:12px; align-self:flex-start;">
                                <div
                                    class="flex-1 flex flex-col gap-2 p-space-md rounded-xl bg-surface-container-low font-body-sm text-body-sm">
                                    @foreach ([['Waktu', 'confirm-time', 'text-primary', '--:--:-- WIB'], ['Lokasi', 'confirm-location', 'text-on-surface text-right', '–'], ['Jarak ke Kantor', 'confirm-distance', 'text-on-surface', '–'], ['Akurasi GPS', 'confirm-accuracy', 'text-on-surface', '–'], ['Perkiraan Status', 'confirm-status', '', '–']] as [$label, $id, $classes, $initial])
                                        <div class="flex justify-between gap-3">
                                            <span class="text-on-surface-variant">{{ $label }}</span>
                                            <span class="font-label-md text-label-md {{ $classes }}"
                                                id="{{ $id }}">{{ $initial }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-start gap-space-xs px-space-sm py-2 rounded-lg bg-surface-container-low">
                                <span class="material-symbols-outlined text-outline text-[18px] mt-0.5">info</span>
                                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                    Batas toleransi kehadiran adalah pukul <strong>{{ $lateAfter->format('H:i') }}
                                        WIB</strong>.
                                    Waktu resmi dicatat oleh server saat absensi dikirim, dan lokasi akan diverifikasi ulang.
                                </p>
                            </div>

                            <div class="flex gap-space-sm">
                                <button type="button" id="btn-back-2"
                                    class="flex-1 h-12 rounded-xl bg-surface-container-high text-on-surface font-label-lg text-label-lg hover:bg-surface-variant transition-colors flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                                    <span>Kembali</span>
                                </button>
                                <button type="button" id="btn-submit"
                                    class="flex-[2] h-12 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container active:scale-[0.99] transition-all flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[22px]">check_circle</span>
                                    <span>Kirim Absensi</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endunless
            </div>

            {{-- ================= KOLOM KANAN ================= --}}
            <div class="xl:col-span-5 flex flex-col gap-space-xl">

                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-low">
                        <div
                            class="w-11 h-11 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold shrink-0">
                            {{ $pembimbingInitials }}
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                                Mentor Lapangan Penanggung Jawab
                            </span>
                            <span
                                class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $pembimbing ?: '-' }}</span>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[20px]">verified_user</span>
                    </div>
                </div>

                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                        <span class="font-headline-sm text-headline-sm text-on-surface">Riwayat Terakhir</span>
                    </div>

                    <div class="flex flex-col gap-space-sm">
                        @forelse ($recentAttendances as $row)
                            <div
                                class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors flex flex-col gap-space-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-label-lg text-label-lg text-on-surface font-semibold">
                                        {{ $row->attendance_date->translatedFormat('l, d M Y') }}
                                    </span>
                                    @if ($row->status === 'hadir')
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[10px]">Tepat
                                            Waktu</span>
                                    @elseif ($row->status === 'telat')
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-[10px]">Terlambat</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-sm text-[10px]">
                                            {{ $row->status === 'sakit' ? 'Sakit' : 'Izin' }}
                                        </span>
                                    @endif
                                </div>
                                <div
                                    class="flex flex-wrap items-center justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant pt-1">
                                    <span class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-primary text-[16px]">login</span>
                                        {{ $row->check_in ? $row->check_in->format('H:i') . ' WIB' : '-' }}
                                    </span>
                                    @if ($row->check_in_distance !== null)
                                        <span class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface">
                                            <span
                                                class="material-symbols-outlined text-outline text-[14px]">pin_drop</span>
                                            {{ round($row->check_in_distance) }}m dari kantor
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div
                                class="p-space-md rounded-xl bg-surface-container-low text-center font-body-sm text-body-sm text-on-surface-variant">
                                Belum ada riwayat absensi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL HASIL ================= --}}
        <div id="result-modal"
            style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.45); backdrop-filter:blur(4px);">
            <div id="result-card"
                class="result-card w-full max-w-sm rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl text-center">
                <div class="result-icon" id="result-icon"></div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface mt-space-md" id="result-title"></h3>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1" id="result-message"></p>
                <div class="flex flex-col gap-2 mt-space-md p-space-sm rounded-xl bg-surface-container-low text-left"
                    id="result-details"></div>
                <button type="button" id="result-btn"
                    class="mt-space-md w-full h-11 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:bg-primary-container shadow transition-colors">
                    Selesai
                </button>
            </div>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (() => {
            'use strict';
            const OFFICES = @json($offices);
            const MAX_ACCURACY = @json($maxAccuracy);
            const SERVER_NOW = @json(now()->getTimestamp() * 1000);
            const LATE_DEADLINE = @json($lateAfter->getTimestamp() * 1000);
            const SUBMIT_URL = @json(route('absensi.check-in'));
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

            const INDONESIA_CENTER = [-2.5, 118];
            const POSITION_MAX_AGE_MS = 5 * 60 * 1000;
            const CLOCK_FORMAT = {
                hour12: false,
                timeZone: 'Asia/Jakarta'
            };
            const $ = (id) => document.getElementById(id);

            const clockOffset = SERVER_NOW - Date.now();
            const serverNow = () => new Date(Date.now() + clockOffset);
            const isPastDeadline = () => serverNow().getTime() > LATE_DEADLINE;
            const formatClock = (date) => date.toLocaleTimeString('id-ID', CLOCK_FORMAT).replace(/\./g, ':');

            function tick() {
                const now = serverNow();
                const time = formatClock(now);

                const clock = $('realtime-clock');
                if (clock) {
                    clock.innerHTML =
                        `${time} <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">WIB</span>`;
                }

                const confirmTime = $('confirm-time');
                if (confirmTime) confirmTime.textContent = `${time} WIB`;

                const countdown = $('late-countdown');
                if (countdown) {
                    const diff = Math.floor((LATE_DEADLINE - now.getTime()) / 1000);
                    countdown.textContent = diff > 0 ?
                        `Sisa toleransi: ${Math.floor(diff / 60)} Menit ${diff % 60} Detik` :
                        'Melewati batas toleransi (akan tercatat terlambat)';
                }
            }

            setInterval(tick, 1000);
            tick();
            if (!$('wizard')) return;
            const modal = {
                root: $('result-modal'),
                card: $('result-card'),
                icon: $('result-icon'),
                title: $('result-title'),
                message: $('result-message'),
                details: $('result-details'),
                button: $('result-btn'),
                onClose: null,
            };

            const ACCENTS = {
                success: '#006857',
                warning: '#d97706',
                error: '#ba1a1a'
            };

            const ICON_OK = `
                <div class="result-ring"></div>
                <svg class="result-svg" viewBox="0 0 52 52">
                    <circle class="result-fill" cx="26" cy="26" r="24"/>
                    <circle class="result-circle" cx="26" cy="26" r="24"/>
                    <path class="result-mark" d="M14 27l8 8 16-17"/>
                </svg>`;

            const ICON_ERR = `
                <div class="result-ring"></div>
                <svg class="result-svg" viewBox="0 0 52 52">
                    <circle class="result-fill" cx="26" cy="26" r="24"/>
                    <circle class="result-circle" cx="26" cy="26" r="24"/>
                    <path class="result-mark is-x" d="M17 17l18 18M35 17L17 35"/>
                </svg>`;

            function burstConfetti(color) {
                const colors = [color, '#f5b942', '#4ecdc4', '#ff6b6b', '#7c83fd'];
                const pieces = 18;

                for (let i = 0; i < pieces; i++) {
                    const angle = (Math.PI * 2 * i) / pieces + Math.random() * 0.4;
                    const distance = 90 + Math.random() * 70;
                    const el = document.createElement('span');

                    el.className = 'confetti-piece';
                    el.style.background = colors[i % colors.length];
                    el.style.setProperty('--dx', `${Math.cos(angle) * distance}px`);
                    el.style.setProperty('--dy', `${Math.sin(angle) * distance * 0.8 + 20}px`);
                    el.style.setProperty('--rot', `${Math.random() * 540 - 270}deg`);
                    modal.card.appendChild(el);
                }
            }

            function renderDetails(details) {
                modal.details.innerHTML = '';

                details.forEach(({
                    label,
                    value
                }, i) => {
                    const row = document.createElement('div');
                    row.className = 'result-row flex justify-between gap-3 font-body-sm text-body-sm';
                    row.style.animationDelay = `${0.75 + i * 0.1}s`;

                    const labelEl = document.createElement('span');
                    labelEl.className = 'text-on-surface-variant';
                    labelEl.textContent = label;

                    const valueEl = document.createElement('span');
                    valueEl.className = 'font-label-md text-label-md text-on-surface text-right';
                    valueEl.textContent = value;

                    row.append(labelEl, valueEl);
                    modal.details.appendChild(row);
                });

                modal.details.style.display = details.length ? 'flex' : 'none';
            }

            let resultAutoCloseTimer = null;
            let resultCloseTimer = null;

            function showResult({
                type = 'success',
                title,
                message = '',
                details = [],
                buttonText = 'Selesai',
                onClose = null,
                autoCloseMs = 0
            }) {
                clearTimeout(resultAutoCloseTimer);
                clearTimeout(resultCloseTimer);
                resultCloseTimer = null;

                const accent = ACCENTS[type];

                modal.card.classList.remove('is-closing');
                modal.card.style.setProperty('--accent', accent);
                modal.card.classList.toggle('is-error', type === 'error');

                modal.card.querySelectorAll('.confetti-piece').forEach((el) => el.remove());

                modal.icon.innerHTML = type === 'error' ? ICON_ERR : ICON_OK;
                modal.icon.style.color = accent;
                modal.title.textContent = title;
                modal.message.textContent = message;
                modal.button.textContent = buttonText;
                modal.button.style.display = autoCloseMs > 0 ? 'none' : '';
                modal.onClose = onClose;
                renderDetails(details);
                modal.card.style.animation = 'none';
                void modal.card.offsetWidth;
                modal.card.style.animation = '';
                modal.root.style.display = 'flex';
                if (type !== 'error') burstConfetti(accent);
                if (autoCloseMs > 0) {
                    resultAutoCloseTimer = setTimeout(closeResult, autoCloseMs);
                } else {
                    modal.button.focus();
                }
            }

            function closeResult() {
                if (resultCloseTimer) return;
                clearTimeout(resultAutoCloseTimer);
                resultAutoCloseTimer = null;
                const callback = modal.onClose;
                modal.onClose = null;
                modal.card.classList.add('is-closing');
                resultCloseTimer = setTimeout(() => {
                    modal.root.style.display = 'none';
                    modal.card.classList.remove('is-closing');

                    resultCloseTimer = null;

                    if (callback) callback();
                }, 250);
            }

            modal.button.addEventListener('click', closeResult);
            modal.root.addEventListener('click', (e) => {
                if (e.target === modal.root) closeResult();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.root.style.display === 'flex') closeResult();
            });

            let position = null;
            let locationValid = false;
            let photoData = null;
            let stream = null;
            let map = null;
            let userMarker = null;
            let userCircle = null;
            let stopLocating = null;
            let flipCamera = false;

            try {
                const saved = localStorage.getItem('absensi_flip_camera');
                if (saved !== null) flipCamera = saved === '1';
            } catch (_) {}

            const CHECK_SVG =
                '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4 4 10-10"/></svg>';

            function goToStep(n) {
                if (n === 2 && !locationValid) return;
                if (n === 3 && !photoData) return;

                [1, 2, 3].forEach((i) => {
                    const dot = $(`dot-${i}`);
                    const label = $(`label-${i}`);

                    $(`panel-${i}`).style.display = i === n ? 'flex' : 'none';
                    dot.classList.toggle('active', i === n);
                    dot.classList.toggle('done', i < n);
                    label.classList.toggle('active', i === n);
                    label.classList.toggle('done', i < n);
                    dot.innerHTML = i < n ? CHECK_SVG : String(i);
                });
                [1, 2].forEach((i) => $(`line-${i}`).classList.toggle('done', i < n));

                if (n === 2) {
                    if (photoData) {
                        showCaptured();
                    } else {
                        showLive();
                        startCamera();
                    }
                } else {
                    stopCamera();
                }

                if (n === 1 && map) setTimeout(() => map.invalidateSize(), 120);
                if (n === 3) fillConfirm();

                $('wizard').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            // ===== Langkah 1: lokasi =====
            const GPS_BADGE_CLASSES = {
                idle: 'bg-surface-container-high text-on-surface-variant',
                loading: 'bg-secondary-fixed text-on-secondary-fixed-variant',
                ok: 'bg-primary-fixed/40 text-on-primary-fixed-variant',
                error: 'bg-error-container text-on-error-container',
            };

            const GEO_ERROR_MESSAGES = {
                1: 'Izin lokasi ditolak. Aktifkan izin lokasi di browser dan layanan lokasi di sistem operasi.',
                2: 'Lokasi tidak tersedia saat ini. Pastikan GPS/Wi-Fi aktif.',
                3: 'Waktu permintaan lokasi habis. Coba lagi.',
            };

            function setGps(state, badge, subtitle) {
                $('gps-status-badge').className =
                    `px-space-sm py-1 rounded-full font-label-sm text-label-sm flex items-center gap-1.5 ${GPS_BADGE_CLASSES[state]}`;
                $('gps-status-text').textContent = badge;
                $('gps-status-subtitle').textContent = subtitle;
                $('metric-gps-status').textContent = badge;
            }

            window.addEventListener('error', (e) => {
                console.error(e.error || e.message);
                setGps('error', 'Error Script', e.message || 'Terjadi kesalahan pada script.');
            });

            const toRad = (deg) => (deg * Math.PI) / 180;

            /** Haversine, hasil dalam meter. Rumus yang sama dengan di controller. */
            function distanceMeters(lat1, lng1, lat2, lng2) {
                const R = 6371000;
                const dLat = toRad(lat2 - lat1);
                const dLng = toRad(lng2 - lng1);
                const a = Math.sin(dLat / 2) ** 2 +
                    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLng / 2) ** 2;

                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            /**
             * Kantor terdekat yang radiusnya mencakup titik. Jika tidak ada,
             * kantor terdekat saja (inside = false) untuk pesan error.
             */
            function nearestOffice(lat, lng) {
                const byDistance = (a, b) => a.distance - b.distance;
                const measured = OFFICES.map((office) => ({
                    office,
                    distance: distanceMeters(lat, lng, office.lat, office.lng),
                }));

                const inside = measured.filter((m) => m.distance <= m.office.radius).sort(byDistance)[0];
                const pick = inside ?? measured.sort(byDistance)[0];

                return {
                    office: pick.office,
                    distance: pick.distance,
                    inside: Boolean(inside)
                };
            }


            function initMap() {
                if (typeof L === 'undefined') {
                    $('location-map').innerHTML =
                        '<div style="padding:16px;font-size:13px">Peta gagal dimuat (Leaflet tidak ter-load). Periksa koneksi internet.</div>';
                    return;
                }

                map = L.map('location-map').setView(INDONESIA_CENTER, 5);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                OFFICES.forEach((office) => {
                    L.marker([office.lat, office.lng]).addTo(map)
                        .bindPopup(`<strong>${office.name}</strong><br>Radius geofence: ${office.radius} m`);

                    L.circle([office.lat, office.lng], {
                        radius: office.radius,
                        color: '#006857',
                        fillColor: '#006857',
                        fillOpacity: 0.15,
                        weight: 2,
                    }).addTo(map);
                });

                if (OFFICES.length === 1) {
                    map.setView([OFFICES[0].lat, OFFICES[0].lng], 16);
                } else if (OFFICES.length > 1) {
                    const bounds = L.latLngBounds(
                        OFFICES.map(office => [office.lat, office.lng])
                    );

                    map.fitBounds(bounds, {
                        padding: [40, 40],
                        maxZoom: 10,
                    });
                }

                setTimeout(() => map.invalidateSize(), 200);
            }


            function drawUserPosition({
                latitude,
                longitude,
                accuracy,
                office
            }) {
                if (!map) return;

                if (userMarker) userMarker.setLatLng([latitude, longitude]);
                else userMarker = L.marker([latitude, longitude]).addTo(map).bindPopup('Posisi Anda');

                if (userCircle) userCircle.setLatLng([latitude, longitude]).setRadius(accuracy);
                else {
                    userCircle = L.circle([latitude, longitude], {
                        radius: accuracy,
                        color: '#006398',
                        fillColor: '#006398',
                        fillOpacity: 0.12,
                        weight: 1,
                    }).addTo(map);
                }

                map.fitBounds(L.latLngBounds([
                    [office.lat, office.lng],
                    [latitude, longitude]
                ]), {
                    padding: [40, 40],
                    maxZoom: 17,
                });
            }

            function onPosition({
                coords
            }) {
                const {
                    latitude,
                    longitude,
                    accuracy
                } = coords;
                const {
                    office,
                    distance,
                    inside
                } = nearestOffice(latitude, longitude);
                const accurate = accuracy <= MAX_ACCURACY;

                position = {
                    latitude,
                    longitude,
                    accuracy,
                    distance,
                    office,
                    time: Date.now()
                };
                locationValid = inside && accurate;

                drawUserPosition(position);

                $('metric-distance').textContent =
                    `${Math.round(distance)} meter ${inside ? '(Aman)' : '(Terlalu Jauh)'}`;
                $('metric-accuracy').textContent = `±${Math.round(accuracy)} meter`;

                if (!inside) {
                    setGps('error', 'Di Luar Radius',
                        `Anda ${Math.round(distance)} m dari ${office.name} (maks ${Math.round(office.radius)} m). Presensi tidak dapat dilakukan.`
                    );
                } else if (!accurate) {
                    setGps('error', 'Akurasi Rendah',
                        `Akurasi ±${Math.round(accuracy)} m, maksimal ${MAX_ACCURACY} m. Coba di area terbuka atau gunakan HP.`
                    );
                } else {
                    setGps('ok', 'Terkunci & Valid',
                        `Lokasi Anda berada di dalam radius ${office.name}. Silakan lanjut ambil swafoto.`);
                }

                $('btn-to-2').disabled = !locationValid;
            }

            function onPositionError(err) {
                setGps('error', 'Gagal / Ditolak', GEO_ERROR_MESSAGES[err.code] || 'Gagal mengambil lokasi.');
                $('metric-distance').textContent = '–';
                $('metric-accuracy').textContent = '–';
            }

            function locateUser() {
                stopLocating?.();
                locationValid = false;
                $('btn-to-2').disabled = true;

                if (!OFFICES.length) {
                    return setGps('error', 'Belum Dikonfigurasi', 'Lokasi kantor belum diatur oleh admin.');
                }
                if (!('geolocation' in navigator)) {
                    return setGps('error', 'Tidak Didukung', 'Browser ini tidak mendukung geolokasi.');
                }
                if (!window.isSecureContext) {
                    return setGps('error', 'Koneksi Tidak Aman',
                        'Geolokasi hanya jalan di HTTPS atau localhost. Buka lewat https:// atau http://localhost:8000.'
                    );
                }

                setGps('loading', 'Mencari Lokasi…', 'Mengambil lokasi terbaik, tunggu beberapa detik…');

                let best = null;
                let finished = false;
                let watchId = null;
                let timer = null;

                const stop = () => {
                    finished = true;
                    clearTimeout(timer);
                    if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                };

                const finish = () => {
                    if (finished) return;
                    stop();
                    if (best) onPosition(best);
                    else onPositionError({
                        code: 3
                    });
                };

                stopLocating = stop;

                watchId = navigator.geolocation.watchPosition(
                    (pos) => {
                        if (finished) return;
                        if (!best || pos.coords.accuracy < best.coords.accuracy) best = pos;
                        if (best.coords.accuracy <= 30) finish(); // sudah cukup akurat
                    },
                    (err) => {
                        if (finished) return;
                        stop();
                        onPositionError(err);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 20000,
                        maximumAge: 0
                    }
                );

                timer = setTimeout(finish, 10000);
            }

            $('btn-locate-me').addEventListener('click', locateUser);
            $('btn-refresh-location').addEventListener('click', () => {
                const icon = $('btn-refresh-location').querySelector('.material-symbols-outlined');

                icon?.classList.add('animate-spin');
                locateUser();
                setTimeout(() => icon?.classList.remove('animate-spin'), 800);
            });
            $('btn-to-2').addEventListener('click', () => goToStep(2));

            try {
                initMap();
            } catch (e) {
                console.error('Gagal membuat peta:', e);
            }

            // ===== Langkah 2: swafoto =====
            const video = $('camera-preview');
            const placeholder = $('camera-placeholder');
            const canvas = $('camera-canvas');
            const capturedImg = $('captured-photo');
            const faceGuide = $('face-guide');
            const cameraHint = $('camera-hint');
            const btnFlip = $('btn-flip-camera');
            const btnCapture = $('btn-capture');
            const btnRetake = $('btn-retake');
            const btnTo3 = $('btn-to-3');


            const PHOTO_WIDTH = 720;
            const PHOTO_HEIGHT = 960;


            function setPlaceholder(icon, text) {
                placeholder.innerHTML =
                    `<span class="material-symbols-outlined text-[36px]">${icon}</span><span>${text}</span>`;
                placeholder.classList.remove('hidden');
                placeholder.style.display = 'flex';
            }

            const applyFlip = () => {
                video.style.transform = flipCamera ? 'scaleX(-1)' : 'none';
            };

            btnFlip.addEventListener('click', () => {
                flipCamera = !flipCamera;
                try {
                    localStorage.setItem('absensi_flip_camera', flipCamera ? '1' : '0');
                } catch (_) {
                    /* abaikan */
                }
                applyFlip();
            });

            function showLive() {
                capturedImg.style.display = 'none';
                video.style.display = 'block';
                faceGuide.style.display = 'block';
                cameraHint.style.display = 'flex';
                btnFlip.style.display = 'flex';
                btnCapture.style.display = 'flex';
                btnCapture.disabled = true;
                btnRetake.style.display = 'none';
                btnTo3.style.display = 'none';
            }

            function showCaptured() {
                capturedImg.src = photoData;
                capturedImg.style.display = 'block';
                video.style.display = 'none';
                placeholder.style.display = 'none';
                faceGuide.style.display = 'none';
                cameraHint.style.display = 'none';
                btnFlip.style.display = 'none';
                btnCapture.style.display = 'none';
                btnRetake.style.display = 'flex';
                btnTo3.style.display = 'flex';
            }

            function cameraErrorMessage(error) {
                if (error.name === 'NotAllowedError') return 'Izin kamera ditolak. Aktifkan di pengaturan browser.';
                if (error.name === 'NotFoundError') return 'Kamera tidak ditemukan pada perangkat ini.';
                return 'Tidak dapat mengakses kamera.';
            }

            async function startCamera() {
                if (stream) return;
                setPlaceholder('videocam', 'Menunggu izin akses kamera…');

                if (!navigator.mediaDevices?.getUserMedia) {
                    return setPlaceholder('videocam_off',
                        'Kamera tidak didukung browser ini (gunakan HTTPS atau localhost).');
                }

                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: {
                                ideal: 1280
                            },
                            height: {
                                ideal: 720
                            }
                        },
                        audio: false,
                    });
                    video.srcObject = stream;
                    applyFlip();
                    video.onplaying = () => {
                        placeholder.style.display = 'none';
                        btnCapture.disabled = false;
                    };
                } catch (e) {
                    setPlaceholder('videocam_off', cameraErrorMessage(e));
                }
            }

            function stopCamera() {
                stream?.getTracks().forEach((track) => track.stop());
                stream = null;
                video.srcObject = null;
            }

            /** Crop tengah rasio 4:3, lalu kembalikan data URL JPEG. */
            function capturePhoto() {
                if (!video.videoWidth) return null;

                const targetRatio = PHOTO_WIDTH / PHOTO_HEIGHT;
                const vw = video.videoWidth;
                const vh = video.videoHeight;
                let sw = vw;
                let sh = vh;
                let sx = 0;
                let sy = 0;

                if (vw / vh > targetRatio) {
                    sw = vh * targetRatio;
                    sx = (vw - sw) / 2;
                } else {
                    sh = vw / targetRatio;
                    sy = (vh - sh) / 2;
                }

                canvas.width = PHOTO_WIDTH;
                canvas.height = PHOTO_HEIGHT;

                const ctx = canvas.getContext('2d');
                if (flipCamera) {
                    ctx.translate(PHOTO_WIDTH, 0);
                    ctx.scale(-1, 1);
                }
                ctx.drawImage(video, sx, sy, sw, sh, 0, 0, PHOTO_WIDTH, PHOTO_HEIGHT);

                return canvas.toDataURL('image/jpeg', 0.75);
            }

            btnCapture.addEventListener('click', () => {
                const data = capturePhoto();

                if (!data) {
                    return showResult({
                        type: 'error',
                        title: 'Kamera Belum Siap',
                        message: 'Tunggu hingga gambar kamera tampil, lalu coba ambil foto lagi.',
                        buttonText: 'Mengerti',
                    });
                }

                const flash = $('shutter-flash');
                flash.classList.remove('go');
                void flash.offsetWidth;
                flash.classList.add('go');

                photoData = data;
                stopCamera();
                showCaptured();
            });

            btnRetake.addEventListener('click', () => {
                photoData = null;
                showLive();
                startCamera();
            });

            btnTo3.addEventListener('click', () => goToStep(3));
            $('btn-back-1').addEventListener('click', () => goToStep(1));

            // ===== Langkah 3: konfirmasi & kirim =====
            function fillConfirm() {
                const status = $('confirm-status');
                const late = isPastDeadline();

                $('confirm-photo').src = photoData;
                $('confirm-location').textContent = position ? position.office.name : '–';
                $('confirm-distance').textContent = position ? `${Math.round(position.distance)} meter` : '–';
                $('confirm-accuracy').textContent = position ? `±${Math.round(position.accuracy)} meter` : '–';

                status.textContent = late ? 'Terlambat' : 'Tepat Waktu';
                status.style.color = late ? ACCENTS.error : ACCENTS.success;
                tick();
            }

            $('btn-back-2').addEventListener('click', () => goToStep(2));

            const submitBtn = $('btn-submit');
            const submitIdleHtml = submitBtn.innerHTML;

            const showSubmitting = () => {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span><span>Mengirim...</span>';
            };

            const resetSubmit = () => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = submitIdleHtml;
            };

            function showSubmitSuccess(data) {
                const late = data.status === 'telat';

                showResult({
                    type: late ? 'warning' : 'success',
                    title: late ? 'Absensi Tercatat' : 'Absensi Berhasil!',
                    message: late ?
                        'Kehadiran Anda tercatat terlambat.' :
                        'Kehadiran Anda tercatat tepat waktu. Selamat bekerja!',
                    details: [{
                            label: 'Waktu Absensi',
                            value: `${data.check_in} WIB`
                        },
                        {
                            label: 'Status',
                            value: late ? 'Terlambat' : 'Hadir'
                        },
                        {
                            label: 'Jarak ke Kantor',
                            value: `${data.distance} meter`
                        },
                        {
                            label: 'Kantor',
                            value: data.office || position.office.name
                        }
                    ],
                    autoCloseMs: 2200,
                    onClose: () => window.location.reload()
                });
            }

            function showSubmitFailure(data) {
                const firstValidationError = data.errors ? Object.values(data.errors)[0][0] : null;

                showResult({
                    type: 'error',
                    title: 'Presensi Gagal',
                    message: data.message || firstValidationError || 'Gagal mengirim presensi.',
                    buttonText: 'Coba Lagi',
                });
            }

            submitBtn.addEventListener('click', async () => {
                if (!locationValid || !position || !photoData) {
                    return showResult({
                        type: 'error',
                        title: 'Data Belum Lengkap',
                        message: 'Lokasi dan swafoto harus terverifikasi sebelum absensi dikirim.',
                        buttonText: 'Mengerti',
                        onClose: () => goToStep(1),
                    });
                }

                if (Date.now() - position.time > POSITION_MAX_AGE_MS) {
                    return showResult({
                        type: 'error',
                        title: 'Lokasi Kedaluwarsa',
                        message: 'Verifikasi lokasi sudah terlalu lama. Silakan verifikasi ulang lokasi Anda.',
                        buttonText: 'Verifikasi Ulang',
                        onClose: () => {
                            goToStep(1);
                            locateUser();
                        },
                    });
                }

                showSubmitting();

                try {
                    const response = await fetch(SUBMIT_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: JSON.stringify({
                            photo: photoData,
                            latitude: position.latitude,
                            longitude: position.longitude,
                            accuracy: position.accuracy,
                        }),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (response.ok && data.success) showSubmitSuccess(data);
                    else showSubmitFailure(data);
                } catch (e) {
                    console.error(e);
                    showResult({
                        type: 'error',
                        title: 'Gangguan Jaringan',
                        message: 'Terjadi kesalahan saat menghubungi server. Coba lagi.',
                        buttonText: 'Coba Lagi',
                    });
                } finally {
                    resetSubmit();
                }
            });

            window.addEventListener('beforeunload', stopCamera);
            locateUser();
        })();
    </script>
@endsection
