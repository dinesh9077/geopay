<?php
	
	namespace App\Console\Commands;
	
	use Illuminate\Console\Command;
	use App\Models\Transaction;
	use Illuminate\Support\Facades\Http;
	use App\Services\LiquidNetService;
	use App\Notifications\AirtimeRefundNotification;
	use Notification;
	use App\Enums\LightnetStatus;
	use Log;
	class UpdateLightnetStatus extends Command
	{
		/**
			* The name and signature of the console command.
			*
			* @var string
		*/
		protected $signature = 'transaction:update-lightnet-status';
		
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
			//Log::info('Update transaction statuses via an API call => '.now());
			$this->info('Starting to update transaction statuses...');
			
			// Fetch transactions that need status updates
			$transactions = Transaction::query()
			->where('platform_provider', 'lightnet')
			->where('is_refunded', 0)
			->whereNotIn('txn_status', ['paid', 'cancelled and refunded'])
			->first();
			 
			if ($transactions->isEmpty()) {
				return;
			}
			
			foreach ($transactions as $transaction)
			{
				try { 
					 
					$response = $this->liquidNetService->getTXNStatus($transaction->order_id);
					
					// Return 0 on failure or unexpected response
					if (!$response['success'] || ($response['response']['code'] ?? -1) != 0) {
						continue; // Skip to the next transaction
					} 
					 
					$txn_status = $transaction->txn_status;
					if(!empty($response['response']['status']))
					{
						$txn_status = LightnetStatus::from($response['response']['status'])->label(); 
						if($txn_status === "cancelled and refunded")
						{
							$transaction->processAutoRefund($txn_status);
						}
					}
					
					$transaction->update(['txn_status' => $txn_status]);
					
					//$user = $transaction->user; 
					//Notification::send($user, new AirtimeRefundNotification($user, $transaction->txn_amount, $transaction->id, $transaction->comments, $transaction->notes, ucfirst($transaction->txn_status)));
				} 
				catch (\Throwable $e) 
				{ 
					//$this->info("{$e->getMessage()}");  
					//\Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
				}
			}
			
			$this->info('Transaction status updates complete.');
		}
	}
