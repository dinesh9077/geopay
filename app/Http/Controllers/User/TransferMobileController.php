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
	MasterService, LiquidNetService, OnafricService
}; 
use App\Notifications\WalletTransactionNotification;
use App\Notifications\AirtimeRefundNotification;
use Illuminate\Support\Facades\Notification;
use Helper;
use Carbon\Carbon;  
use App\Enums\OnafricStatus;

class TransferMobileController extends Controller
{ 
	use WebResponseTrait;
	protected $liquidNetService;
	protected $masterService;
	protected $onafricService;
    public function __construct()
    {
		$this->liquidNetService = new LiquidNetService(); 
		$this->masterService = new MasterService(); 
		$this->onafricService = new OnafricService(); 
		$this->middleware('auth')->except('transferToMobileWebhook');
    }	
	
	public function transferToMobileMoney()
	{   
		$countries = $this->onafricService->country();  
		
		$beneficiaries = Beneficiary::where('category_name', 'transfer to mobile') 
		->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.recipient_country')) as recipient_country")
		->where('user_id', auth()->user()->id)
		->pluck('recipient_country')
		->unique()
		->values()
		->toArray();
		
		$countries = $countries->whereIn('id', $beneficiaries)->values();
		return view('user.transaction.transfer-mobile.index', compact('countries'));
	}
	 
	public function transferToMobileBeneficiary()
	{  
		$countries = $this->onafricService->country(); 
		$view = view('user.transaction.transfer-mobile.transfer-mobile-beneficiary', compact('countries'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	 
	public function transferToMobileBeneficiaryStore(Request $request)
	{    
    	// $recipient_country_code = $request->recipient_country_code; 
		// $recipient_mobile = $request->recipient_mobile;
		// $response = $this->onafricService->getAccountRequest($recipient_country_code, $recipient_mobile);
		
		// if (
        //     !isset($response['success']) || 
        //     !$response['success'] || 
        //     (isset($response['response']['status_code']) && $response['response']['status_code'] != "Active")
        // ) {
               
        //     return $this->errorResponse('Provided country and mobile number are not active');
        // }
        
		try { 
			$user = Auth::user();
			 
			DB::beginTransaction();
			$beneficiaryData = $request->except('_token', 'recipient_mobile', 'mobile_code');
			
			$mobile_code = $request->mobile_code ?? '';
			$mobile_num = $request->recipient_mobile ?? ''; 
				
			$beneficiaryData['recipient_mobile'] = $mobile_num ?? '';
			$beneficiaryData['mobile_code'] = $mobile_code ?? '';
			$beneficiaryData['sender_country'] = $user->country->id ?? '';
			$beneficiaryData['sender_country_code'] = $user->country->iso ?? '';
			$beneficiaryData['sender_country_name'] = $user->country->name ?? '';
			$beneficiaryData['sender_mobile'] = isset($user->formatted_number) ? ltrim($user->formatted_number, '+') : '';
			/* $beneficiaryData['sender_name'] = $user->first_name ?? '';
			$beneficiaryData['sender_surname'] = $user->last_name ?? '';
			$beneficiaryData['sender_placeofbirth'] = $user->date_of_birth ?? '';
			$beneficiaryData['sender_address'] = $user->address ?? '';
			$beneficiaryData['sender_city'] = $user->city ?? '';
			$beneficiaryData['sender_state'] = $user->state ?? '';
			$beneficiaryData['sender_postalcode'] = $user->zip_code ?? '';
			$beneficiaryData['sender_email'] = $user->email ?? '';
			$beneficiaryData['purposeOfTransfer'] = $user->business_activity_occupation ?? '';
			$beneficiaryData['sourceOfFunds'] = $user->source_of_fund ?? ''; */
			
			$recipientCountry = Country::find($request->recipient_country ?? null);
			$beneficiaryData['payoutCountry'] = $recipientCountry->iso3 ?? '';
			$beneficiaryData['payoutCurrency'] = $recipientCountry->currency_code ?? '';
			
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
			return $this->successResponse('The recipient was completed successfully.');
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
		$output = '<option value="">Select Recipient</option>';

		// Loop through beneficiaries and prepare output
		foreach ($beneficiaries as $beneficiary) {
			$data = $beneficiary->data ?? [];
			$firstName = $data['recipient_name'] ?? '';
			$lastName = $data['recipient_surname'] ?? ''; 
			$recipientMobile = ($data['mobile_code'] ?? '').($data['recipient_mobile'] ?? ''); 

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
				return $this->errorResponse('No Recipient found.');
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
		$countries = $this->onafricService->country();
		$beneficiary = Beneficiary::find($id);
		$edit = $beneficiary->data;
		   
		$view = view('user.transaction.transfer-mobile.edit-transfer-mobile-beneficiary',
		compact('countries', 'beneficiary', 'edit'))
		->render();
		return $this->successResponse('success', ['view' => $view]);	
	}
	
	public function transferToMobileBeneficiaryUpdate(Request $request, $id)
	{   
        DB::beginTransaction();
		try {
			
			$user = Auth::user();
			$beneficiary = Beneficiary::find($id);
			
			$recipient_country_code = $request->recipient_country_code; 
    		$recipient_mobile = $request->recipient_mobile;
    		 
            $beneficiaryDataArray = is_array($beneficiary->data) ? $beneficiary->data : [];
    
            // if ($recipient_country_code !== ($beneficiaryDataArray['recipient_country_code'] ?? '') ||
            //     $recipient_mobile !== ($beneficiaryDataArray['recipient_mobile'] ?? '')) 
            // {
        	// 	$response = $this->onafricService->getAccountRequest($recipient_country_code, $recipient_mobile);
        		
        	// 	if (
            //         !isset($response['success']) || 
            //         !$response['success'] || 
            //         (isset($response['response']['status_code']) && $response['response']['status_code'] != "Active")
            //     ) {
                       
            //         return $this->errorResponse('Provided country and mobile number are not active');
            //     }	
    		// }
			$beneficiaryData = $request->except('_token', 'recipient_mobile', 'mobile_code');
			
			$mobile_code = $request->mobile_code ?? '';
			$mobile_num = $request->recipient_mobile ?? ''; 
				
			$beneficiaryData['recipient_mobile'] = $mobile_num ?? '';
			$beneficiaryData['mobile_code'] = $mobile_code ?? '';
			 
			$beneficiaryData['sender_country'] = $user->country->id ?? '';
			$beneficiaryData['sender_country_code'] = $user->country->iso ?? '';
			$beneficiaryData['sender_country_name'] = $user->country->name ?? '';
			$beneficiaryData['sender_mobile'] = isset($user->formatted_number) ? ltrim($user->formatted_number, '+') : '';
			 
			/* $beneficiaryData['sender_name'] = $user->first_name ?? '';
			$beneficiaryData['sender_surname'] = $user->last_name ?? '';
			$beneficiaryData['sender_placeofbirth'] = $user->date_of_birth ?? '';
			$beneficiaryData['sender_address'] = $user->address ?? '';
			$beneficiaryData['sender_city'] = $user->city ?? '';
			$beneficiaryData['sender_state'] = $user->state ?? '';
			$beneficiaryData['sender_postalcode'] = $user->zip_code ?? '';
			$beneficiaryData['sender_email'] = $user->email ?? '';
			$beneficiaryData['purposeOfTransfer'] = $user->business_activity_occupation ?? '';
			$beneficiaryData['sourceOfFunds'] = $user->source_of_fund ?? '' */;
			
			$recipientCountry = Country::find($request->recipient_country ?? null);
			$beneficiaryData['payoutCountry'] = $recipientCountry->iso3 ?? '';
			$beneficiaryData['payoutCurrency'] = $recipientCountry->currency_code ?? '';
			
			$data = []; 
			$data['category_name'] = $beneficiaryData['category_name'];
			$data['service_name'] = $beneficiaryData['service_name'];
			$data['user_id'] = Auth::id(); 
			$data['updated_at'] = now(); 
			$data['data'] = $beneficiaryData;
			  
			$beneficiary->update($data);
			Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to mobile beneficiary');
			
			DB::commit(); 
			return $this->successResponse('The recipient was updated successfully.');
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
				throw new \Exception('Recipient not found.');
			}

			// Log ID before deletion
			Helper::updateLogName($beneficiary->id, Beneficiary::class, 'transfer to mobile beneficiary');

			// Delete the beneficiary
			$beneficiary->delete();

			DB::commit(); 
			return redirect()->back()->withSuccess('The recipient was deleted successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();  
			return redirect()->back()->withError($e->getMessage());
		}  
	}
	
	public function transferToMobileCommission(Request $request)
	{
		$beneficiaryId = $request->beneficiaryId;
		$txnAmount = $request->txnAmount;
		 
		$beneficiary = Beneficiary::find($beneficiaryId);
		if (!$beneficiary || empty($beneficiary->data ?? [])) {
			return $this->errorResponse('recipient not found.');
		}
		
		$country = Country::find($beneficiary->data['recipient_country']);
		$liveExchangeRate = LiveExchangeRate::select('markdown_rate', 'aggregator_rate')
		->where('channel', $beneficiary->data['service_name'])
		->where('currency', $country->currency_code)
		->first(); 
		
		if(!$liveExchangeRate)
		{
			$liveExchangeRate = ExchangeRate::select('exchange_rate as markdown_rate', 'aggregator_rate')
			->where('type', 2)
			->where('service_name', $beneficiary->data['service_name'])
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
		
		$onafricChannel = OnafricChannel::find($beneficiary->data['channel_id'] ?? null);
		
		$sendFee = $onafricChannel && $onafricChannel->fees ? $onafricChannel->fees : 0;
		$commissionType = $onafricChannel && $onafricChannel->commission_type ? $onafricChannel->commission_type : 'flat';
		$commissionCharge = $onafricChannel && $onafricChannel->commission_charge ? $onafricChannel->commission_charge : 0;
		
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
	  
	public function transferToMobileStore(Request $request)
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
			$request['order_id'] = "GPTM-".$user->id."-".time();
			$request['timestamp'] = now()->format('Y-m-d H:i:s');
			
			$remitCurrency = config('setting.default_currency') ?? 'USD';
			
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
			
			$response = $this->onafricService->sendMobileTransaction($request, $beneficiary->data);
			  
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
			
			$onafricStatus = $response['response']['details']['transResponse'][0]['status']['message'] ?? 'Accepted';
			$apiStatus = $onafricStatus;
			$txnStatus = OnafricStatus::from($onafricStatus)->label();
			
			$txnAmount = $request->input('txnAmount');
			$netAmount = $request->input('netAmount');
			
			// Deduct balance
			$user->decrement('balance', $netAmount); 
			
			// Check if necessary fields exist to prevent undefined index warnings
			$beneficiaryFirstName = $beneficiary->data['recipient_name'] ?? '';
			$beneficiaryLastName = $beneficiary->data['recipient_surname'] ?? ''; 
			$mobileNumber = ltrim(($beneficiary->data['mobile_code'] ?? ''), '+').($beneficiary->data['recipient_mobile'] ?? '');
			$payoutCurrency = $beneficiary->data['payoutCurrency'] ?? '';
			$payoutCurrencyAmount = $request->payoutCurrencyAmount;
			$aggregatorCurrencyAmount = $request->aggregatorCurrencyAmount;
			$exchangeRate = $request->exchangeRate; 
			$confirmationId = $request['order_id'];
			// Concatenate beneficiary name safely
			$beneficiaryName = trim("$beneficiaryFirstName $beneficiaryLastName"); // Using trim to remove any leading/trailing spaces

			// Build the comment using sprintf for better readability
			$comments = sprintf( 
				"You initiated a payout to %s for $%s via Mobile Money. Thank you for trusting GEOPAY for instant mobile money transactions. ",
				$beneficiaryName,
				number_format($netAmount, 2) 
			); 
			
			// Create transaction record
			$transaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'transfer to mobile',
				'platform_provider' => $beneficiary->data['service_name'],
				'transaction_type' => 'debit',
				'country_id' => $user->country_id,
				'txn_amount' => $netAmount,
				'txn_status' => $txnStatus,
				'comments' => $comments,
				'notes' => $request->input('notes'),
				'unique_identifier' => $confirmationId,
				'product_name' => null, 
				'product_id' => null,
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
			Helper::updateLogName($transaction->id, Transaction::class, 'transfer to mobile transaction', $user->id); 
			
			Notification::send($user, new AirtimeRefundNotification($user, $netAmount, $transaction->id, $comments, $transaction->notes, ucfirst($txnStatus)));
			
			DB::commit();  
			return $this->successResponse('Mobile transfer has been successfully processed.', ['userBalance' => Helper::decimalsprint($user->balance, 2), 'currencyCode' => config('setting.default_currency')]);
		} catch (\Throwable $e) {
			DB::rollBack();  
			return $this->errorResponse($e->getMessage()); 
		}  
	}
	
	public function transferToMobileWebhook(Request $request, $uniqueId)
	{
		Log::info('Webhook received', ['data' => $request->all()]);

		if (!$request->all()) {
			return response()->json([
				'status' => false,
				'error_code' => 'EMPTY_REQUEST',
				'message' => 'Empty request payload.'
			], 400);
		}
 
		$thirdPartyTransId = $request->input('thirdPartyTransId');
		$statusMessage = $request->input('status.message');
		
		if (!$thirdPartyTransId || !$statusMessage) {
			return response()->json([
				'status' => false,
				'error_code' => 'MISSING_FIELDS',
				'message' => 'Required fields are missing.'
			], 422);
		} 
		
		// Find the transaction based on thirdPartyTransId
		$transaction = Transaction::where('order_id', $thirdPartyTransId)->first();

		if (!$transaction) { 
			return response()->json([
				'status' => false,
				'error_code' => 'TRANSACTION_NOT_FOUND',
				'message' => 'Transaction not found.'
			], 404);
		}

		try
		{
			$txnStatus = OnafricStatus::from($statusMessage)->label();

			// Handle refund
			if ($txnStatus === "cancelled and refunded") {
				$transaction->processAutoRefund($txnStatus, $statusMessage);
			}

			// Track old value before change
			$oldStatus = $transaction->txn_status;

			// Assign new values
			$transaction->txn_status = ($txnStatus === "cancelled and refunded")
				? $transaction->txn_status
				: $txnStatus;

			$transaction->api_status = $statusMessage;

			if ($txnStatus === "paid") {
				$transaction->complete_transaction_at = now();
			}
 
			// Save changes
			$transaction->save();

			$user = $transaction->user;
			Notification::send(
				$user,
				new AirtimeRefundNotification(
					$user,
					$transaction->txn_amount,
					$transaction->id,
					$transaction->comments,
					$transaction->notes,
					ucfirst($transaction->txn_status)
				)
			);

			return response()->json([
				'status' => true,
				'message' => 'Transaction updated successfully'
			], 200);

		} catch (\Throwable $e) {
			Log::error("Error processing webhook: {$e->getMessage()}");

			return response()->json([
				'status' => false,
				'error_code' => 'SERVER_ERROR',
				'message' => 'Internal server error'
			], 500);
		}
	}
}
