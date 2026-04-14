<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'approved_by',
        'clinic_notes',
        'half_day'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
