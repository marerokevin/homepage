<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'user_id',
        'can_post',
        'can_delete_post',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
