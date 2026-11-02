@extends('layouts.app') 

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-3xl font-bold tracking-tight text-slate-900">
            Monitoring Daily Activity &amp; Logbook Mahasiswa
        </h1>
        <p class="mt-1 max-w-2xl text-sm text-slate-600">
            Pantau tiket teknis yang diselesaikan Siswa setiap hari, jam pengerjaan lapangan, dan kelengkapan logbook.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Total Siswa aktif</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['total_students'] }} <span class="text-base font-medium text-slate-500">Peserta</span></p>
            <p class="mt-1 text-xs text-emerald-700">{{ $stats['active_students'] }} mengerjakan tiket pada tanggal ini</p>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Tiket selesai</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['total_tickets'] }} <span class="text-base font-medium text-slate-500">Tiket</span></p>
            <p class="mt-1 text-xs text-slate-500">{{ $date->translatedFormat('l, d M Y') }}</p>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Rata-rata durasi kerja</p>
            <p class="mt-1 text-3xl font-bold text-slate-900">{{ $stats['avg_hours'] }} <span class="text-base font-medium text-slate-500">Jam / hari</span></p>
            <p class="mt-1 text-xs text-emerald-700">Standar operasional 8 jam kerja</p>
        </div>

        <div class="rounded-xl bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold text-slate-500">Belum isi logbook</p>
            <p class="mt-1 text-3xl font-bold {{ $stats['logbook_missing'] ? 'text-red-700' : 'text-slate-900' }}">
                {{ $stats['logbook_missing'] }} <span class="text-base font-medium">Mahasiswa</span>
            </p>
            <p class="mt-1 text-xs text-slate-500">Punya tiket selesai, tetapi catatan harian masih kosong</p>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="flex flex-wrap items-center gap-3 rounded-xl bg-white p-4 shadow-sm">
        <div class="flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1.5">
            <a href="{{ request()->fullUrlWithQuery(['date' => $prevDate, 'page' => null]) }}"
               class="px-2 text-slate-600 hover:text-slate-900" aria-label="Hari sebelumnya">&lsaquo;</a>
            <input type="date" name="date" value="{{ $date->toDateString() }}"
                   onchange="this.form.submit()"
                   class="border-0 bg-transparent text-sm font-medium text-slate-800 focus:ring-0">
            <a href="{{ request()->fullUrlWithQuery(['date' => $nextDate, 'page' => null]) }}"
               class="px-2 text-slate-600 hover:text-slate-900" aria-label="Hari berikutnya">&rsaquo;</a>
        </div>

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama, NIM, atau kode tiket…"
               class="min-w-[220px] flex-1 rounded-lg border-slate-200 bg-slate-50 text-sm">

        <select name="division" onchange="this.form.submit()" class="rounded-lg border-slate-200 bg-slate-50 text-sm">
            <option value="">Semua divisi</option>
            @foreach ($divisions as $division)
                <option value="{{ $division }}" @selected(request('division') === $division)>{{ $division }}</option>
            @endforeach
        </select>

        <select name="logbook" onchange="this.form.submit()" class="rounded-lg border-slate-200 bg-slate-50 text-sm">
            <option value="">Semua logbook</option>
            <option value="sudah" @selected(request('logbook') === 'sudah')>Sudah diisi</option>
            <option value="belum" @selected(request('logbook') === 'belum')>Belum diisi</option>
        </select>

        @if (request()->hasAny(['search', 'division', 'logbook']))
            <a href="{{ route('admin.daily-activity.index', ['date' => $date->toDateString()]) }}"
               class="text-sm text-slate-500 underline">Reset</a>
        @endif
    </form>

    {{-- Tabel --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">
        <div class="flex items-center justify-between bg-slate-50 px-5 py-4">
            <h2 class="font-semibold text-slate-900">Daftar rekap aktivitas &amp; logbook lapangan</h2>
            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-medium text-slate-700">
                {{ $students->total() }} mahasiswa
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Divisi &amp; posko</th>
                        <th class="px-5 py-3">Tiket selesai</th>
                        <th class="px-5 py-3">Jam kerja</th>
                        <th class="px-5 py-3">Logbook</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse ($students as $student)
                    @php
                        $activity = $student->dailyActivities->first();
                        $hours = intdiv($student->work_minutes, 60);
                        $mins  = $student->work_minutes % 60;
                    @endphp
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=047857&color=fff"
                                     alt="" class="h-10 w-10 rounded-full">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500">NIM: {{ $student->nim }}</p>
                                    <p class="text-xs font-medium text-emerald-800">{{ $student->university }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-800">{{ $student->division }}</p>
                            <p class="text-xs text-slate-500">{{ $student->posko }}</p>
                        </td>

                        <td class="px-5 py-4">
                            <ul class="space-y-2">
                                @foreach ($student->tickets as $ticket)
                                    <li>
                                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-xs font-semibold">{{ $ticket->code }}</span>
                                        @if ($ticket->priority === 'high')
                                            <span class="ml-1 rounded bg-red-100 px-1.5 py-0.5 text-xs font-semibold text-red-700">High</span>
                                        @endif
                                        <p class="mt-0.5 text-slate-700">{{ $ticket->title }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap">
                            <p class="font-semibold text-slate-900">{{ $hours }}j {{ str_pad($mins, 2, '0', STR_PAD_LEFT) }}m</p>
                            @if ($student->work_start && $student->work_end)
                                <p class="text-xs text-slate-500">
                                    {{ $student->work_start->format('H:i') }} – {{ $student->work_end->format('H:i') }} WIB
                                </p>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @if ($activity)
                                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-800">Sudah diisi</span>
                                <p class="mt-1 line-clamp-2 max-w-[220px] text-xs text-slate-500">{{ $activity->description }}</p>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">Belum diisi</span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.daily-activity.show', [$student, 'date' => $date->toDateString()]) }}"
                               class="inline-block rounded-md bg-emerald-800 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-900">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            Tidak ada tiket selesai pada {{ $date->translatedFormat('d F Y') }}.
                            Coba pilih tanggal lain.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-slate-50 px-5 py-3">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection