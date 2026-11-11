<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Riwayat Absensi</title>
    <style>
        @page { margin: 25px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #202124; font-size: 9px; }
        h1 { margin: 0 0 5px; font-size: 17px; }
        .muted { color: #5f6368; }
        .meta { width: 100%; border-collapse: collapse; margin: 12px 0; }
        .meta td { padding: 4px 6px; border: 1px solid #d8dce0; }
        .summary { width: 100%; border-collapse: collapse; margin: 10px 0 14px; }
        .summary th, .summary td { border: 1px solid #d8dce0; padding: 6px; text-align: center; }
        .summary th { background: #eef2f5; font-weight: bold; }
        .records { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .records th, .records td { border: 1px solid #cfd4da; padding: 6px 5px; vertical-align: top; }
        .records th { background: #e9eef2; text-align: left; font-weight: bold; }
        .records thead { display: table-header-group; }
        .records tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .right { text-align: right; }
        .footer { margin-top: 12px; font-size: 8px; color: #666; }
        .status-hadir { color: #087443; font-weight: bold; }
        .status-telat { color: #b42318; font-weight: bold; }
        .status-other { color: #475467; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Rekap Riwayat Absensi PKL PLN Icon Plus</h1>
    <div class="muted">Laporan berdasarkan data Check-in. Tidak ada data Check-out pada laporan ini.</div>

    <table class="meta">
        <tr>
            <td width="18%"><strong>Periode</strong></td>
            <td width="32%">{{ \Carbon\Carbon::parse($dari)->locale('id')->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($sampai)->locale('id')->translatedFormat('d F Y') }}</td>
            <td width="18%"><strong>Filter status</strong></td>
            <td width="32%">{{ $statusLabel }}</td>
        </tr>
        <tr>
            <td><strong>Kata kunci</strong></td>
            <td>{{ $keyword ?: 'Semua peserta' }}</td>
            <td><strong>Jumlah catatan</strong></td>
            <td>{{ $attendances->count() }}</td>
        </tr>
    </table>

    <table class="summary">
        <thead>
            <tr>
                <th>Total</th>
                @foreach ($statusOptions as $key => $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>{{ $rekap->sum() }}</strong></td>
                @foreach ($statusOptions as $key => $label)
                    <td>{{ $rekap[$key] ?? 0 }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <table class="records">
        <thead>
            <tr>
                <th width="5%" class="center">No.</th>
                <th width="18%">Peserta / NIM</th>
                <th width="15%">Kelompok / Divisi</th>
                <th width="14%">Tanggal</th>
                <th width="9%">Check-in</th>
                <th width="17%">Kantor</th>
                <th width="10%">Jarak</th>
                <th width="12%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $index => $attendance)
                @php
                    $statusClass = match ($attendance->status) {
                        'hadir' => 'status-hadir',
                        'telat' => 'status-telat',
                        default => 'status-other',
                    };
                    $statusText = $statusOptions[$attendance->status] ?? ucfirst($attendance->status);
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $attendance->user?->name ?? '-' }}</strong><br>
                        <span class="muted">{{ $attendance->user?->nim ?? '-' }}</span>
                    </td>
                    <td>{{ $attendance->user?->division ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->locale('id')->translatedFormat('d M Y') }}</td>
                    <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i:s') : '-' }}</td>
                    <td>{{ $attendance->office_name ?? '-' }}</td>
                    <td>{{ $attendance->check_in_distance !== null ? number_format((float) $attendance->check_in_distance, 0, ',', '.') . ' m' : '-' }}</td>
                    <td class="{{ $statusClass }}">{{ $statusText }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data absensi pada periode dan filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y H:i:s') }} WIB. Dokumen ini dibuat otomatis oleh Sistem Manajemen PKL PLN Icon Plus.
    </div>
</body>
</html>
