<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'user_id',
        'environment',
        'status',
        'client_id',
        'client_secret',
        'api_url'
    ];
}
