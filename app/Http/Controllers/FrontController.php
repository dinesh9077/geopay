<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\Transaction;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        return view('welcome');
    }    
	
    public function termAndCondition()
    {
        return view('terms_condition');
    } 
	
    public function handleDepositCallback(Request $request)
    {
        $merchantOrderId = $request->merchant_orderid;
		$status          = strtolower($request->status);
		$reason          = $request->reason ?? null;

		\Log::info( $request->all());

		// Find transaction by merchant_orderid
		$transaction = Transaction::where('order_id', $merchantOrderId)->first();
	 
		if (!$transaction) {
			\Log::warning("Webhook: Transaction not found for order_id {$merchantOrderId}", $request->all());
			return response()->json(['status' => 'transaction_not_found'], 404);
		}

		// Default update
		$updateData = [
			'txn_status' => $status,
			'api_status' => $status,
			'comments'      => $reason,
		];

		// If payment captured, update wallet too
		if ($transaction->txn_status != "authorised" && $status === 'authorised') 
		{
			$transaction->user->increment('balance', $transaction->txn_amount);

			$updateData['comments'] = 'Payment successful. Wallet updated.';
			$updateData['complete_transaction_at'] = now();
			
			if ($transaction->user && !empty($transaction->user->email)) {
				try {
					app(\App\Services\TransactionEmailService::class)
						->send($transaction->user, $transaction, 'add_funds_deposit');
				} catch (\Throwable $e) {
					Log::error("Email sending add_funds_deposit failed: " . $e->getMessage());
				}
			}   
		}

		$transaction->update($updateData);

		return response()->json(['status' => 'ok']);
    } 
	 
}
