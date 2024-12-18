<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LightnetCountry extends Model
{
    use HasFactory;
	
	 // Define fillable fields to allow mass assignment
    protected $fillable = [
        'data', 
        'value', 
        'label', 
        'service_name', 
        'status',
        'updated_at'
    ];

    // If you want to allow automatic timestamps, make sure this is set to true
    public $timestamps = true;
}
