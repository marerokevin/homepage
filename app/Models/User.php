<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Vehicle;
use App\Models\Driver;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'is_vehicle_admin',
        'can_book_vehicle',
        'otp',
        'otp_expires_at',
        'is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // ... existing code (fillable, casts, etc.)

    /**
    * Define the relationship: A User has many Posts
    */
    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function resources()
    {
        return response()->json([
            'vehicles' => Vehicle::orderBy('name')->get(),
            'drivers'  => Driver::orderBy('name')->get(),
        ]);
    }

    public function permission()
    {
        return $this->hasOne(Permission::class);
    }

    public function canPost()
    {
        return $this->permission?->can_post ?? false;
    }

    public function canDeletePost()
    {
        return $this->permission?->can_delete_post ?? false;
    }
}
