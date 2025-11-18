<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpBlock extends Model
{
    use HasFactory; 
    protected $table = 'otp_blocks';

    protected $fillable = [
        'email_mobile',
        'blocked_until',
        'attempts',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
    ];

    public function isBlocked(): bool
    {
        return $this->blocked_until && Carbon::now()->lt($this->blocked_until);
    }
}

