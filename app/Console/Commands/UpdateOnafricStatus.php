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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:update-onafric-status';

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
		$transactions = Transaction::select('id', 'user_id', 'txn_status', 'platform_provider', 'order_id')
		->where('platform_provider', 'onafric')
		->where('is_refunded', 0)
		->whereDate('created_at', '>=', '2025-08-14')
		->whereNotIn('txn_status', ['paid', 'cancelled and refunded'])
		->get();
		 
		if (empty($transactions)) {
			return;
		}
		
		foreach ($transactions as $transaction)
		{
			try { 
				 
				$response = $this->onafricService->getTransactionStatus($transaction->order_id);
				// Return 0 on failure or unexpected response
				if (!$response['success']) {
					continue; // Skip to the next transaction
				} 
				
				// Update transaction status 
				$txn_status = $transaction->txn_status;
				if(!empty($response['response']['data']['status']['message']))
				{
					$txn_status = OnafricStatus::from($response['response']['data']['status']['message'])->label(); 
					if($txn_status === "cancelled and refunded")
					{
						$transaction->processAutoRefund($txn_status);
					}
				}
				
				$transaction->update(['txn_status' => $txn_status]);  
			} 
			catch (\Throwable $e) 
			{  
				//\Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
			}
		}
		
		$this->info('Transaction status updates complete.');
	}
}
