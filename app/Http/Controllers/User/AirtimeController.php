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
use Illuminate\Support\Facades\Notification;
use Helper;
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
		$countries = $this->airtimeService->getCountries(); 
		return view('user.transaction.international-airtime', compact('countries'));
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

			$transactionAmountQuery = Transaction::query();

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
					"You have reached your {$limitType} transaction limit of {$transactionLimit}. " .
					"Current total transactions: {$transactionAmount}."
				);
			}
			 
			$response = $this->airtimeService->transactionRecord($request, $user);  
			if (!$response['success']) {
				$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
				throw new \Exception($errorMsg);
			}
            //Log::info($response);
			// Transaction variables
			$txnAmount = $request->input('retail_unit_amount') + $request->input('platform_fees');
			$productName = $request->input('product_name');
			$mobileNumber = '+' . ltrim($request->input('mobile_number'), '+');
			 
            $txnStatus = strtoupper($response['response']['status']['message']) ?? 'process';
            
			$productId = $request->input('product_id');
			
			$productResponse = $this->airtimeService->getProductById($productId); 
			$productRes = [];
			if($productResponse['success'])
			{
				$productRes = $productResponse['response'];
			}
			// Deduct balance
			$user->decrement('balance', $txnAmount); 
			$comments = "You have successfully recharged $txnAmount USD for $productName.";
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
				'rates' => $request->input('retail_rates', 0),
				'unit_convert_currency' => $request->input('wholesale_unit_currency', ''),
				'unit_convert_amount' => $request->input('wholesale_unit_amount', ''),
				'unit_convert_exchange' => $request->input('wholesale_rates', 0),
				'api_request' => $response['request'],
				'api_response' => $response['response'],
				'api_response_second' => $productRes,
				'order_id' => $request->order_id,
				'fees' => $request->input('platform_fees'),
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// Log the transaction creation
			Helper::updateLogName($transaction->id, Transaction::class, 'international airtime transaction', $user->id);
			  
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
		if (!isset($request['external_id'], $request['status']['message'])) {
			return ;
		}

		$uniqueIdentifier = $request['external_id'];
		 
        $txnStatus = strtoupper($request['status']['message']) ?? 'process';
          
		$updated = Transaction::where('unique_identifier', $uniqueIdentifier)
			->update(['txn_status' => $txnStatus]);

		return $updated;
	}
}
