<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MerchantCorridor extends Model
{
    use HasFactory, LogsActivity;
	
	protected $fillable = [
        'user_id',
        'service',
        'payout_country',
        'payout_currency',
        'fee_type',
        'fee_value'
    ];
	
	protected static $recordEvents = ['created', 'deleted', 'updated'];
		
	public function getActivitylogOptions(string $logName = 'Merchant Corridor'): LogOptions
	{  
		$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
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
