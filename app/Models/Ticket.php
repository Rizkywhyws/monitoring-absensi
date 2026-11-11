<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    /** Status yang dihitung sebagai "selesai" untuk logbook. */
    public const FINISHED = ['done', 'verified'];

    public const STATUSES = [
        'to_do',
        'on_progress',
        'hold',
        'revision',
        'done',
        'verified',
        'cancelled',
    ];

    /** Kategori pekerjaan yang bisa dipilih saat membuat tiket. */
    public const CATEGORIES = [
        'Instalasi',
        'Maintenance',
        'Troubleshooting',
        'Splicing',
        'Survey',
        'Umum',
    ];

    protected $fillable = [
        'ticket_number',
        'user_id',
        'title',
        'description',
        'category',
        'status',
        'started_at',
        'completed_at',
        'verified_by',
        'verified_at',
        'revision_note',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'verified_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStartAtAttribute(): ?Carbon
    {
        return $this->started_at;
    }

    public function getEndAtAttribute(): ?Carbon
    {
        return $this->completed_at;
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }

        return (int) round($this->started_at->diffInMinutes($this->completed_at, true));
    }

    public function toModalArray(): array
    {
        $minutes = $this->duration_minutes;

        return [
            'code'          => $this->code ?: $this->ticket_number,
            'ticket_number' => $this->ticket_number,
            'title'         => $this->title,
            'description'   => $this->description,
            'category'      => $this->category,
            'status'        => $this->status,
            'started_at'    => $this->started_at?->translatedFormat('d M Y, H:i'),
            'completed_at'  => $this->completed_at?->translatedFormat('d M Y, H:i'),
            'duration'      => $minutes === null ? null : intdiv($minutes, 60) . 'j ' . str_pad($minutes % 60, 2, '0', STR_PAD_LEFT) . 'm',
            'verifier'      => $this->verifier?->name,
            'verified_at'   => $this->verified_at?->translatedFormat('d M Y, H:i'),
            'revision_note' => $this->revision_note,
            'attachments'   => $this->attachments->map(fn($a) => [
                'name'     => $a->file_name,
                'url'      => $a->url,
                'type'     => $a->file_type,
                'size'     => $a->file_size ? number_format($a->file_size / 1024, 1) . ' KB' : null,
                'is_image' => str_starts_with((string) $a->file_type, 'image/'),
            ])->values()->all(),
        ];
    }

    /** Nomor tiket acak, contoh: TKT-7K3QX. Dijamin belum dipakai. */
    public static function generateNumber(): string
    {
        $alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

        do {
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $number = 'TKT-' . $code;
        } while (static::where('ticket_number', $number)->exists());

        return $number;
    }
}