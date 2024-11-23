<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class UserLimit extends Model
{
    use HasFactory, LogsActivity;
	
	protected $fillable = [
        'name',
        'daily_add_limit',
        'daily_pay_limit', 
        'is_active',
    ];
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];
	
	public function getActivitylogOptions(string $logName = 'user limit'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
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
