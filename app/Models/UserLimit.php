<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLimit extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'name',
        'daily_add_limit',
        'daily_pay_limit', 
        'is_active',
    ];
}
