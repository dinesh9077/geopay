<?php
	namespace App\Http\Controllers\Api;
	
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
	use App\Http\Traits\ApiResponseTrait; 
	use App\Services\{ 
		MasterService, OnafricService, DepositPaymentService
	}; 
	use App\Notifications\WalletTransactionNotification;
	use App\Notifications\AirtimeRefundNotification;
	use Illuminate\Support\Facades\Notification;
	use Helper;
	use Carbon\Carbon;  
 
	class ReceiveMoneyController extends Controller
	{ 
		use ApiResponseTrait;  
		protected $masterService;
		protected $onafricService;
		public function __construct()
		{ 
			$this->masterService = new MasterService(); 
			$this->onafricService = new OnafricService();  
		}	
		
		public function collectionCountryList()
		{
			$collectionCountries = $this->onafricService->collectionCountry(); 
			return $this->successResponse('country fetched.', $collectionCountries);
		}
		
		public function collectionCommission(Request $request)
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
					
			$totalCharges = $platformFees + $serviceCharge;
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
				'sendFee' => $sendFee,
				'totalCharges' => $totalCharges,
				'netAmount' => ($totalCharges + $txnAmount)
			];
			return $this->successResponse('commission fetched.', $comissions);
		}
		
		public function storeTransaction(Request $request)
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
				\Log::info('collection request and response', ['response' => $response]);
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
		
		public function depositPaymentLink(Request $request, DepositPaymentService $depositService)
		{  
			$validator = Validator::make($request->all(), [
				'cardtype'   => 'required|in:visa,mastercard,amex,discover,diners',
				'cardname'   => 'required|string|max:100',
				'cardnumber' => 'required',
				'month'      => 'required|digits:2|min:1|max:12',
				'year'       => 'required|digits:4|integer|min:' . date('Y'),
				'cvv'        => 'required|digits_between:3,4',
				'amount'     => 'required|numeric|min:1',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}

			$user = Auth::user();

			// Prepare user & card data
			$userData = [
				'first_name' => $user->first_name,
				'last_name'  => $user->last_name,
				'email'      => $user->email,
				'phone'      => $user->mobile_number,
				'address'    => $user->address,
				'city'       => $user->city,
				'state'      => $user->state,
				'postalcode' => $user->zip_code,
				'country'    => $user->country->iso ?? '',
			];

			$cardData = $request->only(['cardtype', 'cardname', 'cardnumber', 'month', 'year', 'cvv']);
			
			do {
				$orderId = "GPMC-".$user->id."-".time();
			} while (Transaction::where('order_id', $orderId)->exists());

			$amount = $request->amount;
			$response = $depositService->deposit(
				$userData,
				$cardData,
				$amount,
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
				'platform_provider' => 'deposit card payment',
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
				'fees' => 0,
				'service_charge' => 0,
				'total_charge' => 0,
				'api_status' => 'pending',
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// Log the transaction creation
			Helper::updateLogName($transaction->id, Transaction::class, 'add bank card payment', $user->id); 
			return $this->successResponse("transaction authorized", ['payment_link' => $response['response']['payment_url']]); 
		} 
	}
