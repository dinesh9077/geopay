<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnafriqCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'country_name',
        'channels',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'channels' => 'array', // automatically converts JSON <-> PHP array
    ];
}
