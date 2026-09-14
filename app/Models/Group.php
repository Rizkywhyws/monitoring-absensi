<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Satu group memiliki banyak user.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}