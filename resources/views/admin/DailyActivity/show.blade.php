@extends('layouts.app') {{-- ganti dengan nama layout Anda --}}

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.daily-activity..index', ['date' => $date->toDateString()]) }}"
       class="text-sm text-emerald-800 hover:underline">&lsaquo; Kembali ke rekap</a>

    <div>
        <h1 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h1>
        <p class="text-sm text-slate-500">
            NIM {{ $user->nim }} · {{ $user->university }} · {{ $date->translatedFormat('l, d F Y') }}
        </p>
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    {{-- Tiket selesai --}}
    <div class="rounded-xl bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-slate-900">Tiket selesai ({{ $tickets->count() }})</h2>
        <ul class="mt-3 divide-y divide-slate-100 text-sm">
            @forelse ($tickets as $ticket)
                <li class="flex items-start justify-between gap-4 py-3">
                    <div>
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-xs font-semibold">{{ $ticket->code }}</span>
                        <p class="mt-1 text-slate-700">{{ $ticket->title }}</p>
                    </div>
                    <p class="whitespace-nowrap text-xs text-slate-500">
                        {{ \Carbon\Carbon::parse($ticket->started_at)->format('H:i') }}
                        – {{ \Carbon\Carbon::parse($ticket->completed_at)->format('H:i') }}
                    </p>
                </li>
            @empty
                <li class="py-3 text-slate-500">Tidak ada tiket selesai pada tanggal ini.</li>
            @endforelse
        </ul>
    </div>

    {{-- Catatan harian --}}
    <form method="POST" action="{{ route('admin.daily-activity..store', $user) }}" class="rounded-xl bg-white p-5 shadow-sm">
        @csrf
        <input type="hidden" name="activity_date" value="{{ $date->toDateString() }}">

        <label for="description" class="font-semibold text-slate-900">Catatan harian</label>
        <textarea id="description" name="description" rows="6"
                  class="mt-2 w-full rounded-lg border-slate-200 text-sm"
                  placeholder="Ringkas pekerjaan, kendala, dan hasil hari ini…">{{ old('description', $activity?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="mt-3 rounded-md bg-emerald-800 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-900">
            Simpan catatan
        </button>
    </form>
</div>
@endsection