<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveClinicEvaluation extends Model
{
    protected $table = 'leave_clinic_evaluations';

    protected $fillable = [
        'leave_id',
        'evaluated_by',
        'symptoms',
        'medication',
        'visited_clinic',
        'symptoms_present',
        'clinic_notes',
        'decision',
    ];

    protected $casts = [
        'visited_clinic' => 'boolean',
        'symptoms_present' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Evaluation belongs to a leave
    public function leave()
    {
        return $this->belongsTo(Leave::class);
    }

    // Evaluation done by a nurse (user)
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
