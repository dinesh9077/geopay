<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
	
	protected $fillable = [
		'admin_id',
		'type', 
		'service_name', 
		'country_name', 
		'currency', 
		'exchange_rate', 
		'aggregator_rate', 
		'markdown_type', 
		'markdown_charge', 
		'status'
	];
	
	public function createdBy()
	{
		return $this->belongsTo(Admin::class, 'admin_id');
	}
}
