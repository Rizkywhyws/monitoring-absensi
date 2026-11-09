@extends('layouts.app')

@section('title', 'Profil Admin')
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
            <p class="font-label-sm text-label-sm uppercase tracking-wider text-primary">Administrator</p>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">{{ $user->name }}</h1>
        </div>
    </div>

    <div class="grid gap-space-lg xl:grid-cols-3">

        <form method="POST" action="{{ route('admin.profil.update') }}"
              class="rounded-xl bg-surface-container-lowest p-space-lg shadow-sm xl:col-span-2">
            @csrf
            @method('PUT')

            <h2 class="font-headline-sm text-headline-sm text-on-surface">Data pribadi</h2>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                Perbarui data kamu. Kolom bertanda <span class="text-error">*</span> wajib diisi.
            </p>

            <div class="mt-space-md grid gap-space-md sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="{{ $label }}">Nama lengkap <span class="text-error">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                           class="{{ $input }}" autocomplete="name" maxlength="100" required>
                    @error('name')
                        <p class="mt-1 font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="nomor_telepon" class="{{ $label }}">Nomor telepon / WhatsApp</label>
                    <input id="nomor_telepon" name="nomor_telepon" type="tel" inputmode="tel" maxlength="20"
                           value="{{ old('nomor_telepon', $user->nomor_telepon) }}"
                           class="{{ $input }}" placeholder="Contoh: 081234567890">
                    @error('nomor_telepon')
                        <p class="mt-1 font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-space-lg flex justify-end">
                <button type="submit"
                        class="rounded-lg bg-primary px-space-lg py-2.5 font-label-lg text-label-lg text-on-primary transition-colors hover:bg-primary-container">
                    Simpan perubahan
                </button>
            </div>
        </form>

        <div class="h-fit rounded-xl bg-surface-container-lowest p-space-lg shadow-sm">
            <h2 class="font-headline-sm text-headline-sm text-on-surface">Identitas akun</h2>
            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                Data ini tidak bisa diubah dari halaman profil.
            </p>

            <dl class="mt-space-md divide-y divide-surface-container">
                @foreach ($official as $key => $value)
                    <div class="py-space-sm">
                        <dt class="font-label-md text-label-md text-on-surface-variant">{{ $key }}</dt>
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