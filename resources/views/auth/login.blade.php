<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Portal Magang</title>
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

            --surface: #f6f8f7;
            --surface-card: #ffffff;
            --line: #e2e8e6;

            --danger: #c4392f;
            --radius-lg: 20px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        * { box-sizing: border-box; }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--surface);
            color: var(--ink-900);
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .auth-shell {
            width: 100%;
            max-width: 980px;
            background: var(--surface-card);
            border-radius: var(--radius-lg);
            box-shadow: 0 24px 60px -20px rgba(11, 59, 50, 0.25);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            min-height: 560px;
        }

        .brand-panel {
            background: linear-gradient(160deg, var(--brand-900) 0%, var(--brand-700) 55%, var(--brand-600) 100%);
            color: white;
            padding: 40px 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 15%, rgba(255,255,255,0.06), transparent 45%);
            pointer-events: none;
        }

        .brand-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-logo-mark {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: rgba(255,255,255,0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-logo-mark svg { width: 18px; height: 18px; }

        .brand-logo-text {
            font-weight: 700;
            font-size: 15px;
            line-height: 1.2;
        }
        .brand-logo-text small {
            display: block;
            font-weight: 500;
            font-size: 11px;
            opacity: 0.7;
            letter-spacing: 0.02em;
        }

        .brand-year-tag {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            white-space: nowrap;
        }

        .brand-context-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            padding: 6px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,0.1);
            width: fit-content;
            margin-top: 22px;
        }

        .brand-headline {
            margin-top: 26px;
        }
        .brand-headline h1 {
            font-size: 28px;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 0 10px;
            letter-spacing: -0.01em;
        }
        .brand-headline p {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255,255,255,0.78);
            margin: 0;
            max-width: 340px;
        }

        .brand-feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 26px;
        }

        .brand-feature {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: var(--radius-md);
            padding: 12px 10px;
        }
        .brand-feature .icon {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: rgba(255,255,255,0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }
        .brand-feature .icon svg { width: 13px; height: 13px; }
        .brand-feature strong {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .brand-feature span {
            font-size: 11px;
            color: rgba(255,255,255,0.65);
            line-height: 1.4;
        }

        .brand-footer {
            font-size: 11.5px;
            color: rgba(255,255,255,0.55);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            margin-top: 24px;
        }
        .brand-footer a {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
        }

        /* Panel kanan: form */
        .form-panel {
            padding: 40px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-panel-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .role-pill {
            font-size: 12px;
            font-weight: 600;
            color: var(--brand-700);
            background: var(--brand-100);
            padding: 5px 11px;
            border-radius: 999px;
        }

        .version-tag {
            font-size: 12px;
            color: var(--ink-300);
        }

        .form-heading h2 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px;
            color: var(--ink-900);
        }
        .form-heading p {
            font-size: 13.5px;
            color: var(--ink-500);
            margin: 0 0 26px;
            line-height: 1.5;
        }

        /* Alert error umum */
        .alert-error {
            background: #fdeceb;
            border: 1px solid #f3c9c5;
            color: var(--danger);
            font-size: 13px;
            padding: 12px 14px;
            border-radius: var(--radius-sm);
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .field-group {
            margin-bottom: 16px;
        }

        .field-label-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 6px;
        }

        .field-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-700);
        }

        .field-hint {
            font-size: 11.5px;
            color: var(--ink-300);
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap .field-icon {
            position: absolute;
            left: 13px;
            width: 16px;
            height: 16px;
            color: var(--ink-300);
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1px solid var(--line);
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: inherit;
            color: var(--ink-900);
            background: var(--surface);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .input-wrap input::placeholder { color: var(--ink-300); }

        .input-wrap input:focus {
            outline: none;
            border-color: var(--brand-600);
            box-shadow: 0 0 0 3px var(--brand-100);
            background: var(--surface-card);
        }

        .input-wrap input.has-error {
            border-color: var(--danger);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            border: none;
            background: none;
            cursor: pointer;
            color: var(--ink-300);
            padding: 4px;
            display: flex;
            align-items: center;
        }
        .toggle-password svg { width: 16px; height: 16px; }
        .toggle-password:hover { color: var(--ink-700); }

        .field-error {
            font-size: 12px;
            color: var(--danger);
            margin-top: 6px;
        }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 18px 0 22px;
        }

        .remember-check {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: var(--ink-700);
            cursor: pointer;
        }
        .remember-check input {
            width: 15px;
            height: 15px;
            accent-color: var(--brand-600);
            cursor: pointer;
        }

        .link-muted {
            font-size: 13px;
            color: var(--brand-700);
            text-decoration: none;
            font-weight: 500;
        }
        .link-muted:hover { text-decoration: underline; }

        .btn-primary {
            width: 100%;
            background: var(--brand-700);
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 14.5px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s ease;
        }
        .btn-primary:hover { background: var(--brand-800); }
        .btn-primary:active { background: var(--brand-900); }
        .btn-primary svg { width: 15px; height: 15px; }

        .help-box {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            padding: 12px 14px;
            margin-top: 22px;
        }
        .help-box .icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--brand-100);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .help-box .icon svg { width: 14px; height: 14px; color: var(--brand-700); }
        .help-box strong {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--ink-900);
            margin-bottom: 2px;
        }
        .help-box span {
            font-size: 12px;
            color: var(--ink-500);
            line-height: 1.5;
        }

        /*Responsive: mobile*/
        @media (max-width: 860px) {
            .auth-shell {
                grid-template-columns: 1fr;
                min-height: unset;
            }
            .brand-panel {
                padding: 28px 24px;
            }
            .brand-headline h1 { font-size: 22px; }
            .brand-feature-grid { display: none; } 
            .brand-footer { display: none; }
            .form-panel {
                padding: 28px 24px 32px;
            }
        }

        @media (max-width: 420px) {
            .page { padding: 0; }
            .auth-shell { border-radius: 0; box-shadow: none; }
        }

        /* Menghormati preferensi reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="auth-shell">

        <div class="brand-panel">
            <div>
                <div class="brand-top">
                    <div class="brand-logo">
                        <div class="brand-logo-mark">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 2 4 14h6l-1 8 9-12h-6l1-8Z" fill="white"/>
                            </svg>
                        </div>
                        <div class="brand-logo-text">
                            Portal Magang
                            <small>Sistem Monitoring Absensi</small>
                        </div>
                    </div>
                    <span class="brand-year-tag">Tahun Akademik {{ date('Y') }}/{{ date('Y') + 1 }}</span>
                </div>

                <span class="brand-context-tag">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    Portal Peserta Magang
                </span>

                <div class="brand-headline">
                    <h1>Monitoring Absensi Peserta Magang</h1>
                    <p>Catat kehadiran, lihat riwayat presensi, dan pantau status magang dalam satu sistem.</p>
                </div>

                <div class="brand-feature-grid">
                    <div class="brand-feature">
                        <div class="icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21s7-6.5 7-11.5a7 7 0 1 0-14 0C5 14.5 12 21 12 21Z" stroke="white" stroke-width="1.6"/>
                                <circle cx="12" cy="9.5" r="2.3" stroke="white" stroke-width="1.6"/>
                            </svg>
                        </div>
                        <strong>Verifikasi Lokasi</strong>
                        <span>Absen dicatat sesuai titik lokasi kantor</span>
                    </div>
                    <div class="brand-feature">
                        <div class="icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="17" rx="2" stroke="white" stroke-width="1.6"/>
                                <path d="M3 9h18M8 2v4M16 2v4" stroke="white" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <strong>Riwayat Absensi</strong>
                        <span>Semua catatan kehadiran tersimpan rapi</span>
                    </div>
                    <div class="brand-feature">
                        <div class="icon">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 17l4-4 4 4 6-8" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14 5h4v4" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <strong>Rekap &amp; Monitoring</strong>
                        <span>Pembimbing dapat memantau kehadiran</span>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                <span>&copy; {{ date('Y') }} Portal Magang. Seluruh hak cipta dilindungi.</span>
            </div>
        </div>

        {{--PANEL KANAN: FORM LOGIN --}}
        <div class="form-panel">
            <div class="form-panel-top">
                <span class="role-pill">Login Peserta / Pembimbing</span>
            </div>

            <div class="form-heading">
                <h2>Masuk ke Akun Anda</h2>
                <p>Gunakan email dan kata sandi yang telah didaftarkan oleh admin.</p>
            </div>

            @if ($errors->has('email') && !$errors->has('password') && session('login_failed'))
                <div class="alert-error">
                    Email atau kata sandi yang Anda masukkan tidak sesuai. Silakan periksa kembali.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="field-group">
                    <div class="field-label-row">
                        <label class="field-label" for="email">Email</label>
                    </div>
                    <div class="input-wrap">
                        <svg class="field-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M3 7l9 6 9-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@kampus.ac.id"
                            class="{{ $errors->has('email') ? 'has-error' : '' }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <div class="field-label-row">
                        <label class="field-label" for="password">Kata Sandi</label>
                    </div>
                    <div class="input-wrap">
                        <svg class="field-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.6"/>
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan kata sandi"
                            class="{{ $errors->has('password') ? 'has-error' : '' }}"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-password" id="togglePasswordBtn" aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z" stroke="currentColor" stroke-width="1.6"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.6"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="row-between">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" id="remember">
                        Ingat saya
                    </label>
                    {{-- Rute lupa password bersifat opsional --}}
                    <a href="#" class="link-muted">Lupa kata sandi?</a>
                </div>

                <button type="submit" class="btn-primary">
                    Masuk
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <div class="help-box">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M12 16v-4M12 8h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <strong>Butuh bantuan akun?</strong>
                    <span>Hubungi pembimbing lapangan atau admin sistem jika lupa akun atau mengalami kendala login.</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('togglePasswordBtn').addEventListener('click', function () {
        const input = document.getElementById('password');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        this.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
</script>

</body>
</html>