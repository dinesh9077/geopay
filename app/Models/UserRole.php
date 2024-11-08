<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;
    public function transactionLimits()
    {
        return $this->hasMany(TransactionLimit::class, 'role_id');
    }
}
