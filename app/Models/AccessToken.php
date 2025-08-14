<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'user_id','name','token_hash','expires_at','revoked_at','ip','ua','last_used_at'
    ];
	
    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];
	
    public function user() {
        return $this->belongsTo(User::class);
    }
	
    public function isActive()
	{
        return is_null($this->revoked_at) && (is_null($this->expires_at) || now()->lt($this->expires_at));
    }
}
