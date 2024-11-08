<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLimit extends Model
{
    use HasFactory;

    protected $table = 'transaction_limits'; // Explicitly defining the table name

    protected $fillable = [
        'role_id',
        'slug',
        'daily_max_limit',
        'monthly_max_limit',
        'max_amount_limit',
        'min_amount_limit',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    
    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }
}
