<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\WebResponseTrait;
use App\Notifications\WalletTransactionNotification;
use Illuminate\Support\Facades\Notification;
use Helper;

class TransactionController extends Controller
{
	use WebResponseTrait;
	
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function walletToWallet()
    { 
		$countries = Country::select('id', 'name', 'isdcode', 'country_flag')->get();

        $countriesWithFlags = $countries->transform(function ($country) {
            if ($country->country_flag) {
                $country->country_flag = asset('country/' . $country->country_flag);
            } 
            return $country;
        });
        return view('user.transaction.wallet-to-wallet', compact('countriesWithFlags'));
    }
	
	public function walletToWalletStore(Request $request)
	{
		$user = auth()->user();
		
		// Validation rules
		$validator = Validator::make($request->all(), [
			'country_id' => 'required|integer|exists:countries,id', // Check if country_id exists in the 'countries' table
			'mobile_number' => 'required|integer',
			'amount' => 'required|numeric|gt:0',
			'notes' => 'nullable|string',
		]);
		
		// Retrieve country details (if exists)
		$country = Country::find($request->country_id);

		// Custom validation logic
		$validator->after(function ($validator) use ($request, $user, $country)
		{
			if ($request->input('country_id') && $request->input('mobile_number')) 
			{
				$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');

				// Check if user is trying to pay themselves
				if ($formattedNumber === $user->formatted_number) {
					$validator->errors()->add('mobile_number', 'You cannot transfer funds to your own account.');
				}

				// Check if the mobile number is registered
				if (!User::where('formatted_number', $formattedNumber)->exists()) {
					$validator->errors()->add('mobile_number', 'The provided mobile number is not registered.');
				}
				 
				// Check if user has sufficient balance
				if ($request->input('amount') > $user->balance) {
					$validator->errors()->add('amount', 'Insufficient balance to complete this transaction.');
				}
			}
		});
		 
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		try {
			
			DB::beginTransaction();

			$txnAmount = $request->amount;
			$countryId = $request->country_id;
			$notes = $request->notes;

			// Format the mobile number again to ensure correct recipient
			$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			
			// Retrieve the recipient user
			$toUser = User::where('formatted_number', $formattedNumber)->first();

			// Check if recipient user is found
			if (!$toUser) {
				return $this->errorResponse('Recipient user not found.');
			}
 
			// Update sender's balance (debit the amount)
			$user->decrement('balance', $txnAmount);

			// Update receiver's balance (credit the amount)
			$toUser->increment('balance', $txnAmount);
			
			$fromComment = 'You have successfully transferred ' . $txnAmount . ' USD to ' . $toUser->first_name . ' ' . $toUser->last_name . '.';
			$toComment = $user->first_name . ' ' . $user->last_name . ' has sent you ' . $txnAmount . ' USD to your wallet.';
			
			// Create a transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $toUser->id,
				'platform_name' => 'wallet to wallet',
				'platform_provider' => 'wallet to wallet',
				'transaction_type' => 'debit', // Indicating that the user is debiting funds
				'country_id' => $countryId,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success', // Assuming the transaction is successful
				'comments' => $fromComment,
				'notes' => $notes,
				'created_at' => now(),
				'updated_at' => now(),
			]);
			
			Helper::updateLogName($transaction->id, Transaction::class, 'wallet to wallet transaction', $user->id);
			 
			Notification::send($user, new WalletTransactionNotification($user, $toUser, $txnAmount, $fromComment, $notes)); // Sender Notification
			Notification::send($toUser, new WalletTransactionNotification($user, $toUser, $txnAmount, $toComment, $notes)); // Receiver Notification

 
			DB::commit();

			// Success response
			return $this->successResponse('The wallet transaction was completed successfully.');
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        } 
	}
}
