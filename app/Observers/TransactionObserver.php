<?php
namespace App\Observers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction)
    {
        // Check if txn_status changed
        if ($transaction->isDirty('txn_status'))
		{
            $oldStatus = $transaction->getOriginal('txn_status');
            $newStatus = $transaction->txn_status;

            Log::info("Transaction status changed", [
                'transaction_id' => $transaction->id,
                'old_status'     => $oldStatus,
                'new_status'     => $newStatus,
            ]);

            // Get the registered webhook URL for this client
            $callbackUrl = $transaction->user->webhook->url ?? null;
            if (!$callbackUrl) {
                Log::warning("No webhook URL for user {$transaction->user_id}");
                return;
            }

			$payload = [
				'thirdPartyId' => $transaction->order_id,
				 "status" =>  [
					"status" =>  $transaction->txn_status,
					"message" =>  $transaction->api_status
				],
				'exchangeRate' => $transaction->rates,
				'receiveAmount' => [
					'amount' => $transaction->unit_amount,
					'currencyCode' => $transaction->unit_convert_currency 
				],
				"txExecutedDate" =>  $transaction->complete_transaction_at
			];

            // Send the webhook
            try {
                Http::timeout(10)->post($callbackUrl, $payload);
                Log::info("Webhook sent successfully", ['url' => $callbackUrl, 'payload' => $payload]);
            } catch (\Exception $e) {
                Log::error("Webhook sending failed: " . $e->getMessage());
            }
        }
    }
}

