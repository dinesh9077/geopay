<?php
	namespace App\Http\Controllers\ApiProvider;
	
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
	use App\Http\Traits\ApiServiceResponseTrait; 
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
		use ApiServiceResponseTrait;  
		
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
			$user = auth()->user(); 
			$payoutCountry = $user->merchantCorridors->where('service', 'mobile_money')->pluck('payout_country')->toArray(); 
		 
			$countries = Country::with('channels:id,country_id,channel')
			->select('id', 'nicename', 'iso', 'iso3', 'currency_code')
			->whereHas('channels')
			->whereIn('iso3', $payoutCountry)
			->get(); 
			  
			return $this->successResponse('country fetched successfully.', $countries);
		} 
		
		public function getFields()
		{ 
			$fields = [
				["fieldName" => "recipient_mobile", "fieldLabel" => "Recipient Mobile Number", "required" => true, "inputType" => "text"],
				["fieldName" => "recipient_name", "fieldLabel" => "Recipient Name", "required" => true, "inputType" => "text"],
				["fieldName" => "recipient_surname", "fieldLabel" => "Recipient Surname", "required" => true, "inputType" => "text"],
				["fieldName" => "recipient_address", "fieldLabel" => "Recipient Address", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_city", "fieldLabel" => "Recipient City", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_state", "fieldLabel" => "Recipient State", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_postalcode", "fieldLabel" => "Recipient Postal Code", "required" => false, "inputType" => "text"],
				["fieldName" => "recipient_dateofbirth", "fieldLabel" => "Recipient Date Of Birth", "required" => false, "inputType" => "date"],
				["fieldName" => "sender_placeofbirth", "fieldLabel" => "Sender Date Of Birth", "required" => true, "inputType" => "date"],
				["fieldName" => "purposeOfTransfer", "fieldLabel" => "Purpose Of Transfer", "required" => true, "inputType" => "text"],
				["fieldName" => "sourceOfFunds", "fieldLabel" => "Source Of Funds", "required" => true, "inputType" => "text"]
			];

			return $this->successResponse('fields fetched successfully.', $fields);
		}  
		
		public function createTransaction(Request $request)
		{	
			$user = Auth::user(); 
			$validator = Validator::make($request->all(), [
				// Transaction details
				'amount' => 'required|numeric|min:0.01',
				'exchange_rate_id' => 'required|integer',
				'exchange_rate' => 'required|numeric',
				'converted_amount' => 'required|numeric',
				'payoutCurrency' => 'required|string|size:3',
				'channel_name' => 'required|string',

				// Sender
				'sender_mobile' => 'required|string|max:20',
				'sender_country_code' => 'required|string|size:2',
				'sender_name' => 'required|string|max:100',
				'sender_surname' => 'required|string|max:100',
				'sender_address' => 'required|string|max:255',
				'sender_city' => 'required|string|max:100',
				'sender_state' => 'required|string|max:100',
				'sender_postalCode' => 'required|string|max:20',
				'sender_email' => 'nullable|email',
				'sender_dateOfBirth' => 'nullable|date',
				'sender_document' => 'nullable|string|max:255',
				'sender_placeofbirth' => 'nullable|string|max:100',

				// Recipient
				'recipient_mobile' => 'required|string|max:20',
				'recipient_country_code' => 'required|string|size:2',
				'recipient_name' => 'required|string|max:100',
				'recipient_surname' => 'required|string|max:100',
				'recipient_address' => 'nullable|string|max:255',
				'recipient_city' => 'nullable|string|max:100',
				'recipient_state' => 'nullable|string|max:100',
				'recipient_postalcode' => 'nullable|string|max:20',
				'recipient_email' => 'nullable|email',
				'recipient_dateofbirth' => 'nullable|date',
				'recipient_document' => 'nullable|string|max:255',
				'recipient_destinationAccount' => 'nullable|string|max:255',

				// Transfer details
				'purposeOfTransfer' => 'required|string|max:255',
				'sourceOfFunds' => 'required|string|max:255',
			]); 

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}
  
			try {
				DB::beginTransaction(); 
				
				$txnAmount = $request->input('amount');
				$netAmount = $request->input('amount');
				
				$totalCharge = 0; 
				$mobileMoneyCharge = $user->mobileMoneyCharge ?? null; 
				if ($mobileMoneyCharge) {
					$chargeType = $mobileMoneyCharge->charge_type ?? 'flat';
					$chargeValue = $mobileMoneyCharge->charge_value ?? 0;
					
					if($chargeType === "percentage")
					{
						$totalCharge = $netAmount * $chargeValue / 100; 
					}
					else
					{
						$totalCharge = $chargeValue; 
					} 
				}
				
				$netAmount += $totalCharge;  
				if ($netAmount > $user->balance) {
					return $this->errorResponse('Insufficient balance to complete this transaction.', 'ERR_INSUFFICIENT_BALANCE'); 
				}
				
				$request['order_id'] = "GPTM-".$user->id."-".time();
				$request['timestamp'] = now()->format('Y-m-d H:i:s');
				
				$remitCurrency = config('setting.default_currency') ?? 'USD';
				
				$liveExchangeRate = LiveExchangeRate::find($request->exchange_rate_id); 
				if(!$liveExchangeRate)
				{ 
					$liveExchangeRate = ExchangeRate::find($request->exchange_rate_id);
					if (!$liveExchangeRate) {
						return $this->errorResponse('A technical issue has occurred. Please try again later.', 'ERR_RATE', 401); 
					}
				}
				
				$transactionLimit = Transaction::whereIn('platform_name', ['transfer to mobile'])
					->where('user_id', $user->id)
					->whereDate('created_at', Carbon::today())
					->sum('txn_amount');

				$dailyLimit = $user->mobileMoneyLimit->daily_limit ?? 0;

				if (!$dailyLimit || $transactionLimit >= $dailyLimit) {
					return $this->errorResponse(
						"You have reached your daily transaction limit.",
						'ERR_DAILY_LIMIT_EXCEEDED',
						403
					);
				}
			 
				$response = $this->onafricService->apiSendMobileTransaction($request, $user);
				  
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
				$apiStatus = $onafricStatus;
				$txnStatus = OnafricStatus::from($onafricStatus)->label();
				  
				// Deduct balance
				$user->decrement('balance', $netAmount); 
				 
				// Check if necessary fields exist to prevent undefined index warnings
				$beneficiaryFirstName = $request->recipient_name ?? '';
				$beneficiaryLastName = $request->recipient_surname ?? '';
				$mobileNumber = ltrim(($request->recipient_mobile ?? ''), '+');
				$payoutCurrency = $request->payoutCurrency ?? '';
				$payoutCurrencyAmount = $request->converted_amount;
				$aggregatorCurrencyAmount = ($liveExchangeRate->aggregator_rate * $txnAmount);
				$exchangeRate = $request->exchange_rate; 
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
					'platform_provider' => 'onafric',
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
					'unit_convert_exchange' => $liveExchangeRate->aggregator_rate ?? 0,
					'beneficiary_request' => ['data' => $request->all()],
					'api_request' => $response['request'] ?? [],
					'api_response' => $response['response'] ?? [],
					'order_id' => $request->order_id,
					'fees' => 0,
					'service_charge' => 0,
					'total_charge' => $totalCharge,
					'is_api_service' => 1,
					'api_status' => $apiStatus,
					'created_at' => now(),
					'updated_at' => now(),
				]);

				// Log the transaction creation
				Helper::updateLogName($transaction->id, Transaction::class, 'api transfer to mobile transaction', $user->id);  
				DB::commit();  
				
				$data = [
					"thirdPartyId" => $confirmationId,  
					"status_message" => $txnStatus,
					"timestamp" => now()
				]; 

				return $this->successResponse('transaction successfully processed.', $data);
			} 
			catch (\Throwable $e)
			{
				DB::rollBack();  
				return $this->errorResponse($e->getMessage(), 'ERR_INTERNAL_SERVER'); 
			}   
		}   
		
	}
