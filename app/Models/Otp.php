<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    // Specify the table name if it's different from the default plural form
    protected $table = 'otps';

    // Specify the fillable attributes
    protected $fillable = [
        'email_mobile',
        'otp',
        'expires_at',
        'created_at',
    ];

    // Disable timestamps if you do not want to use created_at and updated_at
    public $timestamps = true;
}
