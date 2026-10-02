<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Portal Magang')</title>
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
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

        @media (max-width: 900px) {
            .app-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
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
                    "colors": {
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
                        "surface": "#faf8ff",
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
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
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
                    "fontFamily": {
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
                    "fontSize": {
                        "body-lg": ["16px", {
                            "lineHeight": "26px",
                            "letterSpacing": "0em",
                            "fontWeight": "400"
                        }],
                        "label-md": ["12px", {
                            "lineHeight": "16px",
                            "letterSpacing": "0.02em",
                            "fontWeight": "600"
                        }],
                        "headline-lg-mobile": ["22px", {
                            "lineHeight": "30px",
                            "letterSpacing": "-0.015em",
                            "fontWeight": "700"
                        }],
                        "body-sm": ["12px", {
                            "lineHeight": "18px",
                            "letterSpacing": "0.01em",
                            "fontWeight": "400"
                        }],
                        "headline-lg": ["28px", {
                            "lineHeight": "36px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "stat-counter": ["32px", {
                            "lineHeight": "38px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "label-sm": ["11px", {
                            "lineHeight": "14px",
                            "letterSpacing": "0.04em",
                            "fontWeight": "700"
                        }],
                        "label-lg": ["14px", {
                            "lineHeight": "20px",
                            "letterSpacing": "0.01em",
                            "fontWeight": "600"
                        }],
                        "headline-xl-mobile": ["28px", {
                            "lineHeight": "36px",
                            "letterSpacing": "-0.02em",
                            "fontWeight": "700"
                        }],
                        "headline-xl": ["36px", {
                            "lineHeight": "44px",
                            "letterSpacing": "-0.025em",
                            "fontWeight": "700"
                        }],
                        "headline-sm": ["16px", {
                            "lineHeight": "24px",
                            "letterSpacing": "0em",
                            "fontWeight": "600"
                        }],
                        "body-md": ["14px", {
                            "lineHeight": "22px",
                            "letterSpacing": "0em",
                            "fontWeight": "400"
                        }],
                        "headline-md": ["20px", {
                            "lineHeight": "28px",
                            "letterSpacing": "-0.01em",
                            "fontWeight": "600"
                        }]
                    }
                },
            },
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
            ['admin.daily-activity', 'Daily Activity', 'event_note'],
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
                    <span class="font-headline-sm text-headline-sm text-primary leading-tight font-bold">SIM-PKL</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant tracking-wider uppercase">PLN Icon
                        Plus</span>
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
                            $active = request()->routeIs($m[0] . '*');
                            $href = \Illuminate\Support\Facades\Route::has($m[0]) ? route($m[0]) : '#';
                        @endphp
                        <a href="{{ $href }}" class="{{ $navBase }} {{ $active ? $navActive : $navIdle }}"
                            @if ($active) aria-current="page" @endif>
                            <div class="flex items-center gap-space-sm">
                                <span class="material-symbols-outlined text-[20px]">{{ $m[2] }}</span>
                                <span>{{ $m[1] }}</span>
                            </div>
                            @isset($m[3])
                                <span
                                    class="px-space-xs py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-bold">{{ $m[3] }}</span>
                            @endisset
                        </a>
                    @endforeach
                @else
                    <a class="{{ $navBase }} {{ request()->routeIs('dashboard') ? $navActive : $navIdle }}"
                        href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">grid_view</span>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <a class="{{ $navBase }} {{ request()->routeIs('absensi') ? $navActive : $navIdle }}"
                        href="{{ route('absensi') }}" @if (request()->routeIs('absensi')) aria-current="page" @endif>
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">schedule</span>
                            <span>Absensi</span>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                    </a>

                    <div class="flex flex-col gap-space-2xs">
                        <button type="button" data-toggle="submenu" aria-controls="submenuTicket"
                            aria-expanded="{{ $ticketGroupOpen ? 'true' : 'false' }}"
                            class="w-full {{ $navBase }} {{ $ticketGroupOpen ? 'text-on-surface font-semibold' : $navIdle }}">
                            <div class="flex items-center gap-space-sm">
                                <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                                <span>Tiket Penugasan</span>
                            </div>
                            <span data-chevron
                                class="material-symbols-outlined text-[18px] transition-transform duration-200 {{ $ticketGroupOpen ? 'rotate-180' : '' }}">expand_more</span>
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
                            <span class="material-symbols-outlined text-[20px]">assignment</span>
                            <span>Daily Activity</span>
                        </div>
                    </a>

                    <a class="{{ $navBase }} {{ $navIdle }}" href="#">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                            <span>Notifikasi</span>
                        </div>
                        <span
                            class="px-space-xs py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-bold">3</span>
                    </a>

                    <a class="{{ $navBase }} {{ $navIdle }}" href="{{ $profileUrl ?? '#' }}">
                        <div class="flex items-center gap-space-sm">
                            <span class="material-symbols-outlined text-[20px]">person</span>
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
                    <span class="material-symbols-outlined text-2xl">menu</span>
                </button>
                <span class="font-label-md text-label-md text-on-surface-variant">@yield('breadcrumb')</span>
            </div>
            <div class="flex items-center gap-space-md">
                <div
                    class="hidden md:flex items-center gap-space-2xs text-on-surface-variant font-label-md text-label-md">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    <span>{{ $today ?? now()->translatedFormat('l, d F Y') }}</span>
                </div>

                {{-- Profil (dropdown) --}}
                <div class="relative" id="profileMenu">
                    <button type="button" id="profileBtn" aria-haspopup="true" aria-expanded="false"
                        aria-controls="profileDropdown" aria-label="Menu profil"
                        class="flex items-center gap-space-xs rounded-full pl-1 pr-space-xs py-1 hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/40 transition-colors">
                        <span class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                            <span
                                class="font-label-md text-label-md text-on-primary font-bold">{{ $userInitials }}</span>
                        </span>
                        <span
                            class="hidden md:inline max-w-[140px] truncate font-label-md text-label-md text-on-surface font-semibold">{{ $userName }}</span>
                        <span
                            class="material-symbols-outlined text-[18px] text-on-surface-variant transition-transform duration-200"
                            id="profileChevron">expand_more</span>
                    </button>

                    <div id="profileDropdown" role="menu"
                        class="hidden absolute right-0 mt-2 w-64 rounded-xl bg-surface-container-lowest shadow-lg overflow-hidden">
                        <div class="px-space-md py-space-sm bg-surface-container-low">
                            <div class="font-label-lg text-label-lg font-semibold text-on-surface truncate">
                                {{ $userName }}</div>
                            <div class="font-body-sm text-body-sm text-on-surface-variant truncate">
                                {{ $authUser?->email }}</div>
                            <span
                                class="inline-block mt-1 px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">
                                {{ $isAdmin ? 'Administrator' : 'Peserta PKL' }}
                            </span>
                        </div>

                        <div class="p-space-xs flex flex-col">
                            @if ($profileUrl)
                                <a href="{{ $profileUrl }}" role="menuitem"
                                    class="flex items-center gap-space-sm px-space-sm py-space-xs rounded-lg font-label-lg text-label-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">person</span>
                                    <span>Profil Saya</span>
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" role="menuitem"
                                    class="w-full flex items-center gap-space-sm px-space-sm py-space-xs rounded-lg font-label-lg text-label-lg text-error hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">logout</span>
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

    <script>
        (function() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const menuBtn = document.getElementById('mobileMenuBtn');

            function toggleSidebar() {
                sidebar.classList.toggle('is-open');
                backdrop.classList.toggle('is-open');
            }

            menuBtn?.addEventListener('click', toggleSidebar);
            backdrop?.addEventListener('click', toggleSidebar);

            document.querySelectorAll('[data-toggle="submenu"]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const menu = document.getElementById(btn.getAttribute('aria-controls'));
                    const isOpen = !menu.classList.toggle('hidden');
                    btn.setAttribute('aria-expanded', isOpen);
                    btn.querySelector('[data-chevron]').classList.toggle('rotate-180', isOpen);
                });
            });
            const profileMenu = document.getElementById('profileMenu');
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            const profileChevron = document.getElementById('profileChevron');

            function setProfileMenu(open) {
                profileDropdown.classList.toggle('hidden', !open);
                profileBtn.setAttribute('aria-expanded', open);
                profileChevron.classList.toggle('rotate-180', open);
            }

            profileBtn?.addEventListener('click', (e) => {
                e.stopPropagation();
                setProfileMenu(profileDropdown.classList.contains('hidden'));
            });

            document.addEventListener('click', (e) => {
                if (!profileMenu.contains(e.target)) setProfileMenu(false);
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') setProfileMenu(false);
            });
        })();
    </script>
    @yield('extra-scripts')
</body>

</html>