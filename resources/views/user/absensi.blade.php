@extends('layouts.app')

@section('title', 'Absensi — Portal Magang')

@section('breadcrumb')
    Presensi &amp; Kehadiran <span style="color: var(--ink-300);">/</span> <strong>Absensi</strong>
@endsection

@section('extra-styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .absensi-header {
        margin-bottom: 20px;
    }
    .absensi-header h1 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 4px;
    }
    .absensi-header p {
        font-size: 13px;
        color: var(--ink-500);
        margin: 0 0 18px;
    }

    .step-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
    }
    .step-dot-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }
    .step-dot {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        background: var(--line);
        color: var(--ink-500);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        flex-shrink: 0;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .step-dot.done {
        background: var(--brand-600);
        color: white;
    }
    .step-dot.active {
        background: var(--brand-700);
        color: white;
        box-shadow: 0 0 0 4px var(--brand-100);
    }
    .step-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--ink-300);
        white-space: nowrap;
    }
    .step-label.active { color: var(--ink-900); }
    .step-line {
        flex: 1;
        height: 2px;
        background: var(--line);
        border-radius: 2px;
    }
    .step-line.done { background: var(--brand-600); }

    @media (max-width: 640px) {
        .step-label { display: none; }
    }

    .step-panel {
        display: none;
        background: var(--surface-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
        padding: 24px;
    }
    .step-panel.active { display: block; }

    .step-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .step-panel-head h2 {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 4px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .step-panel-head h2 svg { width: 18px; height: 18px; color: var(--brand-700); }
    .step-panel-head p {
        font-size: 12.5px;
        color: var(--ink-500);
        margin: 0;
    }

    .badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge svg { width: 12px; height: 12px; }
    .badge.green { background: var(--green-bg); color: var(--green-text); }
    .badge.red { background: var(--red-bg); color: var(--red-text); }
    .badge.amber { background: var(--amber-bg); color: var(--amber-text); }
    .badge.blue { background: var(--blue-bg); color: var(--blue-text); }
    .badge.neutral { background: var(--surface); color: var(--ink-500); }

    .geo-status-box {
        border: 1px solid var(--line);
        border-radius: var(--radius-md);
        padding: 18px;
        text-align: center;
        margin-bottom: 16px;
        background: var(--surface);
    }
    .geo-status-box .icon-circle {
        width: 56px;
        height: 56px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }
    .geo-status-box .icon-circle svg { width: 26px; height: 26px; }
    .icon-circle.neutral { background: var(--line); color: var(--ink-500); }
    .icon-circle.loading { background: var(--blue-bg); color: var(--blue-text); }
    .icon-circle.valid { background: var(--green-bg); color: var(--green-text); }
    .icon-circle.invalid { background: var(--red-bg); color: var(--red-text); }
    .icon-circle.denied { background: var(--amber-bg); color: var(--amber-text); }

    .geo-status-box h3 {
        font-size: 15px;
        font-weight: 700;
        margin: 0 0 6px;
    }
    .geo-status-box p {
        font-size: 12.5px;
        color: var(--ink-500);
        margin: 0;
        max-width: 420px;
        margin-inline: auto;
        line-height: 1.5;
    }

    .geo-map-wrap {
        border: 1px solid var(--line);
        border-radius: var(--radius-md);
        overflow: hidden;
        margin-bottom: 16px;
        display: none;
        position: relative;
    }
    .geo-map-wrap.visible { display: block; }
    #geo-map {
        width: 100%;
        height: 260px;
        background: var(--surface);
    }
    .geo-map-status-overlay {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 1000;
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .geo-map-status-overlay.valid { background: var(--green-text); color: white; }
    .geo-map-status-overlay.invalid { background: var(--red-text); color: white; }
    .geo-map-status-overlay svg { width: 12px; height: 12px; }
    .geo-map-legend {
        display: flex;
        gap: 16px;
        padding: 10px 14px;
        border-top: 1px solid var(--line);
        background: var(--surface-card);
        font-size: 11.5px;
        color: var(--ink-500);
    }
    .geo-map-legend span {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        flex-shrink: 0;
        display: inline-block;
    }
    .legend-dot.office { background: var(--brand-700); }
    .legend-dot.user { background: #2a5fa5; }
    .legend-dot.radius { background: transparent; border: 2px solid var(--brand-500); }

    .geo-metric-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }
    .geo-metric {
        border: 1px solid var(--line);
        border-radius: var(--radius-md);
        padding: 12px;
        text-align: center;
    }
    .geo-metric span {
        display: block;
        font-size: 11px;
        color: var(--ink-300);
        margin-bottom: 4px;
    }
    .geo-metric strong {
        font-size: 15px;
        font-weight: 700;
    }

    .info-note {
        display: flex;
        gap: 8px;
        align-items: flex-start;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        font-size: 12px;
        color: var(--ink-700);
        line-height: 1.5;
        margin-bottom: 18px;
    }
    .info-note svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; color: var(--ink-500); }

    .camera-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3;
        background: #0d1a17;
        border-radius: var(--radius-md);
        overflow: hidden;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .camera-wrap video,
    .camera-wrap canvas,
    .camera-wrap img.captured-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .camera-placeholder {
        color: rgba(255,255,255,0.5);
        font-size: 12.5px;
        text-align: center;
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .camera-placeholder svg { width: 32px; height: 32px; }

    .camera-guide-oval {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 58%;
        height: 78%;
        border: 2.5px dashed rgba(255,255,255,0.55);
        border-radius: 50% / 45%;
        pointer-events: none;
    }

    .camera-tag {
        position: absolute;
        font-size: 10.5px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 6px;
        background: rgba(0,0,0,0.55);
        color: white;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .camera-tag svg { width: 10px; height: 10px; }
    .camera-tag.top-left { top: 10px; left: 10px; }
    .camera-tag.top-right { top: 10px; right: 10px; }

    .camera-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
    }

    .cam-instruction {
        font-size: 12px;
        color: var(--ink-500);
        text-align: center;
        margin-bottom: 18px;
        line-height: 1.5;
    }
    .confirm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
        margin-bottom: 18px;
    }
    .confirm-photo {
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--line);
        aspect-ratio: 4/3;
        background: var(--surface);
    }
    .confirm-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .confirm-detail-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .confirm-detail-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--line);
        font-size: 13px;
    }
    .confirm-detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .confirm-detail-row span { color: var(--ink-500); }
    .confirm-detail-row strong { text-align: right; }

    @media (max-width: 700px) {
        .confirm-grid { grid-template-columns: 1fr; }
        .geo-metric-grid { grid-template-columns: 1fr; }
    }

    .btn-row {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    .btn-row.split { justify-content: space-between; }

    .btn {
        font-size: 13.5px;
        font-weight: 600;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-family: inherit;
        text-decoration: none;
    }
    .btn svg { width: 15px; height: 15px; }
    .btn-primary { background: var(--brand-700); color: white; }
    .btn-primary:hover { background: var(--brand-800); }
    .btn-primary:disabled { background: var(--ink-300); cursor: not-allowed; }
    .btn-outline { background: var(--surface-card); color: var(--ink-700); border: 1px solid var(--line); }
    .btn-outline:hover { background: var(--surface); }

    .success-box {
        text-align: center;
        padding: 30px 20px;
    }
    .success-box .icon-circle {
        width: 64px;
        height: 64px;
        margin-bottom: 16px;
    }
    .success-box h2 {
        font-size: 18px;
        margin: 0 0 8px;
    }
    .success-box p {
        font-size: 13px;
        color: var(--ink-500);
        max-width: 380px;
        margin: 0 auto 20px;
        line-height: 1.6;
    }
</style>
@endsection

@section('content')

    <div class="absensi-header">
        <h1>Absensi Hari Ini</h1>
        <p>Ikuti 3 langkah berikut untuk mencatat kehadiran Anda: verifikasi lokasi, ambil foto, lalu konfirmasi.</p>

        <div class="step-indicator">
            <div class="step-dot-wrap">
                <div class="step-dot active" id="dot-1">1</div>
                <span class="step-label active" id="label-1">Verifikasi Lokasi</span>
            </div>
            <div class="step-line" id="line-1"></div>
            <div class="step-dot-wrap">
                <div class="step-dot" id="dot-2">2</div>
                <span class="step-label" id="label-2">Ambil Foto</span>
            </div>
            <div class="step-line" id="line-2"></div>
            <div class="step-dot-wrap">
                <div class="step-dot" id="dot-3">3</div>
                <span class="step-label" id="label-3">Konfirmasi</span>
            </div>
        </div>
    </div>

    <div class="step-panel active" id="panel-1">
        <div class="step-panel-head">
            <div>
                <h2>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.6"/></svg>
                    Verifikasi Lokasi
                </h2>
                <p>Langkah 1 dari 3 — pastikan Anda berada di sekitar lokasi kantor/tempat magang.</p>
            </div>
            <span class="badge neutral" id="geo-badge">Belum Dicek</span>
        </div>

        <div class="geo-status-box">
            <div class="icon-circle neutral" id="geo-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.6"/></svg>
            </div>
            <h3 id="geo-title">Siap memeriksa lokasi Anda</h3>
            <p id="geo-desc">Tekan tombol di bawah untuk mengizinkan akses lokasi perangkat. Data lokasi hanya dipakai untuk memverifikasi kehadiran, tidak disimpan untuk keperluan lain.</p>
        </div>

        <div class="geo-map-wrap" id="geo-map-wrap">
            <div class="geo-map-status-overlay" id="geo-map-status-overlay" style="display:none;"></div>
            <div id="geo-map"></div>
            <div class="geo-map-legend">
                <span><span class="legend-dot office"></span> Lokasi Kantor</span>
                <span><span class="legend-dot user"></span> Posisi Anda</span>
                <span><span class="legend-dot radius"></span> Radius Toleransi</span>
            </div>
        </div>

        <div class="geo-metric-grid">
            <div class="geo-metric">
                <span>Jarak ke Kantor</span>
                <strong id="geo-distance">–</strong>
            </div>
            <div class="geo-metric">
                <span>Radius Diizinkan</span>
                <strong id="geo-radius">–</strong>
            </div>
            <div class="geo-metric">
                <span>Akurasi GPS</span>
                <strong id="geo-accuracy">–</strong>
            </div>
        </div>

        <div class="info-note">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v.01M12 11v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>Koordinat &amp; radius kantor pada mockup ini masih nilai contoh (dummy) yang ditulis langsung di kode. Setelah backend tersedia, nilai ini akan diambil dari pengaturan sistem agar mudah diubah admin tanpa mengedit kode.</span>
        </div>

        <div id="geo-resolution-box" style="display:none; border: 1px solid #f3c9c5; background: var(--red-bg); border-radius: var(--radius-md); padding: 16px; margin-bottom: 18px;">
            <p style="font-size: 13px; font-weight: 700; color: var(--red-text); margin: 0 0 12px; display:flex; align-items:center; gap:7px;">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:15px; height:15px;"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Panduan Penyelesaian
            </p>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <span style="flex-shrink:0; width:20px; height:20px; border-radius:999px; background:var(--red-text); color:white; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center;">1</span>
                    <div>
                        <strong style="display:block; font-size:12.5px; color: var(--ink-900); margin-bottom:2px;">Mendekat ke area kantor</strong>
                        <span style="font-size:12px; color: var(--ink-700); line-height:1.5;">Silakan masuk ke lingkungan atau halaman kantor hingga jarak terdeteksi di bawah radius yang diizinkan.</span>
                    </div>
                </div>
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <span style="flex-shrink:0; width:20px; height:20px; border-radius:999px; background:var(--red-text); color:white; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center;">2</span>
                    <div>
                        <strong style="display:block; font-size:12.5px; color: var(--ink-900); margin-bottom:2px;">Aktifkan GPS akurasi tinggi</strong>
                        <span style="font-size:12px; color: var(--ink-700); line-height:1.5;">Pastikan fitur lokasi (GPS) perangkat Anda diatur ke mode akurasi tinggi, dan sinyal WiFi/data aktif untuk membantu penentuan posisi.</span>
                    </div>
                </div>
                <div style="display:flex; gap:10px; align-items:flex-start;">
                    <span style="flex-shrink:0; width:20px; height:20px; border-radius:999px; background:var(--red-text); color:white; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center;">3</span>
                    <div>
                        <strong style="display:block; font-size:12.5px; color: var(--ink-900); margin-bottom:2px;">Coba periksa ulang lokasi</strong>
                        <span style="font-size:12px; color: var(--ink-700); line-height:1.5;">Setelah berada di area yang sesuai, tekan tombol "Cek Ulang Lokasi" di bawah untuk memverifikasi ulang.</span>
                    </div>
                </div>
            </div>
            <p id="geo-last-attempt" style="font-size:11.5px; color: var(--ink-500); margin: 12px 0 0; padding-top:12px; border-top:1px solid #f3c9c5;"></p>
        </div>

        <div class="btn-row">
            <button class="btn btn-primary" id="btn-check-location">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.8"/></svg>
                Cek Lokasi Saya
            </button>
            <button class="btn btn-primary" id="btn-to-step-2" style="display:none;">
                Lanjut ke Foto
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>
    <div class="step-panel" id="panel-2">
        <div class="step-panel-head">
            <div>
                <h2>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.7"/></svg>
                    Ambil Foto Presensi
                </h2>
                <p>Langkah 2 dari 3 — posisikan wajah Anda di dalam bingkai panduan.</p>
            </div>
            <span class="badge blue" id="cam-badge">Kamera Belum Aktif</span>
        </div>

        <div class="camera-wrap" id="camera-wrap">
            <div class="camera-placeholder" id="camera-placeholder">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.6"/></svg>
                Kamera belum aktif. Tekan tombol "Aktifkan Kamera" di bawah.
            </div>
            <video id="camera-video" autoplay playsinline style="display:none;"></video>
            <canvas id="camera-canvas" style="display:none;"></canvas>
            <img id="captured-photo" class="captured-preview" style="display:none;" alt="Hasil foto presensi">
            <div class="camera-guide-oval" id="camera-guide" style="display:none;"></div>
        </div>

        <p class="cam-instruction">Pastikan pencahayaan cukup dan wajah terlihat jelas di dalam bingkai sebelum mengambil foto.</p>

        <div class="camera-actions">
            <button class="btn btn-outline" id="btn-start-camera" style="flex:1; justify-content:center;">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.7"/></svg>
                Aktifkan Kamera
            </button>
            <button class="btn btn-primary" id="btn-take-photo" style="flex:1; justify-content:center; display:none;">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/></svg>
                Ambil Foto
            </button>
            <button class="btn btn-outline" id="btn-retake-photo" style="flex:1; justify-content:center; display:none;">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4v6h6M20 20v-6h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 10a8 8 0 0 1 14.9-3.5M20 14a8 8 0 0 1-14.9 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Ambil Ulang
            </button>
        </div>

        <div class="info-note" id="camera-error-note" style="display:none; background: var(--red-bg); border-color: #f3c9c5; color: var(--red-text);">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v.01M12 11v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span id="camera-error-text">Tidak dapat mengakses kamera. Pastikan Anda mengizinkan akses kamera pada browser.</span>
        </div>

        <div class="btn-row split">
            <button class="btn btn-outline" id="btn-back-to-1">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 12H5M11 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </button>
            <button class="btn btn-primary" id="btn-to-step-3" disabled>
                Gunakan Foto &amp; Lanjutkan
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
        </div>
    </div>

    <div class="step-panel" id="panel-3">
        <div class="step-panel-head">
            <div>
                <h2>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Konfirmasi Absensi
                </h2>
                <p>Langkah 3 dari 3 — periksa kembali data sebelum mengirim.</p>
            </div>
            <span class="badge amber">Menunggu Konfirmasi</span>
        </div>

        <div class="confirm-grid">
            <div class="confirm-photo">
                <img id="confirm-photo-preview" src="" alt="Foto presensi">
            </div>
            <div class="confirm-detail-list">
                <div class="confirm-detail-row">
                    <span>Nama Peserta</span>
                    <strong>{{ $userName ?? 'Nama Peserta' }}</strong>
                </div>
                <div class="confirm-detail-row">
                    <span>NIM</span>
                    <strong>{{ $nim ?? '240810101052' }}</strong>
                </div>
                <div class="confirm-detail-row">
                    <span>Jenis Presensi</span>
                    <strong>Check-in Masuk</strong>
                </div>
                <div class="confirm-detail-row">
                    <span>Tanggal &amp; Waktu</span>
                    <strong id="confirm-datetime">–</strong>
                </div>
                <div class="confirm-detail-row">
                    <span>Jarak dari Kantor</span>
                    <strong id="confirm-distance">–</strong>
                </div>
                <div class="confirm-detail-row">
                    <span>Status Lokasi</span>
                    <strong id="confirm-geo-status">–</strong>
                </div>
            </div>
        </div>

        <div class="info-note">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 8v.01M12 11v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>Ini masih tampilan mockup frontend. Tombol "Kirim Absensi" belum benar-benar mengirim data ke server karena backend belum dibangun.</span>
        </div>

        <div class="btn-row split">
            <button class="btn btn-outline" id="btn-back-to-2">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 12H5M11 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Ambil Ulang Foto
            </button>
            <button class="btn btn-primary" id="btn-submit-absensi">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kirim Absensi
            </button>
        </div>
    </div>

    <div class="step-panel" id="panel-4">
        <div class="success-box">
            <div class="icon-circle valid" style="margin-inline:auto;">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h2>Absensi Berhasil Dicatat (Contoh)</h2>
            <p>Ini adalah simulasi tampilan sukses. Setelah backend tersedia, data ini akan benar-benar tersimpan dan riwayat absensi akan diperbarui secara otomatis.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary" style="display:inline-flex;">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

@endsection

@section('extra-scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    "use strict";

    const OFFICE_LOCATION = {
        latitude: -8.175420,
        longitude: 113.689240,
        radiusMeters: 100,
    };

    let currentStep = 1;
    let geoResult = null;
    let capturedPhotoDataUrl = null;
    let mediaStream = null;
    let leafletMap = null;
    let officeMarker = null;
    let userMarker = null;
    let radiusCircle = null;

    function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = (deg) => (deg * Math.PI) / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    function formatMeters(m) {
        return Math.round(m) + ' meter';
    }

    function showOrUpdateMap(userLat, userLng, isValid) {
        const mapWrap = document.getElementById('geo-map-wrap');
        mapWrap.classList.add('visible');

        const overlay = document.getElementById('geo-map-status-overlay');
        overlay.style.display = 'inline-flex';
        overlay.className = 'geo-map-status-overlay ' + (isValid ? 'valid' : 'invalid');
        overlay.innerHTML = isValid
            ? '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg> Dalam Radius'
            : '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 9l-6 6M9 9l6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg> Di Luar Radius';

        if (!leafletMap) {
            leafletMap = L.map('geo-map', {
                zoomControl: true,
                attributionControl: true,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(leafletMap);

            const officeIcon = L.divIcon({
                className: '',
                html: '<div style="width:16px;height:16px;border-radius:999px;background:#146b5a;border:2.5px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>',
                iconSize: [16, 16],
                iconAnchor: [8, 8],
            });
            officeMarker = L.marker([OFFICE_LOCATION.latitude, OFFICE_LOCATION.longitude], { icon: officeIcon })
                .addTo(leafletMap)
                .bindPopup('Lokasi Kantor');

            radiusCircle = L.circle([OFFICE_LOCATION.latitude, OFFICE_LOCATION.longitude], {
                radius: OFFICE_LOCATION.radiusMeters,
                color: '#1a9678',
                fillColor: '#1a9678',
                fillOpacity: 0.12,
                weight: 2,
            }).addTo(leafletMap);
        }

        const userColor = isValid ? '#0f7a4c' : '#c4392f';
        const userIcon = L.divIcon({
            className: '',
            html: '<div style="width:16px;height:16px;border-radius:999px;background:' + userColor + ';border:2.5px solid white;box-shadow:0 1px 4px rgba(0,0,0,0.4);"></div>',
            iconSize: [16, 16],
            iconAnchor: [8, 8],
        });

        if (userMarker) {
            userMarker.setLatLng([userLat, userLng]);
            userMarker.setIcon(userIcon);
        } else {
            userMarker = L.marker([userLat, userLng], { icon: userIcon })
                .addTo(leafletMap)
                .bindPopup('Posisi Anda');
        }

        const bounds = L.latLngBounds([
            [OFFICE_LOCATION.latitude, OFFICE_LOCATION.longitude],
            [userLat, userLng],
        ]);
        leafletMap.fitBounds(bounds, { padding: [40, 40], maxZoom: 17 });

        setTimeout(() => leafletMap.invalidateSize(), 200);
    }

    function goToStep(step) {
        document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + step).classList.add('active');

        for (let i = 1; i <= 3; i++) {
            const dot = document.getElementById('dot-' + i);
            const label = document.getElementById('label-' + i);
            const line = document.getElementById('line-' + i);
            dot.classList.remove('active', 'done');
            label.classList.remove('active');
            if (i < step) {
                dot.classList.add('done');
                dot.innerHTML = '&#10003;';
            } else if (i === step) {
                dot.classList.add('active');
                label.classList.add('active');
                dot.textContent = i;
            } else {
                dot.textContent = i;
            }
            if (line && i < 3) {
                if (i < step) line.classList.add('done');
                else line.classList.remove('done');
            }
        }
        currentStep = step;
    }

    const geoIcon = document.getElementById('geo-icon');
    const geoTitle = document.getElementById('geo-title');
    const geoDesc = document.getElementById('geo-desc');
    const geoBadge = document.getElementById('geo-badge');
    const geoDistanceEl = document.getElementById('geo-distance');
    const geoRadiusEl = document.getElementById('geo-radius');
    const geoAccuracyEl = document.getElementById('geo-accuracy');
    const btnCheckLocation = document.getElementById('btn-check-location');
    const btnToStep2 = document.getElementById('btn-to-step-2');
    const geoResolutionBox = document.getElementById('geo-resolution-box');
    const geoLastAttempt = document.getElementById('geo-last-attempt');

    geoRadiusEl.textContent = OFFICE_LOCATION.radiusMeters + ' meter';

    function setGeoState(state, title, desc) {
        geoIcon.className = 'icon-circle ' + state;
        geoTitle.textContent = title;
        geoDesc.textContent = desc;

        const badgeMap = {
            loading: ['blue', 'Mencari Lokasi...'],
            valid: ['green', 'Lokasi Valid'],
            invalid: ['red', 'Di Luar Radius'],
            denied: ['amber', 'Izin Ditolak'],
            neutral: ['neutral', 'Belum Dicek'],
        };
        const [cls, text] = badgeMap[state] || badgeMap.neutral;
        geoBadge.className = 'badge ' + cls;
        geoBadge.textContent = text;
    }

    btnCheckLocation.addEventListener('click', function () {
        if (!navigator.geolocation) {
            setGeoState('denied', 'Geolocation Tidak Didukung', 'Browser Anda tidak mendukung fitur lokasi. Coba gunakan browser lain seperti Chrome atau Firefox terbaru.');
            return;
        }

        setGeoState('loading', 'Mencari Lokasi Anda...', 'Mohon tunggu, sistem sedang mengambil koordinat perangkat Anda.');
        btnCheckLocation.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const { latitude, longitude, accuracy } = position.coords;
                const distance = calculateDistanceMeters(
                    latitude, longitude,
                    OFFICE_LOCATION.latitude, OFFICE_LOCATION.longitude
                );
                const isValid = distance <= OFFICE_LOCATION.radiusMeters;

                geoDistanceEl.textContent = formatMeters(distance);
                geoAccuracyEl.textContent = '±' + Math.round(accuracy) + ' meter';

                geoResult = { distance, accuracy, valid: isValid };

                showOrUpdateMap(latitude, longitude, isValid);

                if (isValid) {
                    setGeoState('valid', 'Lokasi Anda Valid', 'Silakan lanjutkan ke pengambilan foto.');
                    btnCheckLocation.style.display = 'none';
                    btnToStep2.style.display = 'inline-flex';
                    geoResolutionBox.style.display = 'none';
                } else {
                    setGeoState('invalid', 'Lihat posisi Anda di peta', 'Lokasi Anda berada di luar radius yang ditandai pada peta di bawah.');
                    btnCheckLocation.disabled = false;
                    btnCheckLocation.textContent = '';
                    btnCheckLocation.innerHTML = '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 4v6h6M20 20v-6h-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 10a8 8 0 0 1 14.9-3.5M20 14a8 8 0 0 1-14.9 3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg> Cek Ulang Lokasi';

                    geoResolutionBox.style.display = 'block';
                    const attemptTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    geoLastAttempt.textContent = 'Percobaan terakhir pukul ' + attemptTime + ' WIB — jarak terdeteksi ' + formatMeters(distance) + ', melebihi batas ' + OFFICE_LOCATION.radiusMeters + ' meter.';
                }
            },
            function (error) {
                btnCheckLocation.disabled = false;
                geoResolutionBox.style.display = 'none';
                if (error.code === error.PERMISSION_DENIED) {
                    setGeoState('denied', 'Izin Lokasi Ditolak', 'Anda menolak permintaan akses lokasi. Aktifkan izin lokasi untuk situs ini melalui pengaturan browser, lalu coba lagi.');
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    setGeoState('denied', 'Lokasi Tidak Tersedia', 'Sistem tidak dapat menentukan lokasi Anda saat ini. Pastikan GPS/layanan lokasi perangkat aktif.');
                } else {
                    setGeoState('denied', 'Waktu Habis', 'Permintaan lokasi memakan waktu terlalu lama. Periksa koneksi dan coba lagi.');
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });

    btnToStep2.addEventListener('click', function () {
        goToStep(2);
    });

    const cameraWrap = document.getElementById('camera-wrap');
    const cameraPlaceholder = document.getElementById('camera-placeholder');
    const cameraVideo = document.getElementById('camera-video');
    const cameraCanvas = document.getElementById('camera-canvas');
    const cameraGuide = document.getElementById('camera-guide');
    const capturedPhotoImg = document.getElementById('captured-photo');
    const camBadge = document.getElementById('cam-badge');
    const btnStartCamera = document.getElementById('btn-start-camera');
    const btnTakePhoto = document.getElementById('btn-take-photo');
    const btnRetakePhoto = document.getElementById('btn-retake-photo');
    const btnToStep3 = document.getElementById('btn-to-step-3');
    const cameraErrorNote = document.getElementById('camera-error-note');
    const cameraErrorText = document.getElementById('camera-error-text');

    btnStartCamera.addEventListener('click', async function () {
        cameraErrorNote.style.display = 'none';

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraErrorText.textContent = 'Browser ini tidak mendukung akses kamera. Coba gunakan browser lain.';
            cameraErrorNote.style.display = 'flex';
            return;
        }

        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user' },
                audio: false,
            });
            cameraVideo.srcObject = mediaStream;
            cameraPlaceholder.style.display = 'none';
            cameraVideo.style.display = 'block';
            cameraGuide.style.display = 'block';
            camBadge.className = 'badge green';
            camBadge.textContent = 'Kamera Aktif';
            btnStartCamera.style.display = 'none';
            btnTakePhoto.style.display = 'inline-flex';
        } catch (err) {
            let message = 'Tidak dapat mengakses kamera. Pastikan Anda mengizinkan akses kamera pada browser.';
            if (err.name === 'NotAllowedError') {
                message = 'Akses kamera ditolak. Aktifkan izin kamera untuk situs ini melalui pengaturan browser.';
            } else if (err.name === 'NotFoundError') {
                message = 'Kamera tidak ditemukan pada perangkat ini.';
            }
            cameraErrorText.textContent = message;
            cameraErrorNote.style.display = 'flex';
            camBadge.className = 'badge red';
            camBadge.textContent = 'Kamera Gagal Diakses';
        }
    });

    btnTakePhoto.addEventListener('click', function () {
        const w = cameraVideo.videoWidth;
        const h = cameraVideo.videoHeight;
        cameraCanvas.width = w;
        cameraCanvas.height = h;
        const ctx = cameraCanvas.getContext('2d');
        ctx.translate(w, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(cameraVideo, 0, 0, w, h);

        capturedPhotoDataUrl = cameraCanvas.toDataURL('image/jpeg', 0.9);
        capturedPhotoImg.src = capturedPhotoDataUrl;

        cameraVideo.style.display = 'none';
        cameraGuide.style.display = 'none';
        capturedPhotoImg.style.display = 'block';

        btnTakePhoto.style.display = 'none';
        btnRetakePhoto.style.display = 'inline-flex';
        btnToStep3.disabled = false;

        camBadge.className = 'badge green';
        camBadge.textContent = 'Foto Diambil';

        stopCameraStream();
    });

    btnRetakePhoto.addEventListener('click', async function () {
        capturedPhotoImg.style.display = 'none';
        btnRetakePhoto.style.display = 'none';
        btnToStep3.disabled = true;
        btnStartCamera.click();
    });

    function stopCameraStream() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
    }

    document.getElementById('btn-back-to-1').addEventListener('click', function () {
        stopCameraStream();
        goToStep(1);
    });

    btnToStep3.addEventListener('click', function () {
        stopCameraStream();

        document.getElementById('confirm-photo-preview').src = capturedPhotoDataUrl || '';

        const now = new Date();
        const formatted = now.toLocaleDateString('id-ID', {
            weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'
        }) + ', ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        document.getElementById('confirm-datetime').textContent = formatted;

        if (geoResult) {
            document.getElementById('confirm-distance').textContent = formatMeters(geoResult.distance);
            const statusEl = document.getElementById('confirm-geo-status');
            statusEl.textContent = geoResult.valid ? 'Valid — Dalam Radius' : 'Di Luar Radius';
            statusEl.style.color = geoResult.valid ? 'var(--green-text)' : 'var(--red-text)';
        }

        goToStep(3);
    });

    document.getElementById('btn-back-to-2').addEventListener('click', function () {
        goToStep(2);
        capturedPhotoImg.style.display = 'none';
        cameraPlaceholder.style.display = 'flex';
        btnRetakePhoto.style.display = 'none';
        btnStartCamera.style.display = 'inline-flex';
        btnToStep3.disabled = true;
    });

    document.getElementById('btn-submit-absensi').addEventListener('click', function () {
        goToStep(4);
    });

    window.addEventListener('beforeunload', stopCameraStream);

})();
</script>
@endsection