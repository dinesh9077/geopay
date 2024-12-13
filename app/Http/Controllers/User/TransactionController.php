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

class TransactionController extends Controller
{
	use WebResponseTrait;
	protected $airtimeService;
    public function __construct()
    {
		$this->airtimeService = new AirtimeService();
        $this->middleware('auth')->except('internationalAirtimeCallback');
    }
	
	public function index()
    {  
        return view('user.transaction.transaction-list');
    }
	
	public function transactionAjax(Request $request)
	{
		if ($request->ajax()) {
			// Define the columns for ordering and searching
			$columns = ['id', 'platform_name', 'order_id', 'fees', 'txn_amount', 'unit_convert_exchange', 'comments', 'notes', 'status', 'created_at', 'created_at', 'action'];

			 // Global search value
			$start = $request->input('start'); // Offset for pagination
			$limit = $request->input('length'); // Limit for pagination
			$orderColumnIndex = $request->input('order.0.column', 0);
			$orderDirection = $request->input('order.0.dir', 'asc'); // Default order direction
			//$search = $request->input('search.value'); 
			$search = $request->input('search');

			$query = Transaction::query();

			// Apply filters dynamically based on request inputs
			if ($request->filled('platform_name')) {
				$query->where('platform_name', $request->platform_name);
			}

			if ($request->filled(['start_date', 'end_date'])) {
				$query->whereBetween('created_at', [$request->start_date, $request->end_date]);
			}

			if ($request->filled('txn_status')) {
				$query->where('txn_status', $request->txn_status);
			}

			// Apply search filter if present
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->orWhere('platform_name', 'LIKE', "%{$search}%")
						->orWhere('order_id', 'LIKE', "%{$search}%")
						->orWhere('comments', 'LIKE', "%{$search}%")
						->orWhere('notes', 'LIKE', "%{$search}%")
						->orWhere('txn_amount', 'LIKE', "%{$search}%")
						->orWhere('created_at', 'LIKE', "%{$search}%");
				});
			}
 
			$totalData = $query->count(); // Total records before pagination
			$totalFiltered = $totalData; // Total records after filtering

			// Apply ordering, limit, and offset for pagination
			$values = $query
				->orderBy($columns[$orderColumnIndex] ?? 'id', $orderDirection)
				->offset($start)
				->limit($limit)
				->get();

			// Format data for the response
			$data = [];
			$i = $start + 1;
			foreach ($values as $value) {
				
				switch ($value->txn_status) {
					case 'pending':
						$value->txn_status = '<span class="badge badge-warning">Pending</span>';
						break;
					case 'process':
						$value->txn_status = '<span class="badge badge-info">In Process</span>';
						break;
					case 'success':
						$value->txn_status = '<span class="badge badge-success">Success</span>';
						break; 
					default:
						$value->txn_status = '<span class="badge badge-secondary">Unknown</span>';
						break;
				}
				 
				$data[] = [
					'id' => $i,
					'platform_name' => $value->platform_name,
					'order_id' => $value->order_id,
					'fees' => Helper::decimalsprint($value->fees, 2).' '.config('setting.default_currency'),
					'txn_amount' => Helper::decimalsprint($value->txn_amount, 2).' '.config('setting.default_currency') ?? 0,
					'unit_convert_exchange' => $value->unit_convert_exchange ? Helper::decimalsprint($value->unit_convert_exchange, 2) : "1.00",
					'comments' => $value->comments ?? 'N/A',
					'notes' => $value->notes,
					'status' => $value->txn_status,
					'created_at' => $value->created_at->format('M d, Y H:i:s'),
					'action' => '', // Initialize action buttons
				];

				// Manage actions with permission checks
				$actions = [];
				$actions[] = '<a href="' . route('transaction.details', ['id' => $value->id]) . '" class="btn btn-sm btn-primary"><i class="bi bi-info-circle"></i></a>';
				 
				// Assign actions to the row if permissions exist
				$data[$i - $start - 1]['action'] = implode(' ', $actions);
				$i++;
			}

			// Return JSON response
			return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
			]);
		} 
	}
	
	// geopay to geopay wallet
	public function walletToWallet()
    { 
		$countries = Country::select('id', 'name', 'isdcode', 'country_flag')->get();

        $countriesWithFlags = $countries->transform(function ($country) {
            if ($country->country_flag) {
                $country->country_flag = asset('country/' . $country->country_flag);
            } 
            return $country;
        });
        return view('user.transaction.wallet-to-wallet', compact('countriesWithFlags'));
    }
	
	public function walletToWalletStore(Request $request)
	{
		$user = auth()->user();
		
		// Validation rules
		$validator = Validator::make($request->all(), [
			'country_id' => 'required|integer|exists:countries,id', // Check if country_id exists in the 'countries' table
			'mobile_number' => 'required|integer',
			'amount' => 'required|numeric|gt:0',
			'notes' => 'nullable|string',
		]);
		
		// Retrieve country details (if exists)
		$country = Country::find($request->country_id);

		// Custom validation logic
		$validator->after(function ($validator) use ($request, $user, $country)
		{
			if ($request->input('country_id') && $request->input('mobile_number')) 
			{
				$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');

				// Check if user is trying to pay themselves
				if ($formattedNumber === $user->formatted_number) {
					$validator->errors()->add('mobile_number', 'You cannot transfer funds to your own account.');
				}

				// Check if the mobile number is registered
				if (!User::where('formatted_number', $formattedNumber)->exists()) {
					$validator->errors()->add('mobile_number', 'The provided mobile number is not registered.');
				}
				 
				// Check if the mobile number is registered and KYC is approved
				if (!User::where('formatted_number', $formattedNumber)->where('is_kyc_verify', 1)->exists()) {
					$validator->errors()->add('mobile_number', 'The mobile number entered is not linked to an account with approved KYC. Please complete your KYC verification to proceed.');
				}
 
				// Check if user has sufficient balance
				if ($request->input('amount') > $user->balance) {
					$validator->errors()->add('amount', 'Insufficient balance to complete this transaction.');
				}
			}
		});
		 
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		try {
			
			DB::beginTransaction();

			$txnAmount = $request->amount;
			$countryId = $request->country_id;
			$notes = $request->notes;

			// Format the mobile number again to ensure correct recipient
			$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			
			// Retrieve the recipient user
			$toUser = User::where('formatted_number', $formattedNumber)->first();

			// Check if recipient user is found
			if (!$toUser) {
				return $this->errorResponse('Recipient user not found.');
			}
 
			// Update sender's balance (debit the amount)
			$user->decrement('balance', $txnAmount);

			// Update receiver's balance (credit the amount)
			$toUser->increment('balance', $txnAmount);
			
			$fromComment = 'You have successfully transferred ' . $txnAmount . ' USD to ' . $toUser->first_name . ' ' . $toUser->last_name . '.';
			$toComment = $user->first_name . ' ' . $user->last_name . ' has sent you ' . $txnAmount . ' USD to your wallet.';
			$orderId = "GPWW-".time();
			// Create a transaction record
			$creditTransaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $toUser->id,
				'platform_name' => 'geopay to geopay wallet',
				'platform_provider' => 'geopay to geopay wallet',
				'transaction_type' => 'credit', // Indicating that the user is debiting funds
				'country_id' => $toUser->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success', // Assuming the transaction is successful
				'comments' => $toComment,
				'notes' => $notes,
				'order_id' => $orderId,
				'created_at' => now(),
				'updated_at' => now(),
			]);
			
			Helper::updateLogName($creditTransaction->id, Transaction::class, 'wallet to wallet transaction', $toUser->id);
			
			// Create a transaction record
			$debitTransaction = Transaction::create([
				'user_id' => $user->id,
				'receiver_id' => $user->id,
				'platform_name' => 'geopay to geopay wallet',
				'platform_provider' => 'geopay to geopay wallet',
				'transaction_type' => 'debit', // Indicating that the user is debiting funds
				'country_id' => $user->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success', // Assuming the transaction is successful
				'comments' => $fromComment,
				'notes' => $notes,
				'order_id' => $orderId,
				'created_at' => now(),
				'updated_at' => now(),
			]);
			
			Helper::updateLogName($debitTransaction->id, Transaction::class, 'wallet to wallet transaction', $user->id);
			 
			Notification::send($user, new WalletTransactionNotification($user, $toUser, $txnAmount, $fromComment, $notes)); // Sender Notification
			Notification::send($toUser, new WalletTransactionNotification($user, $toUser, $txnAmount, $toComment, $notes)); // Receiver Notification

 
			DB::commit();

			// Success response
			return $this->successResponse('The wallet transaction was completed successfully.');
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        } 
	}
	
	public function internationalAirtime()
	{ 
		$countries = $this->airtimeService->getCountries(); 
		return view('user.transaction.international-airtime', compact('countries'));
	}
	
	public function internationalAirtimeOperator(Request $request)
	{ 
		$countryCode = $request->country_code;
		return $this->airtimeService->getOperators($countryCode, true); 
	}
	
	public function internationalAirtimeProduct(Request $request)
	{ 
		$countryCode = $request->country_code;
		$operatorId = $request->operator_id;
		return $this->airtimeService->getProducts($countryCode, $operatorId, true); 
	}
	 
	public function internationalAirtimeValidatePhone(Request $request)
	{ 
		$mobile_number = '+' . ltrim($request->mobile_number, '+');
		$operator_id = $request->operator_id;
		return $this->airtimeService->getValidatePhoneByOperator($mobile_number, $operator_id, true); 
	}
	
	public function internationalAirtimeStore(Request $request)
	{  
		$user = auth()->user();
		 
		// Validation rules
		$validator = Validator::make($request->all(), [
			'product_name' => 'required|string', 
			'unit_convert_amount' => 'required|numeric', 
			'unit_convert_exchange' => 'required|numeric', 
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
			if ($request->input('unit_convert_amount') > $user->balance) {
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
			$request['order_id'] = "GPIA-".time();
			
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
			 
			$response = $this->airtimeService->transactionRecord($request, $user, true); 
			  
			if (!$response['success']) {
				$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
				throw new \Exception($errorMsg);
			}
            //Log::info($response);
			// Transaction variables
			$txnAmount = $request->input('unit_convert_amount');
			$productName = $request->input('product_name');
			$mobileNumber = '+' . ltrim($request->input('mobile_number'), '+');
			
			$statusMessage = strtoupper($response['response']['status']['message']);
            $txnStatus = '';
            
            switch ($statusMessage) {
                case 'COMPLETED':
                    $txnStatus = 'success';
                    break;
                case 'DECLINED':
                    $txnStatus = 'declined';
                    break;
                default:
                    $txnStatus = 'process';
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
				'unique_identifier' => $response['response']['external_id'],
				'product_name' => $productName,
				'operator_id' => $request->input('operator_id'),
				'product_id' => $request->input('product_id'),
				'mobile_number' => $mobileNumber,
				'unit_currency' => $request->input('unit_currency', ''),
				'unit_amount' => $request->input('unit_amount', ''),
				'rates' => $request->input('rates', ''),
				'unit_convert_currency' => $request->input('unit_convert_currency', ''),
				'unit_convert_amount' => $txnAmount,
				'unit_convert_exchange' => $request->input('unit_convert_exchange', 0),
				'api_request' => json_encode($response['request']),
				'api_response' => json_encode($response['response']),
				'order_id' => $request->order_id,
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
		$statusMessage = strtoupper($request['status']['message']);
 
        $txnStatus = '';
        
        switch ($statusMessage) {
            case 'COMPLETED':
                $txnStatus = 'success';
                break;
            case 'DECLINED':
                $txnStatus = 'declined';
                break;
            default:
                $txnStatus = 'process';
        }
 
		$updated = Transaction::where('unique_identifier', $uniqueIdentifier)
			->update(['txn_status' => $txnStatus]);

		return true;
	}

}
