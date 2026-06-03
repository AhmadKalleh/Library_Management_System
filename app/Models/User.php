<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role',
        'verification_code',
        'verification_code_expires_at',
        'verification_attempts',
        'email_verified_at',
        'mobile',
    ];

    protected $hidden = [
        'password',
        'verification_code',
        'verification_code_expires_at',
    ];

    protected $casts = [
        'verification_code_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    // Relation with Image (polymorphic)
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}
