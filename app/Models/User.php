<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nim',
        'email',
        'email_pribadi',
        'email_korporat',
        'password',
        'role',
        'status',
        'division',
        'periode_mulai',
        'periode_selesai',
        'unit_rayon',
        'mentor',
        'asal_universitas',
        'nomor_telepon',
        'no_hp',
        'foto',
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