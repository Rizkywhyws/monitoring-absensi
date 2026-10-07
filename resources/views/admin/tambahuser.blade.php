<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#006857",
                        "primary-container": "#00846e",
                        "primary-fixed": "#7cf8da",
                        "on-primary": "#ffffff",
                        "on-primary-fixed": "#00201a",
                        "on-primary-fixed-variant": "#005143",

                        secondary: "#006398",
                        "secondary-fixed": "#cce5ff",
                        "secondary-fixed-dim": "#93ccff",

                        tertiary: "#825100",

                        surface: "#faf8ff",
                        "surface-container-low": "#f2f3ff",
                        "surface-container": "#eaedff",
                        "surface-container-high": "#e2e7ff",
                        "surface-container-highest": "#dae2fd",
                        "surface-container-lowest": "#ffffff",

                        "on-surface": "#131b2e",
                        "on-surface-variant": "#3d4945",

                        outline: "#6d7a75",
                        "outline-variant": "#bccac4",

                        error: "#ba1a1a",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                        "on-error": "#ffffff",
                    },

                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px",
                    },

                    spacing: {
                        "space-xs": "0.5rem",
                        "space-sm": "0.75rem",
                        "space-md": "1rem",
                        "space-lg": "1.5rem",
                        "space-xl": "2rem",
                        "space-2xl": "2.5rem",
                        "space-3xl": "3rem",

                        "sidebar-width": "280px",
                    },

                    fontFamily: {
                        headline: ["Plus Jakarta Sans"],
                        body: ["Plus Jakarta Sans"],
                    },
                }
            }
        };
    </script>

    <style>
        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            overscroll-behavior: none;
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-surface text-on-surface antialiased">

    {{-- ========================================================= --}}
    {{-- SIDEBAR --}}
    {{-- ========================================================= --}}

    <aside
        class="fixed left-0 top-0 h-screen w-sidebar-width bg-surface-container-low z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">

        <div class="flex flex-col">

            {{-- LOGO --}}
            <div class="h-16 px-space-lg flex items-center gap-space-sm">

                <div
                    class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center text-on-primary shadow-sm">

                    <span class="material-symbols-outlined text-[22px]">
                        bolt
                    </span>

                </div>

                <div class="flex flex-col">

                    <span class="font-bold text-primary">
                        PLN Icon Plus
                    </span>

                    <span class="text-[11px] text-outline uppercase tracking-wider">
                        PKL Management
                    </span>

                </div>

            </div>

            {{-- NAVIGASI --}}
            <div class="px-space-md py-space-xs">

                <p class="px-space-sm py-1 text-[11px] text-outline uppercase tracking-wider">
                    Navigasi Utama
                </p>

            </div>

            <nav class="px-space-sm space-y-1">

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high">

                    <span class="material-symbols-outlined text-[20px]">
                        grid_view
                    </span>

                    <span class="font-semibold">
                        Dashboard
                    </span>

                </a>

                <a
                    href="{{ route('tambahuser') }}"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg bg-primary text-on-primary shadow-sm font-semibold">

                    <span class="material-symbols-outlined text-[20px]">
                        group
                    </span>

                    <span>
                        User
                    </span>

                </a>

                <a
                    href="{{ route('absensi') }}"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high">

                    <span class="material-symbols-outlined text-[20px]">
                        how_to_reg
                    </span>

                    <span class="font-semibold">
                        Absensi
                    </span>

                </a>

                <a
                    href="#"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high">

                    <span class="material-symbols-outlined text-[20px]">
                        confirmation_number
                    </span>

                    <span class="font-semibold">
                        Ticket
                    </span>

                </a>

                <a
                    href="#"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high">

                    <span class="material-symbols-outlined text-[20px]">
                        edit_calendar
                    </span>

                    <span class="font-semibold">
                        Daily Activity
                    </span>

                </a>

                <a
                    href="#"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high">

                    <span class="material-symbols-outlined text-[20px]">
                        description
                    </span>

                    <span class="font-semibold">
                        Laporan
                    </span>

                </a>

                <a
                    href="#"
                    class="flex items-center gap-space-sm px-space-sm py-2.5 rounded-lg text-on-surface-variant hover:bg-surface-container-high">

                    <span class="material-symbols-outlined text-[20px]">
                        account_circle
                    </span>

                    <span class="font-semibold">
                        Profil
                    </span>

                </a>

            </nav>

        </div>

        {{-- ADMIN --}}
        <div class="p-space-sm m-space-sm rounded-xl bg-white shadow-sm">

            <div class="flex items-center justify-between gap-space-sm">

                <div class="flex items-center gap-space-sm">

                    <div
                        class="w-9 h-9 rounded-full bg-primary flex items-center justify-center">

                        <span class="material-symbols-outlined text-white text-[18px]">
                            person
                        </span>

                    </div>

                    <div>

                        <span class="font-semibold text-sm">
                            Admin PKL
                        </span>

                        <span class="block text-[11px] text-outline">
                            Administrator
                        </span>

                    </div>

                </div>

                <form action="{{ route('logout') }}" method="POST">

                    @csrf

                    <button
                        class="p-1.5 rounded-lg text-outline hover:text-error"
                        title="Keluar"
                        type="submit">

                        <span class="material-symbols-outlined text-[18px]">
                            logout
                        </span>

                    </button>

                </form>

            </div>

        </div>

    </aside>

    {{-- ========================================================= --}}
    {{-- MAIN --}}
    {{-- ========================================================= --}}

    <div class="pl-sidebar-width">

        {{-- HEADER --}}
        <header
            class="fixed top-0 left-sidebar-width right-0 h-16 bg-surface/80 backdrop-blur-xl shadow-sm z-40 px-space-lg flex items-center">

            <div class="relative w-full max-w-lg">

                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">
                    search
                </span>

                <input
                    class="w-full pl-9 pr-4 py-2 rounded-lg bg-white text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                    placeholder="Cari mahasiswa, aktivitas, atau tiket..."
                    type="text">

            </div>

        </header>

        {{-- CONTENT --}}
        <main class="w-full pt-16 px-space-lg bg-surface min-h-screen">

            <div class="flex flex-col w-full pb-space-3xl">

                {{-- PAGE HEADER --}}
                <div class="flex flex-col gap-space-sm pt-space-md mb-space-xl">

                    <div class="flex items-center justify-between">

                        <nav class="flex items-center gap-2">

                            <a
                                href="{{ route('tambahuser') }}"
                                class="text-sm text-on-surface-variant hover:text-primary flex items-center gap-1">

                                <span class="material-symbols-outlined text-[16px]">
                                    group
                                </span>

                                <span>
                                    Kelola User
                                </span>

                            </a>

                            <span class="material-symbols-outlined text-outline text-[14px]">
                                chevron_right
                            </span>

                            <span class="text-sm text-primary font-semibold">
                                Tambah User
                            </span>

                        </nav>

                        {{-- KEMBALI --}}
                        <a
                            href="{{ route('admin.daftar-user') }}"
                            class="px-space-md py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold transition-all shadow-sm flex items-center gap-1">

                            <span class="material-symbols-outlined text-[18px]">
                                arrow_back
                            </span>

                            <span>
                                Kembali
                            </span>

                        </a>

                    </div>

                    <div>

                        <div class="flex items-center gap-space-sm">

                            <h1 class="text-2xl font-bold tracking-tight">
                                Tambah User
                            </h1>

                            <span
                                class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-[11px] font-bold uppercase">

                                Akun Baru

                            </span>

                        </div>

                        <p class="text-sm text-on-surface-variant mt-1">
                            Buat akun peserta PKL baru dan alokasikan kelompok operasional kerja lapangan
                        </p>

                    </div>

                </div>

                {{-- ERROR --}}
                @if ($errors->any())

                    <div
                        class="mb-space-md p-space-md rounded-lg bg-error-container text-on-error-container">

                        <ul class="list-disc pl-5">

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                {{-- SUCCESS --}}
                @if (session('success'))

                    <div
                        class="mb-space-md p-space-md rounded-lg bg-primary-fixed text-on-primary-fixed">

                        {{ session('success') }}

                    </div>

                @endif

                {{-- KREDENSIAL --}}
                @if (session('kredensial'))

                    <div
                        class="mb-space-md p-space-md rounded-lg bg-secondary-fixed text-on-secondary-fixed">

                        <p class="font-semibold mb-2">
                            Akun berhasil dibuat.
                        </p>

                        <p>
                            Email Login:
                            <strong>
                                {{ session('kredensial.email_login') }}
                            </strong>
                        </p>

                        <p>
                            Password:
                            <strong>
                                {{ session('kredensial.password') }}
                            </strong>
                        </p>

                        <p class="text-xs mt-2">
                            Simpan informasi login ini karena password hanya ditampilkan setelah akun dibuat.
                        </p>

                    </div>

                @endif

                {{-- ================================================= --}}
                {{-- FORM --}}
                {{-- ================================================= --}}

                <form
                    class="grid grid-cols-1 lg:grid-cols-12 gap-space-xl"
                    id="tambahUserForm"
                    action="{{ route('tambahuser.store') }}"
                    method="POST">

                    @csrf

                    <div class="lg:col-span-12 flex flex-col gap-space-xl">

                        {{-- ================================================= --}}
                        {{-- SECTION 1 --}}
                        {{-- ================================================= --}}

                        <div
                            class="bg-white rounded-xl p-space-xl shadow-sm relative overflow-hidden">

                            <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>

                            <div class="flex items-center gap-space-sm pb-space-md mb-space-lg">

                                <div
                                    class="w-8 h-8 rounded-lg bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold">

                                    01

                                </div>

                                <div>

                                    <h2 class="text-base font-semibold">
                                        Informasi Peserta PKL
                                    </h2>

                                    <p class="text-xs text-on-surface-variant">
                                        Data identitas mahasiswa dan kontak peserta
                                    </p>

                                </div>

                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">

                                {{-- NAMA --}}
                                <div class="flex flex-col gap-1.5 md:col-span-3">

                                    <label
                                        class="text-sm font-semibold"
                                        for="namaLengkap">

                                        Nama Lengkap
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            badge

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="namaLengkap"
                                            name="namaLengkap"
                                            placeholder="Contoh: Budi Santoso"
                                            required
                                            type="text"
                                            value="{{ old('namaLengkap') }}">

                                    </div>

                                </div>

                                {{-- NIM --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="nimMahasiswa">

                                        NIM / ID Mahasiswa
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            pin

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="nimMahasiswa"
                                            name="nimMahasiswa"
                                            placeholder="Contoh: 22053460012"
                                            required
                                            type="text"
                                            value="{{ old('nimMahasiswa') }}">

                                    </div>

                                </div>

                                {{-- ASAL UNIVERSITAS --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="asalUniversitas">

                                        Asal Universitas / Institusi
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            account_balance

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="asalUniversitas"
                                            name="asalUniversitas"
                                            placeholder="Contoh: Universitas Jember"
                                            required
                                            type="text"
                                            value="{{ old('asalUniversitas') }}">

                                    </div>

                                </div>

                                {{-- EMAIL --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="emailPribadi">

                                        Email Kampus / Pribadi
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            alternate_email

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="emailPribadi"
                                            name="emailPribadi"
                                            placeholder="nama@student.ac.id"
                                            required
                                            type="email"
                                            value="{{ old('emailPribadi') }}">

                                    </div>

                                </div>

                                {{-- NOMOR TELEPON --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="nomorTelepon">

                                        Nomor WhatsApp / Telepon
                                        <span class="text-error">*</span>

                                    </label>

                                    <div
                                        class="flex items-center rounded-lg bg-surface-container-low overflow-hidden focus-within:ring-2 focus-within:ring-primary">

                                        <span
                                            class="px-3.5 py-2.5 bg-surface-container text-on-surface-variant font-semibold flex items-center gap-1">

                                            <span class="material-symbols-outlined text-[16px] text-primary">
                                                call
                                            </span>

                                            +62

                                        </span>

                                        <input
                                            class="w-full px-3 py-2.5 bg-transparent placeholder:text-outline focus:outline-none"
                                            id="nomorTelepon"
                                            name="nomorTelepon"
                                            placeholder="812-3456-7890"
                                            required
                                            type="tel"
                                            value="{{ old('nomorTelepon') }}">

                                    </div>

                                </div>

                                {{-- PASSWORD --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="password">

                                        Password / Sandi
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            lock

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-12 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="password"
                                            name="password"
                                            placeholder="Minimal 6 karakter"
                                            required
                                            minlength="6"
                                            maxlength="100"
                                            type="password">

                                        <button
                                            type="button"
                                            id="togglePassword"
                                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors"
                                            title="Tampilkan password">

                                            <span
                                                class="material-symbols-outlined text-[20px]"
                                                id="passwordIcon">

                                                visibility

                                            </span>

                                        </button>

                                    </div>

                                    <p class="text-xs text-on-surface-variant flex items-center gap-1.5">

                                        <span class="material-symbols-outlined text-[16px] text-secondary">
                                            info
                                        </span>

                                        Password ini digunakan peserta untuk login ke sistem.

                                    </p>

                                </div>

                                {{-- KELOMPOK / DIVISI --}}
                                <div class="flex flex-col gap-1.5 md:col-span-3">

                                    <label
                                        class="text-sm font-semibold"
                                        for="kelompokDivisi">

                                        Kelompok / Divisi Penugasan
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            lan

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="kelompokDivisi"
                                            name="kelompokDivisi"
                                            placeholder="Contoh: Install"
                                            required
                                            type="text"
                                            value="{{ old('kelompokDivisi') }}">

                                    </div>

                                    <p class="text-xs text-on-surface-variant flex items-center gap-1.5">

                                        <span class="material-symbols-outlined text-[16px] text-secondary">
                                            info
                                        </span>

                                        Masukkan kelompok atau divisi penugasan peserta PKL.

                                    </p>

                                </div>

                            </div>

                        </div>

                                <div class="flex flex-col gap-1.5 md:col-span-3">
            <label class="text-sm font-semibold" for="mentor">
                Nama Mentor
                <span class="text-error">*</span>
            </label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">
                    person
                </span>
                <input
                    class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                    id="mentor"
                    name="mentor"
                    placeholder="Contoh: Bpk. Hendra Kusuma"
                    required
                    type="text"
                    value="{{ old('mentor') }}">
            </div>
            <p class="text-xs text-on-surface-variant flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px] text-secondary">
                    info
                </span>
                Masukkan nama mentor atau pembimbing peserta PKL.
            </p>
        </div>

                        {{-- ================================================= --}}
                        {{-- SECTION 2 --}}
                        {{-- ================================================= --}}

                        <div
                            class="bg-white rounded-xl p-space-xl shadow-sm relative overflow-hidden">

                            <div class="absolute top-0 left-0 w-1.5 h-full bg-secondary"></div>

                            <div class="flex items-center gap-space-sm pb-space-md mb-space-lg">

                                <div
                                    class="w-8 h-8 rounded-lg bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center font-bold">

                                    02

                                </div>

                                <div>

                                    <h2 class="text-base font-semibold">
                                        Periode & Penempatan PKL
                                    </h2>

                                    <p class="text-xs text-on-surface-variant">
                                        Jadwal resmi masa magang dan lokasi unit penugasan
                                    </p>

                                </div>

                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">

                                {{-- TANGGAL MULAI --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="tanggalMulai">

                                        Tanggal Mulai
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            calendar_today

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="tanggalMulai"
                                            name="tanggalMulai"
                                            required
                                            type="date"
                                            value="{{ old('tanggalMulai') }}">

                                    </div>

                                </div>

                                {{-- TANGGAL SELESAI --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="tanggalSelesai">

                                        Tanggal Selesai
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]">

                                            event_available

                                        </span>

                                        <input
                                            class="w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm"
                                            id="tanggalSelesai"
                                            name="tanggalSelesai"
                                            required
                                            type="date"
                                            value="{{ old('tanggalSelesai') }}">

                                    </div>

                                </div>

                                {{-- UNIT / RAYON --}}
                                <div class="flex flex-col gap-1.5">

                                    <label
                                        class="text-sm font-semibold"
                                        for="unitRayon">

                                        Unit / Rayon Kerja
                                        <span class="text-error">*</span>

                                    </label>

                                    <div class="relative">

                                        <span
                                            class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-secondary text-[20px]">

                                            pin_drop

                                        </span>

                                        <select
                                            class="w-full pl-11 pr-10 py-2.5 rounded-lg bg-surface-container-low focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm appearance-none cursor-pointer"
                                            id="unitRayon"
                                            name="unitRayon"
                                            required>

                                            <option
                                                value=""
                                                disabled
                                                {{ old('unitRayon') ? '' : 'selected' }}>

                                                Pilih Unit / Rayon...

                                            </option>

                                            <option
                                                value="KP Jember"
                                                {{ old('unitRayon') == 'KP Jember' ? 'selected' : '' }}>

                                                Kantor Perwakilan (KP) Jember

                                            </option>

                                            <option
                                                value="KP Banyuwangi"
                                                {{ old('unitRayon') == 'KP Banyuwangi' ? 'selected' : '' }}>

                                                Kantor Perwakilan (KP) Banyuwangi

                                            </option>

                                            <option
                                                value="KP Probolinggo"
                                                {{ old('unitRayon') == 'KP Probolinggo' ? 'selected' : '' }}>

                                                Kantor Perwakilan (KP) Probolinggo

                                            </option>

                                            <option
                                                value="SBU Surabaya"
                                                {{ old('unitRayon') == 'SBU Surabaya' ? 'selected' : '' }}>

                                                Kantor SBU Regional Jawa Timur - Ketintang Surabaya

                                            </option>

                                        </select>

                                        <span
                                            class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">

                                            expand_more

                                        </span>

                                    </div>

                                </div>

                                <div class="md:col-span-3">

                                    <p class="text-xs text-on-surface-variant flex items-center gap-1.5">

                                        <span class="material-symbols-outlined text-[16px] text-outline">
                                            schedule
                                        </span>

                                        Periode PKL digunakan sebagai informasi dan filter data operasional presensi serta logbook.

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </form>

                {{-- ================================================= --}}
                {{-- BUTTON --}}
                {{-- ================================================= --}}

                <div
                    class="mt-space-xl p-space-md rounded-xl bg-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-space-md">

                    <div class="flex items-center gap-space-sm text-on-surface-variant">

                        <div
                            class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-primary">

                            <span class="material-symbols-outlined text-[18px]">
                                verified_user
                            </span>

                        </div>

                        <p class="text-xs">
                            Pastikan seluruh data penempatan dan kontak telah divalidasi sesuai surat penerimaan resmi.
                        </p>

                    </div>

                    <div class="flex items-center gap-space-sm w-full sm:w-auto justify-end">

                        {{-- BATAL --}}
                        <button
                            class="px-space-md py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold transition-all shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto"
                            id="btnBatal"
                            type="button">

                            <span class="material-symbols-outlined text-[18px]">
                                close
                            </span>

                            <span>
                                Batal
                            </span>

                        </button>

                        {{-- SIMPAN --}}
                        <button
                            class="px-space-lg py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-semibold transition-all shadow-md flex items-center justify-center gap-2 w-full sm:w-auto"
                            form="tambahUserForm"
                            type="submit">

                            <span class="material-symbols-outlined text-[20px]">
                                person_add
                            </span>

                            <span>
                                Simpan Peserta PKL
                            </span>

                        </button>

                    </div>

                </div>

            </div>

        </main>

    </div>

    {{-- BATAL --}}
    <script>
        (function () {
            const btnBatal =
                document.getElementById('btnBatal');

            const form =
                document.getElementById('tambahUserForm');

            if (btnBatal) {
                btnBatal.addEventListener(
                    'click',
                    function () {
                        if (
                            confirm(
                                'Batalkan pengisian formulir? Data yang belum disimpan akan hilang.'
                            )
                        ) {
                            if (form) {
                                form.reset();
                            }
                        }
                    }
                );
            }
        })();
    </script>

    {{-- TOGGLE PASSWORD --}}
    <script>
        (function () {
            const password =
                document.getElementById('password');

            const togglePassword =
                document.getElementById('togglePassword');

            const passwordIcon =
                document.getElementById('passwordIcon');

            if (password && togglePassword && passwordIcon) {

                togglePassword.addEventListener('click', function () {

                    if (password.type === 'password') {

                        password.type = 'text';

                        passwordIcon.textContent =
                            'visibility_off';

                        togglePassword.title =
                            'Sembunyikan password';

                    } else {

                        password.type = 'password';

                        passwordIcon.textContent =
                            'visibility';

                        togglePassword.title =
                            'Tampilkan password';

                    }

                });

            }

        })();
    </script>

</body>

</html>