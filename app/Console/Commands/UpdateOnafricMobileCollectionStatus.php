<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use App\Services\OnafricService;
use Log;
	
class UpdateOnafricMobileCollectionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:update-onafric-collection-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update transaction statuses via an API call';

    /**
     * Execute the console command.
     */
	
	/**
	 * TransactionService instance.
	 */
	protected $onafricService;

	/**
	 * Create a new command instance.
	 */
	public function __construct(OnafricService $onafricService)
	{
		parent::__construct();
		$this->onafricService = $onafricService;
	}
    
	public function handle()
	{  
		$this->info('Starting to update transaction statuses...');
		
		// Fetch transactions that need status updates
		$transactions = Transaction::select('id', 'user_id', 'txn_status', 'platform_provider', 'order_id', 'unique_identifier')
		->where('platform_provider', 'onafric mobile collection')
		->whereNotIn('txn_status', ['successful', 'failed'])
		->get();
		
		if ($transactions->isEmpty()) {
			return;
		}
		
		foreach ($transactions as $transaction)
		{
			try { 
				 
				$response = $this->onafricService->getCollectionStatus($transaction->unique_identifier);
				\Log::info($response);
				// Return 0 on failure or unexpected response
				if (!$response['success']) {
					continue; // Skip to the next transaction
				} 
				
				// Update transaction status
				$txn_status = strtolower($response['response']['status'] ?? $transaction->txn_status);
				$errorMsg =  !empty($response['response']['error_message']) ? $response['response']['error_message'] ? (!empty($response['response']['instructions']) ? $response['response']['instructions'] : $transaction->comments);
				$transaction->update(['txn_status' => strtolower($txn_status), 'comments' => $errorMsg]);  
				
				if(strtolower($txn_status) == 'successful')
				{
					$transaction->user->increment('balance', $transaction->txn_amount);  
					$transaction->update(['comments' => "Payment received successfully. Wallet updated."]); 
				} 
			} 
			catch (\Throwable $e) 
			{  
				//\Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
			}
		}
		
		$this->info('Transaction status updates complete.');
	}
}
