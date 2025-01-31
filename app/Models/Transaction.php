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
        'notes',
        'created_at',
        'updated_at',
        'unique_identifier',
        'country_code',
        'product_name',
        'operator_id',
        'product_id',
        'mobile_number',
        'unit_currency',
        'unit_amount',
        'unit_rates',
        'rates',
        'unit_convert_currency',
        'unit_convert_amount',
        'unit_convert_exchange',
        'api_request',
        'api_response',
        'order_id',
        'fees',
        'beneficiary_request',
        'api_response_second',
        'service_charge',
        'total_charge',
    ]; 
	
	protected $casts = [
        'api_request' => 'array',
        'api_response' => 'array',
        'beneficiary_request' => 'array',
        'api_response_second' => 'array',
    ];
     
	protected static $recordEvents = ['created', 'deleted', 'updated'];
	
	public function getActivitylogOptions(string $logName = 'transaction'): LogOptions
	{  
		$user_name = auth()->check() 
		? (auth()->guard('admin')->check() 
			? auth()->guard('admin')->user()->name 
			: auth()->user()->name) 
		: 'Unknown User';
		
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
		return $this->belongsTo(User::class, 'receiver_id');
	}
	
	public function getApiResponseAsArrayAttribute()
	{
		return $this->api_response ?? [];
	}
   
}
