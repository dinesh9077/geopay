<?php
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\{ 
		User, Banner, Faq, Setting, Country, Transaction, Beneficiary 
	};
	use Illuminate\Support\Facades\{Http, Storage, Hash, DB, Log, Auth, Notification};
	use App\Http\Traits\ApiResponseTrait;
	use Validator;
	use ImageManager, Helper;
	use App\Notifications\WalletTransactionNotification;
	
	class TransactionController extends Controller
	{ 
		use ApiResponseTrait;  
		
		public function walletToWalletStore(Request $request)
		{ 
			$user = auth()->user();
			
			// Validation rules
			$validator = Validator::make($request->all(), [
				'country_id' => 'required|integer|exists:countries,id', // Check if country_id exists in the 'countries' table
				'mobile_code' => 'required|string',
				'mobile_number' => 'required|string',
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
					$formattedNumber = '+' . ltrim(($request->mobile_code ?? $country->isdcode ?? '') . $request->mobile_number, '+');
					
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
				$formattedNumber = '+' . ltrim(($request->mobile_code ?? $country->isdcode ?? '') . $request->mobile_number, '+');
				
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
				
				$fromComment = 'You have successfully transferred ' . $txnAmount . ' USD to ' . $toUser->first_name . ' ' . $toUser->last_name . ' via Wallet-to-Wallet. Thank you for choosing GEOPAY for secure wallet transfers.';
				$toComment = $user->first_name . ' ' . $user->last_name . ' has sent you ' . $txnAmount . ' USD to your wallet.';
				
				$orderId = "GPWW-".$user->id."-".time();
				// Create a transaction record
				$creditTransaction = Transaction::create([
					'user_id' => $toUser->id,
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
		
		public function transactionList(Request $request)
		{
			// Global search value
			$start = $request->input('start'); 
			$limit = $request->input('limit');  
			$orderDirection = 'desc';   
			$search = $request->input('search');

			$query = Transaction::where('user_id', auth()->user()->id);

			// Apply filters dynamically based on request inputs
			if ($request->filled('platform_name')) {
				$query->where('platform_name', $request->platform_name);
			}

			if ($request->filled(['start_date', 'end_date'])) {
				if ($request->start_date === $request->end_date) {
					// If both dates are the same, use 'whereDate' for exact match
					$query->whereDate('created_at', $request->start_date);
				} else {
					// Otherwise, use 'whereBetween' for the range
					$query->whereBetween('created_at', [$request->start_date, $request->end_date]);
				}
			}


			if ($request->filled('txn_status')) {
				$query->where('txn_status', $request->txn_status);
			}

			// Apply search filter if present
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->orWhere('platform_name', 'LIKE', "%{$search}%")
						->orWhere('order_id', 'LIKE', "%{$search}%")
						->orWhere('comments', 'LIKE', "%{$search}%")
						->orWhere('notes', 'LIKE', "%{$search}%")
						->orWhere('transaction_type', 'LIKE', "%{$search}%")
						->orWhere('txn_amount', 'LIKE', "%{$search}%")
						->orWhere('created_at', 'LIKE', "%{$search}%");
				});
			}
  
			// Apply ordering, limit, and offset for pagination
			$values = $query
				->orderBy('id', $orderDirection) 
				->offset($start)
				->limit($limit)
				->get()->map(function ($item) {
					return collect($item)
					->put('receipt_url', url('transaction-receipt-pdf', $item->id))
					->except(['api_request', 'api_response', 'beneficiary_request', 'api_response_second']);
				});

			$data['data'] = $values;
			$txnStatuses = Transaction::select('txn_status')
			->groupBy('txn_status')
			->pluck('txn_status');
			$data['txnStatuses'] = $txnStatuses;
			$transactionTypes = [
				'geopay to geopay wallet',
				'add money',
				'international airtime',
				'transfer to bank',
				'transfer to mobile',
				'admin transfer',
			];
			$data['transactionTypes'] = $transactionTypes;
			return $this->successResponse('transaction fetched successfully.', $data);	
		}
		
		public function beneficieryList(Request $request)
		{	 
			if(!in_array($request->type, ["transfer to bank", "transfer to mobile"]))
			{
				return $this->errorResponse("You have send type not match"); 
			}
			
			$beneficiaries = Beneficiary::where('user_id', auth()->user()->id)
				->where('category_name', $request->type)  
				->get();

			foreach ($beneficiaries as $beneficiary)
			{  
				if($request->type === "transfer to mobile")
				{
					$beneficiary->country_detail = [
						'id'   => $beneficiary->mobileCountry()->id ?? '',
						'country_code'   => $beneficiary->mobileCountry()->id ?? '',
						'flag' => $beneficiary->mobileCountry()->country_flag 
									? url('country/' . $beneficiary->mobileCountry()->country_flag) 
									: ''
					]; 
				}
				else{
					$country = $beneficiary->country(); // Access the related model, not the query

					if ($country) {
						$country->country_flag = !empty($country->country_flag) 
												? url('country/' . $country->country_flag)  
												: '';
						$beneficiary->country_detail = $country;
					} else {
						$beneficiary->country_detail = null;
					} 
				}
			}

			return $this->successResponse('beneficiary fetched successfully.', $beneficiaries);	
		}
	}
