<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\WebResponseTrait;
use App\Notifications\WalletTransactionNotification;
use Illuminate\Support\Facades\Notification;
use Helper;
use App\Services\AirtimeService;

class TransactionController extends Controller
{
	use WebResponseTrait;
	protected $airtimeService;
    public function __construct()
    {
		$this->airtimeService = new AirtimeService();
        $this->middleware('auth')->except('internationalAirtimeCallback');
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
				 
				// Check if the mobile number is registered and KYC is approved
				if (!User::where('formatted_number', $formattedNumber)->where('is_kyc_verify', 1)->exists()) {
					$validator->errors()->add('mobile_number', 'The mobile number entered is not linked to an account with approved KYC. Please complete your KYC verification to proceed.');
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
			$orderId = "GPWW-".time();
			// Create a transaction record
			$creditTransaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $toUser->id,
				'platform_name' => 'geopay to geopay wallet',
				'platform_provider' => 'geopay to geopay wallet',
				'transaction_type' => 'credit', // Indicating that the user is debiting funds
				'country_id' => $toUser->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success', // Assuming the transaction is successful
				'comments' => $toComment,
				'notes' => $notes,
				'order_id' => $orderId,
				'created_at' => now(),
				'updated_at' => now(),
			]);
			
			Helper::updateLogName($creditTransaction->id, Transaction::class, 'wallet to wallet transaction', $toUser->id);
			
			// Create a transaction record
			$debitTransaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'geopay to geopay wallet',
				'platform_provider' => 'geopay to geopay wallet',
				'transaction_type' => 'debit', // Indicating that the user is debiting funds
				'country_id' => $user->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success', // Assuming the transaction is successful
				'comments' => $fromComment,
				'notes' => $notes,
				'order_id' => $orderId,
				'created_at' => now(),
				'updated_at' => now(),
			]);
			
			Helper::updateLogName($debitTransaction->id, Transaction::class, 'wallet to wallet transaction', $user->id);
			 
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
	
	public function internationalAirtime()
	{ 
		$countries = $this->airtimeService->getCountries(); 
		return view('user.transaction.international-airtime', compact('countries'));
	}
	
	public function internationalAirtimeOperator(Request $request)
	{ 
		$countryCode = $request->country_code;
		return $this->airtimeService->getOperators($countryCode, true); 
	}
	
	public function internationalAirtimeProduct(Request $request)
	{ 
		$countryCode = $request->country_code;
		$operatorId = $request->operator_id;
		return $this->airtimeService->getProducts($countryCode, $operatorId, true); 
	}
	

	public function internationalAirtimeValidatePhone(Request $request)
	{ 
		$mobile_number = '+' . ltrim($request->mobile_number, '+');
		$operator_id = $request->operator_id;
		return $this->airtimeService->getValidatePhoneByOperator($mobile_number, $operator_id, true); 
	}
	
	public function internationalAirtimeStore(Request $request)
	{ 
		$user = auth()->user();
		
		// Validation rules
		$validator = Validator::make($request->all(), [
			'product_name' => 'required|string', 
			'unit_convert_amount' => 'required|numeric', 
			'unit_convert_exchange' => 'required|numeric', 
			'country_code' => 'required|string', 
			'operator_id' => 'required|integer',
			'product_id' => 'required|integer',
			'mobile_number' => 'required|integer', 
			'is_operator_match' => 'required|integer|in:0,1', 
			'notes' => 'nullable|string',
		]);
		 
		// Custom validation logic
		$validator->after(function ($validator) use ($request, $user)
		{
			// Check if user has sufficient balance
			if ($request->input('unit_convert_amount') > $user->balance) {
				$validator->errors()->add('product_id', 'Insufficient balance to complete this transaction.');
			}
			
			if($request->input('is_operator_match') == 0)
			{
				$validator->errors()->add('mobile_number', 'The operator is not identified for this mobile number.');
			}
		});
		 
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
			
		try {
			
			DB::beginTransaction();
			 
			$response = $this->airtimeService->transactionRecord($request, $user, true); 
			  
			if (!$response['success']) {
				$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
				throw new \Exception($errorMsg);
			}
            //Log::info($response);
			// Transaction variables
			$txnAmount = $request->input('unit_convert_amount');
			$productName = $request->input('product_name');
			$mobileNumber = '+' . ltrim($request->input('mobile_number'), '+');
			
			$statusMessage = strtoupper($response['response']['status']['message']);
            $txnStatus = '';
            
            switch ($statusMessage) {
                case 'COMPLETED':
                    $txnStatus = 'success';
                    break;
                case 'DECLINED':
                    $txnStatus = 'declined';
                    break;
                default:
                    $txnStatus = 'process';
            }
  
			// Deduct balance
			$user->decrement('balance', $txnAmount);
			$orderId = "GPIA-".time();
			$comments = "You have successfully recharged $txnAmount USD for $productName.";
			// Create transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'international airtime',
				'platform_provider' => 'airtime',
				'transaction_type' => 'debit',
				'country_id' => $user->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => $txnStatus,
				'comments' => $comments,
				'notes' => $request->input('notes'),
				'unique_identifier' => $response['response']['external_id'],
				'product_name' => $productName,
				'operator_id' => $request->input('operator_id'),
				'product_id' => $request->input('product_id'),
				'mobile_number' => $mobileNumber,
				'unit_currency' => $request->input('unit_currency', ''),
				'unit_amount' => $request->input('unit_amount', ''),
				'rates' => $request->input('rates', ''),
				'unit_convert_currency' => $request->input('unit_convert_currency', ''),
				'unit_convert_amount' => $txnAmount,
				'unit_convert_exchange' => $request->input('unit_convert_exchange', 0),
				'api_request' => json_encode($response['request']),
				'api_response' => json_encode($response['response']),
				'order_id' => $orderId,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// Log the transaction creation
			Helper::updateLogName($transaction->id, Transaction::class, 'international airtime transaction', $user->id);
			  
			DB::commit(); 
			// Success response
			return $this->successResponse('The transaction was completed successfully.');
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        } 
	}
	
	public function internationalAirtimeCallback(Request $request)
	{ 
		if (!isset($request['external_id'], $request['status']['message'])) {
			return ;
		}

		$uniqueIdentifier = $request['external_id'];
		$statusMessage = strtoupper($request['status']['message']);
 
        $txnStatus = '';
        
        switch ($statusMessage) {
            case 'COMPLETED':
                $txnStatus = 'success';
                break;
            case 'DECLINED':
                $txnStatus = 'declined';
                break;
            default:
                $txnStatus = 'process';
        }
 
		$updated = Transaction::where('unique_identifier', $uniqueIdentifier)
			->update(['txn_status' => $txnStatus]);

		return true;
	}

}
