<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LiveExchangeRate extends Model
{
    use HasFactory, LogsActivity;
	
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
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];
	 
	public function getActivitylogOptions(string $logName = 'Live Exchange Rate'): LogOptions
	{  
		$user_name = auth()->check() 
		? (auth()->guard('admin')->check() 
			? auth()->guard('admin')->user()->name 
			: auth()->user()->name) 
		: 'Unknown User';

		return LogOptions::defaults()
		->logOnly(['*'])
		->logOnlyDirty()
		->dontSubmitEmptyLogs()
		->useLogName($logName)
		->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
			return "The {$logName} has been {$eventName} by {$user_name}";
		});
	}
}
