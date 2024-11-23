<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasFactory, LogsActivity;
	
    protected $fillable = [
        'user_id',
        'receiver_id',
        'platform_name',
        'platform_provider',
        'transaction_type',
        'country_id',
        'txn_amount',
        'txn_status',
        'comments',
        'notes'
    ];
   
	protected static $recordEvents = ['created', 'deleted', 'updated'];
	
	public function getActivitylogOptions(string $logName = 'transaction'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
		return LogOptions::defaults()
		->logOnly(['*', 'user.first_name', 'receive.first_name'])
		->logOnlyDirty()
		->dontSubmitEmptyLogs()
		->useLogName($logName)
		->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
			return "The {$logName} has been {$eventName} by {$user_name}";
		});
	}
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function receive()
	{
		return $this->belongsTo(User::class, 'receive_id');
	}
   
}
