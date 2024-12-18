<?php
	
	namespace App\Console\Commands;
	
	use Illuminate\Console\Command;
	use App\Models\Transaction;
	use Illuminate\Support\Facades\Http;
	use App\Services\LiquidNetService;
	class UpdateTransactionStatus extends Command
	{
		/**
			* The name and signature of the console command.
			*
			* @var string
		*/
		protected $signature = 'transaction:update-status';
		
		/**
			* The console command description.
			*
			* @var string
		*/
		protected $description = 'Update transaction statuses via an API call';
		
		/**
		 * TransactionService instance.
		 */
		protected $liquidNetService;

		/**
		 * Create a new command instance.
		 */
		public function __construct(LiquidNetService $liquidNetService)
		{
			parent::__construct();
			$this->liquidNetService = $liquidNetService;
		}
	
		/**
			* Execute the console command.
		*/
		public function handle()
		{
			$this->info('Starting to update transaction statuses...');
			
			// Fetch transactions that need status updates
			$transactions = Transaction::select('id', 'user_id', 'txn_status', 'platform_provider', 'order_id')->whereIn('platform_provider', ['lightnet'])
			->whereNotIn('txn_status', ['paid'])
			->get();
			 
			foreach ($transactions as $transaction)
			{
				try { 
					
					if($transaction->platform_provider == "lightnet")
					{
						$response = $this->liquidNetService->getTXNStatus($transaction->order_id);
						// Return 0 on failure or unexpected response
						if (!$response['success'] || ($response['response']['code'] ?? -1) != 0) {
							continue; // Skip to the next transaction
						} 
						// Update transaction status
                        $txn_status = strtolower($response['response']['status'] ?? $transaction->txn_status);
                        $transaction->update(['txn_status' => $txn_status]); 
					}
				} 
				catch (\Throwable $e) 
				{ 
					\Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
				}
			}
			
			$this->info('Transaction status updates complete.');
		}
	}
