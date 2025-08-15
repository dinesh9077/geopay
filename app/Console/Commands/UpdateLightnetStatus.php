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
		protected $signature = 'transaction:update-lightnet-status'; 
		protected $description = 'Update transaction statuses via an API call';
		 
		protected $liquidNetService;
 
		public function __construct(LiquidNetService $liquidNetService)
		{
			parent::__construct();
			$this->liquidNetService = $liquidNetService;
		}
	 
		public function handle()
		{
			// Fetch transactions that need status updates
			$transactions = Transaction::query()
				->where('platform_provider', 'lightnet')
				->where('is_refunded', 0)
				->whereDate('created_at', '>=', '2025-08-14')
				->whereNotIn('txn_status', ['paid', 'cancelled and refunded'])
				->get();

			if ($transactions->isEmpty()) {
				return;
			}
			
			foreach ($transactions as $transaction) {
				try {
					$response = $this->liquidNetService->getTXNStatus($transaction->order_id);

					// Skip if failed or unexpected response code
					if (!$response['success'] || empty($response['response']['status']) || !isset($response['response']['status'])) {
						continue;
					}

					$statusMessage = $response['response']['status'];
					$txnStatus = LightnetStatus::from($statusMessage)->label();

					// Handle refund case
					if ($txnStatus === "cancelled and refunded") {
						$transaction->processAutoRefund($txnStatus, $statusMessage);
					}

					// Set attributes directly
					if ($txnStatus !== "cancelled and refunded") {
						$transaction->txn_status = $txnStatus;
					}
					$transaction->api_status = $statusMessage;

					if ($txnStatus === "paid") {
						$transaction->complete_transaction_at = now();
					}

					// Save using Eloquent
					$transaction->save();

				} catch (\Throwable $e) {
					Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}");
				}
			}


			/* foreach ($transactions as $transaction)
			{
				try
				{    
					$response = $this->liquidNetService->getTXNStatus($transaction->order_id);

					// Skip if failed or unexpected response code
					if (!$response['success'] || ($response['response']['code'] ?? -1) !== 0 || empty($response['response']['status'])) 
					{
						continue;
					}
		 
					$statusMessage = $response['response']['status'];
					$txnStatus = LightnetStatus::from($statusMessage)->label();

					// Handle refund case
					if ($txnStatus === "cancelled and refunded") {
						$transaction->processAutoRefund($txnStatus, $statusMessage); 
					}
			
					// Prepare update payload
					$updateData = [
						'txn_status' => $txnStatus === "cancelled and refunded" ? $transaction->txn_status : $txnStatus,
						'api_status' => $statusMessage
					];

					if ($txnStatus === "paid") {
						$updateData['complete_transaction_at'] = now();
					}

					$transaction->update($updateData);
				} 
				catch (\Throwable $e) 
				{ 
					Log::error("Error updating transaction ID {$transaction->id}: {$e->getMessage()}"); 
				}
			} */
			
			$this->info('Transaction status updates complete.');
		}
	}
