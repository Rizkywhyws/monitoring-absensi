<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Magang')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-900: #0b3b32;
            --brand-800: #0f5145;
            --brand-700: #146b5a;
            --brand-600: #16806a;
            --brand-500: #1a9678;
            --brand-100: #e3f3ee;

            --ink-900: #16211f;
            --ink-700: #445350;
            --ink-500: #6d7a77;
            --ink-300: #a9b3b1;

            --surface: #f4f6f5;
            --surface-card: #ffffff;
            --line: #e5eae8;

            --amber-bg: #fdf3e3;
            --amber-text: #92620a;
            --green-bg: #e6f6ee;
            --green-text: #0f7a4c;
            --red-bg: #fdeceb;
            --red-text: #c4392f;
            --blue-bg: #eaf1fb;
            --blue-text: #2a5fa5;

            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--surface);
            color: var(--ink-900);
        }

        a { color: inherit; }

        .app-shell {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(11, 59, 50, 0.35);
        z-index: 30;
        }
        .sidebar-backdrop.is-open { display: block; }
        .sidebar {
            background: var(--surface-card);
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            padding: 20px 16px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 8px 20px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 16px;
        }
        .sidebar-logo-mark {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: var(--brand-700);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-logo-mark svg { width: 17px; height: 17px; }
        .sidebar-logo-text {
            font-weight: 700;
            font-size: 14px;
            line-height: 1.25;
        }
        .sidebar-logo-text small {
            display: block;
            font-weight: 500;
            font-size: 10.5px;
            color: var(--ink-500);
        }

        .nav-group-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--ink-300);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .nav-list {
            list-style: none;
            margin: 0 0 20px;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 500;
            color: var(--ink-700);
            text-decoration: none;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .nav-item a svg { width: 17px; height: 17px; flex-shrink: 0; }
        .nav-item a:hover { background: var(--surface); }

        .nav-item.active a {
            background: var(--brand-700);
            color: white;
        }

        .nav-item .badge-count {
            margin-left: auto;
            background: var(--red-text);
            color: white;
            font-size: 10.5px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }
        .nav-item.active .badge-count {
            background: rgba(255,255,255,0.25);
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid var(--line);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border-radius: var(--radius-sm);
        }
        .sidebar-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            background: var(--brand-100);
            color: var(--brand-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }
        .sidebar-user-text { flex: 1; min-width: 0; }
        .sidebar-user-text strong {
            display: block;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-text span {
            font-size: 11px;
            color: var(--ink-500);
        }
        .sidebar-user-logout {
            color: var(--ink-300);
            flex-shrink: 0;
        }
        .sidebar-user-logout svg { width: 16px; height: 16px; }

        .main-area {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 28px;
            border-bottom: 1px solid var(--line);
            background: var(--surface-card);
        }

        .breadcrumb {
            font-size: 13px;
            color: var(--ink-500);
        }
        .breadcrumb strong { color: var(--ink-900); font-weight: 600; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-date {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            color: var(--ink-500);
        }
        .topbar-date svg { width: 14px; height: 14px; }

        .topbar-avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: var(--brand-700);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }

        .mobile-menu-btn {
            display: none;
            border: none;
            background: none;
            padding: 4px;
            cursor: pointer;
        }
        .mobile-menu-btn svg { width: 22px; height: 22px; }

        .content {
            padding: 24px 28px 40px;
            flex: 1;
        }

        @media (max-width: 900px) {
            .app-shell { grid-template-columns: 1fr; }
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                z-index: 40;
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                box-shadow: 20px 0 40px -20px rgba(0,0,0,0.15);
            }
            .sidebar.is-open { transform: translateX(0); }
            .mobile-menu-btn { display: inline-flex; }
            .topbar { padding: 14px 16px; }
            .content { padding: 18px 16px 32px; }
            .breadcrumb { display: none; }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(11, 59, 50, 0.35);
                z-index: 30;
            }
            .sidebar-backdrop.is-open { display: block; }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
        }
    </style>

    @yield('extra-styles')
</head>
<body>

<div class="app-shell">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-mark">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="white"/>
                </svg>
            </div>
            <div class="sidebar-logo-text">
                Portal Magang
                <small>Monitoring Absensi</small>
            </div>
        </div>

        <div class="nav-group-label">Menu Peserta</div>
        <ul class="nav-list">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="3" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="3" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/><rect x="13" y="13" width="8" height="8" rx="1.5" stroke="currentColor" stroke-width="1.6"/></svg>
                    Dashboard
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('absensi') ? 'active' : '' }}">
                <a href="{{ route('absensi') }}">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Absensi
                </a>
            </li>
            <li class="nav-item">
                <a href="#">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M8 9h8M8 13h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Ticket
                    <span class="badge-count">2</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 4h11l3 3v13H5V4Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 10h6M9 14h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Daily Activity
                </a>
            </li>
            <li class="nav-item">
                <a href="#">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 8a6 6 0 1 0-12 0c0 4-2 5-2 7h16c0-2-2-3-2-7Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M10 20a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.6"/></svg>
                    Notifikasi
                    <span class="badge-count">3</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.6"/><path d="M5 20c0-3.5 3.2-6 7-6s7 2.5 7 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    Profil
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">{{ $initials ?? 'RA' }}</div>
                <div class="sidebar-user-text">
                    <strong>{{ $userName ?? 'Nama Peserta' }}</strong>
                    <span>Peserta Magang</span>
                </div>
                <a href="#" class="sidebar-user-logout" title="Keluar">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 17l5-5-5-5M20 12H8M13 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Buka menu">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <div class="breadcrumb">@yield('breadcrumb')</div>
            </div>
            <div class="topbar-right">
                <div class="topbar-date">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="3" y="4" width="18" height="17" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M3 9h18M8 2v4M16 2v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    {{ $today ?? now()->translatedFormat('l, d F Y') }}
                </div>
                <div class="topbar-avatar">{{ $initials ?? 'RA' }}</div>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>
</div>

<script>
    // Toggle sidebar di mobile
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const menuBtn = document.getElementById('mobileMenuBtn');

    function toggleSidebar() {
        sidebar.classList.toggle('is-open');
        backdrop.classList.toggle('is-open');
    }
    menuBtn?.addEventListener('click', toggleSidebar);
    backdrop?.addEventListener('click', toggleSidebar);
</script>

@yield('extra-scripts')
</body>
</html>