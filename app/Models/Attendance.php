<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const STATUS_LABELS = [
        'hadir' => 'Hadir',
        'telat' => 'Terlambat',
        'izin'  => 'Izin',
        'sakit' => 'Sakit',
    ];

    protected $fillable = [
        'user_id',
        'attendance_setting_id',
        'attendance_date',
        'check_in',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_accuracy',
        'check_in_distance',
        'check_in_photo',
        'status',
        'leave_request_id',
    ];
    protected $casts = [
        'attendance_date'    => 'date',
        'check_in'           => 'datetime',
        'check_in_latitude'  => 'float',
        'check_in_longitude' => 'float',
        'check_in_accuracy'  => 'float',
        'check_in_distance'  => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(AttendanceSetting::class, 'attendance_setting_id');
    }

    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('attendance_date', [$from, $to]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status ?: '-');
    }

    public function getCheckInPhotoUrlAttribute(): ?string
    {
        return $this->check_in_photo ? route('absensi.photo', [$this, 'in']) : null;
    }
}
