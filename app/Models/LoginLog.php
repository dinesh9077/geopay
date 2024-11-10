<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory; 
    protected $hidden = ['created_at', 'updated_at'];
	
	protected $fillable = [
        'user_id',
        'type', 
        'ip_address',
        'device',
        'browser',
        'source',
    ];
	
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
