<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\LightnetCatalogue;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\WebResponseTrait;
use App\Services\LiquidNetService;
use App\Notifications\WalletTransactionNotification;
use Illuminate\Support\Facades\Notification;
use Helper;
use Carbon\Carbon;  

class TransferBankController extends Controller
{ 
	use WebResponseTrait;
	protected $liquidNetService;
    public function __construct()
    {
		$this->liquidNetService = new LiquidNetService(); 
		$this->middleware('auth');
    }	
	
	public function transferToBank()
	{
		$countries = $this->countries();
		return view('user.transaction.transfer-bank.index', compact('countries'));
	}
	
	public function countries()
	{
		$countries = [
			[
				"data" => "BGD",
				"value" => "BDT",
				"label" => "Bangladesh",
				"service_name" => "lightnet"
			],
			[
				"data" => "IDN",
				"value" => "IDR",
				"label" => "Indonesia",
				"service_name" => "lightnet"
			],
			[
				"data" => "LKA",
				"value" => "LKR",
				"label" => "Sri Lanka",
				"service_name" => "lightnet"
			],
			[
				"data" => "MYS",
				"value" => "MYR",
				"label" => "Malaysia",
				"service_name" => "lightnet"
			],
			[
				"data" => "NPL",
				"value" => "NPR",
				"label" => "Nepal",
				"service_name" => "lightnet"
			],
			[
				"data" => "PHL",
				"value" => "PHP",
				"label" => "Philippines",
				"service_name" => "lightnet"
			],
			[
				"data" => "VNM",
				"value" => "VND",
				"label" => "Vietnam",
				"service_name" => "lightnet"
			]
		];
		return $countries;
	}
	public function transferToBankBeneficiary()
	{ 
		/* $catalogues = [
			'OCC' => 'Get Occupation',
			'SOF' => 'Get Source of Fund',
			'REL' => 'Get Relationship list',
			'POR' => 'Get Purpose of Remittance',
			'DOC' => 'Get Customer Document ID Type'
		];  
		$data = [];
		foreach($catalogues as $key => $catalogue)
		{ 
			$timestamp = time();
			$body = [
				'agentSessionId' => (string) $timestamp,
				'catalogueType' => (string) $key, 
			];
			
			$response = $this->liquidNetService->serviceApi('post', '/GetCatalogue', $timestamp, $body);
			if($response['success'] && $response['response']['code'] == 0)
			{ 
				$data[] = [
					'category_name' => 'transfer to bank',
					'service_name' => 'lightnet',
					'catalogue_type' => $key,
					'catalogue_description' => $catalogue,
					'data' => json_encode($response['response']['result']),
					'created_at' => now(),
					'updated_at' => now() 
				]; 
			}  
		}
		
		LightnetCatalogue::insert($data); */
		
		$countries = $this->countries();
		$catalogues = LightnetCatalogue::where('category_name', 'transfer to bank')
		->where('service_name', 'lightnet')
		->whereNotNull('data')
		->get()
		->keyBy('catalogue_type');
		 
		$relationships = $catalogues->has('REL')
        ? json_decode($catalogues->get('REL')->data, true)
        : [];
		 
		$purposeRemittances = $catalogues->has('POR')
        ? json_decode($catalogues->get('POR')->data, true)
        : [];
		 
		$sourceOfFunds = $catalogues->has('SOF')
        ? json_decode($catalogues->get('SOF')->data, true)
        : [];
		
		$documentOfCustomers = $catalogues->has('DOC')
        ? json_decode($catalogues->get('DOC')->data, true)
        : [];
		
		 
		$view = view('user.transaction.modal.transfer-bank-beneficiary', compact('catalogues', 'countries', 'relationships', 'purposeRemittances', 'sourceOfFunds', 'documentOfCustomers'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	
	public function transferToBankList(Request $request)
	{
		$timestamp = time();
		$body = [
			'agentSessionId' => (string) $timestamp,
			'paymentMode' => 'B',
			'payoutCountry' => (string) $request->payoutCountry,
		];

		// Call the service API
		$response = $this->liquidNetService->serviceApi('post', '/GetAgentList', $timestamp, $body);
		
		// Initialize the output
		if (!isset($response['success']) || !$response['success'] && $response['response']['code'] !== 0) {
			return $this->successResponse('success', ['output' => '<option value="">No banks available</option>']);
		} 
		// Process bank list
		$banks = $response['response']['locationDetail'] ?? [];
		$output = '<option value="">Select Bank Name</option>';
		
		foreach ($banks as $bank) {
			$output .= sprintf(
				'<option value="%s" data-bank-name="%s">%s</option>',
				htmlspecialchars($bank['locationId'] ?? '', ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($bank['locationName'] ?? '', ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($bank['locationName'] ?? '', ENT_QUOTES, 'UTF-8')
			);
		}

		return $this->successResponse('success', ['output' => $output]);
	} 
	
	public function transferToBankBeneficiaryStore(Request $request)
	{  
		// Validation rules
		$validator = Validator::make($request->all(), [
			'beneficiaryType' => 'required|string', // Check if country_id exists in the 'countries' table
			'payoutCurrency' => 'required|string',
			'bankId' => 'required|string',
			'bankAccountNumber' => 'required|integer',
			'beneficiaryFirstName' => 'required|string',
			'beneficiaryLastName' => 'required|string',
			'beneficiaryAddress' => 'required|string', 
			'beneficiaryEmail' => 'required|string',
			'beneficiaryMobile' => 'required|string',
			'senderBeneficiaryRelationship' => 'required|string', 
			'purposeOfRemittance' => 'required|string',
			'senderSourceOfFund' => 'required|string',
			'receiverIdType' => 'required|string', 
			'receiverIdTypeRemarks' => 'required|string', 
		]);
		  
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		try {
			
			DB::beginTransaction();
			$beneficiaryData = $request->except('_token');
		
			$data = []; 
			$data['category_name'] = $beneficiaryData['category_name'];
			$data['service_name'] = $beneficiaryData['service_name'];
			$data['user_id'] = Auth::id();
			$data['user_id'] = Auth::id();
			$data['created_at'] = now();
			$data['updated_at'] = now();
			$data['data'] = json_encode($beneficiaryData);
			 
			$beneficiary = Beneficiary::create($data);
			Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to bank beneficiary');
			
			DB::commit(); 
			return $this->successResponse('The beneficiary was completed successfully.');
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        } 	
	}
	
	public function transferToBeneficiaryList(Request $request)
	{  
		// Extract request data
		$userId = Auth::id();
		$payoutCurrency = $request->payoutCurrency;
		$payoutCountry = $request->payoutCountry;
		$categoryName = $request->categoryName;
		$serviceName = $request->serviceName;

		// Fetch beneficiaries with filters
		$beneficiaries = Beneficiary::where('user_id', $userId)
			->where('category_name', $categoryName)
			->where('service_name', $serviceName)
			->where('data->payoutCurrency', $payoutCurrency)
			->where('data->payoutCountry', $payoutCountry)
			->get();
		 
		// Initialize output
		$output = '<option value="">Select Beneficiary</option>';

		// Loop through beneficiaries and prepare output
		foreach ($beneficiaries as $beneficiary) {
			$dataArr = $beneficiary->dataArr ?? [];
			$firstName = $dataArr['beneficiaryFirstName'] ?? '';
			$lastName = $dataArr['beneficiaryLastName'] ?? '';
			$bankName = $dataArr['bankName'] ?? '';

			// Skip beneficiaries with missing required data
			if (empty($firstName) || empty($lastName)) {
				continue;
			}

			// Build option element safely
			$output .= sprintf(
				'<option value="%s">%s %s (%s)</option>',
				htmlspecialchars($beneficiary->id, ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'),
				htmlspecialchars($bankName, ENT_QUOTES, 'UTF-8')
			);
		}

		// Return response
		return $this->successResponse('success', ['output' => $output]);
	}
	 
	public function transferToBeneficiaryDetail(Request $request)
	{	
		try {
			// Retrieve the beneficiary ID from the request
			$beneficiaryId = $request->input('beneficiaryId');

			// Fetch the beneficiary record
			$beneficiary = Beneficiary::find($beneficiaryId);

			// Check if the beneficiary exists
			if (!$beneficiary) {
				return $this->errorResponse('No Beneficiary found.');
			}

			// Render the view with the beneficiary data
			$view = view('user.transaction.modal.confirm-beneficiary', compact('beneficiary'))->render();

			// Return success response with rendered view
			return $this->successResponse('success', ['view' => $view]); 
		} catch (\Exception $e) 
		{ 
			return $this->errorResponse('Something went wrong. Please try again later.');
		}
	}
	
	public function transferToBankBeneficiaryEdit($id)
	{
		$countries = $this->countries();
		$catalogues = LightnetCatalogue::where('category_name', 'transfer to bank')
		->where('service_name', 'lightnet')
		->whereNotNull('data')
		->get()
		->keyBy('catalogue_type');
		 
		$relationships = $catalogues->has('REL')
        ? json_decode($catalogues->get('REL')->data, true)
        : [];
		 
		$purposeRemittances = $catalogues->has('POR')
        ? json_decode($catalogues->get('POR')->data, true)
        : [];
		 
		$sourceOfFunds = $catalogues->has('SOF')
        ? json_decode($catalogues->get('SOF')->data, true)
        : [];
		
		$documentOfCustomers = $catalogues->has('DOC')
        ? json_decode($catalogues->get('DOC')->data, true)
        : [];
		
		$beneficiary = Beneficiary::find($id);
		$edit = $beneficiary->dataArr;
		
		
		$timestamp = time();
		$body = [
			'agentSessionId' => (string) $timestamp,
			'paymentMode' => 'B',
			'payoutCountry' => (string) $edit['payoutCountry'],
		];

		// Call the service API
		$response = $this->liquidNetService->serviceApi('post', '/GetAgentList', $timestamp, $body); 
		$banks = $response['response']['locationDetail'] ?? [];
		
		$view = view('user.transaction.modal.edit-transfer-bank-beneficiary', compact('catalogues', 'countries', 'relationships', 'purposeRemittances', 'sourceOfFunds', 'documentOfCustomers', 'beneficiary', 'edit', 'banks'))->render();
		return $this->successResponse('success', ['view' => $view]);	
	}
	
	public function transferToBankBeneficiaryUpdate(Request $request, $id)
	{  
		// Validation rules
		$validator = Validator::make($request->all(), [
			'beneficiaryType' => 'required|string', // Check if country_id exists in the 'countries' table
			'payoutCurrency' => 'required|string',
			'bankId' => 'required|string',
			'bankAccountNumber' => 'required|integer',
			'beneficiaryFirstName' => 'required|string',
			'beneficiaryLastName' => 'required|string',
			'beneficiaryAddress' => 'required|string', 
			'beneficiaryEmail' => 'required|string',
			'beneficiaryMobile' => 'required|string',
			'senderBeneficiaryRelationship' => 'required|string', 
			'purposeOfRemittance' => 'required|string',
			'senderSourceOfFund' => 'required|string',
			'receiverIdType' => 'required|string', 
			'receiverIdTypeRemarks' => 'required|string', 
		]);
		  
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		try {
			
			DB::beginTransaction();
			$beneficiaryData = $request->except('_token');
		
			$data = []; 
			$data['category_name'] = $beneficiaryData['category_name'];
			$data['service_name'] = $beneficiaryData['service_name'];
			$data['user_id'] = Auth::id(); 
			$data['updated_at'] = now(); 
			$data['data'] = json_encode($beneficiaryData);
			 
			$beneficiary = Beneficiary::find($id);
			$beneficiary->update($data);
			Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to bank beneficiary');
			
			DB::commit(); 
			return $this->successResponse('The beneficiary was completed successfully.');
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        } 	
	}
	
	public function transferToBeneficiaryDelete($id)
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
			Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to bank beneficiary');

			// Delete the beneficiary
			$beneficiary->delete();

			DB::commit(); 
			return redirect()->back()->withSuccess('The beneficiary was deleted successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();  
			return redirect()->back()->withError($e->getMessage());
		}  
	}
	
	public function transferToBankCommission(Request $request)
	{
		$beneficiaryId = $request->beneficiaryId;
		$txnAmount = $request->txnAmount;
		
		$beneficiary = Beneficiary::find($beneficiaryId);
		if (!$beneficiary || empty($beneficiary->dataArr)) {
			return $this->errorResponse('Something went wrong.');
		}
		
		$exhnageRate = ExchangeRate::where('type', 2)->where('currency', $beneficiary->dataArr['payoutCurrency'])->first();
		if (!$exhnageRate) {
			return $this->errorResponse('Something went wrong.');
		}
		
		$exchangeRate = $exhnageRate->exchange_rate ?? 0;
		$payoutCurrencyAmount = ($txnAmount * $exchangeRate);
		$serviceCharge = $this->serviceCharge((int)$payoutCurrencyAmount, $beneficiary->dataArr);
		
		$comissions = [
			'payoutCurrency' => $beneficiary->dataArr['payoutCurrency'],
			'payoutCountry' => $beneficiary->dataArr['payoutCountry'],
			'txnAmount' => $txnAmount,
			'exchangeRate' => $exchangeRate,
			'payoutCurrencyAmount' => $payoutCurrencyAmount,
			'remitCurrency' => config('setting.default_currency'),
			'platformCharge' => 0,
			'serviceCharge' => $serviceCharge,
		];
		return $this->successResponse('success', $comissions);
	}
	
	public function serviceCharge($payoutCurrencyAmount, $beneficiary)
	{
		// Prepare API Request Body
		$timestamp = time();
		$defaultCurrency = config('setting.default_currency'); // Cache config value

		$body = [
			"agentSessionId"   => (string) $timestamp,
			"transferAmount"   => (string) $payoutCurrencyAmount,
			"calcBy"           => "P",
			"payoutCurrency"   => $beneficiary['payoutCurrency'] ?? '',
			"paymentMode"      => "B",
			"locationId"       => $beneficiary['bankId'] ?? '',
			"payoutCountry"    => $beneficiary['payoutCountry'] ?? '',
			"remitCurrency"    => $defaultCurrency,
		];

		// Call the service API
		$response = $this->liquidNetService->serviceApi('post', '/GetEXRate', $timestamp, $body);

		// Return 0 on failure or unexpected response
		if (!$response['success'] || ($response['response']['code'] ?? -1) != 0) {
			return 0;
		}

		// Return Service Charge or Default to 0
		return $response['response']['serviceCharge'] ?? 0;
	}
 
	public function transferToBankStore(Request $request)
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
			$txnAmount = (float) $request->input('txnAmount', 0);
			$payoutCurrencyAmount = (float) $request->input('payoutCurrencyAmount', 0);

			if ($txnAmount > $user->balance) {
				$validator->errors()->add('txnAmount', 'Insufficient balance to complete this transaction.');
			}

			if (!$request->filled('payoutCurrencyAmount')) {
				$validator->errors()->add('txnAmount', 'The payout currency amount field is required.');
			} elseif ($payoutCurrencyAmount <= 0) {
				$validator->errors()->add('txnAmount', 'The payout currency amount must be greater than 0.');
			}
		});

		// Return validation response if fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
	 
		try {
			DB::beginTransaction();
			$request['order_id'] = "GPTB-".time();
			$request['timestamp'] = time();
		
			$beneficiary = Beneficiary::find($request->beneficiaryId);
			if (!$beneficiary || empty($beneficiary->dataArr)) {
				return $this->errorResponse('Something went wrong.');
			}

			$response = $this->liquidNetService->sendTransaction($request, $beneficiary->dataArr);
			
			if (!$response['success']) {
				$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
				throw new \Exception($errorMsg);
			}
			 
			if($response['response']['code'] != 0)
			{
				$errorMsg = $response['response']['message'] ?? 'An error occurred.';
				throw new \Exception($errorMsg);
			}
			
			$txnAmount = $request->input('txnAmount');
			
			// Deduct balance
			$user->decrement('balance', $txnAmount); 
			
			// Check if necessary fields exist to prevent undefined index warnings
			$beneficiaryFirstName = $beneficiary->dataArr['beneficiaryFirstName'] ?? '';
			$beneficiaryLastName = $beneficiary->dataArr['beneficiaryLastName'] ?? '';
			$bankName = $beneficiary->dataArr['bankName'] ?? 'Unknown Bank';
			$bankId = $beneficiary->dataArr['bankId'] ?? '';
			$mobileNumber = $beneficiary->dataArr['beneficiaryMobile'] ?? '';
			$payoutCurrency = $beneficiary->dataArr['payoutCurrency'] ?? '';
			$payoutCurrencyAmount = $request->payoutCurrencyAmount;
			$exchangeRate = $request->exchangeRate;
			$remitCurrency = config('setting.default_currency');
			
			// Concatenate beneficiary name safely
			$beneficiaryName = trim("$beneficiaryFirstName $beneficiaryLastName"); // Using trim to remove any leading/trailing spaces

			// Build the comment using sprintf for better readability
			$comments = sprintf(
				"You have successfully transferred %s USD to %s, of bank: %s.",
				number_format($txnAmount, 2), // Ensure txnAmount is formatted to 2 decimal places
				$beneficiaryName,
				$bankName
			);

			// Create transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'transfer to bank',
				'platform_provider' => 'bank transfer',
				'transaction_type' => 'debit',
				'country_id' => $user->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => "PENDING",
				'comments' => $comments,
				'notes' => $request->input('notes'),
				'unique_identifier' => $response['response']['confirmationId'],
				'product_name' => $bankName, 
				'product_id' => $bankId,
				'mobile_number' => $mobileNumber,
				'unit_currency' => $remitCurrency,
				'unit_amount' => $txnAmount,
				'rates' => $exchangeRate,
				'unit_convert_currency' => $payoutCurrency,
				'unit_convert_amount' => $payoutCurrencyAmount,
				'unit_convert_exchange' => $exchangeRate,
				'beneficiary_request' => json_encode($beneficiary),
				'api_request' => json_encode($response['request']),
				'api_response' => json_encode($response['response']),
				'order_id' => $request->order_id,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// Log the transaction creation
			Helper::updateLogName($transaction->id, Transaction::class, 'transfer to bank transaction', $user->id);
			
			DB::commit(); 
		 
			return $this->successResponse('The transaction was completed successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();  
			return $this->errorResponse($e->getMessage()); 
		}  
	}
 
}
