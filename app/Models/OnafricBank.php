<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnafricBank extends Model
{
    use HasFactory;
    // Mass assignable fields
    protected $fillable = [
        'mfs_bank_code',
        'payout_iso',
        'bank_name', 
        'response',
        'status'
    ];

    // Optional: Cast fields to correct data types
    protected $casts = [
        'response' => 'array'
    ];
}
