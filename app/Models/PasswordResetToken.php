<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PasswordResetToken extends Model
{
    protected $collection = 'password_reset_tokens';
    protected $fillable = ['email','token','expires_at'];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
