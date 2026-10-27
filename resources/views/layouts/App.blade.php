<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal Magang')</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <style>
        @layer base {
            html,
            body {
                margin: 0;
                padding: 0;
            }
            body {
                overscroll-behavior: none;
            }
            main>:first-child {
                margin-top: 0 !important;
            }
            main>:last-child {
                margin-bottom: 0 !important;
            }
        }
        ::-webkit-scrollbar {
            display: none;
        }
        .sidebar-backdrop {
            display: none;
        }
        .sidebar-backdrop.is-open {
            display: block;
        }
        #logoutModal.is-open {
            animation: modalFadeIn .18s ease-out both;
        }
        #logoutModal.is-open #logoutCard {
            animation: modalCardIn .22s cubic-bezier(.22, 1, .36, 1) both;
        }
        #logoutModal.is-leaving {
            animation: modalFadeOut .14s ease-in both;
        }
        #logoutModal.is-leaving #logoutCard {
            animation: modalCardOut .14s ease-in both;
        }
        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
        @keyframes modalFadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }
        @keyframes modalCardIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes modalCardOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateY(5px) scale(.985);
            }
        }
        .lo-ring,
        .lo-check {
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
        }
        #logoutModal.is-open .lo-ring {
            animation: lo-draw .28s ease-out .08s forwards;
        }
        #logoutModal.is-open .lo-check {
            animation: lo-draw .22s ease-out .25s forwards;
        }
        @keyframes lo-draw {
            to {
                stroke-dashoffset: 0;
            }
        }
        .lo-halo {
            opacity: 0;
        }

        #logoutModal.is-open .lo-halo {
            animation: lo-halo .5s ease-out .18s both;
        }
        @keyframes lo-halo {
            from {
                transform: scale(.9);
                opacity: .35;
            }

            to {
                transform: scale(1.25);
                opacity: 0;
            }
        }
        .lo-up {
            opacity: 0;
        }
        #logoutModal.is-open .lo-up {
            animation: lo-up .2s ease-out .18s forwards;
        }
        @keyframes lo-up {
            from {
                opacity: 0;
                transform: translateY(4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        #logoutBar {
            width: 0;
        }
        #logoutModal.is-open #logoutBar {
            animation: lo-bar .65s cubic-bezier(.4, 0, .2, 1) .08s forwards;
        }
        @keyframes lo-bar {
            to {
                width: 100%;
            }
        }
        .pp-modal.is-open {
            animation: modalFadeIn .18s ease-out both;
        }
        .pp-modal.is-open .pp-card {
            animation: modalCardIn .2s cubic-bezier(.22, 1, .36, 1) both;
        }
        .pp-modal.is-leaving {
            animation: modalFadeOut .14s ease-in both;
        }
        .pp-modal.is-leaving .pp-card {
            animation: modalCardOut .14s ease-in both;
        }
        .pp-ring,
        .pp-check,
        .pp-mark {
            stroke-dasharray: 1;
            stroke-dashoffset: 1;
        }
        .pp-modal.is-open .pp-ring {
            animation: lo-draw .25s ease-out .06s forwards;
        }
        .pp-modal.is-open .pp-check,
        .pp-modal.is-open .pp-mark {
            animation: lo-draw .2s ease-out .18s forwards;
        }
        .pp-bar {
            transform-origin: left center;
            will-change: transform;
        }
        #popupModal.is-open .pp-bar {
            animation: pp-bar var(--pp-duration, 700ms) linear forwards;
        }
        @keyframes pp-bar {
            from {
                transform: scaleX(1);
            }

            to {
                transform: scaleX(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-delay: 0s !important;
                transition-duration: .01ms !important;
            }
        }
        @media (max-width: 900px) {
            .app-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                transform: translateX(-100%);
                transition: transform .18s ease-out;
            }

            .app-sidebar.is-open {
                transform: translateX(0);
            }

            .app-main-area {
                padding-left: 0 !important;
            }

            .app-header {
                left: 0 !important;
            }

            .mobile-menu-btn {
                display: inline-flex !important;
            }
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-low": "#f2f3ff",
                        "tertiary-container": "#a36700",
                        "primary-container": "#00846e",
                        "on-secondary-fixed": "#001d31",
                        "on-surface-variant": "#3d4945",
                        "surface-bright": "#faf8ff",
                        "inverse-primary": "#5ddbbe",
                        "tertiary-fixed-dim": "#ffb95f",
                        "on-surface": "#131b2e",
                        "on-tertiary-fixed-variant": "#653e00",
                        "on-secondary-container": "#00476e",
                        "secondary-fixed-dim": "#93ccff",
                        "on-primary-fixed": "#00201a",
                        "on-tertiary": "#ffffff",
                        "surface-variant": "#dae2fd",
                        "error": "#ba1a1a",
                        "inverse-surface": "#283044",
                        "secondary": "#006398",
                        "secondary-fixed": "#cce5ff",
                        "on-tertiary-fixed": "#2a1700",
                        "surface-tint": "#006b59",
                        "on-primary-fixed-variant": "#005143",
                        "inverse-on-surface": "#eef0ff",
                        "primary-fixed": "#7cf8da",
                        "surface-container-lowest": "#ffffff",
                        "surface-dim": "#d2d9f4",
                        "tertiary": "#825100",
                        "primary-fixed-dim": "#5ddbbe",
                        "on-tertiary-container": "#fffbff",
                        "secondary-container": "#5bb8fe",
                        "on-error-container": "#93000a",
                        "outline": "#6d7a75",
                        "surface-container-highest": "#dae2fd",
                        "on-error": "#ffffff",
                        "on-background": "#131b2e",
                        "surface-container-high": "#e2e7ff",
                        "outline-variant": "#bccac4",
                        "on-secondary": "#ffffff",
                        "background": "#faf8ff",
                        "tertiary-fixed": "#ffddb8",
                        "on-secondary-fixed-variant": "#004b73",
                        "surface-container": "#eaedff",
                        "primary": "#006857",
                        "error-container": "#ffdad6",
                        "on-primary-container": "#f4fffa",
                        "on-primary": "#ffffff"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        "margin-mobile": "1rem",
                        "space-lg": "1.5rem",
                        "space-xl": "2rem",
                        "gutter-desktop": "1.5rem",
                        "margin-tablet": "1.5rem",
                        "sidebar-collapsed": "80px",
                        "margin-desktop": "2rem",
                        "space-md": "1rem",
                        "space-xs": "0.5rem",
                        "sidebar-width": "280px",
                        "space-2xs": "0.25rem",
                        "space-sm": "0.75rem",
                        "gutter-mobile": "1rem",
                        "space-2xl": "2.5rem",
                        "container-max": "1440px",
                        "space-3xl": "3rem"
                    },
                    fontFamily: {
                        "body-lg": ["Plus Jakarta Sans"],
                        "label-md": ["Plus Jakarta Sans"],
                        "headline-lg-mobile": ["Plus Jakarta Sans"],
                        "body-sm": ["Plus Jakarta Sans"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "stat-counter": ["Plus Jakarta Sans"],
                        "label-sm": ["Plus Jakarta Sans"],
                        "label-lg": ["Plus Jakarta Sans"],
                        "headline-xl-mobile": ["Plus Jakarta Sans"],
                        "headline-xl": ["Plus Jakarta Sans"],
                        "headline-sm": ["Plus Jakarta Sans"],
                        "body-md": ["Plus Jakarta Sans"],
                        "headline-md": ["Plus Jakarta Sans"]
                    },
                    fontSize: {
                        "body-lg": ["16px", {
                            lineHeight: "26px",
                            letterSpacing: "0em",
                            fontWeight: "400"
                        }],
                        "label-md": ["12px", {
                            lineHeight: "16px",
                            letterSpacing: "0.02em",
                            fontWeight: "600"
                        }],
                        "headline-lg-mobile": ["22px", {
                            lineHeight: "30px",
                            letterSpacing: "-0.015em",
                            fontWeight: "700"
                        }],
                        "body-sm": ["12px", {
                            lineHeight: "18px",
                            letterSpacing: "0.01em",
                            fontWeight: "400"
                        }],
                        "headline-lg": ["28px", {
                            lineHeight: "36px",
                            letterSpacing: "-0.02em",
                            fontWeight: "700"
                        }],
                        "stat-counter": ["32px", {
                            lineHeight: "38px",
                            letterSpacing: "-0.02em",
                            fontWeight: "700"
                        }],
                        "label-sm": ["11px", {
                            lineHeight: "14px",
                            letterSpacing: "0.04em",
                            fontWeight: "700"
                        }],
                        "label-lg": ["14px", {
                            lineHeight: "20px",
                            letterSpacing: "0.01em",
                            fontWeight: "600"
                        }],
                        "headline-xl-mobile": ["28px", {
                            lineHeight: "36px",
                            letterSpacing: "-0.02em",
                            fontWeight: "700"
                        }],
                        "headline-xl": ["36px", {
                            lineHeight: "44px",
                            letterSpacing: "-0.025em",
                            fontWeight: "700"
                        }],
                        "headline-sm": ["16px", {
                            lineHeight: "24px",
                            letterSpacing: "0em",
                            fontWeight: "600"
                        }],
                        "body-md": ["14px", {
                            lineHeight: "22px",
                            letterSpacing: "0em",
                            fontWeight: "400"
                        }],
                        "headline-md": ["20px", {
                            lineHeight: "28px",
                            letterSpacing: "-0.01em",
                            fontWeight: "600"
                        }]
                    }
                }
            }
        };
    </script>
    @yield('extra-styles')
</head>

<body
    class="bg-background font-body-md text-body-md text-on-surface antialiased selection:bg-primary-fixed selection:text-on-primary-fixed">
    <div class="sidebar-backdrop fixed inset-0 bg-inverse-surface/40 z-40" id="sidebarBackdrop"></div>

    @php
        $authUser = auth()->user();
        $isAdmin = $authUser?->role === 'admin';
        $userName = $authUser?->name ?? 'Pengguna';

        $userInitials = \Illuminate\Support\Str::of($userName)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))
            ->implode('');

        $ticketGroupOpen = request()->routeIs('tickets', 'ticket.*');

        $adminMenu = [
            ['admin.dashboard', 'Dashboard', 'grid_view'],
            ['admin.daftar-user', 'User', 'group'],
            ['admin.absensi', 'Absensi', 'co_present'],
            ['admin.tickets', 'Ticket', 'confirmation_number'],
            ['admin.daily-activity.index', 'Daily Activity', 'event_note'],
            ['admin.laporan', 'Laporan', 'assessment'],
            ['admin.notifikasi', 'Notifikasi', 'notifications', 3],
            ['admin.profil', 'Profil', 'manage_accounts'],
        ];

        $navBase =
            'flex items-center justify-between px-space-sm py-space-sm rounded-lg font-label-lg text-label-lg transition-colors';
        $navActive = 'bg-primary-container text-on-primary-container font-semibold shadow-sm';
        $navIdle = 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface';
        $profileRoute = $isAdmin ? 'admin.profil' : 'profil';
        $profileUrl = \Illuminate\Support\Facades\Route::has($profileRoute) ? route($profileRoute) : null;
    @endphp

    <aside
        class="app-sidebar fixed left-0 top-0 h-screen w-sidebar-width bg-surface-container-low shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between overflow-y-auto"
        id="appSidebar">
        <div class="flex flex-col">
            <div class="h-16 px-space-md flex items-center gap-space-sm bg-surface-container-low">
                <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-primary-fixed shrink-0">
                    <span class="material-symbols-outlined text-xl"
                        style="font-variation-settings: 'FILL' 1;">bolt</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary leading-tight font-bold">
                        SIM-PKL
                    </span>

                    <span class="font-label-sm text-label-sm text-on-surface-variant tracking-wider uppercase">
                        PLN Icon Plus
                    </span>
                </div>
            </div>
            <div class="px-space-md pt-space-md pb-space-xs">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-outline">
                    {{ $isAdmin ? 'Navigasi Utama' : 'Daftar Menu' }}
                </span>
            </div>
            <nav class="flex flex-col gap-space-xs px-space-xs">
                @if ($isAdmin)
                    @foreach ($adminMenu as $m)
                        @php
                            $active = request()->routeIs(\Illuminate\Support\Str::beforeLast($m[0], '.index') . '*');
                            $href = \Illuminate\Support\Facades\Route::has($m[0]) ? route($m[0]) : '#';
                        @endphp
                        <a href="{{ $href }}" class="{{ $navBase }} {{ $active ? $navActive : $navIdle }}"
                            @if ($active) aria-current="page" @endif>

                            <div class="flex items-center gap-space-sm">
                                <span class="material-symbols-outlined text-[20px]">
                                    {{ $m[2] }}
                                </span>

                                <span>{{ $m[1] }}</span>
                            </div>
                            @isset($m[3])
                                <span
                                    class="px-space-xs py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-bold">
                                    {{ $m[3] }}
                                </span>
                            @endisset
                        </a>
                    @endforeach
                @else
                    <a class="{{ $navBase }} {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}"
                        href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>

                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">
                                grid_view
                            </span>

                            <span>Dashboard</span>
                        </div>
                    </a>
                    <a class="{{ $navBase }} {{ request()->routeIs('absensi') ? $navActive : $navIdle }}"
                        href="{{ route('absensi') }}" @if (request()->routeIs('absensi')) aria-current="page" @endif>

                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">
                                schedule
                            </span>
                            <span>Absensi</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                    </a>
                    <div class="flex flex-col gap-space-2xs">
                        <button type="button" data-toggle="submenu" aria-controls="submenuTicket"
                            aria-expanded="{{ $ticketGroupOpen ? 'true' : 'false' }}"
                            class="w-full {{ $navBase }} {{ $ticketGroupOpen ? 'text-on-surface font-semibold' : $navIdle }}">

                            <div class="flex items-center gap-space-sm">
                                <span class="material-symbols-outlined text-[20px]">
                                    confirmation_number
                                </span>
                                <span>Tiket Penugasan</span>
                            </div>
                            <span data-chevron
                                class="material-symbols-outlined text-[18px] transition-transform duration-150 {{ $ticketGroupOpen ? 'rotate-180' : '' }}">
                                expand_more
                            </span>
                        </button>
                        <div id="submenuTicket"
                            class="pl-space-md flex flex-col gap-space-2xs {{ $ticketGroupOpen ? '' : 'hidden' }}">

                            <a href="{{ route('tickets') }}"
                                class="flex items-center px-space-sm py-space-xs rounded-lg font-label-md text-label-md transition-colors {{ request()->routeIs('tickets') ? $navActive : $navIdle }}"
                                @if (request()->routeIs('tickets')) aria-current="page" @endif>
                                Daftar &amp; Detail Tiket
                            </a>

                            <a href="{{ route('ticket.create') }}"
                                class="flex items-center px-space-sm py-space-xs rounded-lg font-label-md text-label-md transition-colors {{ request()->routeIs('ticket.create') ? $navActive : $navIdle }}"
                                @if (request()->routeIs('ticket.create')) aria-current="page" @endif>
                                Buat Tiket Baru
                            </a>
                        </div>
                    </div>
                    <a class="{{ $navBase }} {{ $navIdle }}" href="#">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">
                                assignment
                            </span>

                            <span>route'admin.daily-activity.</span>
                        </div>
                    </a>
                    <a class="{{ $navBase }} {{ $navIdle }}" href="#">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">
                                notifications
                            </span>

                            <span>Notifikasi</span>
                        </div>
                        <span
                            class="px-space-xs py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-bold">
                            3
                        </span>
                    </a>
                    <a class="{{ $navBase }} {{ $navIdle }}" href="{{ $profileUrl ?? '#' }}">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">
                                person
                            </span>
                            <span>Profil</span>
                        </div>
                    </a>
                @endif
            </nav>
        </div>
    </aside>
    <div class="app-main-area pl-sidebar-width min-h-screen flex flex-col">

        <header
            class="app-header fixed top-0 left-sidebar-width right-0 h-16 bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-30 flex items-center justify-between px-space-xl">
            <div class="flex items-center gap-space-sm">
                <button class="mobile-menu-btn hidden p-1 text-on-surface-variant" id="mobileMenuBtn" type="button"
                    aria-label="Buka menu">
                    <span class="material-symbols-outlined text-2xl">
                        menu
                    </span>
                </button>
                <span class="font-label-md text-label-md text-on-surface-variant">
                    @yield('breadcrumb')
                </span>
            </div>

            <div class="flex items-center gap-space-md">
                <div
                    class="hidden md:flex items-center gap-space-2xs text-on-surface-variant font-label-md text-label-md">
                    <span class="material-symbols-outlined text-[16px]">
                        calendar_today
                    </span>
                    <span>
                        {{ $today ?? now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
                <div class="relative" id="profileMenu">
                    <button type="button" id="profileBtn" aria-haspopup="true" aria-expanded="false"
                        aria-controls="profileDropdown" aria-label="Menu profil"
                        class="flex items-center gap-space-xs rounded-full pl-1 pr-space-xs py-1 hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/40 transition-colors">
                        <span class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span class="font-label-md text-label-md text-on-primary font-bold">
                                {{ $userInitials }}
                            </span>
                        </span>
                        <span
                            class="hidden md:inline max-w-[140px] truncate font-label-md text-label-md text-on-surface font-semibold">
                            {{ $userName }}
                        </span>
                        <span
                            class="material-symbols-outlined text-[18px] text-on-surface-variant transition-transform duration-150"
                            id="profileChevron">
                            expand_more
                        </span>
                    </button>
                    <div id="profileDropdown" role="menu"
                        class="hidden absolute right-0 mt-2 w-64 rounded-xl bg-surface-container-lowest shadow-lg overflow-hidden">
                        <div class="px-space-md py-space-sm bg-surface-container-low">
                            <div class="font-label-lg text-label-lg font-semibold text-on-surface truncate">
                                {{ $userName }}
                            </div>
                            <div class="font-body-sm text-body-sm text-on-surface-variant truncate">
                                {{ $authUser?->email }}
                            </div>
                            <span
                                class="inline-block mt-1 px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">
                                {{ $isAdmin ? 'Administrator' : 'Peserta PKL' }}
                            </span>
                        </div>
                        <div class="p-space-xs flex flex-col">

                            @if ($profileUrl)
                                <a href="{{ $profileUrl }}" role="menuitem"
                                    class="flex items-center gap-space-sm px-space-sm py-space-xs rounded-lg font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">
                                        person
                                    </span>
                                    <span>Profil Saya</span>
                                </a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                @csrf
                                <button type="submit" role="menuitem"
                                    class="w-full flex items-center gap-space-sm px-space-sm py-space-xs rounded-lg font-label-lg text-label-lg text-error hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">
                                        logout
                                    </span>
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="w-full pt-16 bg-surface flex-1 px-space-xl py-space-xl">
            @yield('content')
        </main>
    </div>
    <div id="logoutModal"
        class="hidden fixed inset-0 z-[60] flex items-center justify-center p-space-md bg-inverse-surface/40"
        role="status" aria-live="polite">
        <div id="logoutCard"
            class="relative w-full max-w-sm overflow-hidden rounded-2xl bg-surface-container-lowest shadow-2xl text-center">
            <div class="h-1.5 bg-gradient-to-r from-primary via-primary-container to-secondary-container"></div>
            <div class="px-space-xl pt-space-xl pb-space-lg flex flex-col items-center">
                <div class="relative w-20 h-20 mb-space-md flex items-center justify-center">
                    <span class="lo-halo absolute inset-0 rounded-full bg-primary/20"></span>
                    <div class="absolute inset-0 rounded-full bg-primary/10"></div>
                    <svg class="relative w-12 h-12 text-primary" viewBox="0 0 52 52" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle class="lo-ring" cx="26" cy="26" r="24" pathLength="1"></circle>
                        <path class="lo-check" d="M15 27 l8 8 l15 -17" pathLength="1"></path>
                    </svg>
                </div>
                <p class="lo-up font-headline-md text-headline-md font-bold text-on-surface"
                    style="animation-delay:.18s">
                    Berhasil Keluar!
                </p>
                <p class="lo-up font-body-md text-body-md text-on-surface-variant mt-1" style="animation-delay:.22s">
                    Mengalihkan ke halaman login...
                </p>
            </div>
            <div class="h-1 bg-surface-container">
                <div id="logoutBar" class="h-full bg-primary"></div>
            </div>
        </div>
    </div>
    <div id="popupModal"
        class="pp-modal hidden fixed inset-0 z-[70] flex items-center justify-center p-space-md bg-inverse-surface/40"
        role="status" aria-live="polite">
        <div
            class="pp-card relative w-full max-w-sm overflow-hidden rounded-2xl bg-surface-container-lowest shadow-2xl text-center">
            <div class="h-1.5 bg-gradient-to-r from-primary via-primary-container to-secondary-container"></div>
            <div class="px-space-xl pt-space-xl pb-space-lg flex flex-col items-center">
                <div class="relative w-20 h-20 mb-space-md flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-primary/10"></div>
                    <svg class="relative w-12 h-12 text-primary" viewBox="0 0 52 52" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle class="pp-ring" cx="26" cy="26" r="24" pathLength="1"></circle>
                        <path class="pp-check" d="M15 27 l8 8 l15 -17" pathLength="1"></path>
                    </svg>
                </div>
                <p id="popupTitle" class="font-headline-md text-headline-md font-bold text-on-surface">
                    Berhasil!
                </p>
                <p id="popupMessage" class="font-body-md text-body-md text-on-surface-variant mt-1">
                </p>
                <div id="popupCred"
                    class="hidden w-full mt-space-md p-space-md rounded-lg bg-secondary-fixed text-on-secondary-fixed text-left">
                    <p class="font-label-md text-label-md uppercase tracking-wider opacity-70">
                        Email Login
                    </p>
                    <p id="popupCredEmail" class="font-label-lg text-label-lg font-bold break-all">
                    </p>
                    <p class="font-label-md text-label-md uppercase tracking-wider opacity-70 mt-space-sm">
                        Password
                    </p>
                    <p id="popupCredPass" class="font-label-lg text-label-lg font-bold break-all">
                    </p>
                    <p class="font-body-sm text-body-sm mt-space-sm">
                        Simpan informasi ini, password hanya ditampilkan sekali.
                    </p>
                    <div class="flex gap-space-sm mt-space-md">
                        <button type="button" id="popupCopy"
                            class="flex-1 px-space-md py-2 rounded-lg bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-colors">
                            Salin
                        </button>
                        <button type="button" id="popupClose"
                            class="flex-1 px-space-md py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
            <div id="popupBarWrap" class="h-1 bg-surface-container">
                <div class="pp-bar h-full bg-primary"></div>
            </div>
        </div>
    </div>

    <div id="confirmModal"
        class="pp-modal hidden fixed inset-0 z-[70] flex items-center justify-center p-space-md bg-inverse-surface/40"
        role="alertdialog" aria-modal="true">
        <div
            class="pp-card relative w-full max-w-sm overflow-hidden rounded-2xl bg-surface-container-lowest shadow-2xl text-center">
            <div class="h-1.5 bg-error"></div>
            <div class="px-space-xl pt-space-xl pb-space-lg flex flex-col items-center">
                <div class="relative w-20 h-20 mb-space-md flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full bg-error/10"></div>
                    <sVg class="relative w-12 h-12 text-error" viewBox="0 0 52 52" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle class="pp-ring" cx="26" cy="26" r="24" pathLength="1"></circle>
                        <path class="pp-mark" d="M26 15 v14 M26 36 v.5" pathLength="1"></path>
                    </svg>
                </div>
                <p class="font-headline-md text-headline-md font-bold text-on-surface">
                    Hapus User?
                </p>
                <p id="confirmMessage" class="font-body-md text-body-md text-on-surface-variant mt-1">
                </p>
                <div class="flex gap-space-sm w-full mt-space-lg">
                    <button type="button" id="confirmCancel"
                        class="flex-1 px-space-md py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg transition-colors">
                        Batal
                    </button>
                    <button type="button" id="confirmOk"
                        class="flex-1 px-space-md py-2.5 rounded-lg bg-error hover:opacity-90 text-on-error font-label-lg text-label-lg transition-opacity">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const menuBtn = document.getElementById('mobileMenuBtn');
            const profileMenu = document.getElementById('profileMenu');
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            const profileChevron = document.getElementById('profileChevron');
            const logoutForm = document.getElementById('logoutForm');
            const logoutModal = document.getElementById('logoutModal');
            const reduceMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;
            let loggingOut = false;
            function toggleSidebar() {
                sidebar?.classList.toggle('is-open');
                backdrop?.classList.toggle('is-open');
            }
            menuBtn?.addEventListener('click', toggleSidebar);
            backdrop?.addEventListener('click', toggleSidebar);
            document.querySelectorAll('[data-toggle="submenu"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const menu = document.getElementById(
                        btn.getAttribute('aria-controls')
                    );
                    if (!menu) return;
                    const isOpen = !menu.classList.toggle('hidden');
                    btn.setAttribute('aria-expanded', isOpen);
                    btn.querySelector('[data-chevron]')
                        ?.classList.toggle('rotate-180', isOpen);
                });
            });
            function setProfileMenu(open) {
                if (!profileDropdown || !profileBtn) return;
                profileDropdown.classList.toggle('hidden', !open);
                profileBtn.setAttribute('aria-expanded', open);
                profileChevron?.classList.toggle('rotate-180', open);
            }
            profileBtn?.addEventListener('click', e => {
                e.stopPropagation();

                setProfileMenu(
                    profileDropdown?.classList.contains('hidden')
                );
            });
            document.addEventListener('click', e => {
                if (profileMenu && !profileMenu.contains(e.target)) {
                    setProfileMenu(false);
                }
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    setProfileMenu(false);
                }
            });
            function showLogoutModal(redirectUrl) {
                if (!logoutModal) {
                    window.location.href = redirectUrl;
                    return;
                }
                logoutModal.classList.remove('hidden', 'is-leaving');
                logoutModal.classList.add('is-open');
                const closeDelay = reduceMotion ? 50 : 500;
                const redirectDelay = reduceMotion ? 100 : 560;
                setTimeout(() => {
                    logoutModal.classList.remove('is-open');
                    logoutModal.classList.add('is-leaving');
                }, closeDelay);

                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, redirectDelay);
            }
            logoutForm?.addEventListener('submit', async e => {
                e.preventDefault();
                if (loggingOut) return;
                loggingOut = true;
                setProfileMenu(false);
                const loginUrl = @json(route('login'));
                try {
                    const res = await fetch(logoutForm.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content
                        },
                        body: new FormData(logoutForm)
                    });
                    if (
                        res.ok ||
                        res.status === 419 ||
                        res.status === 401
                    ) {
                        const data = await res.json().catch(() => ({}));
                        showLogoutModal(
                            data.redirect || loginUrl
                        );
                        return;
                    }
                } catch (error) {
                    loggingOut = false;
                }
                loggingOut = false;
                logoutForm.submit();
            });

            window.addEventListener('pageshow', e => {
                if (e.persisted) {
                    window.location.reload();
                }
            });
        })();
    </script>

    <script>
        (() => {
            const reduceMotion = window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches;
            const popup = document.getElementById('popupModal');
            const confirmModal = document.getElementById('confirmModal');
            const credBox = document.getElementById('popupCred');
            const barWrap = document.getElementById('popupBarWrap');
            let popupTimer = null;
            let pendingForm = null;
            function openModal(el) {
                if (!el) return;
                clearTimeout(el._closeTimer);

                el.classList.remove(
                    'hidden',
                    'is-leaving'
                );
                el.classList.add('is-open');
            }
            function closeModal(el, after = null) {
                if (!el || el.classList.contains('hidden')) {
                    if (typeof after === 'function') {
                        after();
                    }
                    return;
                }
                clearTimeout(el._closeTimer);
                el.classList.remove('is-open');
                el.classList.add('is-leaving');
                el._closeTimer = setTimeout(() => {
                    el.classList.add('hidden');
                    el.classList.remove('is-leaving');

                    if (typeof after === 'function') {
                        after();
                    }
                }, reduceMotion ? 20 : 100);
            }
            window.showSuccessPopup = function(message, opts = {}) {
                const {
                    title = 'Berhasil!',
                        duration = 600,
                        credentials = null
                } = opts;
                if (!popup) return;
                const titleEl =
                    document.getElementById('popupTitle');
                const messageEl =
                    document.getElementById('popupMessage');
                const emailEl =
                    document.getElementById('popupCredEmail');
                const passEl =
                    document.getElementById('popupCredPass');
                if (titleEl) {
                    titleEl.textContent = title;
                }
                if (messageEl) {
                    messageEl.textContent = message || '';
                }
                clearTimeout(popupTimer);
                popup.style.setProperty(
                    '--pp-duration',
                    `${duration}ms`
                );
                if (credentials) {
                    if (emailEl) {
                        emailEl.textContent =
                            credentials.email_login || '';
                    }
                    if (passEl) {
                        passEl.textContent =
                            credentials.password || '';
                    }
                    credBox?.classList.remove('hidden');
                    barWrap?.classList.add('hidden');
                } else {
                    credBox?.classList.add('hidden');
                    barWrap?.classList.remove('hidden');

                    popupTimer = setTimeout(() => {
                        window.hideSuccessPopup();
                    }, reduceMotion ? 300 : duration);
                }
                openModal(popup);
            };
            window.hideSuccessPopup = function() {
                clearTimeout(popupTimer);

                closeModal(popup);
            };
            popup?.addEventListener('click', e => {
                if (
                    e.target === popup &&
                    credBox?.classList.contains('hidden')
                ) {
                    window.hideSuccessPopup();
                }
            });
            document.getElementById('popupClose')
                ?.addEventListener('click', () => {
                    window.hideSuccessPopup();
                });
            document.getElementById('popupCopy')
                ?.addEventListener('click', async e => {
                    const email =
                        document.getElementById(
                            'popupCredEmail'
                        )?.textContent || '';
                    const password =
                        document.getElementById(
                            'popupCredPass'
                        )?.textContent || '';
                    const text =
                        `Email Login: ${email}\nPassword: ${password}`;
                    const btn = e.currentTarget;
                    try {
                        await navigator.clipboard.writeText(text);
                        btn.textContent = 'Tersalin!';
                    } catch (error) {
                        btn.textContent = 'Gagal menyalin';
                    }
                    setTimeout(() => {
                        btn.textContent = 'Salin';
                    }, 900);
                });
            document.addEventListener('submit', e => {
                const form =
                    e.target.closest?.('form.form-hapus');
                if (!form) return;
                if (form.dataset.confirmed === '1') {
                    return;
                }
                e.preventDefault();
                pendingForm = form;
                const nama = form.dataset.nama;
                const messageEl =
                    document.getElementById('confirmMessage');
                if (messageEl) {
                    messageEl.textContent = nama ?
                        `User "${nama}" akan dihapus permanen dan tidak bisa dikembalikan.` :
                        'Data yang dihapus tidak dapat dikembalikan.';
                }
                openModal(confirmModal);
            });

            function cancelConfirm() {
                pendingForm = null;
                closeModal(confirmModal);
            }
            document.getElementById('confirmCancel')
                ?.addEventListener(
                    'click',
                    cancelConfirm
                );
            confirmModal?.addEventListener('click', e => {
                if (e.target === confirmModal) {
                    cancelConfirm();
                }
            });
            document.getElementById('confirmOk')
                ?.addEventListener('click', () => {
                    if (!pendingForm) return;
                    const form = pendingForm;
                    pendingForm = null;
                    form.dataset.confirmed = '1';
                    form.submit();
                });
            document.addEventListener('keydown', e => {
                if (e.key !== 'Escape') return;
                cancelConfirm();
                window.hideSuccessPopup();
            });
            @if (session('success'))
                document.addEventListener('DOMContentLoaded', () => {
                    window.showSuccessPopup(
                        @json(session('success'))
                    );
                });
            @endif
        })();
    </script>
    @yield('extra-scripts')
</body>

</html>
