<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use App\Services\OnafricService;
use Log;
	
class UpdateOnafricMobileCollectionStatus extends Command
{ 
    protected $signature = 'transaction:update-onafric-collection-status'; 
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
			->where('platform_provider', 'onafric mobile collection')
			->whereNotIn('txn_status', ['successful', 'failed'])
			->get();

		if ($transactions->isEmpty()) {
			$this->info('No pending transactions found.');
			return;
		}
		
		foreach ($transactions as $transaction)
		{
			try {   
				$response = $this->onafricService->getCollectionStatus($transaction->unique_identifier);
				 
				if (!$response['success'] || empty($response['response']['status'])) {
					continue; 
				} 
				  
				$txnStatus = strtolower($response['response']['status'] ?? $transaction->txn_status);
				
				$errorMsg = $response['response']['error_message']
                ?? $response['response']['instructions']
                ?? $transaction->comments;
				 
				$updateData = [
					'txn_status' => $txnStatus,
					'api_status' => $txnStatus,
					'comments'   => $errorMsg
				];

				if ($txnStatus === 'successful') {
					$transaction->user->increment('balance', $transaction->txn_amount);
					$updateData['comments'] = "Payment received successfully. Wallet updated.";
					$updateData['complete_transaction_at'] = now();
				}

				$transaction->update($updateData);
			} 
			catch (\Throwable $e) 
			{  
				\Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
			}
		}
		
		$this->info('Transaction status updates complete.');
	}
}
