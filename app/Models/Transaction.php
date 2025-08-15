<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use DB, Helper;

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
        'is_refunded',
        'refund_reason',
        'additional_message',
        'is_api_service',
        'api_status',
        'complete_transaction_at',
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
	
	public function processAutoRefund(string $txnStatus = 'cancelled and refunded', $statusMessage)
    {
        return DB::transaction(function () use ($txnStatus, $statusMessage) {
			
            // Only allow refund if eligible
            if ($this->is_refunded) {
				return;
			}
 
			// Find user
			$user = User::findOrFail($this->user_id);
			
			// Calculate refund amount
			$refundAmount = $this->txn_amount;

			// Increment user balance
			$user->increment('balance', $refundAmount);

            // Create refund transaction
            $refundTransaction = $this->replicate()->toArray();
            $refundTransaction['transaction_type'] = 'credit';
            $refundTransaction['comments'] = "A refund of {$refundAmount} " . config('setting.default_currency') . " has been processed.";
            $refundTransaction['created_at'] = now();
            $refundTransaction['updated_at'] = now();
            $refundTransaction['refund_reason'] = 'Auto refund by system';
            $refundTransaction['is_refunded'] = 0;
            $refundTransaction['txn_status'] = $txnStatus;
            $refundTransaction['api_status'] = $statusMessage;
            $refundTransaction['complete_transaction_at'] = now();

            $refundedTransaction = self::create($refundTransaction);

            Helper::updateLogName(
                $refundedTransaction->id,
                self::class,
                'Refund Transaction',
                auth()->guard('admin')->id()
            );

            // Mark original as refunded
            $this->update(['is_refunded' => 1]);

            return $refundedTransaction;
        });
    }
}
