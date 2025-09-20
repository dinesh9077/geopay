<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use App\Services\OnafricService;
use App\Enums\OnafricStatus;
use Log;
	
class UpdateOnafricStatus extends Command
{
     
    protected $signature = 'transaction:update-onafric-status'; 
    protected $description = 'Update transaction statuses via an API call'; 
    
	protected $onafricService; 
	public function __construct(OnafricService $onafricService)
	{
		parent::__construct();
		$this->onafricService = $onafricService;
	}
    
	public function handle()
	{  
		$this->info('Starting to update transaction statuses...');
		
		$transactions = Transaction::query()
			->where('platform_provider', 'onafric')
			->where('is_refunded', 0)
			->whereDate('created_at', '>=', '2025-08-14')
			->whereNotIn('txn_status', ['paid', 'cancelled and refunded'])
			->get();

		if ($transactions->isEmpty()) {
			return;
		}
		
		foreach ($transactions as $transaction)
		{
			try { 
				 
				$response = $this->onafricService->getTransactionStatus($transaction->order_id);

				// Skip if failed or malformed
				if (!$response['success'] || empty($response['response']['data']['status']['message'])) {
					continue;
				}

				$statusMessage = $response['response']['data']['status']['message'];
				$txnStatus = OnafricStatus::from($statusMessage)->label();

				// Handle refund case
				if ($txnStatus === "cancelled and refunded") {
					$transaction->processAutoRefund($txnStatus, $statusMessage); 
				}

				// Assign values
				if ($txnStatus !== "cancelled and refunded") {
					$transaction->txn_status = $txnStatus;
				}
				$transaction->api_status = $statusMessage;

				if ($txnStatus === "paid") {
					$transaction->complete_transaction_at = now();
					 
					if ($transaction->user && !empty($transaction->user->email)) {
						try {
							$transferType = $transaction->platform_name === "transfer to bank" ? 'transfer_to_bank' : 'transfer_to_mobile';
							app(\App\Services\TransactionEmailService::class)
							->send($transaction->user, $transaction, $transferType);
						} catch (\Throwable $e) {
							Log::error("Email sending {$transferType} failed: " . $e->getMessage());
						}
					}   
				}

				// Save using Eloquent (fires events and allows change detection)
				$transaction->save();
			} 
			catch (\Throwable $e) 
			{  
				Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
			}
		}
		
		$this->info('Transaction status updates complete.');
	}
}
