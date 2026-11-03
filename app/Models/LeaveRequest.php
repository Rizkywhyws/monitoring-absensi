<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveRequest extends Model
{
    public const TYPES = [
        'sakit' => 'Sakit',
        'izin'  => 'Izin',
    ];

    public const STATUSES = [
        'pending'   => 'Menunggu',
        'approved'  => 'Disetujui',
        'rejected'  => 'Ditolak',
        'cancelled' => 'Dibatalkan',
    ];

    public const MAX_CALENDAR_DAYS = 14;


    public const SICK_BACKDATE_DAYS = 2;

    public const MAX_ATTACHMENT_KB = 2048;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'is_urgent',
        'attachment',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_urgent'   => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /** Baris absensi (sakit/izin) yang dibuat saat pengajuan disetujui */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Daftar tanggal kerja (Senin–Jumat) dalam rentang, inklusif.
     * Hari libur nasional belum diperhitungkan.
     *
     * @return array<int, \Carbon\CarbonInterface>
     */
    public static function workingDates(CarbonInterface $start, CarbonInterface $end): array
    {
        $dates = [];
        $cursor = $start->copy()->startOfDay();
        $last = $end->copy()->startOfDay();

        while ($cursor->lte($last)) {
            if ($cursor->isWeekday()) {
                $dates[] = $cursor->copy();
            }
            $cursor = $cursor->copy()->addDay();
        }

        return $dates;
    }

    public function workingDays(): int
    {
        return count(self::workingDates($this->start_date, $this->end_date));
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst((string) $this->type);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}