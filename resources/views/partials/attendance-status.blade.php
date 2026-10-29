@php
    $cls = match ($attendance->status) {
        'hadir' => 'bg-primary-fixed text-on-primary-fixed',
        'terlambat' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
        'izin', 'sakit' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
        default => 'bg-error-container text-on-error-container',
    };
@endphp
<span class="inline-flex items-center gap-1 px-space-xs py-1 rounded-full {{ $cls }} font-label-md text-label-md font-bold whitespace-nowrap">
    ● {{ $attendance->status_label }}
</span>