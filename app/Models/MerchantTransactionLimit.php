<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MerchantTransactionLimit extends Model
{
    use HasFactory, LogsActivity;
	
	protected $fillable = ['user_id', 'service', 'daily_limit'];
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];
		
	public function getActivitylogOptions(string $logName = 'Merchant Daily Transaction Limit'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; 
		return LogOptions::defaults()
		->logOnly(['*', 'user.first_name', 'user.last_name'])
		->logOnlyDirty()
		->dontSubmitEmptyLogs()
		->useLogName($logName)
		->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
			return "The {$logName} has been {$eventName} by {$user_name}";
		});
	}
		  
    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
