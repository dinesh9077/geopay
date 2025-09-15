<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        // Verify signature / securehash if required
        \Log::info('Payment Callback Received', $request->all());
  
        return response()->json(['status' => 'ok']);
    }
	
	public function depositPaymentReturn(Request $request)
	{
		$merchantOrderId = $request->merchant_orderid;
		$status          = strtolower($request->status);
		$reason          = $request->reason ?? null;

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
			'notes'      => $reason,
		];

		// If payment captured, update wallet too
		if ($status === 'captured') {
			$transaction->user->increment('balance', $transaction->txn_amount);

			$updateData['comments'] = 'Payment successful. Wallet updated.';
		}

		$transaction->update($updateData);

		return response()->json(['status' => 'ok']);
	} 
}
