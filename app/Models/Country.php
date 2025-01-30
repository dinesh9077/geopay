<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
	
	// Mass assignable attributes
    protected $fillable = [
        'iso',
        'name',
        'nicename',
        'iso3',
        'numcode',
        'isdcode',
        'currency_code',
        'country_flag',
    ];
 
    // Relationship with OnafricChannel (if applicable)
    public function channels()
    {
        return $this->hasMany(OnafricChannel::class, 'country_id');
    }
}
