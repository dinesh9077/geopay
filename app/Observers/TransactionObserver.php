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
    public function created(Transaction $transaction)
    { 
        if (!in_array($transaction->platform_name, ['transfer to mobile', 'transfer to bank']) && $transaction->txn_status != "cancelled and refunded") {
            return;
        }
   
        $oldStatus = $transaction->getOriginal('txn_status');
        $newStatus = $transaction->txn_status;
        $apiStatus = $transaction->api_status;

        Log::info("Transaction status changed", [
            'transaction_id' => $transaction->id,
            'old_status'     => $oldStatus,
            'new_status'     => $newStatus,
            'api_status'     => $apiStatus,
        ]);

        // ✅ Get webhook URL
        $callbackUrl = optional($transaction->user->webhook)->url;
        if (!$callbackUrl) {
            Log::warning("No webhook URL for user {$transaction->user_id}");
            return;
        }

        // ✅ Prepare payload in a dedicated method for reusability
        $payload = $this->buildPayload($transaction);

        // ✅ Send webhook with error handling
        $this->sendWebhook($callbackUrl, $payload);
    }
	
	public function updated(Transaction $transaction)
    { 
        if (!in_array($transaction->platform_name, ['transfer to mobile', 'transfer to bank'])) {
            return;
        }
 
        if (!$transaction->isDirty('txn_status')) {
            return;
        }

        $oldStatus = $transaction->getOriginal('txn_status');
        $newStatus = $transaction->txn_status;

        Log::info("Transaction status changed", [
            'transaction_id' => $transaction->id,
            'old_status'     => $oldStatus,
            'new_status'     => $newStatus,
        ]);

        // ✅ Get webhook URL
        $callbackUrl = optional($transaction->user->webhook)->url;
        if (!$callbackUrl) {
            Log::warning("No webhook URL for user {$transaction->user_id}");
            return;
        }

        // ✅ Prepare payload in a dedicated method for reusability
        $payload = $this->buildPayload($transaction);

        // ✅ Send webhook with error handling
        $this->sendWebhook($callbackUrl, $payload);
    }

    /**
     * Build the webhook payload.
     */
    private function buildPayload(Transaction $transaction): array
    {
        return [
            'webhook_secret' => $transaction->user->webhook->secret ?? null,
            'thirdPartyId' => $transaction->order_id,
            'status' => [
                'status'  => $transaction->txn_status,
                'message' => $transaction->api_status,
            ],
            'exchangeRate' => $transaction->rates,
            'receiveAmount' => [
                'amount'       => $transaction->unit_amount,
                'currencyCode' => $transaction->unit_convert_currency,
            ],
            'txExecutedDate' => $transaction->complete_transaction_at,
        ];
    }

    /**
     * Send the webhook request.
     */
    private function sendWebhook(string $url, array $payload): void
    {
        try {
            Http::timeout(10)->post($url, $payload);
            Log::info("Webhook sent successfully", [
                'url'     => $url,
                'payload' => $payload,
            ]);
        } catch (\Exception $e) {
            Log::error("Webhook sending failed", [
                'url'     => $url,
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }
}
