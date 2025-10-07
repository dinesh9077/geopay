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
use App\Notifications\AirtimeRefundNotification;
use Helper;
use Carbon\Carbon;  
use Http;
use App\Services\DepositPaymentService;
 

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
		
		$serviceCharge = 0;
		$sendFee = 0;
		$commissionType = config('setting.onafric_collection_commission_type', 'flat'); 
		$commissionCharge = config('setting.onafric_collection_commission_charge', 0);
		
		$platformFees = $commissionType === "flat"
		? max($commissionCharge, 0) // Ensure flat fee is not negative
		: max(($txnAmount * $commissionCharge / 100), 0); // Ensure percentage fee is not negative
		
		$netAmount = $txnAmount + $platformFees;
		
		$aggregatorRate = $liveExchangeRate->aggregator_rate ?? 0;
		$aggregatorCurrencyAmount = ($netAmount * $aggregatorRate);
		
		$exchangeRate = $liveExchangeRate->markdown_rate ?? 0;
		$payoutCurrencyAmount = ($netAmount * $exchangeRate);
		  			
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
			'mobile_code'   => 'required|string', 
			'mobile_no'   => 'required|string', 
			'txnAmount'      => 'required|numeric|gt:0', 
			'notes'          => 'nullable|string|max:255',  
		]);

		// Custom validation logic
		$validator->after(function ($validator) use ($request, $user) {
			$netAmount = (float) $request->input('netAmount', 0);
			$aggregatorCurrencyAmount = (float) $request->input('aggregatorCurrencyAmount', 0);
			  
			// if ($netAmount > $user->balance) {
			// 	$validator->errors()->add('txnAmount', 'Insufficient balance to complete this transaction.');
			// }
			
			if (!$request->filled('aggregatorCurrencyAmount')) {
				$validator->errors()->add('txnAmount', 'The payout currency amount field is required.');
			} elseif ($aggregatorCurrencyAmount <= 0) {
				$validator->errors()->add('txnAmount', 'The payout currency amount must be greater than 0.');
			}
			
			if($request->country_code == 240 && $request->channel === "Vodafone" && $request->txnAmount < 1)
			{
				$validator->errors()->add('txnAmount', 'The minimum allowed receivable amount in 1 USD for DRC Vodafone.');
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
			//\Log::info('collection request and response', ['response' => $response]);  
			if (!$response['success']) {
				$errorFetch = array_keys($response['response'])[0] ?? '';
				$errorMsg = $response['response']['errors'][0]['message'] ?? ($response['response'][$errorFetch][0] ?? ($response['response']['detail'] ?? 'An error occurred.')); 
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
			$mobileNumber = ltrim($request->mobile_code . $request->mobile_no, '+'); 
			
			$unitConvertCurrency =  $request->payoutCurrency;
			$payoutCurrencyAmount = $request->payoutCurrencyAmount;
			$aggregatorCurrencyAmount = $request->aggregatorCurrencyAmount;
			$exchangeRate = $request->exchangeRate; 
			$confirmationId = $response['response']['id'] ?? null;
			
			$beneficiaryName = trim("$beneficiaryFirstName"); // Using trim to remove any leading/trailing spaces

			// Build the comment using sprintf for better readability
			$comments = "Your transaction is being processed. A request has been sent to the sender – please ask them to approve it. You will be notified once the transaction is completed. Thank you for relying on GEOPAY for fast and reliable money requests.";

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
				'api_status' => $txnStatus ?? 'pending',
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// Log the transaction creation
			Helper::updateLogName($transaction->id, Transaction::class, 'add mobile collection transaction', $user->id); 
			
			Notification::send($user, new AirtimeRefundNotification($user, $txnAmount, $transaction->id, $comments, $transaction->notes, ucfirst($txnStatus)));
			
			DB::commit();  
			return $this->successResponse("Message has been sent to the Payer with instructions to authorize the transaction.", ['userBalance' => Helper::decimalsprint($user->balance, 2), 'currencyCode' => config('setting.default_currency')]);
		} catch (\Throwable $e) {
			DB::rollBack();  
			return $this->errorResponse($e->getMessage()); 
		}  
	} 
	
	public function storeMobileCollectionCallback(Request $request)
	{
		//Log::info('Mobile Collection Webhook received', ['data' => $request->all()]);

		if (empty($request->all())) {
			return response()->json(['error' => 'Empty request'], 400);
		}

		$thirdPartyTransId = $request->input('data.id');
		$txnStatus         = strtolower($request->input('data.status', ''));
		$comments          = $request->input('data.error_message') 
							 ?? $request->input('data.instructions');

		if (!$thirdPartyTransId || !$txnStatus) {
			return response()->json(['error' => 'Invalid or missing transaction data'], 422);
		}

		$transaction = Transaction::where([
			'unique_identifier' => $thirdPartyTransId,
			'platform_provider' => 'onafric mobile collection',
		])->first();

		if (!$transaction) {
			return response()->json(['error' => 'Transaction not found'], 404);
		}

		if ($transaction->txn_status === $txnStatus) {
			return response()->json(['message' => 'No changes needed'], 200);
		}

		$updateData = [
			'txn_status' => $txnStatus,
			'api_status' => $txnStatus,
			'comments'   => $comments ?? $transaction->comments,
		];

		if ($txnStatus === 'successful') {
			$transaction->user->increment('balance', $transaction->txn_amount);
			$updateData['comments'] = "Payment received successfully. Wallet updated.";
			$updateData['complete_transaction_at'] = now();
			$updateData['api_response'] = $request->all(); 
			 
			if ($transaction->user && !empty($transaction->user->email)) {
				try {
					app(\App\Services\TransactionEmailService::class)
						->send($transaction->user, $transaction, 'add_funds_mobile');
				} catch (\Throwable $e) {
					Log::error("Email sending add_funds_mobile failed: " . $e->getMessage());
				}
			} 
		}

		$transaction->update($updateData);

		Notification::send(
			$transaction->user,
			new AirtimeRefundNotification(
				$transaction->user,
				$transaction->txn_amount,
				$transaction->id,
				$transaction->comments,
				$transaction->notes,
				ucfirst($transaction->txn_status)
			)
		);

		return response()->json(['message' => 'Transaction updated successfully'], 200);
	}
	
	// Deposit Payment
	public function depositPayment()
	{
		$commissionType = config('setting.guardian_commission_type', 'flat');
		$commissionCharge = config('setting.guardian_commission_charge', 0);
		$remitCurrency = config('setting.default_currency', 'USD');
		
		return view('user.transaction.add-money.deposit-payment', compact('commissionType', 'commissionCharge', 'remitCurrency'));
	}
	
	public function depositPaymentLink(Request $request, DepositPaymentService $depositService)
	{ 
		$validator = Validator::make($request->all(), [
			'cardtype'   => 'required|in:visa,mastercard,amex,discover,diners',
			'cardname'   => 'required|string|max:100',
			'cardnumber' => 'required',
			'month'      => 'required|digits:2|min:1|max:12',
			'year'       => 'required|digits:4|integer|min:' . date('Y'),
			'cvv'        => 'required|digits_between:3,4',
			'amount'     => 'required|numeric', 
			'netAmount'     => 'required|numeric', 
			'platformCharge'     => 'required|numeric',
			// Billing fields
			'first_name' => 'required|string|max:100',
			'last_name'  => 'required|string|max:100',
			'email'      => 'required|email|max:150',
			'phone'      => 'required|string|max:50',
			'address'    => 'required|string|max:255',
			'city'       => 'required|string|max:100',
			'state'      => 'required|string|max:100',
			'postalcode' => 'required|string|max:20',
			'country'    => 'required|alpha|size:2',
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		$user = Auth::user();

		// Prepare user & card data
		$userData = [
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
			'email' => $request->email,
			'phone' => $request->phone,
			'address' => $request->address,
			'city' => $request->city,
			'state' => $request->state,
			'postalcode' => $request->postalcode,
			'country' => $request->country ?? '',
		];

		$cardData = $request->only(['cardtype', 'cardname', 'cardnumber', 'month', 'year', 'cvv']);
	 
		do {
			$orderId = "GPMC-".$user->id."-".time();
		} while (Transaction::where('order_id', $orderId)->exists());

		$amount = $request->amount;
		$netAmount = $request->netAmount;
		$response = $depositService->deposit(
			$userData,
			$cardData,
			$netAmount,
			$orderId
		); 
		 
		if(!$response['success'])
		{
			return $this->errorResponse(
				data_get($response, 'response.message', 'An error occurred')
			); 
		}
		if(empty($response['response']['payment_url']))
		{
			return $this->errorResponse(
				data_get($response, 'response.message', 'something went wrong.')
			); 
		}
		
		$comments = "Your payment was authorized. It will be reviewed shortly and the final status (approved/rejected) will be updated. You can check your transaction list for the latest update.";
		$remitCurrency = config('setting.default_currency', 'USD');
		
		// Create transaction record
		$transaction = Transaction::create([
			'user_id' => $user->id,
			'receiver_id' => $user->id,
			'platform_name' => 'add money',
			'platform_provider' => $request->service_name,
			'transaction_type' => 'credit',
			'country_id' => $user->country_id,
			'country_code' => $request->country_code,
			'txn_amount' => $amount,
			'txn_status' => 'pending',
			'comments' => $comments,
			'notes' => null,
			'unique_identifier' => $orderId,
			'product_name' => null, 
			'product_id' => null,
			'mobile_number' => null,
			'unit_currency' => $remitCurrency,
			'unit_amount' => $amount,
			'unit_rates' => $amount,
			'rates' => 1,
			'unit_convert_currency' => $remitCurrency,
			'unit_convert_amount' => $amount,
			'unit_convert_exchange' => 1,
			'beneficiary_request' => null,
			'api_request' => $response['request'],
			'api_response' => $response['response'],
			'order_id' => $orderId,
			'fees' => $request->platformCharge,
			'service_charge' => 0,
			'total_charge' => $request->platformCharge,
			'api_status' => 'pending',
			'created_at' => now(),
			'updated_at' => now(),
		]);

		// Log the transaction creation
		Helper::updateLogName($transaction->id, Transaction::class, 'add bank card payment', $user->id); 
		return $this->successResponse("transaction authorized", ['payment_link' => $response['response']['payment_url']]); 
	} 
}
