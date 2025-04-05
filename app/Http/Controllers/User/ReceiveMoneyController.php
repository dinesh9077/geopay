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
use App\Models\OnafricChannel;
use App\Models\LightnetCountry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\WebResponseTrait; 
use App\Services\{ 
	MasterService, OnafricService
}; 
use App\Notifications\WalletTransactionNotification;
use Illuminate\Support\Facades\Notification;
use Helper;
use Carbon\Carbon;  

class ReceiveMoneyController extends Controller
{ 
	use WebResponseTrait; 
	protected $masterService;
	protected $onafricService;
    public function __construct()
    { 
		$this->masterService = new MasterService(); 
		$this->onafricService = new OnafricService(); 
		$this->middleware('auth')->except('storeMobileCollectionCallback');
    }	
	
	public function addMoney()
	{   
		$collectionCountries = $this->onafricService->collectionCountry();   
		return view('user.transaction.add-money.index', compact('collectionCountries'));
	}
	
	public function storeMobileCollectionCommission(Request $request)
	{  
		$txnAmount = $request->txnAmount;
		$recipientCountry = $request->recipient_country;
		$serviceName = $request->service_name;
		  
		$country = Country::find($recipientCountry);
		$liveExchangeRate = LiveExchangeRate::select('markdown_rate', 'aggregator_rate')
		->where('channel', $serviceName)
		->where('currency', $country->currency_code)
		->first(); 
		
		if(!$liveExchangeRate)
		{
			$liveExchangeRate = ExchangeRate::select('exchange_rate as markdown_rate', 'aggregator_rate')
			->where('type', 1)
			->where('service_name', $serviceName)
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
		 
		$sendFee = 0;
		$commissionType = config('setting.onafric_collection_commission_type', 'flat'); 
		$commissionCharge = config('setting.onafric_collection_commission_charge', 0);
		
		$platformFees = $commissionType === "flat"
		? max($commissionCharge, 0) // Ensure flat fee is not negative
		: max(($txnAmount * $commissionCharge / 100), 0); // Ensure percentage fee is not negative
						
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
			'sendFee' => $sendFee
		];
		return $this->successResponse('success', $comissions);
	}
	
	public function storeMobileCollection(Request $request)
	{ 
		$user = Auth::user();
	  
		// Validation rules
		$validator = Validator::make($request->all(), [
			'country_code'   => 'required|integer', 
			'channel'   => 'required|string',   
			'mobile_no'   => 'required|string', 
			'txnAmount'      => 'required|numeric|gt:0', 
			'notes'          => 'nullable|string|max:255',  
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
			$request['order_id'] = "GPMC-".$user->id."-".time();
			$request['timestamp'] = now()->format('Y-m-d H:i:s');
			
			$remitCurrency = config('setting.default_currency') ?? 'USD';
			
			$transactionLimit = $user->is_company == 1 
				? config('setting.company_pay_monthly_limit') 
				: ($user->userLimit->daily_add_limit ?? 0);

			$transactionAmountQuery = Transaction::whereIn('platform_name', ['onafric mobile collection'])
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
			  
			$response = $this->onafricService->sendMobileCollectionTransaction($request);
			 
			if (!$response['success']) {
				$errorFetch = array_keys($response['response'])[0] ?? '';
				$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response'][$errorFetch][0] ?? 'An error occurred.'); 
				throw new \Exception($errorMsg);
			}
			
			$txnStatus = $response['response']['status'] ?? '';

			if (empty($txnStatus)) 
			{  
				$errorFetch = array_keys($response['response'])[0] ?? '';;
				$errMessage = $response['response'][$errorFetch][0] ?? 'An error occurred.'; 
				throw new \Exception($errMessage);
			}
			 
			$txnAmount = $request->input('txnAmount');
			$netAmount = $request->input('netAmount');
			
			// Deduct balance
			//$user->decrement('balance', $netAmount); 
			
			// Check if necessary fields exist to prevent undefined index warnings
			$beneficiaryFirstName = $request->beneficiary_name; 
			$mobileNumber = str_replace('+', '', $request->mobile_code.''.$request->mobile_no);
			
			$unitConvertCurrency =  $request->payoutCurrency;
			$payoutCurrencyAmount = $request->payoutCurrencyAmount;
			$aggregatorCurrencyAmount = $request->aggregatorCurrencyAmount;
			$exchangeRate = $request->exchangeRate; 
			$confirmationId = $request['order_id'];
			
			$beneficiaryName = trim("$beneficiaryFirstName"); // Using trim to remove any leading/trailing spaces

			// Build the comment using sprintf for better readability
			$comments = sprintf(
				"You have successfully transferred %s USD to %s, of mobile No: %s.",
				number_format($netAmount, 2), // Ensure txnAmount is formatted to 2 decimal places
				$beneficiaryName,
				$mobileNumber
			);

			// Create transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'add money',
				'platform_provider' => $request->service_name,
				'transaction_type' => 'credit',
				'country_id' => $user->country_id,
				'country_code' => $request->country_code,
				'txn_amount' => $txnAmount,
				'txn_status' => $txnStatus ?? 'pending',
				'comments' => $comments,
				'notes' => $request->input('notes'),
				'unique_identifier' => $confirmationId,
				'product_name' => $beneficiaryFirstName, 
				'product_id' => $request->channel,
				'mobile_number' => $mobileNumber,
				'unit_currency' => $remitCurrency,
				'unit_amount' => $payoutCurrencyAmount,
				'unit_rates' => $txnAmount,
				'rates' => $exchangeRate,
				'unit_convert_currency' => $unitConvertCurrency,
				'unit_convert_amount' => $aggregatorCurrencyAmount,
				'unit_convert_exchange' => $request->aggregatorRate ?? 0,
				'beneficiary_request' => null,
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
			Helper::updateLogName($transaction->id, Transaction::class, 'add mobile collection transaction', $user->id); 
			DB::commit();  
			return $this->successResponse("Transaction is being processed. You will be notified once it's completed.", ['userBalance' => Helper::decimalsprint($user->balance, 2), 'currencyCode' => config('setting.default_currency')]);
		} catch (\Throwable $e) {
			DB::rollBack();  
			return $this->errorResponse($e->getMessage()); 
		}  
	} 
	
	public function storeMobileCollectionCallback(Request $request)
	{
		// Log the incoming request for debugging
		Log::info('Mobile Collection Webhook received', ['data' => $request->all()]);
 
		if (!$request->all()) {
			return response()->json(['error' => 'Empty request'], 400);
		}
 
		$thirdPartyTransId = $request->input('metadata.order_id');
		$txnStatus = strtolower($request->input('status'));

		if (!$thirdPartyTransId || !$txnStatus) {
			return response()->json(['error' => 'Invalid or missing transaction data'], 422);
		}
 
		// Find the transaction based on thirdPartyTransId
		$transaction = Transaction::where('order_id', $thirdPartyTransId)->first();

		if (!$transaction) {
			Log::warning("Transaction not found for order_id: $thirdPartyTransId");
			return response()->json(['error' => 'Transaction not found'], 404);
		}
		
		 // Prevent duplicate updates (optional)
		if ($transaction->txn_status === $txnStatus) {
			return response()->json(['message' => 'No changes needed'], 200);
		}

		// Update transaction status
		$transaction->txn_status = strtolower($txnStatus);
		$transaction->touch(); // Updates the `updated_at` timestamp
		$transaction->save();
		
		if(strtolower($txnStatus) == 'success')
		{
			$transaction->user->increment('balance', $transaction->txn_amount); 
		}
		return response()->json(['message' => 'Transaction updated successfully'], 200);
	}
}
