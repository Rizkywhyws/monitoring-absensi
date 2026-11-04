@extends('layouts.app')

@section('title', 'Profil Saya')
@section('breadcrumb', 'Beranda / Profil Saya')

@section('content')
    @php
        $v = fn($x) => filled($x) ? $x : '-';

        $initials = \Illuminate\Support\Str::of($user->name)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))
            ->implode('');

        $divisi = $user->group?->name ?: $user->division;
        $universitas = $user->asal_universitas ?: $user->university;
        $isActive = $user->status === 'active';
        $fotoUrl = $user->foto ? asset('storage/' . $user->foto) : null;

        $pe = $errors->getBag('profile');
        $pw = $errors->getBag('password');
        $pf = $errors->getBag('photo');
    @endphp

    <div class="flex flex-col w-full gap-space-lg">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-sm">
            <div class="flex flex-col gap-1">
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Profil Saya</h1>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-3xl">
                    Informasi akun peserta PKL, data akademik, dan penempatan. Data yang terkunci diatur oleh admin.
                </p>
            </div>

            <div
                class="flex items-center gap-space-xs self-start md:self-center px-space-md py-space-xs bg-surface-container-lowest rounded-xl shadow-sm">
                <span class="h-2.5 w-2.5 rounded-full {{ $isActive ? 'bg-primary' : 'bg-outline' }}"></span>
                <div class="flex flex-col">
                    <span class="font-label-sm text-[10px] uppercase tracking-wider text-outline font-bold">Status
                        Keaktifan</span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold">
                        {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                        @if ($user->unit_rayon)
                            • {{ $user->unit_rayon }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-space-lg items-start">

            {{-- Kolom kiri --}}
            <div class="xl:col-span-4 flex flex-col gap-space-md">
                <div
                    class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col items-center text-center">

                    {{-- Foto profil --}}
                    <div class="relative">
                        <div
                            class="w-28 h-28 rounded-2xl overflow-hidden bg-primary flex items-center justify-center shadow-md">
                            @if ($fotoUrl)
                                <img src="{{ $fotoUrl }}" alt="Foto profil {{ $user->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="font-headline-xl text-headline-xl text-on-primary">{{ $initials }}</span>
                            @endif
                        </div>
                        <button type="button" title="Ubah foto profil"
                            onclick="document.getElementById('photo-dialog').showModal()"
                            class="absolute -bottom-2 -right-2 w-9 h-9 rounded-full bg-surface-container-lowest text-primary shadow-md flex items-center justify-center ring-4 ring-surface-container-lowest hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                        </button>
                    </div>

                    <h2 class="mt-space-md font-headline-md text-headline-md text-on-surface tracking-tight">
                        {{ $user->name }}</h2>

                    <div class="mt-1 px-3 py-1 rounded-full bg-surface-container text-primary font-label-md text-label-md">
                        Peserta PKL
                        @if ($divisi)
                            • {{ $divisi }}
                        @endif
                    </div>

                    @if ($universitas)
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-space-xs">{{ $universitas }}</p>
                    @endif

                    <div class="w-full flex flex-col gap-space-xs mt-space-lg">
                        <button type="button" onclick="document.getElementById('profile-dialog').showModal()"
                            class="w-full h-[42px] rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg flex items-center justify-center gap-2 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">edit</span>Edit Profil
                        </button>
                        <button type="button" onclick="document.getElementById('password-dialog').showModal()"
                            class="w-full h-[42px] rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg flex items-center justify-center gap-2 transition-all">
                            <span class="material-symbols-outlined text-[18px] text-outline">key</span>Ubah Password
                        </button>
                    </div>

                    <div class="w-full mt-space-md grid grid-cols-2 gap-space-xs">
                        <div class="bg-surface-container-low rounded-lg p-space-sm text-left">
                            <span class="font-label-sm text-label-sm text-outline">Kehadiran</span>
                            <p class="font-headline-md text-headline-md text-on-surface mt-1">
                                {{ $kehadiran === null ? '-' : $kehadiran . '%' }}</p>
                            <span class="font-label-sm text-[11px] text-primary font-medium">{{ $hadir }} hari
                                hadir</span>
                        </div>
                        <div class="bg-surface-container-low rounded-lg p-space-sm text-left">
                            <span class="font-label-sm text-label-sm text-outline">Tiket Selesai</span>
                            <p class="font-headline-md text-headline-md text-on-surface mt-1">{{ $tiket['selesai'] }}</p>
                            <span
                                class="font-label-sm text-[11px] text-on-surface-variant font-medium">{{ $tiket['verified'] }}
                                terverifikasi</span>
                        </div>
                    </div>

                    <div
                        class="w-full mt-space-xs bg-surface-container-low rounded-lg p-space-sm flex items-center gap-space-sm text-left">
                        <div
                            class="w-9 h-9 rounded-full bg-surface-container-highest flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-[20px]">person_check</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-sm text-[11px] text-outline uppercase">Mentor Pembimbing</span>
                            <span
                                class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ $v($user->mentor) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom kanan --}}
            <div class="xl:col-span-8 flex flex-col gap-space-lg">

                {{-- Identitas --}}
                <section class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-9 h-9 rounded-lg bg-surface-container text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">account_box</span>
                        </div>
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface">Informasi Akademik &amp;
                                Identitas</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Telepon dan email pribadi dapat
                                kamu ubah sendiri</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Nama
                                Lengkap</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-semibold">{{ $user->name }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">NIM</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-semibold font-mono">{{ $v($user->nim) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1 md:col-span-2">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Asal
                                Universitas</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-medium">{{ $v($universitas) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Email
                                Login</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-medium truncate">{{ $v($user->email) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Email Pribadi /
                                Kampus</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-medium truncate">{{ $v($user->email_pribadi) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Email
                                Korporat</span>
                            <span
                                class="font-body-md text-body-md text-primary font-semibold truncate">{{ $v($user->email_korporat) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Nomor Telepon /
                                WhatsApp</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-medium">{{ $v($user->nomor_telepon) }}</span>
                        </div>
                    </div>
                </section>

                {{-- Penempatan --}}
                <section class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-sm">
                        <div
                            class="w-9 h-9 rounded-lg bg-surface-container text-secondary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                        </div>
                        <div>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface">Penempatan &amp; Program PKL</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">Diatur oleh admin</p>
                        </div>
                    </div>

                    @if ($program)
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-label-md text-label-md text-on-surface font-semibold">
                                    @if ($program['elapsed'] === 0)
                                        Program belum dimulai
                                    @elseif ($program['remaining'] === 0)
                                        Masa PKL telah selesai
                                    @else
                                        Progres Masa PKL: {{ $program['remaining'] }} hari tersisa
                                    @endif
                                </span>
                                <span class="font-label-sm text-label-sm text-primary font-bold">{{ $program['percent'] }}%
                                    selesai</span>
                            </div>
                            <div class="w-full h-2.5 bg-surface-container-highest rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: {{ $program['percent'] }}%">
                                </div>
                            </div>
                            <div class="flex justify-between text-outline font-label-sm text-[11px]">
                                <span>Mulai: {{ $user->periode_mulai->translatedFormat('d M Y') }}</span>
                                <span>Selesai: {{ $user->periode_selesai->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1 md:col-span-2">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Kelompok /
                                Divisi</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-semibold">{{ $v($divisi) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Unit /
                                Rayon</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-medium">{{ $v($user->unit_rayon) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Posko</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-medium">{{ $v($user->posko) }}</span>
                        </div>
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex flex-col gap-1 md:col-span-2">
                            <span class="font-label-sm text-label-sm text-outline uppercase tracking-wider">Pembimbing
                                Lapangan (Mentor)</span>
                            <span
                                class="font-body-md text-body-md text-on-surface font-semibold">{{ $v($user->mentor) }}</span>
                        </div>
                    </div>
                </section>

                {{-- Keamanan --}}
                <section class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-sm">
                        <div class="w-9 h-9 rounded-lg bg-surface-container text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">security</span>
                        </div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Keamanan Akun</h3>
                    </div>

                    <div class="flex items-center justify-between p-space-sm rounded-lg bg-surface-container-low">
                        <div class="flex flex-col">
                            <span class="font-label-md text-label-md text-on-surface font-semibold">Sesi Saat Ini</span>
                            <span
                                class="font-body-sm text-body-sm text-on-surface-variant">{{ now()->translatedFormat('d M Y, H:i') }}
                                WIB</span>
                        </div>
                        <span
                            class="font-mono text-[11px] px-2 py-1 rounded bg-surface-container-highest text-on-surface-variant">IP:
                            {{ $ip }}</span>
                    </div>
                </section>
            </div>
        </div>
    </div>

    {{-- Dialog Foto Profil --}}
    <dialog id="photo-dialog" class="w-full max-w-md rounded-xl p-0 shadow-xl backdrop:bg-black/40">
        <div class="p-space-lg space-y-space-md">
            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Foto Profil</h2>

            <form method="POST" action="{{ route('profil.foto') }}" enctype="multipart/form-data"
                class="space-y-space-md">
                @csrf

                <div class="flex flex-col items-center gap-space-sm">
                    <div
                        class="w-32 h-32 rounded-2xl overflow-hidden bg-primary flex items-center justify-center shadow-md">
                        <img id="photo-preview" src="{{ $fotoUrl }}" alt="Pratinjau foto"
                            class="w-full h-full object-cover {{ $fotoUrl ? '' : 'hidden' }}">
                        <span id="photo-initials"
                            class="font-headline-xl text-headline-xl text-on-primary {{ $fotoUrl ? 'hidden' : '' }}">{{ $initials }}</span>
                    </div>

                    <label for="foto"
                        class="cursor-pointer px-space-md py-space-xs rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-lg text-label-lg flex items-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">upload</span>Pilih Foto
                    </label>
                    <input id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp"
                        class="sr-only" onchange="previewPhoto(this)">
                    <p class="font-body-sm text-body-sm text-on-surface-variant">JPG, PNG, atau WebP. Maksimal 2 MB.</p>

                    @if ($pf->has('foto'))
                        <p class="font-body-sm text-body-sm text-error">{{ $pf->first('foto') }}</p>
                    @endif
                    <p id="photo-error" class="hidden font-body-sm text-body-sm text-error"></p>
                </div>

                <div class="flex justify-end gap-space-sm">
                    <button type="button" onclick="this.closest('dialog').close()"
                        class="px-space-md py-space-xs rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg">Batal</button>
                    <button type="submit" id="photo-submit" disabled
                        class="px-space-md py-space-xs rounded-lg bg-primary text-on-primary font-label-lg text-label-lg disabled:opacity-40 disabled:cursor-not-allowed">Simpan
                        Foto</button>
                </div>
            </form>

            @if ($user->foto)
                <form method="POST" action="{{ route('profil.foto.hapus') }}"
                    onsubmit="return confirm('Hapus foto profil?')" class="pt-space-xs border-t border-surface-container">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="font-label-lg text-label-lg text-error hover:underline">Hapus foto
                        saat ini</button>
                </form>
            @endif
        </div>
    </dialog>

    {{-- Dialog Edit Profil --}}
    <dialog id="profile-dialog" class="w-full max-w-md rounded-xl p-0 shadow-xl backdrop:bg-black/40">
        <form method="POST" action="{{ route('profil.update') }}" class="p-space-lg space-y-space-md">
            @csrf
            @method('PUT')
            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Edit Profil</h2>

            <div class="space-y-1.5">
                <label for="nomor_telepon" class="font-label-md text-label-md text-on-surface font-medium">Nomor Telepon /
                    WhatsApp</label>
                <input id="nomor_telepon" name="nomor_telepon" type="text" maxlength="20" inputmode="tel"
                    value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
                    class="w-full h-11 px-space-sm rounded-lg bg-surface-container-low font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary">
                @if ($pe->has('nomor_telepon'))
                    <p class="font-body-sm text-body-sm text-error">{{ $pe->first('nomor_telepon') }}</p>
                @endif
            </div>

            <div class="space-y-1.5">
                <label for="email_pribadi" class="font-label-md text-label-md text-on-surface font-medium">Email Pribadi /
                    Kampus</label>
                <input id="email_pribadi" name="email_pribadi" type="email" maxlength="150"
                    value="{{ old('email_pribadi', $user->email_pribadi) }}"
                    class="w-full h-11 px-space-sm rounded-lg bg-surface-container-low font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary">
                @if ($pe->has('email_pribadi'))
                    <p class="font-body-sm text-body-sm text-error">{{ $pe->first('email_pribadi') }}</p>
                @endif
            </div>

            <div class="flex justify-end gap-space-sm">
                <button type="button" onclick="this.closest('dialog').close()"
                    class="px-space-md py-space-xs rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg">Batal</button>
                <button type="submit"
                    class="px-space-md py-space-xs rounded-lg bg-primary text-on-primary font-label-lg text-label-lg">Simpan</button>
            </div>
        </form>
    </dialog>

    {{-- Dialog Ubah Password --}}
    <dialog id="password-dialog" class="w-full max-w-md rounded-xl p-0 shadow-xl backdrop:bg-black/40">
        <form method="POST" action="{{ route('profil.password') }}" class="p-space-lg space-y-space-md">
            @csrf
            @method('PUT')
            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Ubah Password</h2>

            <div class="space-y-1.5">
                <label for="current_password" class="font-label-md text-label-md text-on-surface font-medium">Password
                    Saat Ini</label>
                <input id="current_password" name="current_password" type="password" required
                    autocomplete="current-password"
                    class="w-full h-11 px-space-sm rounded-lg bg-surface-container-low font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary">
                @if ($pw->has('current_password'))
                    <p class="font-body-sm text-body-sm text-error">{{ $pw->first('current_password') }}</p>
                @endif
            </div>

            <div class="space-y-1.5">
                <label for="password" class="font-label-md text-label-md text-on-surface font-medium">Password Baru (min.
                    8 karakter)</label>
                <input id="password" name="password" type="password" required minlength="8"
                    autocomplete="new-password"
                    class="w-full h-11 px-space-sm rounded-lg bg-surface-container-low font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary">
                @if ($pw->has('password'))
                    <p class="font-body-sm text-body-sm text-error">{{ $pw->first('password') }}</p>
                @endif
            </div>

            <div class="space-y-1.5">
                <label for="password_confirmation" class="font-label-md text-label-md text-on-surface font-medium">Ulangi
                    Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                    autocomplete="new-password"
                    class="w-full h-11 px-space-sm rounded-lg bg-surface-container-low font-body-md text-body-md border-0 focus:ring-2 focus:ring-primary">
            </div>

            <div class="flex justify-end gap-space-sm">
                <button type="button" onclick="this.closest('dialog').close()"
                    class="px-space-md py-space-xs rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg">Batal</button>
                <button type="submit"
                    class="px-space-md py-space-xs rounded-lg bg-primary text-on-primary font-label-lg text-label-lg">Ubah
                    Password</button>
            </div>
        </form>
    </dialog>
@endsection

@section('extra-scripts')
    <script>
        const MAX_PHOTO_BYTES = 2 * 1024 * 1024;
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
        let previewUrl = null;

        function previewPhoto(input) {
            const file = input.files[0];
            const errorEl = document.getElementById('photo-error');
            const submitBtn = document.getElementById('photo-submit');
            const img = document.getElementById('photo-preview');
            const initials = document.getElementById('photo-initials');

            errorEl.classList.add('hidden');
            submitBtn.disabled = true;
            if (!file) return;

            if (!ALLOWED_TYPES.includes(file.type)) {
                errorEl.textContent = 'Format foto harus JPG, PNG, atau WebP.';
                errorEl.classList.remove('hidden');
                input.value = '';
                return;
            }
            if (file.size > MAX_PHOTO_BYTES) {
                errorEl.textContent = 'Ukuran foto maksimal 2 MB.';
                errorEl.classList.remove('hidden');
                input.value = '';
                return;
            }

            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = URL.createObjectURL(file);
            img.src = previewUrl;
            img.classList.remove('hidden');
            initials.classList.add('hidden');
            submitBtn.disabled = false;
        }

        @if ($errors->hasBag('profile'))
            document.getElementById('profile-dialog').showModal();
        @elseif ($errors->hasBag('password'))
            document.getElementById('password-dialog').showModal();
        @elseif ($errors->hasBag('photo'))
            document.getElementById('photo-dialog').showModal();
        @endif
    </script>
@endsection
