<?php
	namespace App\Http\Controllers\ApiProvider;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{
		DB, Auth, Log, Validator, Notification
	};
	use App\Models\{
		Country, Transaction, Beneficiary, User, 
		LightnetCatalogue, LiveExchangeRate, ExchangeRate, 
		LightnetCountry, OnafricBank
	};
	use App\Http\Traits\ApiServiceResponseTrait;
	use App\Services\{
		LiquidNetService, OnafricService
	};
	use App\Notifications\WalletTransactionNotification;
	use App\Notifications\AirtimeRefundNotification;
	use Carbon\Carbon;
	use Helper;  
	use App\Enums\LightnetStatus;
	use App\Enums\OnafricStatus;
	
	class TransferBankController extends Controller
	{ 
		use ApiServiceResponseTrait;  
		
		protected $liquidNetService;
		protected $onafricService; 
		public function __construct()
		{
			$this->liquidNetService = new LiquidNetService(); 
			$this->onafricService = new OnafricService();  
		} 
		
		public function countryList()
		{  
			return $this->successResponse('Country fetch successfully.',
				$this->availableCountries()
			);  
		}
		
		public function availableCountries()
		{
			$lightnetCountry = LightnetCountry::select(
				'id',
				'data as payout_country',
				'value as payout_currency',
				'label',
				DB::raw("1 as service"),
				DB::raw("'' as iso")
			)
			->whereNotNull('label')
			->where('status', 1)
			->get();
			  
			$onafricCountry = Country::select('id', 'iso3 as payout_country', 'currency_code as payout_currency', 'nicename as label', DB::raw("2 as service_"), 'iso')
			->whereIn('nicename', $this->onafricService->bankAvailableCountry())
			->get();
			 
			$countriesWithFlags = $lightnetCountry->merge($onafricCountry)->sortBy('label')->values(); 
			return $countriesWithFlags; 
		}
		
		public function bankList(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'payoutCountry' => 'required|string|size:3',  
				'service'       => 'required|integer|in:1,2',
				'payoutIso'     => 'nullable|string|size:2|required_if:service,2',
			], [
				'payoutCountry.required' => 'Payout country is required.',
				'payoutCountry.size'     => 'Payout country must be exactly 3 characters.',
				'service.required'       => 'Service is required.',
				'service.in'             => 'Service must be either 1 or 2.',
				'payoutIso.size'         => 'Payout ISO must be exactly 2 characters.',
				'payoutIso.required_if'  => 'Payout ISO is required when service is 1.',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}
			$service = $request->service; 
			switch ($service) {
				case 1:
					return $this->successResponse('bank fetched successfully.',
						$this->liquidNetService->getAgentLists($request)
					); 
					
				case 2:
					return $this->successResponse('bank fetched successfully.',
						OnafricBank::select(
							'mfs_bank_code as locationId',
							'bank_name as locationName',
							DB::raw("'' as optionalField")
						)
						->where('payout_iso', $request->payoutIso)
						->where('status', 1)
						->get() 
					);
				
				default:
					return $this->successResponse('bank fetched successfully.', []);
			}
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
				$request['order_id'] = "GPTB-".$user->id."-".time();
				$request['timestamp'] = time();
				
				$remitCurrency = config('setting.default_currency');
				
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
				
				if($beneficiary->service_name === "lightnet")
				{
					$response = $this->liquidNetService->sendTransaction($request, $beneficiary->data);
					
					if (!$response['success']) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
						throw new \Exception($errorMsg);
					}
					 
					if($response['response']['code'] != 0)
					{
						$errorMsg = $response['response']['message'] ?? 'An error occurred.';
						throw new \Exception($errorMsg);
					}
					$confirmationId = $response['response']['confirmationId'];
					$txnStatus = 'processing';
				}
				else
				{
					$response = $this->onafricService->sendBankTransaction($request, $beneficiary->data);
				  
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
					$confirmationId = $request['order_id'];
					 
					$onafricStatus  = $response['response']['details']['transResponse'][0]['status']['message'] ?? 'Accepted';
					$txnStatus = OnafricStatus::from($onafricStatus)->label();
				}
				
				$txnAmount = $request->input('txnAmount');
				$netAmount = $request->input('netAmount');
				
				// Deduct balance
				$user->decrement('balance', $netAmount); 
				
				// Check if necessary fields exist to prevent undefined index warnings 
				$beneficiaryFirstName = $beneficiary->data['receiverfirstname'] ?? ($beneficiary->data['beneficiaryFirstName'] ?? '');
				$beneficiaryLastName = $beneficiary->data['receiverlastname'] ?? ($beneficiary->data['beneficiaryLastName'] ?? '');
				$bankName = $beneficiary->data['bankName'] ?? 'Unknown Bank';
				$bankId = $beneficiary->data['bankId'] ?? '';
				$mobileNumber = ltrim(($beneficiary->data['mobile_code'] ?? ''), '+').($beneficiary->data['receivercontactnumber'] ?? '');
				$payoutCurrency = $beneficiary->data['payoutCurrency'] ?? '';
				$payoutCurrencyAmount = $request->payoutCurrencyAmount;
				$aggregatorCurrencyAmount = $request->aggregatorCurrencyAmount;
				$exchangeRate = $request->exchangeRate; 
				
				// Concatenate beneficiary name safely
				$beneficiaryName = trim("$beneficiaryFirstName $beneficiaryLastName"); // Using trim to remove any leading/trailing spaces

				// Build the comment using sprintf for better readability
				$comments = sprintf(
					"Your bank transfer of $%s to %s has been successfully completed. Thanks for using GEOPAY for your seamless bank-to-bank transfers.",
					number_format($netAmount, 2), 
					$bankName
				);
				
				// Create transaction record
				$transaction = Transaction::create([
					'user_id' => $user->id,
					'receiver_id' => $user->id,
					'platform_name' => 'transfer to bank',
					'platform_provider' => $beneficiary->service_name,
					'transaction_type' => 'debit',
					'country_id' => $user->country_id,
					'txn_amount' => $netAmount,
					'txn_status' => $txnStatus,
					'comments' => $comments,
					'notes' => $request->input('notes'),
					'unique_identifier' => $confirmationId,
					'product_name' => $bankName, 
					'product_id' => $bankId,
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
				Helper::updateLogName($transaction->id, Transaction::class, 'transfer to bank transaction', $user->id);
				 
				if($beneficiary->service_name === "lightnet")
				{
					$commitResponse = $this->liquidNetService->commitTransaction($confirmationId, $remitCurrency);

					if (!$commitResponse['success'] || ($commitResponse['response']['code'] ?? 1) != 0) {
						// Provide a clear and user-friendly error message
						$errorMsg = "Your transaction has been accepted but couldn't be committed due to a technical issue. Please visit the transaction list to manually commit the transaction.";
						throw new \Exception($errorMsg);
					}

					// Safely fetch the transaction and update it
					if ($transaction) {
						$commitTransaction = Transaction::find($transaction->id);
						$statusLabel = LightnetStatus::from($commitResponse['response']['status'])->label(); 
						if($statusLabel === "cancelled and refunded")
						{
							$commitTransaction->processAutoRefund($statusLabel);
						}
						$commitTransaction->update(['api_response_second' => $commitResponse['response'], 'txn_status' => $statusLabel]);
					}
					
					$successMsg = $commitResponse['response']['message'];
				}
				
				Notification::send($user, new AirtimeRefundNotification($user, $netAmount, $transaction->id, $comments, $transaction->notes, ucfirst($txnStatus)));
				
				DB::commit();  
				return $this->successResponse($successMsg ?? 'TXN Successfully Accepted.');
			} catch (\Throwable $e) {
				DB::rollBack();  
				return $this->errorResponse($e->getMessage()); 
			}  
		}
		
		 
		public function getFields(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'payoutCountry'  => 'required|string|size:3', 
				'payoutCurrency' => 'required|string|size:3',
				'service'       => 'required|integer|in:1,2',
				'locationId'     => 'required|string',
			], [
				'payoutCountry.required'  => 'Payout country is required.',
				'payoutCountry.size'      => 'Payout country must be exactly 3 characters.',
				'payoutCurrency.required' => 'Payout currency is required.',
				'payoutCurrency.size'     => 'Payout currency must be exactly 3 characters.',
				'service.required'        => 'Service is required.',
				'service.in'              => 'Service must be either 1 or 2.',
				'locationId.required'     => 'Location ID is required.'
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray()); // Using your trait
			}
	
			$payoutCountry = $request->payoutCountry;
			$payoutCurrency = $request->payoutCurrency; 
			$service = $request->service;
			$locationId = $request->locationId;
			
			if($service == 1)
			{  
				return $this->successResponse('fields fetched Successfully.', 
					$this->getLightnetFieldView($payoutCountry, $payoutCurrency, $locationId)
				);
			}
			else
			{
				return $this->successResponse('fields fetched Successfully.', 
					$this->getOnafricFieldView()
				); 
			} 
			
			return []; 
		}
		
		public function getOnafricFieldView()
		{ 
			return [
				["fieldName" => "bankaccountnumber", "fieldLabel" => "Beneficiary Account Number", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receivercontactnumber", "fieldLabel" => "Recipient Mobile Number", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receiverfirstname", "fieldLabel" => "Recipient Name", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receiverlastname", "fieldLabel" => "Recipient Surname", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receiveraddress", "fieldLabel" => "Recipient Address", "required" => false, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "sender_placeofbirth", "fieldLabel" => "Sender Date Of Birth", "required" => true, "inputType" => "date", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "purposeOfTransfer", "fieldLabel" => "Purpose Of Transfer", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "sourceOfFunds", "fieldLabel" => "Source Of Funds", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "idNumber", "fieldLabel" => "Document Id Number", "required" => false, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "idType", "fieldLabel" => "Document Id Type", "required" => false, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "idExpiry", "fieldLabel" => "Document Id Expiry", "required" => false, "inputType" => "date", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []]
			]; 
		}
		
		public function getLightnetFieldView($payoutCountry, $payoutCurrency, $locationId, $editData = null)
		{
			$timestamp = time();
			$body = [
				'agentSessionId' => (string) $timestamp,
				'locationId' => (string) $locationId,
				'payoutCountry' => (string) $payoutCountry,
				'payoutCurrency' => (string) $payoutCurrency,
				'paymentMode' => 'B',
			];
 
			$response = $this->liquidNetService->serviceApi('post', '/GetFieldInfo', $timestamp, $body);
			  
			if (!$response['success']) {
				return [];
			}
			 
			if (($response['response']['code'] ?? 1) != 0) {
				return [];
			}
				 
			$fieldList = $response['response']['fieldList'] ?? collect(); 

			if (!$fieldList) {
				return [];
			}
			
			$catalogue = LightnetCatalogue::where('category_name', 'transfer to bank')
			->where('service_name', 'lightnet')
			->whereNotNull('data')
			->get()
			->keyBy('catalogue_type');
			  
			$fieldLists = [];
			foreach($fieldList as $eky => $field)
			{
				$fieldName = strtolower($field['fieldName']);
				$field['fieldName'] = $fieldName;
				$field['options'] = [];
				$field['inputType'] = "text";
				if (in_array($fieldName, ["beneficiarytype", "remittertype"]))  
				{
					$field['inputType'] = "select";
					$field['options'] = ["I" => "Individual", "B" => "Business"];
				}
				elseif (in_array($fieldName, ["sendergender"])) 
				{
					$field['inputType'] = "select";
					$field['options'] = ["Male" => "Male", "Female" => "Female"];
				}
				elseif ($fieldName == "paymentmode")
				{
					$field['inputType'] = "select";
					$field['options'] = ["B" => "Account Deposit"];
				}
				elseif (in_array($fieldName, ["sendercountry", "sendernationality", "senderidissuecountry", "receivercountry", "receivernationality"])){
					$field['inputType'] = "select";
					$field['options'] = $this->availableCountries()->where('service_name', 'lightnet')->pluck('label', 'data')->toArray() ?? [];
				}
				elseif ($fieldName == "senderbeneficiaryrelationship") 
				{
					$field['inputType'] = "select";
					$field['options'] = $catalogue->has('REL') ? collect($catalogue->get('REL')->data ?? [])->pluck('value', 'data')->toArray() : [];
				}
				elseif (in_array($fieldName, ["receiveroccupation", "senderoccupation"])) 
				{
					$field['inputType'] = "select";
					$field['options'] = $catalogue->has('OCC') ? collect($catalogue->get('OCC')->data ?? [])->pluck('value', 'data')->toArray() : [];
				}
				elseif (in_array($fieldName, ["receiveridtype", "sendersecondaryidtype", "senderidtype"])) 
				{
					$field['inputType'] = "select";
					$field['options'] = $catalogue->has('DOC') ? collect($catalogue->get('DOC')->data ?? [])->pluck('value', 'data')->toArray() : [];
				}
				elseif ($fieldName == "purposeofremittance") 
				{
					$field['inputType'] = "select";
					$field['options'] = $catalogue->has('POR') ? collect($catalogue->get('POR')->data ?? [])->pluck('value', 'data')->toArray() : [];
				}
				elseif ($fieldName == "sendersourceoffund") 
				{
					$field['inputType'] = "select";
					$field['options'] = $catalogue->has('SOF') ? collect($catalogue->get('SOF')->data ?? [])->pluck('value', 'data')->toArray() : [];
				}
				elseif ($fieldName == "receiveridexpiredate" || $fieldName == "receiveridissuedate" || $fieldName == "receiverdateofbirth" || $fieldName == "senderidissuedate" || $fieldName == "senderidexpiredate" || $fieldName == "senderdateofbirth") 
				{
					$field['inputType'] = "date";
					$field['options'] = [];
				}
				$fieldLists[] = $field;
			} 
			return $fieldLists;
		}   
	}
