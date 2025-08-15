<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
    DB, Auth, Log, Validator, Notification
};
use App\Models\{
    Country, Transaction, Beneficiary, User, 
    LightnetCatalogue, LiveExchangeRate, ExchangeRate, 
    LightnetCountry
};
use App\Http\Traits\WebResponseTrait;
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
	use WebResponseTrait; 
	protected $liquidNetService;
	protected $onafricService;
    public function __construct()
    {
		$this->liquidNetService = new LiquidNetService(); 
		$this->onafricService = new OnafricService(); 
		$this->middleware('auth');
    }	
	
	public function transferToBank()
	{
		$countries = $this->countries()->toArray(); 
		return view('user.transaction.transfer-bank.index', compact('countries'));
	}
	
	public function countries()
	{
		$lightnetCountry = LightnetCountry::with('country')
		->select(
			'id',
			'data',
			'value',
			'label',
			'service_name',
			'status',
			'created_at',
			'updated_at',
			'markdown_type',
			'markdown_charge',
			DB::raw("'' as iso")
		)
		->whereNotNull('label')
		->get()
		->map(function ($map) {
			$map->country_flag = optional($map->country)->country_flag ?? '';
			$map->isdcode = optional($map->country)->isdcode ?? '';
			return $map;
		});
		
		$onafricCountry = Country::select('id', 'country_flag', 'iso3 as data', 'currency_code as value', 'nicename as label', DB::raw("'onafric' as service_name"), DB::raw("1 as status"), 'created_at', 'updated_at', DB::raw("'flat' as markdown_type"), DB::raw("0 as markdown_charge"), 'iso', 'isdcode')
		->whereIn('nicename', $this->onafricService->bankAvailableCountry())
		->get();
		

		// Merge both collections
		$countries = $lightnetCountry->merge($onafricCountry)->sortBy('label')->values();
		 
		$countriesWithFlags = $countries->transform(function ($country)  {
			// Add full flag URL
			if ($country->country_flag) {
				$country->country_flag = asset('country/' . $country->country_flag);
			} 
			return $country;
		});
	 
		return $countriesWithFlags; 
	}
 
	public function transferToBankBeneficiary()
	{  
		$countries = $this->countries()->toArray(); 
		$catalogues = LightnetCatalogue::where('category_name', 'transfer to bank')
		->where('service_name', 'lightnet')
		->whereNotNull('data')
		->get()
		->keyBy('catalogue_type');
		 
		$relationships = $catalogues->has('REL')
        ? $catalogues->get('REL')->data
        : [];
		 
		$purposeRemittances = $catalogues->has('POR')
        ? $catalogues->get('POR')->data
        : [];
		 
		$sourceOfFunds = $catalogues->has('SOF')
        ? $catalogues->get('SOF')->data
        : [];
		
		$documentOfCustomers = $catalogues->has('DOC')
        ? $catalogues->get('DOC')->data
        : [];
		 
		$view = view('user.transaction.transfer-bank.transfer-bank-beneficiary', compact('catalogues', 'countries', 'relationships', 'purposeRemittances', 'sourceOfFunds', 'documentOfCustomers'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	
	public function transferToBankList(Request $request)
	{
		$serviceName = $request->serviceName;

		switch ($serviceName) {
			case 'lightnet':
				return $this->successResponse('success', [
					'output' => $this->liquidNetService->getAgentList($request)
				]);
			
			case 'onafric':
				return $this->successResponse('success', [
					'output' => $this->onafricService->getOnafricBank($request)
				]);
			
			default:
				return $this->successResponse('success', [
					'output' => '<option value="">No banks available</option>'
				]);
		}
	}
 
	public function transferToBankBeneficiaryStore(Request $request)
	{
		$user = Auth::user();
	    if($request->service_name == "onafric")
		{
		    $bankaccountnumber = $request->bankaccountnumber; 
    		$payoutIso = $request->payoutIso;
    		$bankId = $request->bankId;
    		
    		$response = $this->onafricService->getValidateBankRequest($payoutIso, $bankId, $bankaccountnumber);
    	 
    		if (
                !isset($response['success']) || 
                !$response['success'] || 
                (isset($response['response']['status_code']) && !in_array($response['response']['status_code'], ["Active"]))
            ) {   
                return $this->errorResponse('Provided bank or account number are not active');
            }  
		}else{
			
			$payoutCountry = $request->payoutCountry; 
			$senderCountry = $user->country->iso3 ?? '';
			if($payoutCountry == $senderCountry)	
			{
				return $this->errorResponse('Domestic remittance is not allowed. Please select a receiver country different from the sender country.');
			}
		} 
		
		try {
			 
			DB::beginTransaction();
			$beneficiaryData = $request->except('_token');
			
			if($beneficiaryData['service_name'] == "onafric")
			{ 
				$beneficiaryData['sender_country'] = $user->country->id ?? '';
				$beneficiaryData['sender_country_code'] = $user->country->iso ?? '';
				$beneficiaryData['sender_country_name'] = $user->country->name ?? '';
				$beneficiaryData['sender_mobile'] = isset($user->formatted_number) ? ltrim($user->formatted_number, '+') : '';
				/* $beneficiaryData['sender_name'] = $user->first_name ?? '';
				$beneficiaryData['sender_surname'] = $user->last_name ?? ''; */
			}
			$data = []; 
			$data['category_name'] = $beneficiaryData['category_name'];
			$data['service_name'] = $beneficiaryData['service_name'];
			$data['user_id'] = Auth::id(); 
			$data['created_at'] = now();
			$data['updated_at'] = now();
			$data['data'] = $beneficiaryData;
			 
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
			$dataArr = $beneficiary->data ?? [];
			$firstName = $dataArr['receiverfirstname'] ?? ($dataArr['recipient_name'] ?? '');
			$lastName = $dataArr['receiverlastname'] ?? ($dataArr['recipient_surname'] ?? '');
			$bankName = $dataArr['bankName'] ?? '';

			// Skip beneficiaries with missing required data
			if (empty($firstName)) {
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
			$view = view('user.transaction.transfer-bank.confirm-beneficiary', compact('beneficiary'))->render();

			// Return success response with rendered view
			return $this->successResponse('success', ['view' => $view]); 
		}
		catch (\Exception $e) 
		{ 
			\Log::error($e->getMessage());
			return $this->errorResponse('Something went wrong. Please try again later.'. $e->getMessage());
		}
	}
	
	public function transferToBankFields(Request $request)
	{
		$payoutCountry = $request->payoutCountry;
		$payoutCurrency = $request->payoutCurrency;
		$payoutIso = $request->payoutIso;
		$serviceName = $request->serviceName;
		$locationId = $request->locationId;
		$isdcode = $request->isdcode;
		
		if($serviceName == "lightnet")
		{
			$view = $this->getLightnetFieldView($payoutCountry, $payoutCurrency, $locationId, $isdcode); 
		}
		else
		{
			$view = $this->getOnafricFieldView($payoutCountry, $payoutCurrency, $locationId, $isdcode); 
		}

		// Return success response with rendered view
		return $this->successResponse('success', ['view' => $view]); 
	}
	
	public function getOnafricFieldView($payoutCountry, $payoutCurrency, $locationId, $isdcode, $editData = null)
	{ 
		$countries = $this->countries()->whereIn('label', $this->onafricService->bankAvailableCountry()); 
	 
		$view = view('user.transaction.transfer-bank.onafric-fields', compact('countries', 'isdcode', 'editData'))->render();
		return $view;
	}
	
	public function getLightnetFieldView($payoutCountry, $payoutCurrency, $locationId, $isdcode, $editData = null)
	{
		error_reporting(0);
		$timestamp = time();
		$body = [
			'agentSessionId' => (string) $timestamp,
			'locationId' => (string) $locationId,
			'payoutCountry' => (string) $payoutCountry,
			'payoutCurrency' => (string) $payoutCurrency,
			'paymentMode' => 'B',
		];

		// Call the service API
		$response = $this->liquidNetService->serviceApi('post', '/GetFieldInfo', $timestamp, $body);
		
		// Handle unsuccessful commit response
		if (!$response['success']) {
			return $this->successResponse('Error loading fields. Please try again.');
		}
		
		// Handle unsuccessful commit response
		if (($response['response']['code'] ?? 1) != 0) {
			return $this->successResponse('Error loading fields. Please try again.');
		}
			
		// Process bank list  
		$fieldList = $response['response']['fieldList'] ?? collect(); 

		if ($fieldList) {
			$fieldList = collect($fieldList)->filter(fn($item) => 
				!in_array(strtolower($item['fieldName']), ['sendercountry', 'senderfirstname', 'senderlastname', 'sendernationality', 'sendermobile', 'sendergender', 'senderaddress', 'sendercity', 'senderstate', 'senderzipcode', 'senderemail', 'senderidexpiredate', 'senderdateofbirth', 'senderidissuecountry', 'senderidtype', 'senderidtyperemarks', 'senderidnumber', 'senderoccupation', 'senderoccupationremarks', 'sendersourceoffund', 'sendersourceoffundremarks', 'sendersecondaryidtype', 'sendersecondaryidnumber', 'senderidissuedate'])
			); 
		}

		$catalogue = LightnetCatalogue::where('category_name', 'transfer to bank')
		->where('service_name', 'lightnet')
		->whereNotNull('data')
		->get()
		->keyBy('catalogue_type');
		
		$states = $this->lightnetStates($payoutCountry);
		 
		$countries = $this->countries()->where('service_name', 'lightnet')->toArray();
		$view = view('user.transaction.transfer-bank.lightnet-fields', compact('isdcode', 'fieldList', 'catalogue', 'countries', 'states', 'editData'))->render();
		return $view;
	}
	
	public function lightnetStates($payoutCountry)
	{
		$timestamp = time();
		$body =  [
			'agentSessionId' => (string) $timestamp,
			'catalogueType' => 'STA',
			'additionalField1' => (string) $payoutCountry,
		];
		
		$response = $this->liquidNetService->serviceApi('post', '/GetCatalogue', $timestamp, $body);
		if (!$response['success']) {
			return LightnetCatalogue::where('category_name', 'transfer to bank')
			->where('service_name', 'lightnet')
			->where('service_name', $payoutCountry)
			->whereNotNull('data')
			->first()->data ?? [];
		}
		
		if(($response['response']['code'] ?? -1) != 0)
		{
			return LightnetCatalogue::where('category_name', 'transfer to bank')
			->where('service_name', 'lightnet')
			->where('service_name', $payoutCountry)
			->whereNotNull('data')
			->first()->data ?? [];
		} 
		
		$result = $response['response']['result'] ?? [];
		return $result;
	}
	
	public function transferToBankBeneficiaryEdit($id)
	{
		$countries = $this->countries()->toArray(); 
		$beneficiary = Beneficiary::find($id);
		$edit = $beneficiary->data;
		
		if($beneficiary->service_name == "lightnet")
		{
			$timestamp = time();
			$body = [
				'agentSessionId' => (string) $timestamp,
				'paymentMode' => 'B',
				'payoutCountry' => (string) $edit['payoutCountry'],
			];

			// Call the service API
			$response = $this->liquidNetService->serviceApi('post', '/GetAgentList', $timestamp, $body); 
			$banks = $response['response']['locationDetail'] ?? [];
			
			$fieldView = $this->getLightnetFieldView($edit['payoutCountry'], $edit['payoutCurrency'], $edit['bankId'], $edit['mobile_code'] ?? null, $edit); 
		}
		else
		{
			$banks = $this->onafricService->getOnafricBank(['payoutIso' => $edit['payoutIso'] ?? '', 'bankId' => $edit['bankId'] ?? '']);
			$fieldView = $this->getOnafricFieldView($edit['payoutCountry'], $edit['payoutCurrency'], $edit['bankId'], $edit['mobile_code'] ?? null, $edit); 
		}
		 
		$view = view('user.transaction.transfer-bank.edit-transfer-bank-beneficiary', compact('countries', 'beneficiary', 'edit', 'banks', 'fieldView'))->render();
		return $this->successResponse('success', ['view' => $view]);	
	}
	
	public function transferToBankBeneficiaryUpdate(Request $request, $id)
	{   	 
		try {
		    
			$beneficiary = Beneficiary::find($id);
			if($request->service_name == "onafric")
    		{
    		    $bankaccountnumber = $request->bankaccountnumber; 
        		$payoutIso = $request->payoutIso;
        		$bankId = $request->bankId;
        		// Ensure beneficiary->data is an array
                $beneficiaryDataArray = is_array($beneficiary->data) ? $beneficiary->data : [];
    
                if ($bankaccountnumber !== ($beneficiaryDataArray['bankaccountnumber'] ?? '') ||
                    $bankId !== ($beneficiaryDataArray['bankId'] ?? '') ||
                    $payoutIso !== ($beneficiaryDataArray['payoutIso'] ?? '')) 
                {
            		$response = $this->onafricService->getValidateBankRequest($payoutIso, $bankId, $bankaccountnumber);
            	    
            		if (
                        !isset($response['success']) || 
                        !$response['success'] || 
                        (isset($response['response']['status_code']) && !in_array($response['response']['status_code'], ["Active"]))
                    ) {
                           
                        return $this->errorResponse('Provided bank or account number are not active');
                    }  
                }
    		}
		
			$user = Auth::user();
			
			DB::beginTransaction();
			$beneficiaryData = $request->except('_token');
			
			if($beneficiaryData['service_name'] == "onafric")
			{
				$beneficiaryData['sender_country'] = $user->country->id ?? '';
				$beneficiaryData['sender_country_code'] = $user->country->iso ?? '';
				$beneficiaryData['sender_country_name'] = $user->country->name ?? '';
				$beneficiaryData['sender_mobile'] = isset($user->formatted_number) ? ltrim($user->formatted_number, '+') : '';
				/* $beneficiaryData['sender_name'] = $user->first_name ?? '';
				$beneficiaryData['sender_surname'] = $user->last_name ?? ''; */
			}
		
			$data = []; 
			$data['category_name'] = $beneficiaryData['category_name'];
			$data['service_name'] = $beneficiaryData['service_name'];
			$data['user_id'] = Auth::id(); 
			$data['updated_at'] = now(); 
			$data['data'] = $beneficiaryData;
			  
			$beneficiary->update($data);
			Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to bank beneficiary');
			
			DB::commit(); 
			return $this->successResponse('The beneficiary was updated successfully.');
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
			return $this->errorResponse('Beneficiary not found.');
		}
		
		$liveExchangeRate = LiveExchangeRate::select('markdown_rate', 'aggregator_rate')->where('channel', $beneficiary->dataArr['service_name'])->where('currency', $beneficiary->dataArr['payoutCurrency'])->first(); 
		if(!$liveExchangeRate)
		{ 
			$liveExchangeRate = ExchangeRate::select('exchange_rate as markdown_rate', 'aggregator_rate')
			->where('type', 2)
			->where('service_name', $beneficiary->service_name)
			->where('currency', $beneficiary->dataArr['payoutCurrency'])
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
		
		if($beneficiary->service_name == "lightnet")
		{
			$commissionType = config('setting.lightnet_commission_type') ?? 'flat';
			$commissionCharge = config('setting.lightnet_commission_charge') ?? 0;
		}
		else
		{
			$commissionType = config('setting.onafric_bank_commission_type') ?? 'flat';
			$commissionCharge = config('setting.onafric_bank_commission_charge') ?? 0;
		}
		
		$platformFees = $commissionType === "flat"
		? max($commissionCharge, 0) // Ensure flat fee is not negative
		: max(($txnAmount * $commissionCharge / 100), 0); // Ensure percentage fee is not negative
						
		$comissions = [
			'payoutCurrency' => $beneficiary->dataArr['payoutCurrency'],
			'payoutCountry' => $beneficiary->dataArr['payoutCountry'],
			'txnAmount' => $txnAmount,
			'aggregatorRate' => $aggregatorRate,
			'aggregatorCurrencyAmount' => $aggregatorCurrencyAmount,
			'exchangeRate' => $exchangeRate,
			'payoutCurrencyAmount' => $payoutCurrencyAmount,
			'remitCurrency' => config('setting.default_currency'),
			'platformCharge' => $platformFees,
			'serviceCharge' => $serviceCharge,
		];
		return $this->successResponse('success', $comissions);
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
				$apiStatus = 'processing';
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
				$apiStatus = $onafricStatus;
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
				'api_status' => $apiStatus,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// Log the transaction creation
			Helper::updateLogName($transaction->id, Transaction::class, 'transfer to bank transaction', $user->id);
			 
			if($beneficiary->service_name === "lightnet")
			{
				$commitResponse = $this->liquidNetService->commitTransaction($confirmationId, $remitCurrency);

				if (!$commitResponse['success'] || ($commitResponse['response']['code'] ?? 1) != 0) { 
					$errorMsg = "Your transaction has been accepted but couldn't be committed due to a technical issue. Please visit the transaction list to manually commit the transaction.";
					throw new \Exception($errorMsg);
				}

				// Safely fetch the transaction and update it
				if ($transaction) {
					$commitTransaction = Transaction::find($transaction->id);
					$statusMessage = $commitResponse['response']['status']; 
					
					$statusLabel = LightnetStatus::from($statusMessage)->label(); 
					if($statusLabel === "cancelled and refunded")
					{
						$commitTransaction->processAutoRefund($statusLabel, $statusMessage);
					}
					
					$updateData = [
						'api_response_second' => $commitResponse['response'],
						'txn_status' => $statusLabel,
						'api_status' => $statusMessage
					];
					$commitTransaction->update($updateData);
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
	
	public function transferToBankCommitTransaction($id)
	{
		try {
			DB::beginTransaction();

			// Fetch the transaction
			$transaction = Transaction::find($id);
			if (!$transaction) {
				return $this->errorResponse('Transaction not found.');
			}

			if (!$transaction->unique_identifier) {
				return $this->errorResponse('Transaction confirmation id is missing.');
			}

			// Call the commitTransaction method
			$commitResponse = $this->liquidNetService->commitTransaction($transaction->unique_identifier, $transaction->unit_currency);

			// Handle unsuccessful commit response
			if (!$commitResponse['success']) {
				$errorMsg = $commitResponse['response']['errors'][0]['message'] ?? 'An error occurred.';
				throw new \Exception($errorMsg);
			}
			
			// Handle unsuccessful commit response
			if (($commitResponse['response']['code'] ?? 1) != 0) {
				$errorMsg = $commitResponse['response']['message'];
				throw new \Exception($errorMsg);
			}

			// Update the transaction status
			$statusMessage = $commitResponse['response']['status'];
			
			$statusLabel = LightnetStatus::from($statusMessage)->label(); 
			if($statusLabel === "cancelled and refunded")
			{
				$transaction->processAutoRefund($statusLabel, $statusMessage);
			}
			$transaction->update([
				'api_response_second' => $commitResponse['response'],
				'txn_status' => $statusLabel,
				'api_status' => $statusMessage,
			]);

			// Log the transaction update
			Helper::updateLogName($transaction->id, Transaction::class, 'Transfer to bank commit transaction');

			DB::commit();

			return $this->successResponse($commitResponse['response']['message'] ?? 'Transaction committed successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();

			return $this->errorResponse($e->getMessage());
		}
	}
	 
}
