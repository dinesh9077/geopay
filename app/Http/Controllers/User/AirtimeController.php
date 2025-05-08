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
	use Helper, Notification;
	use Carbon\Carbon;
	use App\Services\AirtimeService;
	use Barryvdh\DomPDF\Facade\Pdf;
	
	class AirtimeController extends Controller
	{
		use WebResponseTrait;
		protected $airtimeService;
		public function __construct()
		{
			$this->airtimeService = new AirtimeService();
			$this->middleware('auth')->except('internationalAirtimeCallback');
		}
		
		public function internationalAirtime()
		{ 
			//$countries = $this->airtimeService->getCountries(); 
			
			$countries = Country::get();

			$countriesWithFlags = $countries->transform(function ($country) {
				if ($country->country_flag) {
					$country->country_flag = asset('country/' . $country->country_flag);
				} 
				return $country;
			});
			
			return view('user.transaction.international-airtime', compact('countries', 'countriesWithFlags'));
		}
		
		public function internationalAirtimeOperator(Request $request)
		{ 
			try 
			{ 
				$countryCode = $request->country_code;
				$response = $this->airtimeService->getOperators($countryCode); 
				if (!$response['success']) {
					$errorMsg = 'Operator not found.';
					throw new \Exception($errorMsg);
				}
				
				return $this->successResponse('Operator fetched successfully.', $response['response']);
			} 
			catch (\Throwable $e)
			{ 
				return $this->errorResponse($e->getMessage());
			} 
		}
		
		public function internationalAirtimeProduct(Request $request)
		{ 
			try
			{
				$countryCode = $request->country_code;
				$operatorId = $request->operator_id;
				$response = $this->airtimeService->getProducts($countryCode, $operatorId); 
				if (!$response['success']) {
					$errorMsg = 'Product not found.';
					throw new \Exception($errorMsg);
				}
				
				return $this->successResponse('product fetched successfully.', $response['response']);
			} 
			catch (\Throwable $e)
			{ 
				return $this->errorResponse($e->getMessage());
			} 
		}
		
		public function internationalAirtimeValidatePhone(Request $request)
		{ 
			try
			{
				$mobile_number = '+' . ltrim($request->mobile_number, '+');
				$operator_id = $request->operator_id;
				$response =  $this->airtimeService->getValidatePhoneByOperator($mobile_number, $operator_id, true); 
				
				if (!$response['success']) 
				{	 
					$errorMsg = is_array($response['response']) ? $response['response']['errors'][0]['message'] : 'The operator is not identified for this mobile number.';
					throw new \Exception($errorMsg);
				}
				
				return $this->successResponse('product fetched successfully.', $response['response']);
			} 
			catch (\Throwable $e)
			{ 
				return $this->errorResponse($e->getMessage());
			} 
		}
		
		public function internationalAirtimeStore(Request $request)
		{    
			if($request->wholesale_unit_amount > $request->retail_unit_amount)
			{
				return $this->errorResponse('Technical issue detected. Please contact support.');
			}
			
			$user = auth()->user(); 
			// Validation rules
			$validator = Validator::make($request->all(), [
				'product_name' => 'required|string', 
				'wholesale_unit_amount' => 'required|numeric', 
				'retail_unit_amount' => 'required|numeric', 
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
				if ($request->input('retail_unit_amount') > $user->balance) {
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
				$request['order_id'] = "GPIA-".$user->id."-".time();
				
				$transactionLimit = $user->is_company == 1 
				? config('setting.company_pay_monthly_limit') 
				: ($user->userLimit->daily_pay_limit ?? 0);
				 
				$transactionAmountQuery = Transaction::whereIn('platform_name', ['international airtime', 'transfer to bank', 'transfer to mobile'])
				->where('user_id', $user->id); 
				
				// Adjust the date filter based on whether the user is a company or an individual
				if ($user->is_company == 1) {
					$transactionAmountQuery->whereMonth('created_at', Carbon::now()->month);
					} else {
					$transactionAmountQuery->whereDate('created_at', Carbon::today());
				}
				
				// Calculate the total transaction amount
				$transactionAmount = $transactionAmountQuery->sum('txn_amount');
				
				// Check if the transaction amount exceeds the limit
				if ($transactionAmount >= $transactionLimit) {
					$limitType = $user->is_company == 1 ? 'monthly' : 'daily';
					return $this->errorResponse(
					"You have reached your {$limitType} transaction limit of {$remitCurrency} {$transactionLimit}. " .
					"Current total transactions: {$remitCurrency} {$transactionAmount}."
					);
				}
				
				$response = $this->airtimeService->transactionRecord($request, $user);  
				if (!$response['success']) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
					throw new \Exception($errorMsg);
				}
				
				$txnAmount = $request->input('retail_unit_amount') + $request->input('platform_fees');
				$productName = $request->input('product_name');
				$mobileNumber = '+' . ltrim($request->input('mobile_number'), '+');
				
				$txnStatus = strtolower($response['response']['status']['message']) ?? 'process';
				
				$productId = $request->input('product_id');
				
				$productResponse = $this->airtimeService->getProductById($productId); 
				$productRes = [];
				if($productResponse['success'])
				{
					$productRes = $productResponse['response'];
				}
				// Deduct balanced
				$user->decrement('balance', $txnAmount); 
				$comments = "Your recharge of $ {$txnAmount} for $productName has been successfully processed. We appreciate your continued trust in GEOPAY for your mobile top-ups.";
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
				'unique_identifier' => $response['response']['external_id'] ?? '',
				'product_name' => $productName,
				'operator_id' => $request->input('operator_id'),
				'product_id' => $productId,
				'mobile_number' => $mobileNumber,
				'unit_currency' => $request->input('destination_currency', ''),
				'unit_amount' => $request->input('destination_rates', 0),
				'unit_rates' => $request->input('retail_unit_amount', 0),
				'rates' => $request->input('retail_rates', 0),
				'unit_convert_currency' => $request->input('wholesale_unit_currency', ''),
				'unit_convert_amount' => $request->input('wholesale_unit_amount', ''),
				'unit_convert_exchange' => $request->input('wholesale_rates', 0),
				'api_request' => $response['request'],
				'api_response' => $response['response'],
				'api_response_second' => $productRes,
				'order_id' => $request->order_id,
				'fees' => $request->input('platform_fees'),
				'total_charge' => $txnAmount,
				'created_at' => now(),
				'updated_at' => now(),
				]);
				
				// Log the transaction creation
				Helper::updateLogName($transaction->id, Transaction::class, 'international airtime transaction', $user->id);
				
				Notification::send($user, new AirtimeRefundNotification($user, $txnAmount, $transaction->id, $comments, $transaction->notes, ucfirst($txnStatus)));
				
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
			// Get txn status and unique identifier from the request
			$txnStatus = strtolower($request->input('status.class.message', 'process'));
			$uniqueIdentifier = $request->input('external_id', '');

			// Early return if txnStatus or uniqueIdentifier is empty
			if (empty($txnStatus) || empty($uniqueIdentifier)) {
				return response()->json(['error' => 'Missing required parameters'], 400); 
			}

			// Check for declined, cancelled, or rejected status
			if (in_array($txnStatus, ['rejected', 'cancelled', 'declined'])) {
				$transaction = Transaction::where('unique_identifier', $uniqueIdentifier)->first();

				if (!$transaction) {
					return response()->json(['error' => 'Transaction not found'], 404);
				}
				 
				if (Transaction::where('unique_identifier', $uniqueIdentifier)
					->whereIn('txn_status', ['rejected', 'cancelled', 'declined'])
					->exists()) {
					return response()->json(['error' => 'Transaction already made'], 404);
				}


				$txnAmount = $transaction->txn_amount;

				// Update user's balance
				$user = User::find($transaction->user_id);
				if ($user) {
					$user->increment('balance', $txnAmount);
					
					$transactionId = $transaction->id;
					$duetomsg = strtolower($request->input('status.message', 'technical issue'));
					// Prepare the comments for the refund
					$comments = "You have successfully refunded $txnAmount USD for {$transaction->product_name} to this order ID {$uniqueIdentifier} due to {$duetomsg}";
					$notes = 'Refund for transaction: ' . $uniqueIdentifier;
					// Clone transaction data and exclude 'id'
					$newTransactionData = $transaction->toArray();
					unset($newTransactionData['id']); // Remove the 'id' field to avoid duplication

					// Add necessary changes for the new transaction
					$newTransactionData['user_id'] = $user->id;
					$newTransactionData['receiver_id'] = $user->id;
					$newTransactionData['txn_status'] = $txnStatus;
					$newTransactionData['comments'] = $comments;
					$newTransactionData['transaction_type'] = 'credit';
					$newTransactionData['total_charge'] = $txnAmount;
					$newTransactionData['notes'] = $notes;
					$newTransactionData['created_at'] = now();
					$newTransactionData['updated_at'] = now();

					// Create a new transaction record for the refund
					$newTransaction = Transaction::create($newTransactionData);

					// Log the transaction creation
					Helper::updateLogName($newTransaction->id, Transaction::class, 'international airtime transaction', $user->id);
					
					Notification::send($user, new AirtimeRefundNotification($user, $txnAmount, $newTransaction->id, $comments, $notes, ucfirst($txnStatus)));
					 
					// Update the original transaction status
					//	Transaction::where('id', $transactionId)->update(['txn_status' => $txnStatus]); 
					return response()->json(['message' => 'Refund processed successfully', 'transaction' => $newTransaction]);
				} else {
					return response()->json(['error' => 'User not found'], 404);
				}
			}

			Transaction::where('unique_identifier', $uniqueIdentifier)->update(['txn_status' => $txnStatus]);
			  
			return response()->json(['error' => 'Transaction not found'], 404);
		} 
	}
