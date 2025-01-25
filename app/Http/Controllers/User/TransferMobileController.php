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
use App\Services\MasterService;
use App\Notifications\WalletTransactionNotification;
use Illuminate\Support\Facades\Notification;
use Helper;
use Carbon\Carbon;  

class TransferMobileController extends Controller
{ 
	use WebResponseTrait;
	protected $liquidNetService;
	protected $masterService;
    public function __construct()
    {
		$this->liquidNetService = new LiquidNetService(); 
		$this->masterService = new MasterService(); 
		$this->middleware('auth');
    }	
	
	public function transferToMobileMoney()
	{
		/* $countrys = DB::table('country')->get();
		foreach($countrys as $country)
		{ 
			Country::where('iso3', $country->iso3)->update(['currency_code' => $country->currency]);
		}
		  */
		$countries = $this->masterService->getCountries(); 
		return view('user.transaction.transfer-mobile.index', compact('countries'));
	}
	 
	public function transferToMobileBeneficiary()
	{  
		$countries = $this->masterService->getCountries(); 
		$view = view('user.transaction.transfer-mobile.transfer-mobile-beneficiary', compact('countries'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	 
	public function transferToMobileBeneficiaryStore(Request $request)
	{    
		try { 
			DB::beginTransaction();
			$beneficiaryData = $request->except('_token');
		
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
	
	public function transferToBeneficiaryList(Request $request)
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
		// Initialize output
		$output = '<option value="">Select Beneficiary</option>';

		// Loop through beneficiaries and prepare output
		foreach ($beneficiaries as $beneficiary) {
			$data = $beneficiary->data ?? [];
			$firstName = $data['recipient_name'] ?? '';
			$lastName = $data['recipient_surname'] ?? ''; 
			$recipientMobile = $data['recipient_mobile'] ?? ''; 

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
				htmlspecialchars($recipientMobile, ENT_QUOTES, 'UTF-8')
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
			$view = view('user.transaction.transfer-mobile.confirm-beneficiary', compact('beneficiary'))->render();

			// Return success response with rendered view
			return $this->successResponse('success', ['view' => $view]); 
		}
		catch (\Exception $e) 
		{ 
			\Log::error($e->getMessage());
			return $this->errorResponse('Something went wrong. Please try again later.'. $e->getMessage());
		}
	}
	   
	public function transferToMobileBeneficiaryEdit($id)
	{
		$countries = $this->masterService->getCountries(); 
		$beneficiary = Beneficiary::find($id);
		$edit = $beneficiary->data;
		   
		$view = view('user.transaction.transfer-mobile.edit-transfer-mobile-beneficiary',
		compact('countries', 'beneficiary', 'edit'))
		->render();
		return $this->successResponse('success', ['view' => $view]);	
	}
	
	public function transferToMobileBeneficiaryUpdate(Request $request, $id)
	{   	 
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
			if (!$beneficiary || empty($beneficiary->data)) {
				return $this->errorResponse('Something went wrong.');
			}
			
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
