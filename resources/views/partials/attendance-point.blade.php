@if ($time)
    <div class="flex items-center gap-space-sm">
        @if ($photo)
            <a href="{{ $photo }}" target="_blank" rel="noopener" class="shrink-0">
                <img src="{{ $photo }}" alt="Foto absensi" loading="lazy"
                    class="w-10 h-10 rounded-lg object-cover bg-surface-container">
            </a>
        @endif
        <div class="flex flex-col">
            <span class="font-semibold">{{ $time->format('H:i') }}</span>
            @if (!is_null($distance))
                <span class="font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">
                    {{ number_format((float) $distance, 0, ',', '.') }} m dari lokasi
                </span>
            @endif
            @if ($lat && $lng)
                <a href="https://www.google.com/maps?q={{ $lat }},{{ $lng }}" target="_blank"
                    rel="noopener" class="font-label-md text-label-md text-primary hover:underline">
                    Lihat lokasi
                </a>
            @endif
        </div>
    </div>
@else
    <span class="text-on-surface-variant">-</span>
@endif