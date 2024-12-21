<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\Beneficiary;
use App\Models\User;
use App\Models\LightnetCatalogue;
use App\Models\LiveExchangeRate;
use App\Models\ExchangeRate;
use App\Models\LightnetCountry;
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
		return LightnetCountry::where('status', 1)
			->whereNotNull('label')
			->get()
			->toArray();
	}
 
	public function transferToBankBeneficiary()
	{  
		$countries = $this->countries();
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
			$data['data'] = $beneficiaryData;
			 
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
			return $this->errorResponse('Beneficiary not found.');
		}
		
		$liveExchangeRate = LiveExchangeRate::select('markdown_rate', 'aggregator_rate')->where('channel', $beneficiary->dataArr['service_name'])->where('currency', $beneficiary->dataArr['payoutCurrency'])->first(); 
		if(!$liveExchangeRate)
		{
			$liveExchangeRate = ExchangeRate::select('exchange_rate as markdown_rate', 'aggregator_rate')->where('type', 2)->where('currency', $beneficiary->dataArr['payoutCurrency'])->first();
			if (!$liveExchangeRate) {
				return $this->errorResponse('A technical issue has occurred. Please try again later.'); 
			}
		}
		
		$aggregatorRate = $liveExchangeRate->aggregator_rate ?? 0;
		$aggregatorCurrencyAmount = ($txnAmount * $aggregatorRate);
		
		$exchangeRate = $liveExchangeRate->markdown_rate ?? 0;
		$payoutCurrencyAmount = ($txnAmount * $exchangeRate);
		$serviceCharge = 0;
		$commissionType = config('setting.lightnet_commission_type') ?? 'flat';
		$commissionCharge = config('setting.lightnet_commission_charge') ?? 0;
		
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

			$transactionAmountQuery = Transaction::whereIn('platform_name', ['international airtime', 'transfer to bank']);

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
			$netAmount = $request->input('netAmount');
			
			// Deduct balance
			$user->decrement('balance', $netAmount); 
			
			// Check if necessary fields exist to prevent undefined index warnings
			$beneficiaryFirstName = $beneficiary->dataArr['beneficiaryFirstName'] ?? '';
			$beneficiaryLastName = $beneficiary->dataArr['beneficiaryLastName'] ?? '';
			$bankName = $beneficiary->dataArr['bankName'] ?? 'Unknown Bank';
			$bankId = $beneficiary->dataArr['bankId'] ?? '';
			$mobileNumber = $beneficiary->dataArr['beneficiaryMobile'] ?? '';
			$payoutCurrency = $beneficiary->dataArr['payoutCurrency'] ?? '';
			$payoutCurrencyAmount = $request->payoutCurrencyAmount;
			$aggregatorCurrencyAmount = $request->aggregatorCurrencyAmount;
			$exchangeRate = $request->exchangeRate; 
			$confirmationId = $response['response']['confirmationId'];
			// Concatenate beneficiary name safely
			$beneficiaryName = trim("$beneficiaryFirstName $beneficiaryLastName"); // Using trim to remove any leading/trailing spaces

			// Build the comment using sprintf for better readability
			$comments = sprintf(
				"You have successfully transferred %s USD to %s, of bank: %s.",
				number_format($netAmount, 2), // Ensure txnAmount is formatted to 2 decimal places
				$beneficiaryName,
				$bankName
			);

			// Create transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'transfer to bank',
				'platform_provider' => $beneficiary->data['service_name'],
				'transaction_type' => 'debit',
				'country_id' => $user->country_id,
				'txn_amount' => $netAmount,
				'txn_status' => "pending",
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
			 
			$commitResponse = $this->liquidNetService->commitTransaction($confirmationId, $remitCurrency);

			if (!$commitResponse['success'] || ($commitResponse['response']['code'] ?? 1) != 0) {
				// Provide a clear and user-friendly error message
				$errorMsg = "Your transaction has been accepted but couldn't be committed due to a technical issue. Please visit the transaction list to manually commit the transaction.";
				throw new \Exception($errorMsg);
			}

			// Safely fetch the transaction and update it
			if ($transaction) {
				$commitTransaction = Transaction::find($transaction->id);
				$commitTransaction->update(['api_response_second' => $commitResponse['response'], 'txn_status' => strtolower($commitResponse['response']['status'])]);
			}
 
			DB::commit();  
			return $this->successResponse($commitResponse['response']['message']);
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
			$transaction->update([
				'api_response_second' => $commitResponse['response'],
				'txn_status' => strtolower($commitResponse['response']['status'] ?? 'pending'),
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
