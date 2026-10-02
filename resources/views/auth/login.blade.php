<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Manajemen PKL PLN Icon Plus</title>
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

        /* ===== Pop up login berhasil ===== */
        #successModal.is-open { animation: ls-backdrop-in .25s ease-out both; }
        #successModal.is-open #successCard { animation: ls-card-in .45s cubic-bezier(.34, 1.4, .64, 1) both; }
        #successModal.is-leaving { animation: ls-backdrop-out .25s ease-in both; }
        #successModal.is-leaving #successCard { animation: ls-card-out .25s ease-in both; }

        @keyframes ls-backdrop-in { from { opacity: 0; } to { opacity: 1; } }
        @keyframes ls-backdrop-out { from { opacity: 1; } to { opacity: 0; } }
        @keyframes ls-card-in {
            from { opacity: 0; transform: translateY(24px) scale(.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes ls-card-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(8px) scale(.97); }
        }

        /* Lingkaran & centang tergambar, halo berdenyut sekali */
        .ls-ring, .ls-check { stroke-dasharray: 1; stroke-dashoffset: 1; }
        #successModal.is-open .ls-ring { animation: ls-draw .55s ease-out .25s forwards; }
        #successModal.is-open .ls-check { animation: ls-draw .35s ease-out .65s forwards; }
        @keyframes ls-draw { to { stroke-dashoffset: 0; } }

        .ls-halo { opacity: 0; }
        #successModal.is-open .ls-halo { animation: ls-halo 1.1s ease-out .6s both; }
        @keyframes ls-halo {
            from { transform: scale(.8); opacity: .5; }
            to { transform: scale(1.6); opacity: 0; }
        }

        /* Teks naik halus & progress bar */
        .ls-up { opacity: 0; }
        #successModal.is-open .ls-up { animation: ls-up .4s ease-out forwards; }
        @keyframes ls-up {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #successBar { width: 0; }
        #successModal.is-open #successBar { animation: ls-bar 1.15s cubic-bezier(.4, 0, .2, 1) .3s forwards; }
        @keyframes ls-bar { to { width: 100%; } }

        /* Hormati pengguna yang mematikan animasi di perangkatnya */
        @media (prefers-reduced-motion: reduce) {
            #successModal, #successModal * {
                animation-duration: .01ms !important;
                animation-delay: 0s !important;
                animation-iteration-count: 1 !important;
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
                        "body-lg": ["16px", { "lineHeight": "26px", "letterSpacing": "0em", "fontWeight": "400" }],
                        "label-md": ["12px", { "lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "600" }],
                        "headline-lg-mobile": ["22px", { "lineHeight": "30px", "letterSpacing": "-0.015em", "fontWeight": "700" }],
                        "body-sm": ["12px", { "lineHeight": "18px", "letterSpacing": "0.01em", "fontWeight": "400" }],
                        "headline-lg": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "stat-counter": ["32px", { "lineHeight": "38px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-sm": ["11px", { "lineHeight": "14px", "letterSpacing": "0.04em", "fontWeight": "700" }],
                        "label-lg": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "600" }],
                        "headline-xl-mobile": ["28px", { "lineHeight": "36px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-xl": ["36px", { "lineHeight": "44px", "letterSpacing": "-0.025em", "fontWeight": "700" }],
                        "headline-sm": ["16px", { "lineHeight": "24px", "letterSpacing": "0em", "fontWeight": "600" }],
                        "body-md": ["14px", { "lineHeight": "22px", "letterSpacing": "0em", "fontWeight": "400" }],
                        "headline-md": ["20px", { "lineHeight": "28px", "letterSpacing": "-0.01em", "fontWeight": "600" }]
                    }
                },
            },
        };
    </script>
</head>

<body class="bg-background font-body-md text-body-md text-on-surface antialiased">
    <main class="w-full min-h-screen flex items-center justify-center p-space-md bg-background">
        <div class="flex flex-col w-full">
            <div id="loginCard"
                class="w-full max-w-6xl mx-auto min-h-[640px] rounded-2xl shadow-xl overflow-hidden bg-surface-container-lowest flex flex-col lg:flex-row my-auto text-[0.92rem]">

                <!-- LEFT PANEL — Brand / marketing side -->
                <div
                    class="lg:w-[55%] relative flex flex-col justify-between p-space-lg lg:p-space-2xl overflow-hidden bg-gradient-to-br from-primary via-primary-container to-surface-tint text-on-primary">
                    <div
                        class="absolute -top-24 -left-24 w-96 h-96 bg-primary-fixed/20 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div
                        class="absolute bottom-10 right-0 w-[500px] h-[500px] bg-secondary/25 rounded-full blur-3xl pointer-events-none">
                    </div>
                    <div class="absolute inset-0 opacity-10 pointer-events-none">
                        <svg class="w-full h-full" height="100%" width="100%" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern height="40" id="grid-pattern" patternUnits="userSpaceOnUse" width="40">
                                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1">
                                    </path>
                                </pattern>
                            </defs>
                            <rect fill="url(#grid-pattern)" height="100%" width="100%"></rect>
                        </svg>
                    </div>

                    <div class="relative z-10 space-y-space-md">
                        <div class="flex items-center justify-between">
                            <div
                                class="flex items-center space-x-space-sm bg-surface-container-lowest/95 backdrop-blur-md px-4 py-2.5 rounded-xl shadow-md">
                                <div
                                    class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center text-primary-fixed shadow-sm">
                                    <span class="material-symbols-outlined text-2xl"
                                        style="font-variation-settings: 'FILL' 1;">bolt</span>
                                </div>
                                <div class="flex flex-col">
                                    <span
                                        class="font-headline-sm text-headline-sm font-bold text-on-surface leading-none tracking-tight">
                                        PLN <span class="text-primary font-bold">Icon Plus</span>
                                    </span>
                                    <span
                                        class="font-label-sm text-label-sm uppercase tracking-widest text-outline font-semibold mt-0.5">
                                        PKL MANAGEMENT
                                    </span>
                                </div>
                            </div>
                            <div
                                class="hidden sm:inline-flex items-center space-x-1.5 px-3 py-1 bg-surface-container-lowest/15 backdrop-blur-sm rounded-full text-on-primary font-label-md text-label-md">
                                <span class="w-2 h-2 rounded-full bg-primary-fixed animate-ping"></span>
                                <span>Tahun Akademik 2026/2027</span>
                            </div>
                        </div>
                        <div
                            class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-primary-fixed/20 text-on-primary font-label-md text-label-md backdrop-blur-md w-fit">
                            <span class="material-symbols-outlined text-sm text-primary-fixed"
                                style="font-variation-settings: 'FILL' 1;">verified</span>
                            <span>Portal Resmi Peserta PKL &amp; Mahasiswa Magang</span>
                        </div>
                    </div>

                    <div class="relative z-10 my-auto py-space-xl space-y-space-lg">
                        <div class="space-y-space-sm">
                            <h1
                                class="font-headline-xl text-headline-xl font-bold text-on-primary tracking-tight leading-tight">
                                Mulai Pengalaman Magang Profesional di PLN Icon Plus
                            </h1>
                            <p class="font-body-lg text-body-lg text-on-primary/85 max-w-xl">
                                Kelola absensi berbasis geofencing GPS, penugasan tiket operasional lapangan, hingga
                                logbook aktivitas harian dalam satu sistem terintegrasi.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm pt-space-xs">
                            <div
                                class="p-space-md rounded-xl bg-surface-container-lowest/10 backdrop-blur-md hover:bg-surface-container-lowest/15 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-surface-container-lowest/20 flex items-center justify-center mb-space-xs text-primary-fixed">
                                    <span class="material-symbols-outlined text-lg">near_me</span>
                                </div>
                                <div class="font-headline-sm text-headline-sm font-semibold text-on-primary">Geofencing
                                    Presisi</div>
                                <div class="font-body-sm text-body-sm text-on-primary/75 mt-1">Radius 100m Kantor Jember
                                </div>
                            </div>
                            <div
                                class="p-space-md rounded-xl bg-surface-container-lowest/10 backdrop-blur-md hover:bg-surface-container-lowest/15 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-surface-container-lowest/20 flex items-center justify-center mb-space-xs text-secondary-fixed">
                                    <span class="material-symbols-outlined text-lg">receipt_long</span>
                                </div>
                                <div class="font-headline-sm text-headline-sm font-semibold text-on-primary">Pelaporan
                                    Tiket</div>
                                <div class="font-body-sm text-body-sm text-on-primary/75 mt-1">Otomatis ke Daily
                                    Activity</div>
                            </div>
                            <div
                                class="p-space-md rounded-xl bg-surface-container-lowest/10 backdrop-blur-md hover:bg-surface-container-lowest/15 transition-all">
                                <div
                                    class="w-8 h-8 rounded-lg bg-surface-container-lowest/20 flex items-center justify-center mb-space-xs text-tertiary-fixed">
                                    <span class="material-symbols-outlined text-lg">health_and_safety</span>
                                </div>
                                <div class="font-headline-sm text-headline-sm font-semibold text-on-primary">Supervisi
                                    K3</div>
                                <div class="font-body-sm text-body-sm text-on-primary/75 mt-1">Monitoring Pembimbing
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="relative z-10 pt-space-sm flex flex-col sm:flex-row items-start sm:items-center justify-between text-on-primary/70 font-label-md text-label-md">
                        <p>&copy; 2026 PT Indonesia Comnets Plus (PLN Icon Plus). Seluruh hak cipta dilindungi.</p>
                        <div class="flex items-center space-x-3 mt-2 sm:mt-0">
                            <span class="hover:text-on-primary cursor-pointer transition-colors">Kebijakan
                                Privasi</span>
                            <span>&bull;</span>
                            <span class="hover:text-on-primary cursor-pointer transition-colors">Panduan Sistem</span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANEL — Form Login -->
                <div
                    class="lg:w-[45%] flex flex-col justify-between p-space-xl lg:p-space-2xl bg-surface-container-lowest">

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-1.5 text-outline font-label-sm text-label-sm">
                        </div>
                    </div>

                    <div class="my-auto py-space-md max-w-md w-full mx-auto space-y-space-lg">
                        <div class="space-y-space-2xs text-left">
                            <h2 class="font-headline-lg text-headline-lg font-bold text-on-surface">
                                Masuk ke Akun Anda
                            </h2>
                            <p class="font-body-md text-body-md text-on-surface-variant">
                                Silakan masukkan email kampus atau akun peserta magang terdaftar Anda.
                            </p>
                        </div>

                        <form id="loginForm" class="space-y-space-md">
                            @csrf
                            <div class="space-y-space-2xs">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-lg text-label-lg font-semibold text-on-surface"
                                        for="email">
                                        Email Peserta / Kampus
                                    </label>
                                </div>
                                <div class="relative flex items-center">
                                    <span
                                        class="absolute left-3.5 text-outline material-symbols-outlined text-xl pointer-events-none">
                                        mail
                                    </span>
                                    <input
                                        class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-bright text-on-surface font-body-md text-body-md placeholder:text-outline-variant focus:outline-none focus:ring-2 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all shadow-sm"
                                        id="email" name="email" placeholder="nama.mahasiswa@student.ac.id"
                                        type="email" autocomplete="username" required />
                                </div>
                            </div>
                            <div class="space-y-space-2xs">
                                <div class="flex items-center justify-between">
                                    <label class="font-label-lg text-label-lg font-semibold text-on-surface"
                                        for="password">
                                        Kata Sandi
                                    </label>
                                    <span class="text-outline font-body-sm text-body-sm">
                                        Min. 8 karakter
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span
                                        class="absolute left-3.5 text-outline material-symbols-outlined text-xl pointer-events-none">
                                        lock
                                    </span>
                                    <input
                                        class="w-full pl-11 pr-12 py-2.5 rounded-lg bg-surface-bright text-on-surface font-body-md text-body-md placeholder:text-outline-variant focus:outline-none focus:ring-2 focus:ring-primary/40 focus:bg-surface-container-lowest transition-all shadow-sm"
                                        id="password" name="password" placeholder="Masukkan kata sandi"
                                        type="password" autocomplete="current-password" required />
                                    <button aria-label="Toggle kata sandi"
                                        class="absolute right-3.5 text-outline hover:text-on-surface flex items-center justify-center p-1 rounded transition-colors"
                                        id="toggle-password" type="button">
                                        <span class="material-symbols-outlined text-xl"
                                            id="eye-icon">visibility</span>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center space-x-2.5 cursor-pointer select-none">
                                    <input id="remember"
                                        class="w-4 h-4 rounded bg-surface-container text-primary accent-primary focus:ring-0 cursor-pointer"
                                        type="checkbox" />
                                    <span class="font-body-md text-body-md text-on-surface">Ingat Saya</span>
                                </label>
                                <a class="font-label-lg text-label-lg font-semibold text-secondary hover:text-primary transition-colors"
                                    href="javascript:void(0)">
                                    Lupa Password?
                                </a>
                            </div>

                            <div id="loginError"
                                class="hidden bg-error-container text-on-error-container font-body-sm text-body-sm rounded-lg px-4 py-2.5">
                            </div>

                            <button id="loginBtn"
                                class="w-full py-3 px-space-md rounded-lg bg-primary hover:bg-primary-container active:scale-[0.99] text-on-primary font-headline-sm text-headline-sm font-semibold shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-2 group disabled:opacity-70 disabled:cursor-not-allowed"
                                type="submit">
                                <span id="btnText">Masuk Sekarang</span>
                                <svg id="btnSpinner" class="hidden animate-spin h-5 w-5 text-on-primary" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                <span id="btnIcon"
                                    class="material-symbols-outlined text-xl transition-transform group-hover:translate-x-1">
                                    arrow_forward
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- POP UP LOGIN BERHASIL -->
    <div id="successModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-space-md bg-inverse-surface/40 backdrop-blur-sm"
        role="status" aria-live="polite">
        <div id="successCard"
            class="relative w-full max-w-sm overflow-hidden rounded-2xl bg-surface-container-lowest shadow-2xl text-center">
            <div class="h-1.5 bg-gradient-to-r from-primary via-primary-container to-secondary-container"></div>
            <div class="px-space-xl pt-space-xl pb-space-lg flex flex-col items-center">
                <div class="relative w-20 h-20 mb-space-md flex items-center justify-center">
                    <span class="ls-halo absolute inset-0 rounded-full bg-primary/20"></span>
                    <div class="absolute inset-0 rounded-full bg-primary/10"></div>
                    <svg class="relative w-12 h-12 text-primary" viewBox="0 0 52 52" fill="none"
                        stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <circle class="ls-ring" cx="26" cy="26" r="24" pathLength="1"></circle>
                        <path class="ls-check" d="M15 27 l8 8 l15 -17" pathLength="1"></path>
                    </svg>
                </div>
                <p class="ls-up font-headline-md text-headline-md font-bold text-on-surface"
                    style="animation-delay: .45s">Login Berhasil!</p>
                <p id="successSub" class="ls-up font-body-md text-body-md text-on-surface-variant mt-1"
                    style="animation-delay: .55s">Mengalihkan ke dashboard...</p>
            </div>
            <div class="h-1 bg-surface-container">
                <div id="successBar" class="h-full bg-primary"></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');

            if (toggleBtn && passwordInput && eyeIcon) {
                toggleBtn.addEventListener('click', () => {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
                });
            }

            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const btnIcon = document.getElementById('btnIcon');
            const errorBox = document.getElementById('loginError');
            const modal = document.getElementById('successModal');
            const successSub = document.getElementById('successSub');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function setLoading(isLoading) {
                btn.disabled = isLoading;
                btnText.textContent = isLoading ? 'Memproses...' : 'Masuk Sekarang';
                btnSpinner.classList.toggle('hidden', !isLoading);
                btnIcon.classList.toggle('hidden', isLoading);
            }

            function showError(message) {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            }

            function hideError() {
                errorBox.classList.add('hidden');
                errorBox.textContent = '';
            }

            function showSuccessModal(redirectUrl, name) {
                if (name) successSub.textContent = 'Selamat datang, ' + name;

                modal.classList.remove('hidden');
                void modal.offsetWidth; 
                modal.classList.add('is-open');

                setTimeout(() => {
                    modal.classList.remove('is-open');
                    modal.classList.add('is-leaving');
                }, reduceMotion ? 150 : 1450);
                setTimeout(() => { window.location.href = redirectUrl; }, reduceMotion ? 300 : 1700);
            }

            window.addEventListener('pageshow', function (e) {
                if (e.persisted) window.location.reload();
            });

            async function refreshCsrfToken() {
                const res = await fetch('{{ route('login') }}', {
                    headers: { 'Accept': 'text/html' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                const html = await res.text();
                const match = html.match(/<meta name="csrf-token" content="([^"]+)"/);
                if (!match) throw new Error('Token CSRF tidak ditemukan');

                document.querySelector('meta[name="csrf-token"]').content = match[1];
                const hidden = form.querySelector('input[name="_token"]');
                if (hidden) hidden.value = match[1];
            }

            function sendLogin() {
                const formData = new FormData(form);
                formData.append('remember', document.getElementById('remember').checked ? '1' : '0');

                return fetch('{{ route('login') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                hideError();
                setLoading(true);

                try {
                    let response = await sendLogin();

                    if (response.status === 419) {
                        await refreshCsrfToken();
                        response = await sendLogin();
                    }

                    const data = await response.json().catch(() => ({}));

                    if (response.status === 419) {
                        setLoading(false);
                        showError('Sesi tidak valid. Muat ulang halaman (Ctrl+F5) lalu coba lagi.');
                    } else if (response.ok && data.success) {
                        showSuccessModal(data.redirect, data.name || (data.user && data.user.name));
                    } else {
                        setLoading(false);
                        showError(data.message || 'Email atau kata sandi tidak sesuai.');
                    }
                } catch (err) {
                    setLoading(false);
                    showError('Terjadi kesalahan koneksi, silakan coba lagi.');
                }
            });
        })();
    </script>
</body>

</html>