@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('breadcrumb', 'Admin Workspace › Riwayat Absensi')

@section('content')
    <div class="flex flex-col w-full gap-space-lg">
        <div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Riwayat Absensi</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Rekap kehadiran peserta PKL PLN Icon Plus
                ({{ \Carbon\Carbon::parse($dari)->locale('id')->translatedFormat('d M Y') }} –
                {{ \Carbon\Carbon::parse($sampai)->locale('id')->translatedFormat('d M Y') }})
            </p>
        </div>

        @if ($errors->any())
            <div class="px-space-md py-space-sm rounded-xl bg-error-container text-on-error-container font-body-md text-body-md">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Ringkasan --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-space-md">
            <div class="rounded-2xl bg-surface-container-lowest shadow-sm p-space-md">
                <p class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">Total</p>
                <p class="font-stat-counter text-stat-counter text-on-surface">{{ $rekap->sum() }}</p>
            </div>
            @foreach ($statusOptions as $key => $label)
                <div class="rounded-2xl bg-surface-container-lowest shadow-sm p-space-md">
                    <p class="font-label-md text-label-md uppercase tracking-wider text-on-surface-variant">{{ $label }}</p>
                    <p class="font-stat-counter text-stat-counter text-on-surface">{{ $rekap[$key] ?? 0 }}</p>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl bg-surface-container-lowest shadow-sm overflow-hidden">
            {{-- Filter --}}
            <div class="p-space-lg bg-surface-bright">
                <form action="{{ route('admin.riwayat-absensi') }}" method="GET"
                    class="flex flex-wrap items-center gap-space-sm">
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                        placeholder="Cari nama atau NIM..."
                        class="flex-1 min-w-[220px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none focus:bg-surface-container transition-colors">
                    <input type="date" name="dari" value="{{ $dari }}"
                        class="bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                    <input type="date" name="sampai" value="{{ $sampai }}"
                        class="bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                    <select name="status"
                        class="min-w-[160px] bg-surface-container-low rounded-xl px-space-md py-3 font-body-md text-body-md text-on-surface outline-none">
                        <option value="">Semua Status</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="px-space-md py-3 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors">
                        Filter
                    </button>
                    <a href="{{ route('admin.riwayat-absensi') }}"
                        class="px-space-md py-3 rounded-xl bg-surface-container text-on-surface font-label-lg text-label-lg font-semibold hover:bg-surface-container-high transition-colors">
                        Reset
                    </a>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-left text-on-surface-variant font-label-md text-label-md uppercase tracking-wider">
                            @foreach (['Peserta', 'Tanggal', 'Check-in', 'Check-out', 'Durasi', 'Status'] as $th)
                                <th class="px-space-md py-space-sm whitespace-nowrap">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $a)
                            <tr class="hover:bg-surface-bright transition-colors font-body-md text-body-md text-on-surface">
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    <div class="font-semibold">{{ $a->user?->name ?? '-' }}</div>
                                    <div class="font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $a->user?->nim }}
                                        @if ($a->user?->group)
                                            · {{ $a->user->group->name }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    {{ $a->attendance_date->locale('id')->translatedFormat('l, d M Y') }}
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    @include('partials.attendance-point', [
                                        'time' => $a->check_in,
                                        'distance' => $a->check_in_distance,
                                        'photo' => $a->check_in_photo_url,
                                        'lat' => $a->check_in_latitude,
                                        'lng' => $a->check_in_longitude,
                                    ])
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    @include('partials.attendance-point', [
                                        'time' => $a->check_out,
                                        'distance' => $a->check_out_distance,
                                        'photo' => $a->check_out_photo_url,
                                        'lat' => $a->check_out_latitude,
                                        'lng' => $a->check_out_longitude,
                                    ])
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container whitespace-nowrap">
                                    {{ $a->work_duration ?? '-' }}
                                </td>
                                <td class="px-space-md py-space-sm border-b border-surface-container">
                                    @include('partials.attendance-status', ['attendance' => $a])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-space-md py-space-2xl text-center text-on-surface-variant font-body-md text-body-md">
                                    Tidak ada data absensi pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.pagination', ['paginator' => $attendances])
        </div>
    </div>
@endsection