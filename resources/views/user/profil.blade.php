
@extends('layouts.app')

@section('title', 'Profil Saya')
@section('breadcrumb', 'Profil')

@section('content')
@php
    $initials = \Illuminate\Support\Str::of($user->name)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($w) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($w, 0, 1)))
        ->implode('');

    $input = 'w-full rounded-lg border border-outline-variant bg-surface-container-low px-space-sm py-2 font-body-md text-body-md text-on-surface placeholder:text-outline focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30';
    $label = 'mb-1 block font-label-md text-label-md text-on-surface-variant';
@endphp

<div class="flex flex-col gap-space-lg">

    {{-- Judul --}}
    <div class="flex items-center gap-space-md">
        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-primary font-headline-md text-headline-md text-on-primary">
            {{ $initials }}
        </span>

        <div>
            <p class="font-label-sm text-label-sm uppercase tracking-wider text-primary">
                Peserta PKL
            </p>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">
                {{ $user->name }}
            </h1>
        </div>
    </div>

    <div class="grid gap-space-lg xl:grid-cols-3">

        {{-- Form data pribadi --}}
        <form method="POST" action="{{ route('profil.update') }}"
              class="rounded-xl bg-surface-container-lowest p-space-lg shadow-sm xl:col-span-2">

            @csrf
            @method('PUT')

            <h2 class="font-headline-sm text-headline-sm text-on-surface">
                Data pribadi
            </h2>

            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                Isi dan perbarui data kamu. Kolom bertanda
                <span class="text-error">*</span> wajib diisi.
            </p>

            <div class="mt-space-md grid gap-space-md sm:grid-cols-2">

                {{-- Nama lengkap --}}
                <div class="sm:col-span-2">
                    <label for="name" class="{{ $label }}">
                        Nama lengkap <span class="text-error">*</span>
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        class="{{ $input }}"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <p class="mt-1 font-body-sm text-body-sm text-error">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- NIM / NISN --}}
                <div>
                    <label for="nim" class="{{ $label }}">
                        NIM / NISN <span class="text-error">*</span>
                    </label>

                    <input
                        id="nim"
                        name="nim"
                        type="text"
                        value="{{ old('nim', $user->nim) }}"
                        class="{{ $input }}"
                        placeholder="Contoh: 22053460012"
                        required
                    >

                    @error('nim')
                        <p class="mt-1 font-body-sm text-body-sm text-error">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Asal universitas / sekolah --}}
                <div>
                    <label for="asal_universitas" class="{{ $label }}">
                        Asal universitas / sekolah
                    </label>

                    <input
                        id="asal_universitas"
                        name="asal_universitas"
                        type="text"
                        value="{{ old('asal_universitas', $user->asal_universitas) }}"
                        class="{{ $input }}"
                        placeholder="Contoh: Politeknik Negeri Jember"
                    >

                    @error('asal_universitas')
                        <p class="mt-1 font-body-sm text-body-sm text-error">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nomor HP --}}
                <div>
                    <label for="no_hp" class="{{ $label }}">
                        Nomor HP
                    </label>

                    <input
                        id="no_hp"
                        name="no_hp"
                        type="tel"
                        inputmode="tel"
                        value="{{ old('no_hp', $user->no_hp) }}"
                        class="{{ $input }}"
                        placeholder="Contoh: 081234567890"
                    >

                    @error('no_hp')
                        <p class="mt-1 font-body-sm text-body-sm text-error">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            <div class="mt-space-lg flex justify-end">
                <button
                    type="submit"
                    class="rounded-lg bg-primary px-space-lg py-2.5 font-label-lg text-label-lg text-on-primary transition-colors hover:bg-primary-container"
                >
                    Simpan perubahan
                </button>
            </div>
        </form>

        {{-- Data resmi (read-only) --}}
        <div class="h-fit rounded-xl bg-surface-container-lowest p-space-lg shadow-sm">

            <h2 class="font-headline-sm text-headline-sm text-on-surface">
                Identitas resmi
            </h2>

            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                Diatur oleh admin. Hubungi admin kalau ada yang keliru.
            </p>

            <dl class="mt-space-md divide-y divide-surface-container">
                @foreach ($official as $name => $value)
                    <div class="py-space-sm">
                        <dt class="font-label-md text-label-md text-on-surface-variant">
                            {{ $name }}
                        </dt>

                        <dd class="mt-0.5 font-body-md text-body-md text-on-surface break-words">
                            {{ $value ?: '-' }}
                        </dd>
                    </div>
                @endforeach
            </dl>

        </div>
    </div>
</div>
@endsection
