@extends('layouts.app')

@section('title', 'Edit User - Sistem Manajemen PKL PLN Icon Plus')

@section('breadcrumb', 'Admin Workspace › Edit User')

@section('content')
    @php
        $inputClass =
            'w-full px-4 py-2.5 rounded-lg bg-surface-bright text-on-surface font-body-md text-body-md border border-outline-variant placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary focus:bg-surface-container-lowest transition-all';
        $labelClass = 'font-label-lg text-label-lg font-semibold text-on-surface mb-space-xs';
    @endphp

    <div class="flex flex-col w-full gap-space-lg">

        {{-- Header halaman --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
            <div class="flex flex-col min-w-0">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold mb-1">
                    Admin Workspace
                </span>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight leading-tight">Edit User</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                    Perbarui data peserta PKL PLN Icon Plus
                </p>
            </div>
            <a href="{{ route('admin.daftar-user') }}"
                class="shrink-0 inline-flex items-center justify-center gap-space-xs px-space-md py-2.5 rounded-xl bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-label-lg text-label-lg font-semibold shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                <span>Kembali</span>
            </a>
        </div>

        {{-- Notifikasi --}}
        @if ($errors->any())
            <div class="rounded-xl bg-error-container text-on-error-container px-space-md py-space-sm" role="alert">
                <div class="flex items-center gap-space-xs font-label-lg text-label-lg font-bold mb-1">
                    <span class="material-symbols-outlined text-[20px]">error</span>
                    <span>Data belum bisa disimpan</span>
                </div>
                <ul class="list-disc pl-space-lg font-body-md text-body-md">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="flex items-center gap-space-xs rounded-xl bg-primary-fixed/40 text-on-primary-fixed-variant px-space-md py-space-sm font-body-md text-body-md"
                role="status">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-space-xs rounded-xl bg-error-container text-on-error-container px-space-md py-space-sm font-body-md text-body-md"
                role="alert">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-lg">
            <div
                class="flex items-center gap-space-xs rounded-lg bg-surface-container-low px-space-md py-space-sm text-on-surface font-body-md text-body-md">
                <span class="material-symbols-outlined text-primary text-[20px]">badge</span>
                <span><strong class="font-semibold">Data peserta:</strong> {{ $user->name }}</span>
            </div>

            <form action="{{ route('admin.daftar-user.update', ['id' => $user->id]) }}" method="POST"
                class="flex flex-col gap-space-lg">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
                    <div class="flex flex-col">
                        <label for="name" class="{{ $labelClass }}">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="{{ $inputClass }}" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="nim" class="{{ $labelClass }}">NIM</label>
                        <input type="text" id="nim" name="nim" value="{{ old('nim', $user->nim) }}"
                            class="{{ $inputClass }}" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="email" class="{{ $labelClass }}">Email Kampus</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="{{ $inputClass }}" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="mentor" class="{{ $labelClass }}">Nama Mentor</label>
                        <input type="text" id="mentor" name="mentor" value="{{ old('mentor', $user->mentor) }}"
                            placeholder="Masukkan nama mentor" class="{{ $inputClass }}">
                    </div>

                    <div class="flex flex-col">
                        <label for="group_name" class="{{ $labelClass }}">Kelompok / Divisi</label>
                        <input type="text" id="group_name" name="group_name"
                            value="{{ old('group_name', $user->group?->name) }}"
                            placeholder="Masukkan kelompok / divisi" class="{{ $inputClass }}">
                    </div>

                    <div class="flex flex-col">
                        <label for="status" class="{{ $labelClass }}">Status Akun</label>
                        <select id="status" name="status" class="{{ $inputClass }}" required>
                            <option value="active" @selected(old('status', $user->status) === 'active')>Aktif</option>
                            <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex flex-col">
                        <label for="periode_mulai" class="{{ $labelClass }}">Tanggal Mulai PKL</label>
                        <input type="date" id="periode_mulai" name="periode_mulai"
                            value="{{ old('periode_mulai', $user->periode_mulai?->format('Y-m-d')) }}"
                            class="{{ $inputClass }}">
                    </div>

                    <div class="flex flex-col">
                        <label for="periode_selesai" class="{{ $labelClass }}">Tanggal Selesai PKL</label>
                        <input type="date" id="periode_selesai" name="periode_selesai"
                            value="{{ old('periode_selesai', $user->periode_selesai?->format('Y-m-d')) }}"
                            class="{{ $inputClass }}">
                    </div>

                    <div class="flex flex-col md:col-span-2 md:max-w-[calc(50%-0.75rem)]">
                        <label for="role_display" class="{{ $labelClass }}">Role</label>
                        <input type="text" id="role_display" value="Peserta PKL" disabled
                            class="{{ $inputClass }} opacity-70 cursor-not-allowed">
                    </div>

                    <div class="flex flex-col md:col-span-2">
                        <label for="password" class="{{ $labelClass }}">Password Baru</label>
                        <div class="relative flex items-center">
                            <input type="password" id="password" name="password"
                                placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6"
                                maxlength="100" autocomplete="new-password" class="{{ $inputClass }} pr-12">
                            <button type="button" id="togglePassword" aria-label="Tampilkan password"
                                class="absolute right-2 w-9 h-9 flex items-center justify-center rounded-lg text-outline hover:text-on-surface hover:bg-surface-container transition-colors">
                                <span class="material-symbols-outlined text-[20px]" id="togglePasswordIcon">visibility</span>
                            </button>
                        </div>
                        <p class="mt-space-xs font-body-sm text-body-sm text-on-surface-variant">
                            Kosongkan jika password lama tetap digunakan. Minimal 6 karakter jika ingin mengganti password.
                        </p>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:justify-end gap-space-sm pt-space-lg border-t border-surface-container">
                    <a href="{{ route('admin.daftar-user') }}"
                        class="inline-flex items-center justify-center px-space-lg py-2.5 rounded-lg bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface font-label-lg text-label-lg font-semibold transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-space-xs px-space-lg py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg font-semibold shadow-md hover:shadow-lg transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('extra-scripts')
    <script>
        (function() {
            const input = document.getElementById('password');
            const btn = document.getElementById('togglePassword');
            const icon = document.getElementById('togglePasswordIcon');

            btn?.addEventListener('click', () => {
                const hidden = input.type === 'password';
                input.type = hidden ? 'text' : 'password';
                icon.textContent = hidden ? 'visibility_off' : 'visibility';
                btn.setAttribute('aria-label', hidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        })();
    </script>
@endsection