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
		'currency', 
		'exchange_rate', 
	];
	
	public function createdBy()
	{
		return $this->belongsTo(Admin::class, 'admin_id');
	}
}
