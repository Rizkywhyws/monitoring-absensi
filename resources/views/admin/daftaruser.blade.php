@extends('layouts.app')

@section('title', 'Kelola User')
@section('breadcrumb', 'Admin Workspace › User Management')

@section('content')
    @php
        $periodeLabel = [
            'batch-1-2026' => 'Batch I (Feb - Jul 2026)',
            'batch-2-2026' => 'Batch II (Agu 2026 - Jan 2027)',
        ];
        $btnIcon = 'w-9 h-9 rounded-lg bg-surface-container-low text-on-surface-variant flex items-center justify-center hover:bg-surface-container-high transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
    @endphp

    <div class="flex flex-col w-full gap-space-lg">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
            <div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Kelola User</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                    Kelola akun, akses, dan penempatan peserta PKL PLN Icon Plus
                </p>
            </div>
            <a href="{{ route('admin.daftar-user.tambah') }}"
                class="flex items-center justify-center gap-space-xs px-space-md py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors shadow-sm shrink-0">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Tambah User</span>
            </a>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="px-space-md py-space-sm rounded-xl bg-primary-fixed text-on-primary-fixed font-body-md text-body-md">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="px-space-md py-space-sm rounded-xl bg-error-container text-on-error-container font-body-md text-body-md">
                <ul class="list-disc pl-space-md">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
            {{-- Tabs --}}
            <div class="flex gap-space-lg px-space-lg border-b border-surface-container">
                <div class="flex items-center gap-space-xs py-space-md border-b-4 border-primary text-primary font-label-lg text-label-lg font-bold">
                    Daftar User
                    <span class="px-2 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm">{{ $users->total() }}</span>
                </div>
                <div class="flex items-center gap-space-xs py-space-md text-on-surface-variant font-label-lg text-label-lg font-bold">
                    Kelompok / Divisi
                    <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">{{ $groups->count() }}</span>
                </div>
            </div>

            {{-- Filter --}}
            <div class="p-space-lg bg-surface-bright">
                <form action="{{ route('admin.daftar-user') }}" method="GET" class="flex flex-wrap items-center gap-space-sm">
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Cari nama, NIM, atau email..."
                        class="flex-1 min-w-[240px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:bg-surface-container transition-colors">
                    <select name="periode"
                        class="min-w-[200px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                        <option value="">Semua Periode</option>
                        @foreach ($periodeLabel as $val => $label)
                            <option value="{{ $val }}" @selected(request('periode') == $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-space-md py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.daftar-user') }}"
                        class="px-space-md py-3 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg font-semibold hover:bg-surface-container-high transition-colors">
                        Reset
                    </a>
                </form>

                <div class="mt-space-sm font-body-md text-body-md text-on-surface-variant flex flex-wrap items-center gap-space-xs">
                    <span>Menampilkan <strong class="text-on-surface">{{ $users->count() }}</strong> dari
                        <strong class="text-on-surface">{{ $users->total() }}</strong> pengguna</span>
                    @if (request('periode'))
                        <span class="px-space-xs py-1 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md">
                            Periode: {{ $periodeLabel[request('periode')] ?? request('periode') }}
                        </span>
                    @endif
                    @if (request('keyword'))
                        <span class="px-space-xs py-1 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md">
                            Pencarian: {{ request('keyword') }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-left text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
                            @foreach (['Nama', 'NIM', 'Email Kampus', 'Mentor', 'Kelompok / Divisi', 'Periode PKL', 'Status Akun', 'Aksi'] as $th)
                                <th class="px-space-md py-space-sm whitespace-nowrap">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            @php
                                $tanggalSelesai = $user->periode_selesai;
                                $periodeSudahSelesai = $tanggalSelesai && $tanggalSelesai->lt(now()->startOfDay());
                                $statusAktif = $user->status === 'active' && !$periodeSudahSelesai;
                            @endphp
                            <tr class="hover:bg-surface-bright transition-colors font-body-md text-body-md text-on-surface">
                                <td class="px-space-md py-space-sm border-b border-surface-container font-semibold">{{ $user->name }}</td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">{{ $user->nim }}</td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">{{ $user->email }}</td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">{{ $user->mentor ?? '-' }}</td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    @if ($user->group)
                                        <span class="inline-block px-space-xs py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed-variant font-label-md text-label-md font-bold whitespace-nowrap">{{ $user->group->name }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    @if ($user->periode_mulai)
                                        {{ $user->periode_mulai->locale('id')->translatedFormat('d M Y') }}
                                        @if ($user->periode_selesai)
                                            <br>- {{ $user->periode_selesai->locale('id')->translatedFormat('d M Y') }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    @if ($statusAktif)
                                        <span class="inline-flex items-center gap-1 px-space-xs py-1 rounded-full bg-primary-fixed text-on-primary-fixed font-label-md text-label-md font-bold">● Aktif</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-space-xs py-1 rounded-full bg-error-container text-on-error-container font-label-md text-label-md font-bold">● Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <div class="flex items-center gap-space-xs">
                                        <a href="{{ route('admin.daftar-user.edit', ['id' => $user->id]) }}" class="{{ $btnIcon }}" title="Edit">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>

                                        @if ($statusAktif)
                                            <form action="{{ route('admin.daftar-user.nonaktifkan', ['id' => $user->id]) }}" method="POST"
                                                onsubmit="return confirm({{ \Illuminate\Support\Js::from('Nonaktifkan akun ' . $user->name . '?') }})">
                                                @csrf @method('PUT')
                                                <button type="submit" class="{{ $btnIcon }}" title="Nonaktifkan">
                                                    <span class="material-symbols-outlined text-[18px]">power_settings_new</span>
                                                </button>
                                            </form>
                                        @elseif (!$periodeSudahSelesai)
                                            <form action="{{ route('admin.daftar-user.aktifkan', ['id' => $user->id]) }}" method="POST"
                                                onsubmit="return confirm({{ \Illuminate\Support\Js::from('Aktifkan kembali akun ' . $user->name . '?') }})">
                                                @csrf @method('PUT')
                                                <button type="submit" class="{{ $btnIcon }}" title="Aktifkan">
                                                    <span class="material-symbols-outlined text-[18px]">check</span>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="{{ $btnIcon }}" title="PKL sudah selesai" disabled>
                                                <span class="material-symbols-outlined text-[18px]">check</span>
                                            </button>
                                        @endif

                                        <form action="{{ route('admin.daftar-user.hapus', ['id' => $user->id]) }}" method="POST"
                                            onsubmit="return confirm({{ \Illuminate\Support\Js::from('Yakin ingin menghapus ' . $user->name . '? Data tidak dapat dikembalikan.') }})">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="{{ $btnIcon }} hover:!bg-error-container hover:!text-error" title="Hapus">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-space-md py-space-2xl text-center text-on-surface-variant font-body-md text-body-md">
                                    Tidak ada data user.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                @php
                    $pgBase = 'px-space-sm py-space-xs rounded-lg border border-outline-variant font-label-lg text-label-lg';
                @endphp
                <div class="px-space-lg py-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-md text-body-md text-on-surface-variant">
                    <div>Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }}</div>
                    <div class="flex gap-1.5">
                        @if ($users->onFirstPage())
                            <span class="{{ $pgBase }} opacity-50">‹</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="{{ $pgBase }} hover:bg-surface-container">‹</a>
                        @endif

                        @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span class="{{ $pgBase }} bg-primary text-on-primary border-primary">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $pgBase }} hover:bg-surface-container">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="{{ $pgBase }} hover:bg-surface-container">›</a>
                        @else
                            <span class="{{ $pgBase }} opacity-50">›</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection