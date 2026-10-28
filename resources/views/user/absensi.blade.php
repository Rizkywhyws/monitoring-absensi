@extends('layouts.App')

@section('title', 'Absensi - Sistem Manajemen PKL PLN Icon Plus')

@section('breadcrumb')
    Presensi &amp; Kehadiran <span class="material-symbols-outlined text-[14px] align-middle">chevron_right</span>
    <span class="text-primary font-label-md">Absensi</span>
@endsection

@section('extra-styles')
    <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
    <style>
        #location-map { z-index: 0; height: 100%; min-height: 288px; }
        #btn-locate-me { z-index: 1000; }
        .leaflet-control-attribution { font-size: 9px !important; }
        button:disabled { opacity: .55; cursor: not-allowed; }

        .wizard-steps { display: flex; align-items: flex-start; }
        .step-item { display: flex; flex-direction: column; align-items: center; gap: 6px; min-width: 92px; }
        .step-dot { width: 36px; height: 36px; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; background: #e3e6ea; color: #6b7280; transition: all .35s ease; }
        .step-dot.active { background: #006857; color: #fff; box-shadow: 0 0 0 6px rgba(0, 104, 87, .16); }
        .step-dot.done { background: #006857; color: #fff; }
        .step-label { font-size: 12px; font-weight: 600; color: #6b7280; text-align: center; transition: color .3s; }
        .step-label.active, .step-label.done { color: #006857; }
        .step-line { flex: 1; height: 3px; background: #e3e6ea; border-radius: 2px; margin: 17px 6px 0; position: relative; overflow: hidden; }
        .step-line::after { content: ''; position: absolute; inset: 0; background: #006857; transform: scaleX(0); transform-origin: left; transition: transform .45s ease; }
        .step-line.done::after { transform: scaleX(1); }

        .step-panel { animation: panel-in .35s ease both; }
        @keyframes panel-in { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }

        .shutter-flash { position: absolute; inset: 0; background: #fff; opacity: 0; pointer-events: none; }
        .shutter-flash.go { animation: shutter .4s ease-out; }
        @keyframes shutter { 0% { opacity: .9; } 100% { opacity: 0; } }

        .result-card { position: relative; overflow: hidden; animation: result-card-in .4s cubic-bezier(.2, .9, .3, 1.15) both; }
        .result-icon { position: relative; width: 96px; height: 96px; margin: 0 auto; display: flex; align-items: center; justify-content: center; color: var(--accent, #006857); }
        .result-svg { width: 96px; height: 96px; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; fill: none; animation: result-pop .55s cubic-bezier(.34, 1.56, .64, 1) both; }
        .result-fill { fill: currentColor; stroke: none; opacity: 0; animation: result-fill .5s ease-out .6s forwards; }
        .result-circle { stroke-dasharray: 151; stroke-dashoffset: 151; animation: result-draw .6s ease-out .1s forwards; }
        .result-mark { stroke-width: 3.5; stroke-dasharray: 40; stroke-dashoffset: 40; animation: result-draw .4s ease-out .65s forwards; }
        .result-mark.is-x { stroke-dasharray: 60; stroke-dashoffset: 60; }
        .result-ring { position: absolute; inset: 0; border-radius: 50%; background: currentColor; opacity: 0; animation: result-ring 1.3s ease-out .6s; }
        .result-card.is-error .result-svg { animation: result-shake .55s ease .15s both; }
        .result-row { animation: result-row-in .4s ease-out both; }
        .confetti-piece { position: absolute; left: 50%; top: 80px; width: 8px; height: 8px; border-radius: 2px; opacity: 0; animation: confetti-burst 1s ease-out .6s forwards; }

        @keyframes result-card-in { from { opacity: 0; transform: translateY(24px) scale(.92); } to { opacity: 1; transform: none; } }
        @keyframes result-pop { 0% { transform: scale(.6); } 100% { transform: scale(1); } }
        @keyframes result-draw { to { stroke-dashoffset: 0; } }
        @keyframes result-fill { to { opacity: .12; } }
        @keyframes result-ring { 0% { opacity: .35; transform: scale(.9); } 100% { opacity: 0; transform: scale(1.9); } }
        @keyframes result-shake { 0%, 100% { transform: translateX(0); } 20% { transform: translateX(-8px); } 40% { transform: translateX(8px); } 60% { transform: translateX(-5px); } 80% { transform: translateX(5px); } }
        @keyframes result-row-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        @keyframes confetti-burst { 0% { opacity: 1; transform: translate(0, 0) rotate(0) scale(1); } 100% { opacity: 0; transform: translate(var(--dx), var(--dy)) rotate(var(--rot)) scale(.5); } }

        @media (prefers-reduced-motion: reduce) {
            .step-panel, .step-dot, .step-line::after, .shutter-flash, .result-card, .result-svg, .result-fill, .result-circle, .result-mark, .result-ring, .result-row, .confetti-piece {
                animation-duration: .01ms !important;
                animation-delay: 0s !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $checkedIn = (bool) $todayAttendance;
        $checkedOut = $checkedIn && $todayAttendance->check_out;
        $isLate = $checkedIn && $todayAttendance->status === 'telat';
        $actionLabel = $checkedIn ? 'Check-out' : 'Check-in';
    @endphp

    <div class="flex flex-col gap-space-xl">
        <div class="flex flex-col gap-space-md">
            <div>
                <h1 class="font-headline-md text-headline-md text-on-surface">Absensi Hari Ini</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    @if ($checkedOut)
                        Kehadiran Anda hari ini sudah lengkap (check-in &amp; check-out).
                    @elseif ($checkedIn)
                        Anda sudah check-in. Lakukan check-out saat pekerjaan selesai.
                    @else
                        Ikuti 3 langkah berikut: verifikasi lokasi, ambil swafoto, lalu konfirmasi absensi.
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-space-sm">
                <div class="flex items-center gap-2 px-space-md py-space-xs rounded-xl bg-surface-container-low shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-none">Hari &amp; Tanggal</span>
                        <span class="font-label-md text-label-md text-on-surface">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-space-md py-space-xs rounded-xl bg-primary/10 shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">work_history</span>
                    <div class="flex flex-col">
                        <span class="font-label-sm text-label-sm text-primary leading-none">Jadwal Penugasan</span>
                        <span class="font-label-md text-label-md text-primary">Shift Pagi: 08:00 – 17:00 WIB</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-xl items-start">
            <div class="xl:col-span-7 flex flex-col gap-space-xl">
                <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-sm p-space-xl">
                    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col gap-space-lg">
                        <div class="flex items-center justify-between gap-space-sm">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[22px]">fingerprint</span>
                                <span class="font-headline-sm text-headline-sm text-on-surface">Status Presensi Harian</span>
                            </div>

                            @if ($checkedOut)
                                <div class="flex items-center gap-2 px-space-md py-1 rounded-full bg-primary-fixed/40 text-on-primary-fixed-variant">
                                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                                    <span class="font-label-md text-label-md">Selesai</span>
                                </div>
                            @elseif ($checkedIn)
                                <div class="flex items-center gap-2 px-space-md py-1 rounded-full {{ $isLate ? 'bg-error-container text-on-error-container' : 'bg-primary-fixed/40 text-on-primary-fixed-variant' }}">
                                    <span class="w-2 h-2 rounded-full {{ $isLate ? 'bg-error' : 'bg-primary' }}"></span>
                                    <span class="font-label-md text-label-md">{{ $isLate ? 'Terlambat' : 'Hadir' }}</span>
                                </div>
                            @else
                                <div class="flex items-center gap-2 px-space-md py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed">
                                    <span class="w-2 h-2 rounded-full bg-tertiary animate-ping"></span>
                                    <span class="font-label-md text-label-md">Belum Check-in</span>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col md:flex-row items-center justify-between gap-space-md p-space-lg rounded-xl bg-surface-container-low">
                            <div class="flex flex-col items-center md:items-start">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Waktu Saat Ini</span>
                                </div>
                                <div class="font-stat-counter text-[40px] leading-tight text-on-surface font-bold tracking-tight my-1" id="realtime-clock">
                                    --:--:-- <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">WIB</span>
                                </div>
                            </div>

                            @if (!$checkedIn)
                                <div class="flex flex-col gap-1 items-end self-stretch justify-center md:pl-space-lg md:border-l md:border-surface-variant/40">
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Tenggat Check-in Tepat Waktu</span>
                                    <span class="font-headline-sm text-headline-sm text-primary">{{ $lateAfter->format('H:i:s') }} WIB</span>
                                    <span class="font-label-sm text-label-sm text-tertiary" id="late-countdown">–</span>
                                </div>
                            @else
                                <div class="flex flex-col gap-1 items-end self-stretch justify-center md:pl-space-lg md:border-l md:border-surface-variant/40">
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Check-in</span>
                                    <span class="font-headline-sm text-headline-sm text-primary">{{ $todayAttendance->check_in->format('H:i:s') }} WIB</span>
                                    @if ($checkedOut)
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">
                                            Check-out {{ $todayAttendance->check_out->format('H:i:s') }} WIB
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if ($checkedIn)
                            <div class="flex flex-wrap gap-space-md">
                                @if ($todayAttendance->check_in_photo)
                                    <div class="flex flex-col gap-1">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">Foto Check-in</span>
                                        <img src="{{ route('absensi.photo', [$todayAttendance, 'in']) }}" alt="Foto check-in"
                                            class="w-32 h-32 object-cover rounded-xl shadow-sm">
                                    </div>
                                @endif
                                @if ($todayAttendance->check_out_photo)
                                    <div class="flex flex-col gap-1">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">Foto Check-out</span>
                                        <img src="{{ route('absensi.photo', [$todayAttendance, 'out']) }}" alt="Foto check-out"
                                            class="w-32 h-32 object-cover rounded-xl shadow-sm">
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($checkedOut)
                            <div class="w-full h-14 rounded-xl bg-primary-fixed/30 text-on-primary-fixed-variant font-headline-sm text-headline-sm flex items-center justify-center gap-space-sm">
                                <span class="material-symbols-outlined text-[24px]">task_alt</span>
                                <span>Presensi Hari Ini Selesai</span>
                            </div>
                        @endif
                    </div>
                </div>

                @unless ($checkedOut)
                    <div id="wizard" class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-lg">

                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[22px]">{{ $checkedIn ? 'logout' : 'login' }}</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface">{{ $actionLabel }} Absensi</span>
                        </div>

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

                        <div class="step-panel flex flex-col gap-space-lg" id="panel-1">
                            <div class="flex items-center justify-between flex-wrap gap-space-sm">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-primary text-[22px]">radar</span>
                                    <div class="flex flex-col">
                                        <span class="font-headline-sm text-headline-sm text-on-surface">Langkah 1: Verifikasi Lokasi</span>
                                        <span class="font-body-sm text-body-sm text-on-surface-variant" id="gps-status-subtitle">Menunggu izin akses lokasi perangkat</span>
                                    </div>
                                </div>
                                <span id="gps-status-badge"
                                    class="px-space-sm py-1 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                                    <span id="gps-status-text">Belum Aktif</span>
                                </span>
                            </div>

                            <div class="relative w-full rounded-xl overflow-hidden shadow-inner bg-surface-container-low" style="height:288px;">
                                <div class="w-full h-full" id="location-map"></div>
                                <button type="button" id="btn-locate-me"
                                    class="absolute bottom-3 right-3 z-[400] h-10 px-space-md rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md shadow-md hover:bg-surface-container-high transition-colors flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px]">my_location</span>
                                    <span>Gunakan Lokasi Saya</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-space-sm">
                                <div class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                                    <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[22px]">gps_fixed</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant truncate">Status GPS</span>
                                        <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate" id="metric-gps-status">Belum Aktif</span>
                                    </div>
                                </div>
                                <div class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                                    <div class="w-10 h-10 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[22px]">near_me</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant truncate">Jarak ke Kantor</span>
                                        <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate" id="metric-distance">–</span>
                                    </div>
                                </div>
                                <div class="p-space-md rounded-xl bg-surface-container-low flex items-center gap-space-sm">
                                    <div class="w-10 h-10 rounded-lg bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-[22px]">crisis_alert</span>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant truncate">Akurasi GPS</span>
                                        <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate" id="metric-accuracy">–</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
                                <div class="flex items-start gap-2 max-w-lg">
                                    <span class="material-symbols-outlined text-outline text-[20px] mt-0.5 shrink-0">corporate_fare</span>
                                    <div class="flex flex-col">
                                        <span class="font-label-md text-label-md text-on-surface">{{ $office['name'] ?? 'Lokasi kantor belum dikonfigurasi' }}</span>
                                        @if ($office)
                                            <span class="font-body-sm text-body-sm text-on-surface-variant">
                                                Radius presensi: {{ round($office['radius']) }} meter dari titik kantor
                                                (akurasi GPS maks. ±{{ $maxAccuracy }} m)
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

                        <div class="step-panel flex flex-col gap-space-lg" id="panel-2" style="display:none;">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[22px]">photo_camera_front</span>
                                <div class="flex flex-col">
                                    <span class="font-headline-sm text-headline-sm text-on-surface">Langkah 2: Ambil Swafoto</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Pastikan seluruh wajah terlihat jelas dan pencahayaan cukup.</span>
                                </div>
                            </div>

                            <div class="relative w-full rounded-xl overflow-hidden bg-inverse-surface"
                                style="aspect-ratio:4/3; max-width:480px; margin:0 auto;">
                                <video id="camera-preview" autoplay muted playsinline class="w-full h-full object-cover"></video>
                                <img id="captured-photo" alt="Hasil swafoto" class="w-full h-full object-cover absolute inset-0" style="display:none;">

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

                        <div class="step-panel flex flex-col gap-space-lg" id="panel-3" style="display:none;">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[22px]">fact_check</span>
                                <div class="flex flex-col">
                                    <span class="font-headline-sm text-headline-sm text-on-surface">Langkah 3: Konfirmasi Absensi</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Periksa kembali data berikut sebelum dikirim.</span>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-space-md">
                                <img id="confirm-photo" alt="Swafoto"
                                    style="width:180px; aspect-ratio:4/3; object-fit:cover; border-radius:12px; align-self:flex-start;"
                                    class="shadow-sm">

                                <div class="flex-1 flex flex-col gap-2 p-space-md rounded-xl bg-surface-container-low font-body-sm text-body-sm">
                                    <div class="flex justify-between gap-3">
                                        <span class="text-on-surface-variant">Jenis Presensi</span>
                                        <span class="font-label-md text-label-md text-on-surface" id="confirm-type">–</span>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <span class="text-on-surface-variant">Waktu</span>
                                        <span class="font-label-md text-label-md text-primary" id="confirm-time">--:--:-- WIB</span>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <span class="text-on-surface-variant">Lokasi</span>
                                        <span class="font-label-md text-label-md text-on-surface text-right" id="confirm-location">–</span>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <span class="text-on-surface-variant">Jarak ke Kantor</span>
                                        <span class="font-label-md text-label-md text-on-surface" id="confirm-distance">–</span>
                                    </div>
                                    <div class="flex justify-between gap-3">
                                        <span class="text-on-surface-variant">Akurasi GPS</span>
                                        <span class="font-label-md text-label-md text-on-surface" id="confirm-accuracy">–</span>
                                    </div>
                                    <div class="flex justify-between gap-3" id="confirm-status-row">
                                        <span class="text-on-surface-variant">Perkiraan Status</span>
                                        <span class="font-label-md text-label-md" id="confirm-status">–</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start gap-space-xs px-space-sm py-2 rounded-lg bg-surface-container-low">
                                <span class="material-symbols-outlined text-outline text-[18px] mt-0.5">info</span>
                                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                    Batas toleransi kehadiran adalah pukul <strong>{{ $lateAfter->format('H:i') }} WIB</strong>.
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
            <div class="xl:col-span-5 flex flex-col gap-space-xl">

                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-sm p-space-sm rounded-xl bg-surface-container-low">
                        <div class="w-11 h-11 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-bold shrink-0">
                            {{ $pembimbingInitials ?? '-' }}
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Mentor Lapangan Penanggung Jawab</span>
                            <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $pembimbing ?: '-' }}</span>
                        </div>
                        <span class="material-symbols-outlined text-primary text-[20px]">verified_user</span>
                    </div>
                </div>

                <div class="rounded-xl bg-surface-container-lowest shadow-sm p-space-xl flex flex-col gap-space-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[22px]">history</span>
                            <span class="font-headline-sm text-headline-sm text-on-surface">Riwayat Terakhir</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-space-sm">
                        @forelse ($recentAttendances as $row)
                            <div class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container-high transition-colors flex flex-col gap-space-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-label-lg text-label-lg text-on-surface font-semibold">
                                            {{ $row->attendance_date->translatedFormat('l, d M Y') }}
                                        </span>
                                        @if ($row->status === 'hadir')
                                            <span class="px-2 py-0.5 rounded-full bg-primary-fixed/30 text-on-primary-fixed-variant font-label-sm text-[10px]">Tepat Waktu</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-[10px]">Terlambat</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant pt-1">
                                    <div class="flex items-center gap-4">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-primary text-[16px]">login</span>
                                            {{ $row->check_in?->format('H:i') ?? '-' }} WIB
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-secondary text-[16px]">logout</span>
                                            {{ $row->check_out ? $row->check_out->format('H:i') . ' WIB' : '–' }}
                                        </span>
                                    </div>
                                    @if ($row->check_in_distance !== null)
                                        <span class="flex items-center gap-1 font-label-sm text-label-sm text-on-surface">
                                            <span class="material-symbols-outlined text-outline text-[14px]">pin_drop</span>
                                            {{ round($row->check_in_distance) }}m dari kantor
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-space-md rounded-xl bg-surface-container-low text-center font-body-sm text-body-sm text-on-surface-variant">
                                Belum ada riwayat absensi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div id="result-modal"
            style="display:none; position:fixed; inset:0; z-index:60; align-items:center; justify-content:center; padding:16px; background:rgba(0,0,0,.45); backdrop-filter:blur(4px);">
            <div id="result-card"
                class="result-card w-full max-w-sm rounded-2xl bg-surface-container-lowest shadow-2xl p-space-xl text-center">
                <div class="result-icon" id="result-icon"></div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface mt-space-md" id="result-title"></h3>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1" id="result-message"></p>
                <div class="flex flex-col gap-2 mt-space-md p-space-sm rounded-xl bg-surface-container-low text-left" id="result-details"></div>
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
        (function() {
            const OFFICE = @json($office);
            const MAX_ACCURACY = @json($maxAccuracy);
            const LATE_AFTER = @json($lateAfter->format('H:i:s'));
            const MODE = '{{ $checkedIn ? 'out' : 'in' }}'; // 'in' = check-in, 'out' = check-out
            const URLS = {
                in: @json(route('absensi.check-in')),
                out: @json(route('absensi.check-out')),
            };
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
            const POSITION_MAX_AGE = 5 * 60 * 1000; // lokasi dianggap kedaluwarsa setelah 5 menit
            const $ = (id) => document.getElementById(id);
            function tick() {
                const now = new Date();
                const t = now.toLocaleTimeString('id-ID', { hour12: false }).replace(/\./g, ':');

                if ($('realtime-clock')) {
                    $('realtime-clock').innerHTML =
                        `${t} <span class="font-label-lg text-label-lg text-on-surface-variant font-medium">WIB</span>`;
                }
                if ($('confirm-time')) $('confirm-time').textContent = `${t} WIB`;

                const cd = $('late-countdown');
                if (cd) {
                    const [h, m, s] = LATE_AFTER.split(':').map(Number);
                    const limit = new Date(now);
                    limit.setHours(h, m, s, 0);
                    const diff = Math.floor((limit - now) / 1000);
                    cd.textContent = diff > 0 ?
                        `Sisa toleransi: ${Math.floor(diff / 60)} Menit ${diff % 60} Detik` :
                        'Melewati batas toleransi (akan tercatat terlambat)';
                }
            }
            setInterval(tick, 1000);
            tick();
            const resultModal = $('result-modal');
            const resultCard = $('result-card');
            const resultIcon = $('result-icon');
            const resultTitle = $('result-title');
            const resultMessage = $('result-message');
            const resultDetails = $('result-details');
            const resultBtn = $('result-btn');
            let resultOnClose = null;

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
                for (let i = 0; i < 18; i++) {
                    const el = document.createElement('span');
                    const angle = (Math.PI * 2 * i) / 18 + Math.random() * 0.4;
                    const dist = 90 + Math.random() * 70;
                    el.className = 'confetti-piece';
                    el.style.background = colors[i % colors.length];
                    el.style.setProperty('--dx', `${Math.cos(angle) * dist}px`);
                    el.style.setProperty('--dy', `${Math.sin(angle) * dist * 0.8 + 20}px`);
                    el.style.setProperty('--rot', `${Math.random() * 540 - 270}deg`);
                    resultCard.appendChild(el);
                }
            }

            function showResult({ type = 'success', title, message = '', details = [], buttonText = 'Selesai', onClose = null }) {
                const accent = { success: '#006857', warning: '#d97706', error: '#ba1a1a' }[type];
                resultCard.style.setProperty('--accent', accent);
                resultCard.classList.toggle('is-error', type === 'error');
                resultCard.querySelectorAll('.confetti-piece').forEach((n) => n.remove());

                resultIcon.innerHTML = type === 'error' ? ICON_ERR : ICON_OK;
                resultIcon.style.color = accent;
                resultTitle.textContent = title;
                resultMessage.textContent = message;
                resultBtn.textContent = buttonText;
                resultOnClose = onClose;

                resultDetails.innerHTML = '';
                details.forEach((d, i) => {
                    const row = document.createElement('div');
                    row.className = 'result-row flex justify-between gap-3 font-body-sm text-body-sm';
                    row.style.animationDelay = `${0.75 + i * 0.1}s`;
                    const l = document.createElement('span');
                    l.className = 'text-on-surface-variant';
                    l.textContent = d.label;
                    const v = document.createElement('span');
                    v.className = 'font-label-md text-label-md text-on-surface text-right';
                    v.textContent = d.value;
                    row.append(l, v);
                    resultDetails.appendChild(row);
                });
                resultDetails.style.display = details.length ? 'flex' : 'none';

                resultCard.style.animation = 'none';
                void resultCard.offsetWidth;
                resultCard.style.animation = '';

                resultModal.style.display = 'flex';
                if (type !== 'error') burstConfetti(accent);
                resultBtn.focus();
            }

            function closeResult() {
                resultModal.style.display = 'none';
                const cb = resultOnClose;
                resultOnClose = null;
                if (cb) cb();
            }

            resultBtn.addEventListener('click', closeResult);
            resultModal.addEventListener('click', (e) => { if (e.target === resultModal) closeResult(); });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && resultModal.style.display === 'flex') closeResult();
            });

            if (!$('wizard')) return;

            let step = 1;
            let position = null; 
            let locationValid = false;
            let photoData = null;
            let stream = null;
            let map = null;
            let userMarker = null;
            let userCircle = null;

            const FLIP_CAMERA = false; 
            let flipCamera = FLIP_CAMERA;
            try {
                const saved = localStorage.getItem('absensi_flip_camera');
                if (saved !== null) flipCamera = saved === '1';
            } catch (_) {}

            const center = OFFICE ? [OFFICE.lat, OFFICE.lng] : [-8.1543, 113.7025];
            const CHECK_SVG =
                '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4 4 10-10"/></svg>';

            function goToStep(n) {
                if (n === 2 && !locationValid) return;
                if (n === 3 && !photoData) return;
                step = n;

                [1, 2, 3].forEach((i) => {
                    $(`panel-${i}`).style.display = i === n ? 'flex' : 'none';
                    const dot = $(`dot-${i}`);
                    const label = $(`label-${i}`);
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

                $('wizard').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function setGps(state, badge, subtitle) {
                const colors = {
                    idle: 'bg-surface-container-high text-on-surface-variant',
                    loading: 'bg-secondary-fixed text-on-secondary-fixed-variant',
                    ok: 'bg-primary-fixed/40 text-on-primary-fixed-variant',
                    error: 'bg-error-container text-on-error-container',
                };
                $('gps-status-badge').className =
                    `px-space-sm py-1 rounded-full font-label-sm text-label-sm flex items-center gap-1.5 ${colors[state]}`;
                $('gps-status-text').textContent = badge;
                $('gps-status-subtitle').textContent = subtitle;
                $('metric-gps-status').textContent = badge;
            }

            window.addEventListener('error', (e) => {
                console.error(e.error || e.message);
                setGps('error', 'Error Script', e.message || 'Terjadi kesalahan pada script.');
            });

            function distanceMeters(lat1, lng1, lat2, lng2) {
                const R = 6371000;
                const rad = (d) => d * Math.PI / 180;
                const dLat = rad(lat2 - lat1);
                const dLng = rad(lng2 - lng1);
                const a = Math.sin(dLat / 2) ** 2 +
                    Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLng / 2) ** 2;
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function locateUser() {
                locationValid = false;
                $('btn-to-2').disabled = true;

                if (!OFFICE) {
                    setGps('error', 'Belum Dikonfigurasi', 'Lokasi kantor belum diatur oleh admin.');
                    return;
                }
                if (!('geolocation' in navigator)) {
                    setGps('error', 'Tidak Didukung', 'Browser ini tidak mendukung geolokasi.');
                    return;
                }
                if (!window.isSecureContext) {
                    setGps('error', 'Koneksi Tidak Aman',
                        'Geolokasi hanya jalan di HTTPS atau localhost. Buka lewat http://localhost:8000 atau gunakan HTTPS.');
                    return;
                }

                setGps('loading', 'Mencari Lokasi…', 'Meminta izin lokasi dari browser…');

                navigator.geolocation.getCurrentPosition((pos) => {
                    const { latitude, longitude, accuracy } = pos.coords;
                    const dist = distanceMeters(latitude, longitude, OFFICE.lat, OFFICE.lng);
                    const inRadius = dist <= OFFICE.radius;
                    const accurate = accuracy <= MAX_ACCURACY;

                    position = { latitude, longitude, accuracy, distance: dist, time: Date.now() };
                    locationValid = inRadius && accurate;

                    if (map) {
                        if (userMarker) userMarker.setLatLng([latitude, longitude]);
                        else userMarker = L.marker([latitude, longitude]).addTo(map).bindPopup('Posisi Anda');

                        if (userCircle) userCircle.setLatLng([latitude, longitude]).setRadius(accuracy);
                        else userCircle = L.circle([latitude, longitude], {
                            radius: accuracy,
                            color: '#006398',
                            fillColor: '#006398',
                            fillOpacity: 0.12,
                            weight: 1
                        }).addTo(map);

                        map.fitBounds(L.latLngBounds([center, [latitude, longitude]]), {
                            padding: [40, 40],
                            maxZoom: 17
                        });
                    }

                    $('metric-distance').textContent =
                        `${Math.round(dist)} meter ${inRadius ? '(Aman)' : '(Terlalu Jauh)'}`;
                    $('metric-accuracy').textContent = `±${Math.round(accuracy)} meter`;

                    if (!inRadius) {
                        setGps('error', 'Di Luar Radius',
                            `Anda ${Math.round(dist)} m dari kantor (maks ${Math.round(OFFICE.radius)} m). Presensi tidak dapat dilakukan.`);
                    } else if (!accurate) {
                        setGps('error', 'Akurasi Rendah',
                            `Akurasi ±${Math.round(accuracy)} m, maksimal ${MAX_ACCURACY} m. Coba di area terbuka atau gunakan HP.`);
                    } else {
                        setGps('ok', 'Terkunci & Valid', 'Lokasi Anda berada di dalam radius kantor. Silakan lanjut ambil swafoto.');
                    }
                    $('btn-to-2').disabled = !locationValid;
                }, (err) => {
                    const msg = {
                        1: 'Izin lokasi ditolak. Aktifkan izin lokasi di browser dan layanan lokasi di sistem operasi.',
                        2: 'Lokasi tidak tersedia saat ini. Pastikan GPS/Wi-Fi aktif.',
                        3: 'Waktu permintaan lokasi habis. Coba lagi.',
                    }[err.code] || 'Gagal mengambil lokasi.';
                    setGps('error', 'Gagal / Ditolak', msg);
                    $('metric-distance').textContent = '–';
                    $('metric-accuracy').textContent = '–';
                }, {
                    enableHighAccuracy: true,
                    timeout: 20000,
                    maximumAge: 0
                });
            }

            $('btn-locate-me').addEventListener('click', locateUser);
            $('btn-refresh-location').addEventListener('click', () => {
                const icon = $('btn-refresh-location').querySelector('.material-symbols-outlined');
                icon?.classList.add('animate-spin');
                locateUser();
                setTimeout(() => icon?.classList.remove('animate-spin'), 800);
            });
            $('btn-to-2').addEventListener('click', () => goToStep(2));

            function initMap() {
                if (typeof L === 'undefined') {
                    $('location-map').innerHTML =
                        '<div style="padding:16px;font-size:13px">Peta gagal dimuat (Leaflet tidak ter-load). Periksa koneksi internet.</div>';
                    console.error('Leaflet (L) tidak terdefinisi');
                    return;
                }

                map = L.map('location-map').setView(center, 16);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                setTimeout(() => map.invalidateSize(), 200);

                if (OFFICE) {
                    L.marker(center).addTo(map)
                        .bindPopup(`<strong>${OFFICE.name}</strong><br>Radius geofence: ${OFFICE.radius} m`);
                    L.circle(center, {
                        radius: OFFICE.radius,
                        color: '#006857',
                        fillColor: '#006857',
                        fillOpacity: 0.15,
                        weight: 2
                    }).addTo(map);
                }
            }

            try {
                initMap();
            } catch (e) {
                console.error('Gagal membuat peta:', e);
            }
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

            function setPlaceholder(icon, text) {
                placeholder.innerHTML =
                    `<span class="material-symbols-outlined text-[36px]">${icon}</span><span>${text}</span>`;
                placeholder.classList.remove('hidden');
                placeholder.style.display = 'flex';
            }

            function applyFlip() {
                video.style.transform = flipCamera ? 'scaleX(-1)' : 'none';
            }

            btnFlip.addEventListener('click', () => {
                flipCamera = !flipCamera;
                try { localStorage.setItem('absensi_flip_camera', flipCamera ? '1' : '0'); } catch (_) {}
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

            async function startCamera() {
                if (stream) return;
                setPlaceholder('videocam', 'Menunggu izin akses kamera…');

                if (!navigator.mediaDevices?.getUserMedia) {
                    setPlaceholder('videocam_off', 'Kamera tidak didukung browser ini (gunakan HTTPS atau localhost).');
                    return;
                }
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: false
                    });
                    video.srcObject = stream;
                    applyFlip();
                    video.onplaying = () => {
                        placeholder.style.display = 'none';
                        btnCapture.disabled = false;
                    };
                } catch (e) {
                    const msg = e.name === 'NotAllowedError' ? 'Izin kamera ditolak. Aktifkan di pengaturan browser.' :
                        e.name === 'NotFoundError' ? 'Kamera tidak ditemukan pada perangkat ini.' :
                        'Tidak dapat mengakses kamera.';
                    setPlaceholder('videocam_off', msg);
                }
            }

            function stopCamera() {
                stream?.getTracks().forEach((t) => t.stop());
                stream = null;
                video.srcObject = null;
            }

            function capturePhoto() {
                if (!video.videoWidth) return null;
                const W = 640, H = 480, target = W / H;
                const vw = video.videoWidth, vh = video.videoHeight;
                let sw = vw, sh = vh, sx = 0, sy = 0;
                if (vw / vh > target) {
                    sw = vh * target;
                    sx = (vw - sw) / 2;
                } else {
                    sh = vw / target;
                    sy = (vh - sh) / 2;
                }
                canvas.width = W;
                canvas.height = H;
                const ctx = canvas.getContext('2d');
                if (flipCamera) {
                    ctx.translate(W, 0);
                    ctx.scale(-1, 1);
                }
                ctx.drawImage(video, sx, sy, sw, sh, 0, 0, W, H);
                return canvas.toDataURL('image/jpeg', 0.85);
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

            function fillConfirm() {
                $('confirm-photo').src = photoData;
                $('confirm-type').textContent = MODE === 'in' ? 'Check-in' : 'Check-out';
                $('confirm-location').textContent = OFFICE ? OFFICE.name : '–';
                $('confirm-distance').textContent = position ? `${Math.round(position.distance)} meter` : '–';
                $('confirm-accuracy').textContent = position ? `±${Math.round(position.accuracy)} meter` : '–';

                const statusRow = $('confirm-status-row');
                if (MODE === 'in') {
                    const [h, m, s] = LATE_AFTER.split(':').map(Number);
                    const limit = new Date();
                    limit.setHours(h, m, s, 0);
                    const late = new Date() > limit;
                    const el = $('confirm-status');
                    el.textContent = late ? 'Terlambat' : 'Tepat Waktu';
                    el.style.color = late ? '#ba1a1a' : '#006857';
                    statusRow.style.display = 'flex';
                } else {
                    statusRow.style.display = 'none';
                }
                tick();
            }

            $('btn-back-2').addEventListener('click', () => goToStep(2));

            const submitBtn = $('btn-submit');
            const submitHtml = submitBtn.innerHTML;

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

                if (Date.now() - position.time > POSITION_MAX_AGE) {
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

                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<span class="material-symbols-outlined animate-spin text-[20px]">progress_activity</span><span>Mengirim...</span>';

                try {
                    const res = await fetch(URLS[MODE], {
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

                    let data = {};
                    try { data = await res.json(); } catch (_) {}

                    if (res.ok && data.success) {
                        if (MODE === 'in') {
                            const late = data.status === 'telat';
                            showResult({
                                type: late ? 'warning' : 'success',
                                title: late ? 'Check-in Tercatat' : 'Check-in Berhasil!',
                                message: late ? 'Kehadiran Anda tercatat terlambat.' :
                                    'Kehadiran Anda tercatat tepat waktu. Selamat bekerja!',
                                details: [
                                    { label: 'Waktu Check-in', value: `${data.check_in} WIB` },
                                    { label: 'Status', value: late ? 'Terlambat' : 'Hadir' },
                                    { label: 'Jarak ke Kantor', value: `${data.distance} meter` },
                                ],
                                buttonText: 'Lihat Status Presensi',
                                onClose: () => window.location.reload(),
                            });
                        } else {
                            showResult({
                                type: 'success',
                                title: 'Check-out Berhasil!',
                                message: 'Terima kasih, sampai jumpa besok.',
                                details: [
                                    { label: 'Waktu Check-out', value: `${data.check_out} WIB` },
                                    { label: 'Jarak ke Kantor', value: `${data.distance} meter` },
                                ],
                                buttonText: 'Selesai',
                                onClose: () => window.location.reload(),
                            });
                        }
                    } else {
                        const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                        showResult({
                            type: 'error',
                            title: 'Presensi Gagal',
                            message: data.message || firstError || 'Gagal mengirim presensi.',
                            buttonText: 'Coba Lagi',
                        });
                    }
                } catch (e) {
                    console.error(e);
                    showResult({
                        type: 'error',
                        title: 'Gangguan Jaringan',
                        message: 'Terjadi kesalahan saat menghubungi server. Coba lagi.',
                        buttonText: 'Coba Lagi',
                    });
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitHtml;
                }
            });

            window.addEventListener('beforeunload', stopCamera);

            locateUser();
        })();
    </script>
@endsection