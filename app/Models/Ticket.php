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

    /** Semua status valid; dipakai untuk validasi filter di halaman admin. */
    public const STATUSES = [
        'to_do', 'on_progress', 'hold', 'revision', 'done', 'verified', 'cancelled',
    ];

    protected $fillable = [
        'ticket_number',
        'user_id',
        'title',
        'description',
        'category',
        'priority',
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
}