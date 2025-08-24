<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantExchangeRate extends Model
{
    use HasFactory;
	protected $fillable = [
		'user_id',
		'type',
		'referance_id',
		'markdown_type',
		'markdown_charge',
		'markdown_rate',
	];

}
