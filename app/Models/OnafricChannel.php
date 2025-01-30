<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnafricChannel extends Model
{
    use HasFactory;
	
	// Mass assignable fields
    protected $fillable = [
        'country_id',
        'channel',
        'fees',
        'commission_type',
        'commission_charge',
        'status',
    ];

    // Optional: Cast fields to correct data types
    protected $casts = [
        'fees' => 'decimal:2',
        'commission_charge' => 'decimal:2'
    ];

    // Relationship with Country model (if applicable)
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
