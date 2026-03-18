<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRequest extends Model
{
    protected $fillable = [
        'user_id',
        'trip_number',
        'pickup',
        'destination',
        'vehicle',
        'plate',
        'trip_date',
        'departure',
        'eta',
        'return_time',
        'return_date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
