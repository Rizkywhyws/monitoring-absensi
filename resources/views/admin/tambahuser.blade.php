@extends('layouts.app')

@section('title', 'Tambah User')
@section('breadcrumb', 'Admin Workspace › User Management › Tambah User')

@section('content')
    @php
        $input = 'w-full pl-11 pr-4 py-2.5 rounded-lg bg-surface-container-low placeholder:text-outline focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary shadow-sm';
        $iconCls = 'material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-outline text-[20px]';

        $units = [
            'KP Jember' => 'Kantor Perwakilan (KP) Jember',
            'KP Banyuwangi' => 'Kantor Perwakilan (KP) Banyuwangi',
            'KP Probolinggo' => 'Kantor Perwakilan (KP) Probolinggo',
            'SBU Surabaya' => 'Kantor SBU Regional Jawa Timur - Ketintang Surabaya',
        ];

        // type: text | email | date | tel | password | list (input + saran dari $groups) | select
        $sections = [
            [
                'no' => '01', 'title' => 'Informasi Peserta PKL',
                'desc' => 'Data identitas mahasiswa dan kontak peserta',
                'bar' => 'bg-primary', 'badge' => 'bg-primary-fixed text-on-primary-fixed',
                'fields' => [
                    ['namaLengkap', 'Nama Lengkap', 'badge', 'text', 'Contoh: Budi Santoso', 3],
                    ['nimMahasiswa', 'NIM / ID Mahasiswa', 'pin', 'text', 'Contoh: 22053460012', 1],
                    ['asalUniversitas', 'Asal Universitas / Institusi', 'account_balance', 'text', 'Contoh: Universitas Jember', 1],
                    ['emailPribadi', 'Email Kampus / Pribadi', 'alternate_email', 'email', 'nama@student.ac.id', 1],
                    ['nomorTelepon', 'Nomor WhatsApp / Telepon', 'call', 'tel', '812-3456-7890', 1],
                    ['password', 'Password / Sandi', 'lock', 'password', 'Minimal 6 karakter', 1, 'Password ini digunakan peserta untuk login ke sistem.'],
                    ['kelompokDivisi', 'Kelompok / Divisi Penugasan', 'lan', 'list', 'Contoh: Install', 1, 'Pilih dari saran agar penulisan nama kelompok seragam.'],
                    ['mentor', 'Nama Mentor', 'person', 'text', 'Contoh: Bpk. Hendra Kusuma', 3, 'Masukkan nama mentor atau pembimbing peserta PKL.'],
                ],
            ],
            [
                'no' => '02', 'title' => 'Periode & Penempatan PKL',
                'desc' => 'Jadwal resmi masa magang dan lokasi unit penugasan',
                'bar' => 'bg-secondary', 'badge' => 'bg-secondary-fixed text-on-secondary-fixed',
                'fields' => [
                    ['tanggalMulai', 'Tanggal Mulai', 'calendar_today', 'date', '', 1],
                    ['tanggalSelesai', 'Tanggal Selesai', 'event_available', 'date', '', 1],
                    ['unitRayon', 'Unit / Rayon Kerja', 'pin_drop', 'select', '', 1],
                ],
            ],
        ];
    @endphp

    <div class="flex flex-col w-full gap-space-lg pb-space-xl">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-space-md">
            <div>
                <div class="flex items-center gap-space-sm">
                    <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Tambah User</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed text-[11px] font-bold uppercase">Akun Baru</span>
                </div>
                <p class="text-sm text-on-surface-variant mt-1">
                    Buat akun peserta PKL baru dan alokasikan kelompok operasional kerja lapangan
                </p>
            </div>
            <a href="{{ route('admin.daftar-user') }}"
                class="px-space-md py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold transition-all shadow-sm flex items-center gap-1 shrink-0">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        {{-- Alerts --}}
        @if ($errors->any())
            <div class="p-space-md rounded-lg bg-error-container text-on-error-container">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="p-space-md rounded-lg bg-primary-fixed text-on-primary-fixed">{{ session('success') }}</div>
        @endif
        @if (session('kredensial'))
            <div class="p-space-md rounded-lg bg-secondary-fixed text-on-secondary-fixed">
                <p class="font-semibold mb-2">Akun berhasil dibuat.</p>
                <p>Email Login: <strong>{{ session('kredensial.email_login') }}</strong></p>
                <p>Password: <strong>{{ session('kredensial.password') }}</strong></p>
                <p class="text-xs mt-2">Simpan informasi login ini karena password hanya ditampilkan setelah akun dibuat.</p>
            </div>
        @endif

        {{-- Form --}}
        <form id="tambahUserForm" action="{{ route('admin.daftar-user.simpan') }}" method="POST" autocomplete="off"
            class="flex flex-col gap-space-xl">
            @csrf

            @foreach ($sections as $s)
                <div class="bg-white rounded-xl p-space-xl shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full {{ $s['bar'] }}"></div>

                    <div class="flex items-center gap-space-sm pb-space-md mb-space-lg">
                        <div class="w-8 h-8 rounded-lg {{ $s['badge'] }} flex items-center justify-center font-bold">{{ $s['no'] }}</div>
                        <div>
                            <h2 class="text-base font-semibold">{{ $s['title'] }}</h2>
                            <p class="text-xs text-on-surface-variant">{{ $s['desc'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
                        @foreach ($s['fields'] as $f)
                            @php
                                [$name, $label, $icon, $type, $ph, $span] = $f;
                                $hint = $f[6] ?? null;
                                $ring = $errors->has($name) ? ' ring-2 ring-error' : '';
                            @endphp
                            <div class="flex flex-col gap-1.5 {{ $span === 3 ? 'md:col-span-3' : '' }}">
                                <label class="text-sm font-semibold" for="{{ $name }}">
                                    {{ $label }} <span class="text-error">*</span>
                                </label>

                                @if ($type === 'tel')
                                    <div class="flex items-center rounded-lg bg-surface-container-low overflow-hidden focus-within:ring-2 focus-within:ring-primary{{ $ring }}">
                                        <span class="px-3.5 py-2.5 bg-surface-container text-on-surface-variant font-semibold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px] text-primary">call</span>+62
                                        </span>
                                        <input id="{{ $name }}" name="{{ $name }}" type="tel" inputmode="tel" required
                                            placeholder="{{ $ph }}" value="{{ old($name) }}"
                                            class="w-full px-3 py-2.5 bg-transparent placeholder:text-outline focus:outline-none">
                                    </div>
                                @elseif ($type === 'select')
                                    <div class="relative">
                                        <span class="{{ $iconCls }} !text-secondary">{{ $icon }}</span>
                                        <select id="{{ $name }}" name="{{ $name }}" required
                                            class="{{ $input }} !pr-10 appearance-none cursor-pointer{{ $ring }}">
                                            <option value="" disabled @selected(!old($name))>Pilih Unit / Rayon...</option>
                                            @foreach ($units as $val => $text)
                                                <option value="{{ $val }}" @selected(old($name) == $val)>{{ $text }}</option>
                                            @endforeach
                                        </select>
                                        <span class="material-symbols-outlined absolute right-3.5 top-1/2 -translate-y-1/2 text-outline pointer-events-none text-[20px]">expand_more</span>
                                    </div>
                                @else
                                    <div class="relative">
                                        <span class="{{ $iconCls }}">{{ $icon }}</span>
                                        @if ($type === 'password')
                                            <input id="password" name="password" type="password" required minlength="6" maxlength="100"
                                                autocomplete="new-password" placeholder="{{ $ph }}"
                                                class="{{ $input }} !pr-12{{ $ring }}">
                                            <button type="button" id="togglePassword" title="Tampilkan password"
                                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors">
                                                <span class="material-symbols-outlined text-[20px]" id="passwordIcon">visibility</span>
                                            </button>
                                        @else
                                            <input id="{{ $name }}" name="{{ $name }}" type="{{ $type === 'list' ? 'text' : $type }}" required
                                                @if ($type === 'list') list="daftarKelompok" @endif
                                                @if ($ph) placeholder="{{ $ph }}" @endif
                                                value="{{ old($name) }}" class="{{ $input }}{{ $ring }}">
                                        @endif
                                    </div>
                                    @if ($type === 'list' && isset($groups))
                                        <datalist id="daftarKelompok">
                                            @foreach ($groups as $g)
                                                <option value="{{ $g->name ?? $g }}"></option>
                                            @endforeach
                                        </datalist>
                                    @endif
                                @endif

                                @error($name)
                                    <p class="text-xs text-error">{{ $message }}</p>
                                @enderror
                                @if ($hint)
                                    <p class="text-xs text-on-surface-variant flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-secondary">info</span>{{ $hint }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Aksi --}}
            <div class="p-space-md rounded-xl bg-white shadow-lg flex flex-col sm:flex-row items-center justify-between gap-space-md">
                <div class="flex items-center gap-space-sm text-on-surface-variant">
                    <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    </div>
                    <p class="text-xs">Pastikan seluruh data penempatan dan kontak telah divalidasi sesuai surat penerimaan resmi.</p>
                </div>
                <div class="flex items-center gap-space-sm w-full sm:w-auto justify-end">
                    <button type="button" id="btnBatal"
                        class="px-space-md py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold transition-all shadow-sm flex items-center justify-center gap-2 w-full sm:w-auto">
                        <span class="material-symbols-outlined text-[18px]">close</span><span>Batal</span>
                    </button>
                    <button type="submit"
                        class="px-space-lg py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-semibold transition-all shadow-md flex items-center justify-center gap-2 w-full sm:w-auto">
                        <span class="material-symbols-outlined text-[20px]">person_add</span><span>Simpan Peserta PKL</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('extra-scripts')
    <script>
        (function() {
            document.getElementById('btnBatal')?.addEventListener('click', () => {
                if (confirm('Batalkan pengisian formulir? Data yang belum disimpan akan hilang.')) {
                    window.location.href = @json(route('admin.daftar-user'));
                }
            });

            const pw = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');
            const icon = document.getElementById('passwordIcon');
            toggle?.addEventListener('click', () => {
                const show = pw.type === 'password';
                pw.type = show ? 'text' : 'password';
                icon.textContent = show ? 'visibility_off' : 'visibility';
                toggle.title = show ? 'Sembunyikan password' : 'Tampilkan password';
            });

            // Tanggal selesai tidak boleh sebelum tanggal mulai
            const mulai = document.getElementById('tanggalMulai');
            const selesai = document.getElementById('tanggalSelesai');
            const sync = () => {
                if (!mulai || !selesai) return;
                selesai.min = mulai.value || '';
                if (selesai.value && mulai.value && selesai.value < mulai.value) selesai.value = '';
            };
            mulai?.addEventListener('change', sync);
            sync();
        })();
    </script>
@endsection