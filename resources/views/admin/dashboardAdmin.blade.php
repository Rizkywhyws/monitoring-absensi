@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('breadcrumb', 'Dashboard')

@section('content')
    @php
        $groups = $groups ?? [
            ['Install', 5, 'Bambang H. (Team Lead)', 'bg-primary', 'text-primary'],
            ['PM OSP', 4, 'Rizal Wahyudi, S.T.', 'bg-secondary', 'text-secondary'],
            ['Inventory', 3, 'Dewi Anggraini (Log)', 'bg-tertiary', 'text-tertiary'],
            ['Support', 2, 'Eko Sulistyo (NOC)', 'bg-secondary-container', 'text-secondary'],
        ];
        $groupTotal = max(array_sum(array_column($groups, 1)), 1);

        $ticketProgress = $ticketProgress ?? 8;
        $ticketDone = $ticketDone ?? 24;
        $ticketTotal = max($ticketProgress + $ticketDone, 1);
        $donePct = round(($ticketDone / $ticketTotal) * 100);
        $progressPct = 100 - $donePct;

        $activities = $activities ?? [
            ['RA', 'Refangga Ardiansah', 'Install', '15:12', 'Menyelesaikan Ticket TKT-00025 (Pemasangan Patchcord ODF Gandul)', 'bg-primary text-on-primary'],
            ['DP', 'Dimas Pratama', 'PM OSP', '07:54', 'Melakukan Check-in Absensi (32m dari KP Jember)', 'bg-secondary text-on-secondary'],
            ['SN', 'Siti Nurhaliza', 'Inventory', '14:30', 'Submit Daily Activity Logbook (Penyusunan BAST Gudang Material Icon+)', 'bg-tertiary text-on-tertiary'],
            ['AF', 'Ahmad Fauzi', 'Support', '13:45', 'Update Progres Ticket TKT-00026 (75% Splicing Joint Closure)', 'bg-secondary-container text-on-secondary-container'],
        ];
    @endphp

    <div class="flex flex-col w-full">
        {{-- Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md mb-space-xl">
            <div class="flex flex-col min-w-0">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold mb-1">PLN Icon Plus • Command Center</span>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight leading-tight">Dashboard</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                    Pantau aktivitas peserta PKL, absensi lapangan, dan progres tiket secara real-time.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-space-sm shrink-0">
                <div class="flex items-center gap-space-xs px-space-md py-2 rounded-xl bg-surface-container-lowest shadow-sm text-on-surface">
                    <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                    <span class="font-label-lg text-label-lg font-medium">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <div class="flex items-center gap-space-xs px-space-md py-2 rounded-xl bg-surface-container-lowest shadow-sm text-on-surface">
                    <span class="material-symbols-outlined text-secondary text-[20px]">hub</span>
                    <span class="font-label-lg text-label-lg font-semibold">KP Jember - SBU Jawa Timur</span>
                    <span class="material-symbols-outlined text-on-surface-variant text-[18px]">arrow_drop_down</span>
                </div>
                <button type="button"
                    class="flex items-center gap-space-xs px-space-md py-2 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg font-semibold hover:bg-primary-container transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                    <span>Export Quick Report</span>
                </button>
            </div>
        </div>

        {{-- KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-sm mb-space-xl">
            @foreach ([
                ['Total User', 14, 'Peserta Terdaftar', 'group', 'bg-surface-container-high text-primary', 'text-on-surface'],
                ['User Aktif', 12, 'Sedang Aktif PKL', 'person_check', 'bg-primary-fixed text-on-primary-fixed', 'text-primary'],
                ['Daily Activity', 11, 'Logbook Masuk', 'assignment_turned_in', 'bg-tertiary-fixed text-tertiary', 'text-tertiary'],
            ] as $k)
                <div class="p-space-md rounded-xl bg-surface-container-lowest shadow-sm flex flex-col justify-between transition-transform hover:-translate-y-0.5 duration-200">
                    <div class="flex items-center justify-between mb-space-xs">
                        <span class="font-label-md text-label-md text-on-surface-variant font-medium">{{ $k[0] }}</span>
                        <div class="w-8 h-8 rounded-lg {{ $k[4] }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px]">{{ $k[3] }}</span>
                        </div>
                    </div>
                    <div class="font-stat-counter text-stat-counter {{ $k[5] }} font-bold">{{ $k[1] }}</div>
                    <div class="font-body-sm text-body-sm text-on-surface-variant truncate">{{ $k[2] }}</div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-space-xl">
            {{-- Distribusi Peserta --}}
            <div class="p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between mb-space-md">
                    <div class="flex flex-col">
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Distribusi Peserta Berdasarkan Divisi</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Sebaran {{ $groupTotal }} peserta PKL dan mentor pembimbing lapangan</p>
                    </div>
                    <span class="px-space-sm py-1 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm font-semibold">{{ count($groups) }} Kelompok Kerja</span>
                </div>

                <div class="w-full h-4 rounded-full bg-surface-container-low flex gap-1 overflow-hidden p-0.5 mb-space-lg shadow-inner">
                    @foreach ($groups as $g)
                        <div class="h-full rounded-full {{ $g[3] }}" style="width: {{ round(($g[1] / $groupTotal) * 100, 1) }}%" title="{{ $g[0] }}"></div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-space-md">
                    @foreach ($groups as $g)
                        <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-space-sm">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full {{ $g[3] }}"></span>
                                    <span class="font-label-lg text-label-lg font-bold text-on-surface">{{ $g[0] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-surface-container-lowest {{ $g[4] }} font-label-sm text-label-sm font-bold">{{ round(($g[1] / $groupTotal) * 100, 1) }}%</span>
                            </div>
                            <div class="flex items-baseline gap-1.5 mb-2">
                                <span class="font-headline-md text-headline-md font-bold text-on-surface">{{ $g[1] }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">Peserta</span>
                            </div>
                            <div class="mt-auto bg-surface-container-lowest/60 p-2 rounded-lg">
                                <div class="font-label-sm text-label-sm text-on-surface-variant">Mentor Lapangan:</div>
                                <div class="font-label-md text-label-md font-semibold text-on-surface truncate">{{ $g[2] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Attendance & Ticket Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
                {{-- Attendance Overview --}}
                <div class="p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-space-md">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-primary text-[22px]">co_present</span>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Attendance Overview</h3>
                        </div>
                        <span class="font-label-sm text-label-sm px-2 py-1 rounded-md bg-surface-container-high text-on-surface-variant font-semibold">Live GPS Gate</span>
                    </div>

                    <div class="flex items-center gap-space-lg mb-space-lg bg-surface-container-low p-space-md rounded-xl">
                        <div class="relative w-20 h-20 shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-surface-container-high" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                                <path class="text-primary" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="83.3, 100" stroke-linecap="round" stroke-width="3.5"></path>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="font-headline-sm text-headline-sm font-bold text-on-surface">83%</span>
                            </div>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-label-md text-label-md font-bold text-on-surface">Tingkat Kehadiran Harian</span>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">10 dari 12 siswa aktif telah check-in di radius kantor.</p>
                            <span class="font-label-sm text-label-sm text-primary font-semibold mt-1">Status Geofence: 250m Radius Valid</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-space-xs">
                        @foreach ([
                            ['Hadir Tepat Waktu', '10 Peserta', 'bg-primary', 'bg-primary-fixed text-on-primary-fixed'],
                            ['Terlambat (08:14 WIB)', '1 Peserta', 'bg-tertiary', 'bg-tertiary-fixed text-on-tertiary-fixed'],
                            ['Belum Absen (Cut-off Alert)', '1 Peserta', 'bg-error', 'bg-error-container text-on-error-container'],
                        ] as $a)
                            <div class="flex items-center justify-between p-2 rounded-lg bg-surface-container-lowest">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $a[2] }}"></span>
                                    <span class="font-label-md text-label-md text-on-surface">{{ $a[0] }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full {{ $a[3] }} font-label-sm text-label-sm font-bold">{{ $a[1] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Ticket Overview: hanya Progress & Done (akumulasi sejak tiket pertama) --}}
                <div class="p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm flex flex-col justify-between">
                    <div class="flex flex-col mb-space-md">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-secondary text-[22px]">confirmation_number</span>
                            <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Ticket Overview</h3>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Akumulasi sejak tiket pertama dibuat</p>
                    </div>

                    <div class="mb-space-md bg-surface-container-low p-space-md rounded-xl">
                        <div class="flex items-center justify-between text-on-surface mb-2">
                            <span class="font-label-sm text-label-sm font-bold">Total Tiket</span>
                            <span class="font-label-sm text-label-sm text-secondary font-semibold">{{ $ticketTotal }} Tiket</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-surface-container-high flex overflow-hidden gap-0.5">
                            <div class="h-full bg-primary" style="width: {{ $donePct }}%" title="Done: {{ $ticketDone }}"></div>
                            <div class="h-full bg-secondary-container" style="width: {{ $progressPct }}%" title="On Progress: {{ $ticketProgress }}"></div>
                        </div>
                        <div class="flex justify-between text-on-surface-variant font-label-sm text-label-sm mt-2">
                            <span>Done ({{ $donePct }}%)</span>
                            <span>Progress ({{ $progressPct }}%)</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-space-sm text-center">
                        <div class="p-space-sm rounded-lg bg-secondary-fixed">
                            <div class="font-label-sm text-label-sm text-on-secondary-fixed-variant">Progress</div>
                            <div class="font-stat-counter text-stat-counter font-bold text-secondary">{{ $ticketProgress }}</div>
                        </div>
                        <div class="p-space-sm rounded-lg bg-primary-fixed">
                            <div class="font-label-sm text-label-sm text-on-primary-fixed-variant">Done</div>
                            <div class="font-stat-counter text-stat-counter font-bold text-primary">{{ $ticketDone }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Activity Feed: aktivitas saja, tanpa badge status --}}
            <div class="p-space-lg rounded-2xl bg-surface-container-lowest shadow-sm">
                <div class="flex items-center justify-between mb-space-md">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-primary text-[22px]">stream</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Live Activity Feed</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Sinkronisasi Realtime</span>
                    </div>
                </div>
                <div class="flex flex-col gap-space-sm">
                    @foreach ($activities as $a)
                        <div class="p-space-md rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors flex items-start gap-space-sm min-w-0">
                            <div class="w-10 h-10 rounded-full {{ $a[5] }} flex items-center justify-center font-label-lg font-bold shrink-0">{{ $a[0] }}</div>
                            <div class="flex flex-col min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-label-lg text-label-lg font-bold text-on-surface">{{ $a[1] }}</span>
                                    <span class="px-2 py-0.5 rounded-full bg-surface-container-highest text-on-surface-variant font-label-sm text-label-sm">Kelompok: {{ $a[2] }}</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">• {{ $a[3] }} WIB</span>
                                </div>
                                <p class="font-body-md text-body-md text-on-surface mt-0.5">{{ $a[4] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection