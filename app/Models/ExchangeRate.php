<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
	
class ExchangeRate extends Model
{
	use HasFactory, LogsActivity;
	
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
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];
	 
	public function getActivitylogOptions(string $logName = 'Manual Exchange Rate'): LogOptions
	{  
		$user_name = auth()->check() 
		? (auth()->guard('admin')->check() 
			? auth()->guard('admin')->user()->name 
			: auth()->user()->name) 
		: 'Unknown User';

		return LogOptions::defaults()
		->logOnly(['*', 'createdBy.name'])
		->logOnlyDirty()
		->dontSubmitEmptyLogs()
		->useLogName($logName)
		->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
			return "The {$logName} has been {$eventName} by {$user_name}";
		});
	}
		
	public function createdBy()
	{
		return $this->belongsTo(Admin::class, 'admin_id');
	}
}
