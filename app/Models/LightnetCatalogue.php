<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LightnetCatalogue extends Model
{
    use HasFactory;
	 
    /**
     * Fillable attributes for mass assignment
     */
    protected $fillable = [
        'category_name',
        'service_name',
        'catalogue_type',
        'catalogue_description',
        'data',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'data' => 'array', // Automatically casts JSON to array and vice versa
    ];
}
