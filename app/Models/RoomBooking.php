<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomBooking extends Model
{
    protected $fillable = [
        'room',
        'title',
        'booking_date',
        'start_time',
        'end_time',
        'attendees',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
