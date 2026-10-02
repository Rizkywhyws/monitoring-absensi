<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   protected $fillable = [
    'name',
    'nim',
    'asal_universitas',
    'email',
    'email_pribadi',
    'email_korporat',
    'nomor_telepon',
    'password',
    'role',
    'group_id',
    'periode_mulai',
    'periode_selesai',
    'status',
    'unit_rayon',
    'mentor',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'periode_mulai' => 'date',
            'periode_selesai' => 'date',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function dailyActivities(): HasMany
    {
        return $this->hasMany(DailyActivity::class);
    }

    public function verifiedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'verified_by');
    }
}