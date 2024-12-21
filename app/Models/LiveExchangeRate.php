<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveExchangeRate extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'channel',
        'country_name',
        'currency', 
        'markdown_rate',
        'aggregator_rate',
        'markdown_type',
        'markdown_charge',
        'status',
        'created_at',
        'updated_at',
    ];
}
