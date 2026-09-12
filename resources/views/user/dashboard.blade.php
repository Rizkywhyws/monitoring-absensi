@extends('layouts.app')

@section('title', 'Dashboard — Portal Magang')

@section('breadcrumb')
    Dashboard
@endsection

@section('extra-styles')
<style>
    .greeting-card {
        background: linear-gradient(135deg, var(--brand-700), var(--brand-600));
        color: white;
        border-radius: var(--radius-lg);
        padding: 22px 26px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .greeting-card h1 {
        font-size: 21px;
        font-weight: 700;
        margin: 0 0 6px;
    }
    .greeting-card p {
        font-size: 13px;
        margin: 0;
        color: rgba(255,255,255,0.8);
        max-width: 420px;
        line-height: 1.5;
    }
    .dash-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .card {
        background: var(--surface-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        gap: 8px;
    }
    .card-header h3 {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .card-header h3 svg { width: 16px; height: 16px; color: var(--brand-700); }

    .badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .badge.amber { background: var(--amber-bg); color: var(--amber-text); }
    .badge.green { background: var(--green-bg); color: var(--green-text); }
    .badge.blue { background: var(--blue-bg); color: var(--blue-text); }

    .identity-row {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
    }
    .identity-avatar {
        width: 52px;
        height: 52px;
        border-radius: 999px;
        background: var(--brand-100);
        color: var(--brand-700);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        flex-shrink: 0;
    }
    .identity-name { font-size: 15px; font-weight: 700; margin: 0 0 3px; }
    .identity-nim { font-size: 12.5px; color: var(--ink-500); margin: 0; }

    .identity-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px solid var(--line);
    }
    .identity-meta-item span {
        display: block;
        font-size: 11px;
        color: var(--ink-300);
        margin-bottom: 3px;
    }
    .identity-meta-item strong {
        font-size: 13px;
        font-weight: 600;
        color: var(--ink-900);
    }
    .presensi-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 14px;
    }
    .presensi-meta-item span {
        display: block;
        font-size: 11px;
        color: var(--ink-300);
        margin-bottom: 3px;
    }
    .presensi-meta-item strong {
        font-size: 13px;
        font-weight: 600;
    }

    .presensi-status-box {
        background: var(--surface);
        border-radius: var(--radius-md);
        padding: 10px 12px;
        font-size: 12px;
        color: var(--ink-700);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .presensi-status-box svg { width: 15px; height: 15px; color: var(--brand-600); flex-shrink: 0; }

    .btn-primary-full {
        width: 100%;
        background: var(--brand-700);
        color: white;
        border: none;
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-primary-full:hover { background: var(--brand-800); }
    .btn-primary-full svg { width: 15px; height: 15px; }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: var(--surface-card);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
    }
    .stat-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    .stat-card-top span {
        font-size: 12.5px;
        color: var(--ink-500);
        font-weight: 500;
    }
    .stat-icon {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: var(--brand-100);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-icon svg { width: 13px; height: 13px; color: var(--brand-700); }
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 4px;
    }
    .stat-caption {
        font-size: 11.5px;
        color: var(--ink-300);
    }
    .stat-caption.warn { color: var(--red-text); }
    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        border: 1px solid var(--line);
        border-radius: var(--radius-md);
    }
    .activity-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .activity-icon svg { width: 14px; height: 14px; color: var(--ink-500); }
    .activity-body { flex: 1; min-width: 0; }
    .activity-top-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 4px;
        flex-wrap: wrap;
    }
    .activity-code { font-size: 12.5px; font-weight: 700; }
    .activity-title {
        font-size: 13px;
        color: var(--ink-700);
        margin: 0 0 4px;
        line-height: 1.4;
    }
    .activity-date { font-size: 11.5px; color: var(--ink-300); }
    .activity-link {
        font-size: 12px;
        font-weight: 600;
        color: var(--brand-700);
        text-decoration: none;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .card-footer-link {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--brand-700);
        text-decoration: none;
    }

    .dummy-notice {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--blue-bg);
        color: var(--blue-text);
        font-size: 12px;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
    }
    .dummy-notice svg { width: 15px; height: 15px; flex-shrink: 0; }

    @media (max-width: 900px) {
        .dash-grid { grid-template-columns: 1fr; }
        .stat-grid { grid-template-columns: 1fr 1fr; }
        .greeting-card { padding: 18px; }
        .identity-meta { grid-template-columns: 1fr; }
    }

    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
        .greeting-actions { width: 100%; }
        .btn-ghost-light { flex: 1; justify-content: center; }
    }
</style>
@endsection

@section('content')

    <div class="greeting-card">
        <div>
            <h1>Selamat datang, {{ $userName ?? 'Nama Peserta' }} 👋</h1>
            <p>{{ $today ?? now()->translatedFormat('l, d F Y') }} — Semoga hari magangmu berjalan lancar.</p>
        </div>
    </div>

    <div class="dash-grid">

        <div class="card">
            <div class="card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.6"/><path d="M5 20c0-3.5 3.2-6 7-6s7 2.5 7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Identitas Peserta
                </h3>
                <span class="badge green">Aktif &middot; Pekan ke-5</span>
            </div>

            <div class="identity-row">
                <div class="identity-avatar">{{ $initials ?? 'RA' }}</div>
                <div>
                    <p class="identity-name">{{ $userName ?? 'Nama Peserta' }}</p>
                    <p class="identity-nim">NIM: {{ $nim ?? '240810101052' }}</p>
                </div>
            </div>

            <div class="identity-meta">
                <div class="identity-meta-item">
                    <span>Periode Magang</span>
                    <strong>{{ $periodeMagang ?? '01 Agu 2026 – 30 Sep 2026' }}</strong>
                </div>
                <div class="identity-meta-item">
                    <span>Divisi / Penugasan</span>
                    <strong>{{ $divisi ?? 'Belum ditentukan' }}</strong>
                </div>
                <div class="identity-meta-item">
                    <span>Pembimbing Lapangan</span>
                    <strong>{{ $pembimbing ?? 'Belum ditentukan' }}</strong>
                </div>
                <div class="identity-meta-item">
                    <span>Asal Kampus</span>
                    <strong>{{ $kampus ?? 'Belum diisi' }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Presensi Hari Ini
                </h3>
                <span class="badge amber">Belum Check-in</span>
            </div>

            <div class="presensi-meta">
                <div class="presensi-meta-item">
                    <span>Jam Kerja Normal</span>
                    <strong>08:00 – 17:00 WIB</strong>
                </div>
                <div class="presensi-meta-item">
                    <span>Toleransi s/d</span>
                    <strong>08:15 WIB</strong>
                </div>
            </div>

            <div class="presensi-status-box">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="9.5" r="2.3" stroke="currentColor" stroke-width="1.6"/></svg>
                Verifikasi lokasi akan dilakukan saat Anda menekan tombol Check-in.
            </div>

            <a href="{{ route('absensi') }}" class="btn-primary-full">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8"/></svg>
                Ambil Presensi Sekarang (Check-in)
            </a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card-top">
                <span>Ticket Aktif</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/></svg>
                </div>
            </div>
            <p class="stat-value">2</p>
            <p class="stat-caption">Sedang dikerjakan</p>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <span>Ticket Selesai</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <p class="stat-value">18</p>
            <p class="stat-caption">Bulan ini</p>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <span>Perlu Revisi</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M10.3 3.9 2.7 17a1.6 1.6 0 0 0 1.4 2.4h15.8a1.6 1.6 0 0 0 1.4-2.4L13.7 3.9a1.6 1.6 0 0 0-2.8 0Z" stroke="currentColor" stroke-width="1.6"/></svg>
                </div>
            </div>
            <p class="stat-value">1</p>
            <p class="stat-caption warn">Butuh perbaikan segera</p>
        </div>
        <div class="stat-card">
            <div class="stat-card-top">
                <span>Aktivitas Tercatat</span>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 4h11l3 3v13H5V4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </div>
            </div>
            <p class="stat-value">14</p>
            <p class="stat-caption">Pekan ini</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/></svg>
                Ticket Penugasan Terbaru
            </h3>
            <a href="#" class="card-footer-link">Lihat Semua &rarr;</a>
        </div>

        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" stroke="currentColor" stroke-width="1.6"/></svg>
                </div>
                <div class="activity-body">
                    <div class="activity-top-row">
                        <span class="activity-code">TKT-0001</span>
                        <span class="badge blue">On Progress</span>
                    </div>
                    <p class="activity-title">Contoh judul ticket penugasan (data dummy)</p>
                    <span class="activity-date">Tanggal: 05 Sep 2026</span>
                </div>
                <a href="#" class="activity-link">Detail &rarr;</a>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </div>
                <div class="activity-body">
                    <div class="activity-top-row">
                        <span class="activity-code">TKT-0002</span>
                        <span class="badge amber">Perlu Revisi</span>
                    </div>
                    <p class="activity-title">Contoh judul ticket lain yang butuh perbaikan (data dummy)</p>
                    <span class="activity-date">Tanggal: 04 Sep 2026</span>
                </div>
                <a href="#" class="activity-link">Perbaiki &rarr;</a>
            </div>

            <div class="activity-item">
                <div class="activity-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="activity-body">
                    <div class="activity-top-row">
                        <span class="activity-code">TKT-0003</span>
                        <span class="badge green">Selesai</span>
                    </div>
                    <p class="activity-title">Contoh judul ticket yang telah selesai dikerjakan (data dummy)</p>
                    <span class="activity-date">Tanggal: 03 Sep 2026</span>
                </div>
                <a href="#" class="activity-link">Detail &rarr;</a>
            </div>
        </div>
    </div>

@endsection