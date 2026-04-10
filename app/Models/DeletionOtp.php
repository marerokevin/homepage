<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletionOtp extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'target_id',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at);
    }
}
