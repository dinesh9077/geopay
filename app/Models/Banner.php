<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banners';

    // Specify the fillable attributes
    protected $fillable = [
        'name',
        'app_image',
        'web_image',
        'description',
    ];

    // Enable or disable timestamps based on your needs
    public $timestamps = true;
}
