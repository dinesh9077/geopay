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
		
		public function createTransaction(Request $request)
		{	
			$user = Auth::user();
			
			$rules = [
				'service' => 'required|integer|in:1,2',
				'amount' => 'required|numeric|min:0.01',
				'exchange_rate_id' => 'required|integer',
				'exchange_rate' => 'required|numeric|min:0',
				'transferamount' => 'required|numeric|min:0',
				'remitcurrency' => 'required|string|size:3|alpha',
				'payoutCurrency' => 'required|string|size:3|alpha',
				'payoutIso'     => 'nullable|string|size:2|required_if:service,2',
				'fromCountry'     => 'nullable|string|size:2|required_if:service,2',
				'bankId' => 'required|string|max:20',
				'remittertype' => 'required|string|in:I,C',
				'senderfirstname' => 'required|string|max:50',
				'sendermiddlename' => 'nullable|string|max:50',
				'senderlastname' => 'required|string|max:50',
				'sendergender' => 'required|string|in:Male,Female,Other',
				'senderaddress' => 'required|string|max:255',
				'sendercity' => 'required|string|max:100',
				'senderstate' => 'required|string|max:100',
				'senderzipcode' => 'required|string|max:20',
				'sendercountry' => 'required|string|size:3|alpha',
				'sendermobile' => 'required|string|regex:/^[0-9]{10,15}$/',
				'sendernationality' => 'required|string|size:3|alpha',
				'senderidtype' => 'required|string|max:5',
				'senderidtyperemarks' => 'required|string|max:100',
				'senderidnumber' => 'required|string|max:50',
				'senderidissuecountry' => 'required|string|size:3|alpha',
				'senderidissuedate' => 'required|date',
				'senderidexpiredate' => 'required|date',
				'senderdateofbirth' => 'required|date',
				'senderoccupation' => 'required|string|max:5',
				'senderoccupationremarks' => 'required|string|max:255',
				'sendersourceoffund' => 'required|string|max:5',
				'sendersourceoffundremarks' => 'required|string|max:255',
				'senderemail' => 'required|email|max:100'
			];
 
			// Validation rules
			$validator = Validator::make($request->all(), $rules);
 
			// Return validation response if fails
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}
			
			if ($request->amount > $user->balance) {
				return $this->errorResponse('Insufficient balance to complete this transaction.', 'ERR_INSUFFICIENT_BALANCE'); 
			}
		 
			try {
				DB::beginTransaction(); 
				$request['order_id'] = "GPTB-".$user->id."-".time();
				$request['payoutCountry'] = $request->receivercountry ?? '';
				$request['timestamp'] = time();
				
				$remitCurrency = config('setting.default_currency');
				
				$liveExchangeRate = LiveExchangeRate::find($request->exchange_rate_id); 
				if(!$liveExchangeRate)
				{ 
					$liveExchangeRate = ExchangeRate::find($request->exchange_rate_id);
					if (!$liveExchangeRate) {
						return $this->errorResponse('A technical issue has occurred. Please try again later.', 'ERR_RATE', 401); 
					}
				}
				
				$request['service_name'] = $request->service == 1 ? 'lightnet' : 'onafric';
				if($request['service_name'] === "lightnet")
				{
					$response = $this->liquidNetService->apiSendTransaction($request);
					
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
					$apiStatus = 'processing';
				}
				else
				{
					$response = $this->onafricService->apiSendBankTransaction($request);
				  
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
					$apiStatus = $onafricStatus; 
					$txnStatus = OnafricStatus::from($onafricStatus)->label();
				}
				
				$txnAmount = $request->input('amount');
				$netAmount = $request->input('amount');
				
				// Deduct balance
				$user->decrement('balance', $netAmount); 
				
				// Check if necessary fields exist to prevent undefined index warnings 
				$beneficiaryFirstName = $request['receiverfirstname'] ?? ($request['beneficiaryFirstName'] ?? '');
				$beneficiaryLastName = $request['receiverlastname'] ?? ($request['beneficiaryLastName'] ?? '');
				$bankName = $request['bankName'] ?? 'Unknown Bank';
				$bankId = $request['bankId'] ?? '';
				$mobileNumber = ltrim(($request['receivercontactnumber'] ?? ''), '+');
				$payoutCurrency = $request['payoutCurrency'] ?? '';
				$payoutCurrencyAmount = $request->transferamount;
				$aggregatorCurrencyAmount = ($liveExchangeRate->aggregator_rate * $txnAmount);
				$exchangeRate = $request->exchange_rate; 
				
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
					'platform_provider' => $request['service_name'],
					'transaction_type' => 'debit',
					'country_id' => $user->country_id,
					'txn_amount' => $netAmount,
					'txn_status' => $txnStatus,
					'comments' => $comments,
					'notes' => $request->input('notes', ''),
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
					'unit_convert_exchange' => $liveExchangeRate->aggregator_rate ?? 0,
					'beneficiary_request' => ['data' => $request->all()],
					'api_request' => $response['request'] ?? [],
					'api_response' => $response['response'] ?? [],
					'order_id' => $request->order_id,
					'fees' => 0,
					'service_charge' => 0,
					'total_charge' => 0,
					'is_api_service' => 1,
					'api_status' => $apiStatus,
					'created_at' => now(),
					'updated_at' => now(),
				]);

				// Log the transaction creation
				Helper::updateLogName($transaction->id, Transaction::class, 'api transfer to bank transaction', $user->id);
				 
				if($request['service_name'] === "lightnet")
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
						$statusMessage = $commitResponse['response']['status'];

						$statusLabel = LightnetStatus::from($statusMessage)->label();

						if ($statusLabel === "cancelled and refunded") {
							$commitTransaction->processAutoRefund($statusLabel, $statusMessage);
						}

						$commitTransaction->api_response_second = $commitResponse['response'];
						$commitTransaction->txn_status = $statusLabel === "cancelled and refunded" ? $commitTransaction->txn_status : $statusLabel;
						$commitTransaction->api_status = $statusMessage;

						$commitTransaction->save();
					}
					
					$successMsg = $commitResponse['response']['message'];
				}
				
				Notification::send($user, new AirtimeRefundNotification($user, $netAmount, $transaction->id, $comments, $transaction->notes, ucfirst($txnStatus)));
				
				DB::commit();  
				$data = [
					"thirdPartyId" => $request['order_id'],  
					"status_message" => $txnStatus,
					"timestamp" => now()
				]; 

				return $this->successResponse($successMsg ?? 'TXN Successfully Accepted.', $data); 
			} catch (\Throwable $e) {
				DB::rollBack();  
				return $this->errorResponse($e->getMessage(), 'ERR_INTERNAL_SERVER'); 
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
				["fieldName" => "senderdateofbirth", "fieldLabel" => "Sender Date Of Birth", "required" => true, "inputType" => "date", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "senderoccupationremarks", "fieldLabel" => "Purpose Of Transfer", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "sendersourceoffundremarks", "fieldLabel" => "Source Of Funds", "required" => true, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receiveridnumber", "fieldLabel" => "Document Id Number", "required" => false, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receiveridtype", "fieldLabel" => "Document Id Type", "required" => false, "inputType" => "text", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []],
				["fieldName" => "receiveridexpiredate", "fieldLabel" => "Document Id Expiry", "required" => false, "inputType" => "date", "dynamicField" => false, 'minLength' => 1, 'maxLength' => 100, 'options' => []]
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
			 
			$fieldList = collect($fieldList)->filter(fn($item) => 
				!in_array(strtolower($item['fieldName']), ['sendercountry', 'senderfirstname', 'senderlastname', 'sendernationality', 'sendermobile', 'sendergender', 'senderaddress', 'sendercity', 'senderstate', 'senderzipcode', 'senderemail', 'senderidexpiredate', 'senderdateofbirth', 'senderidissuecountry', 'senderidissuedate'])
			);  
			
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
