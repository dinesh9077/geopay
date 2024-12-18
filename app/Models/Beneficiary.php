<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Beneficiary extends Model
{
    use HasFactory, LogsActivity;
	
    // Fillable attributes (fields that can be mass assigned)
    protected $fillable = [
        'user_id',
        'category_name',
        'service_name',
        'data',
    ];
	 
	protected static $recordEvents = ['created', 'deleted', 'updated'];
	
	public function getActivitylogOptions(string $logName = 'beneficiary'): LogOptions
	{  
		$user_name = auth()->user()->name; 
		return LogOptions::defaults()
		->logOnly(['*', 'user.first_name'])
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
	
	public function getDataArrAttribute()
	{
		if (!$this->data) {
			return [];
		}

		$decoded = json_decode($this->data, true);
		return $decoded ?: []; // Return decoded data if successful, otherwise empty array
	}
}
