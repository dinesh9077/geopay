<?php
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Country;
	use App\Models\Transaction;
	use App\Models\Beneficiary;
	use App\Models\User;
	use App\Models\LightnetCatalogue;
	use App\Models\LiveExchangeRate;
	use App\Models\ExchangeRate;
	use App\Models\OnafricChannel;
	use App\Models\LightnetCountry;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Validator;
	use App\Http\Traits\ApiResponseTrait; 
	use App\Services\{ 
		MasterService, LiquidNetService, OnafricService
	}; 
	use App\Notifications\WalletTransactionNotification;
	use App\Notifications\AirtimeRefundNotification;
	use Illuminate\Support\Facades\Notification;
	use Helper;
	use Carbon\Carbon;
	use App\Enums\OnafricStatus;
 
	class TransferMobileController extends Controller
	{ 
		use ApiResponseTrait;  
		protected $liquidNetService;
		protected $masterService;
		protected $onafricService;
		public function __construct()
		{
			$this->liquidNetService = new LiquidNetService(); 
			$this->masterService = new MasterService(); 
			$this->onafricService = new OnafricService();  
		}	
		
		public function countryList()
		{ 
			return $this->successResponse('country fetched successfully.', $this->availableCountries());
		}
		
		public function availableCountries()
		{ 
			$countries = $this->onafricService->country();
			return $countries;
		}
		
		public function beneficiaryList(Request $request)
		{  
			// Extract request data
			$userId = Auth::id();
			$recipientCountry = $request->recipient_country; 
			$categoryName = $request->categoryName;
			$serviceName = $request->serviceName;
			 
			// Fetch beneficiaries with filters
			$beneficiaries = Beneficiary::where('user_id', $userId)
			->where('category_name', $categoryName)
			->where('service_name', $serviceName)
			->where('data->recipient_country', $recipientCountry) 
			->get(); 

			// Return response
			return $this->successResponse('beneficiary list fetched', $beneficiaries);
		}
		
		public function beneficiaryDelete($id)
		{
			try {
				DB::beginTransaction();

				// Fetch the beneficiary
				$beneficiary = Beneficiary::find($id);
				 
				// Check if the beneficiary exists
				if (!$beneficiary) {
					throw new \Exception('Beneficiary not found.');
				}

				// Log ID before deletion
				Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to mobile beneficiary');

				// Delete the beneficiary
				$beneficiary->delete();

				DB::commit(); 
				return $this->successResponse('The beneficiary was deleted successfully.'); 
			} catch (\Throwable $e) {
				DB::rollBack();  
				return $this->errorResponse($e->getMessage()); 
			}  
		}
		
		public function commission(Request $request)
		{
			$beneficiaryId = $request->beneficiaryId;
			$txnAmount = $request->txnAmount;
			 
			$beneficiary = Beneficiary::find($beneficiaryId);
			if (!$beneficiary || empty($beneficiary->data ?? [])) {
				return $this->errorResponse('Beneficiary not found.');
			}
			
			$country = Country::find($beneficiary->data['recipient_country']);
			$liveExchangeRate = LiveExchangeRate::select('markdown_rate', 'aggregator_rate')
			->where('channel', $beneficiary->data['service_name'])
			->where('currency', $country->currency_code)
			->first(); 
			
			if(!$liveExchangeRate)
			{
				$liveExchangeRate = ExchangeRate::select('exchange_rate as markdown_rate', 'aggregator_rate')
				->where('type', 2)
				->where('service_name', $beneficiary->data['service_name'])
				->where('currency', $country->currency_code)
				->first();
				if (!$liveExchangeRate) {
					return $this->errorResponse('A technical issue has occurred. Please try again later.'); 
				}
			}
			
			$aggregatorRate = $liveExchangeRate->aggregator_rate ?? 0;
			$aggregatorCurrencyAmount = ($txnAmount * $aggregatorRate);
			
			$exchangeRate = $liveExchangeRate->markdown_rate ?? 0;
			$payoutCurrencyAmount = ($txnAmount * $exchangeRate);
			$serviceCharge = 0;
			
			$onafricChannel = OnafricChannel::find($beneficiary->data['channel_id'] ?? null);
			
			$sendFee = $onafricChannel && $onafricChannel->fees ? $onafricChannel->fees : 0;
			$commissionType = $onafricChannel && $onafricChannel->commission_type ? $onafricChannel->commission_type : 'flat';
			$commissionCharge = $onafricChannel && $onafricChannel->commission_charge ? $onafricChannel->commission_charge : 0;
			
			$platformFees = $commissionType === "flat"
			? max($commissionCharge, 0) // Ensure flat fee is not negative
			: max(($txnAmount * $commissionCharge / 100), 0); // Ensure percentage fee is not negative
			$totalCharges = $platformFees + $serviceCharge;				
			$comissions = [
				'payoutCurrency' => $country->currency_code,
				'payoutCountry' => $country->iso3,
				'txnAmount' => $txnAmount,
				'aggregatorRate' => $aggregatorRate,
				'aggregatorCurrencyAmount' => $aggregatorCurrencyAmount,
				'exchangeRate' => $exchangeRate,
				'payoutCurrencyAmount' => $payoutCurrencyAmount,
				'remitCurrency' => config('setting.default_currency') ?? 'USD',
				'platformCharge' => $platformFees,
				'serviceCharge' => $serviceCharge,
				'sendFee' => $sendFee,
				'totalCharges' => $totalCharges,
				'netAmount' => ($totalCharges + $txnAmount),
			];
			 
			return $this->successResponse('comission fetched successfully.', $comissions);
		}
		
		public function storeTransaction(Request $request)
		{	
			$user = Auth::user();
	  
			// Validation rules
			$validator = Validator::make($request->all(), [
				'country_code'   => 'required|string|max:10', // Restrict maximum length
				'beneficiaryId'  => 'required|integer|exists:beneficiaries,id', // Explicit column for clarity
				'txnAmount'      => 'required|numeric|gt:0', // Transaction amount must be positive 
				'notes'          => 'nullable|string|max:255', // Restrict notes to 255 characters
			]);

			// Custom validation logic
			$validator->after(function ($validator) use ($request, $user) {
				$netAmount = (float) $request->input('netAmount', 0);
				$aggregatorCurrencyAmount = (float) $request->input('aggregatorCurrencyAmount', 0);
				  
				if ($netAmount > $user->balance) {
					$validator->errors()->add('txnAmount', 'Insufficient balance to complete this transaction.');
				}

				if (!$request->filled('aggregatorCurrencyAmount')) {
					$validator->errors()->add('txnAmount', 'The payout currency amount field is required.');
				} elseif ($aggregatorCurrencyAmount <= 0) {
					$validator->errors()->add('txnAmount', 'The payout currency amount must be greater than 0.');
				}
			});

			// Return validation response if fails
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
		 
			try {
				DB::beginTransaction(); 
				$request['order_id'] = "GPTM-".$user->id."-".time();
				$request['timestamp'] = now()->format('Y-m-d H:i:s');
				
				$remitCurrency = config('setting.default_currency') ?? 'USD';
				
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
				
				$beneficiary = Beneficiary::find($request->beneficiaryId);
				if (!$beneficiary || empty($beneficiary->data)) {
					return $this->errorResponse('Something went wrong.');
				}
				
				$response = $this->onafricService->sendMobileTransaction($request, $beneficiary->data);
				  
				if (!$response['success']) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
					throw new \Exception($errorMsg);
				}
				
				$responseCode = $response['response']['details']['transResponse'][0]['status']['code'] ?? 101;

				if ($responseCode != 100) { 
					$responseMessage = $response['response']['details']['transResponse'][0]['status']['message'] ?? 'Rejected';
					$errMessage = $responseMessage . ': ' . ($response['response']['details']['transResponse'][0]['status']['messageDetail'] ?? 'An error occurred.');
					
					throw new \Exception($errMessage);
				}
				 
				$onafricStatus = $response['response']['details']['transResponse'][0]['status']['message'] ?? 'Accepted';
				$txnStatus = OnafricStatus::from($onafricStatus)->label();
			
				$txnAmount = $request->input('txnAmount');
				$netAmount = $request->input('netAmount');
				
				// Deduct balance
				$user->decrement('balance', $netAmount); 
				
				// Check if necessary fields exist to prevent undefined index warnings
				$beneficiaryFirstName = $beneficiary->data['recipient_name'] ?? '';
				$beneficiaryLastName = $beneficiary->data['recipient_surname'] ?? '';  
				$mobileNumber = ltrim(($beneficiary->data['mobile_code'] ?? ''), '+').($beneficiary->data['recipient_mobile'] ?? '');
				$payoutCurrency = $beneficiary->data['payoutCurrency'] ?? '';
				$payoutCurrencyAmount = $request->payoutCurrencyAmount;
				$aggregatorCurrencyAmount = $request->aggregatorCurrencyAmount;
				$exchangeRate = $request->exchangeRate; 
				$confirmationId = $request['order_id'];
				// Concatenate beneficiary name safely
				$beneficiaryName = trim("$beneficiaryFirstName $beneficiaryLastName"); // Using trim to remove any leading/trailing spaces

				// Build the comment using sprintf for better readability
				$comments = sprintf(
					"You have successfully transferred $%s to %s (%s) via Mobile Money.Thank you for trusting GEOPAY for instant mobile money transactions.",
					number_format($netAmount, 2), // Ensure txnAmount is formatted to 2 decimal places
					$beneficiaryName,
					$mobileNumber
				); 
				
				// Create transaction record
				$transaction = Transaction::create([
					'user_id' => $user->id,
					'receiver_id' => $user->id,
					'platform_name' => 'transfer to mobile',
					'platform_provider' => $beneficiary->data['service_name'],
					'transaction_type' => 'debit',
					'country_id' => $user->country_id,
					'txn_amount' => $netAmount,
					'txn_status' => $txnStatus,
					'comments' => $comments,
					'notes' => $request->input('notes'),
					'unique_identifier' => $confirmationId,
					'product_name' => null, 
					'product_id' => null,
					'mobile_number' => $mobileNumber,
					'unit_currency' => $payoutCurrency,
					'unit_amount' => $payoutCurrencyAmount,
					'unit_rates' => $txnAmount,
					'rates' => $exchangeRate,
					'unit_convert_currency' => $payoutCurrency,
					'unit_convert_amount' => $aggregatorCurrencyAmount,
					'unit_convert_exchange' => $request->aggregatorRate ?? 0,
					'beneficiary_request' => $beneficiary,
					'api_request' => $response['request'],
					'api_response' => $response['response'],
					'order_id' => $request->order_id,
					'fees' => $request->platformCharge ?? 0,
					'service_charge' => $request->serviceCharge ?? 0,
					'total_charge' => $request->totalCharges ?? 0,
					'created_at' => now(),
					'updated_at' => now(),
				]);

				// Log the transaction creation
				Helper::updateLogName($transaction->id, Transaction::class, 'transfer to mobile transaction', $user->id); 
				Notification::send($user, new AirtimeRefundNotification($user, $netAmount, $transaction->id, $comments, $transaction->notes, ucfirst($txnStatus)));
				DB::commit();  
				return $this->successResponse('Mobile transfer has been successfully processed.', ['userBalance' => Helper::decimalsprint($user->balance, 2), 'currencyCode' => config('setting.default_currency')]);
			} catch (\Throwable $e) {
				DB::rollBack();  
				return $this->errorResponse($e->getMessage()); 
			}   
		}
		   
		public function getOnafricFieldView()
		{ 
			$fields = [
				["fieldName" => "recipient_mobile", "fieldLabel" => "Recipient Mobile Number", "required" => true, "inputType" => "text"],
				["fieldName" => "recipient_name", "fieldLabel" => "Recipient Name", "required" => true, "inputType" => "text"],
				["fieldName" => "recipient_surname", "fieldLabel" => "Recipient Surname", "required" => true, "inputType" => "text"],
				["fieldName" => "recipient_address", "fieldLabel" => "Recipient Address", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_city", "fieldLabel" => "Recipient City", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_state", "fieldLabel" => "Recipient State", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_postalcode", "fieldLabel" => "Recipient Postal Code", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_dateofbirth", "fieldLabel" => "Recipient Date Of Birth", "required" => false, "inputType" => "date"]
				/* ["fieldName" => "sender_placeofbirth", "fieldLabel" => "Sender Date Of Birth", "required" => true, "inputType" => "date"],
				["fieldName" => "purposeOfTransfer", "fieldLabel" => "Purpose Of Transfer", "required" => true, "inputType" => "text"],
				["fieldName" => "sourceOfFunds", "fieldLabel" => "Source Of Funds", "required" => true, "inputType" => "text"] */
			];

			return $this->successResponse('fields fetched successfully.', $fields);
		}
		 
		public function beneficiaryStore(Request $request)
		{    
			// $recipient_country_code = $request->recipient_country_code; 
			// $recipient_mobile = $request->recipient_mobile;
			// $response = $this->onafricService->getAccountRequest($recipient_country_code, $recipient_mobile);
			
			// if (
			// 	!isset($response['success']) || 
			// 	!$response['success'] || 
			// 	(isset($response['response']['status_code']) && $response['response']['status_code'] != "Active")
			// ) {
				   
			// 	return $this->errorResponse('Provided country and mobile number are not active');
			// }
			
			try { 
				$user = Auth::user();
				 
				DB::beginTransaction();
				$beneficiaryData = $request->except('_token', 'recipient_mobile', 'mobile_code');
			
				$mobile_code = $request->mobile_code ?? '';
				$mobile_num = $request->recipient_mobile ?? ''; 
					
				$beneficiaryData['recipient_mobile'] = $mobile_num ?? '';
				$beneficiaryData['mobile_code'] = $mobile_code ?? '';
				$beneficiaryData['sender_country'] = $user->country->id ?? '';
				$beneficiaryData['sender_country_code'] = $user->country->iso ?? '';
				$beneficiaryData['sender_country_name'] = $user->country->name ?? '';
				$beneficiaryData['sender_mobile'] = isset($user->formatted_number) ? ltrim($user->formatted_number, '+') : '';
				/* $beneficiaryData['sender_name'] = $user->first_name ?? '';
				$beneficiaryData['sender_surname'] = $user->last_name ?? ''; */
				
				$recipientCountry = Country::find($request->recipient_country ?? null);
				$beneficiaryData['payoutCountry'] = $recipientCountry->iso3 ?? '';
				$beneficiaryData['payoutCurrency'] = $recipientCountry->currency_code ?? '';
				
				$data = []; 
				$data['category_name'] = $beneficiaryData['category_name'];
				$data['service_name'] = $beneficiaryData['service_name'];
				$data['user_id'] = Auth::id(); 
				$data['created_at'] = now();
				$data['updated_at'] = now();
				$data['data'] = $beneficiaryData;
				 
				$beneficiary = Beneficiary::create($data);
				Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to mobile beneficiary');
				
				DB::commit(); 
				return $this->successResponse('The beneficiary was completed successfully.');
			} 
			catch (\Throwable $e)
			{ 
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			} 	  	
		}
		
		public function beneficiaryUpdate(Request $request, $id)
		{   
			DB::beginTransaction();
			try {
				
				$user = Auth::user();
				$beneficiary = Beneficiary::find($id);
				
				$recipient_country_code = $request->recipient_country_code; 
				$recipient_mobile = $request->recipient_mobile;
				
				// Ensure beneficiary->data is an array
				$beneficiaryDataArray = is_array($beneficiary->data) ? $beneficiary->data : [];
		
				// if ($recipient_country_code !== ($beneficiaryDataArray['recipient_country_code'] ?? '') ||
				// 	$recipient_mobile !== ($beneficiaryDataArray['recipient_mobile'] ?? '')) 
				// {
				// 	$response = $this->onafricService->getAccountRequest($recipient_country_code, $recipient_mobile);
					
				// 	if (
				// 		!isset($response['success']) || 
				// 		!$response['success'] || 
				// 		(isset($response['response']['status_code']) && $response['response']['status_code'] != "Active")
				// 	) {
						   
				// 		return $this->errorResponse('Provided country and mobile number are not active');
				// 	}	
				// }
				$beneficiaryData = $request->except('_token', 'recipient_mobile', 'mobile_code');
			
				$mobile_code = $request->mobile_code ?? '';
				$mobile_num = $request->recipient_mobile ?? ''; 
					
				$beneficiaryData['recipient_mobile'] = $mobile_num ?? '';
				$beneficiaryData['mobile_code'] = $mobile_code ?? '';
				$beneficiaryData['sender_country'] = $user->country->id ?? '';
				$beneficiaryData['sender_country_code'] = $user->country->iso ?? '';
				$beneficiaryData['sender_country_name'] = $user->country->name ?? '';
				$beneficiaryData['sender_mobile'] = isset($user->formatted_number) ? ltrim($user->formatted_number, '+') : '';
				/* $beneficiaryData['sender_name'] = $user->first_name ?? '';
				$beneficiaryData['sender_surname'] = $user->last_name ?? ''; */
				
				$recipientCountry = Country::find($request->recipient_country ?? null);
				$beneficiaryData['payoutCountry'] = $recipientCountry->iso3 ?? '';
				$beneficiaryData['payoutCurrency'] = $recipientCountry->currency_code ?? '';
				
				$data = []; 
				$data['category_name'] = $beneficiaryData['category_name'];
				$data['service_name'] = $beneficiaryData['service_name'];
				$data['user_id'] = Auth::id(); 
				$data['updated_at'] = now(); 
				$data['data'] = $beneficiaryData;
				  
				$beneficiary->update($data);
				Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to mobile beneficiary');
				
				DB::commit(); 
				return $this->successResponse('The beneficiary was updated successfully.');
			} 
			catch (\Throwable $e)
			{ 
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			} 	
		}
	}
