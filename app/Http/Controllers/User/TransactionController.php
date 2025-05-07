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

class TransactionController extends Controller
{
	use WebResponseTrait; 
	
	public function __construct()
    {
        $this->middleware('auth')->except('transactionReceiptPdf');
    }
	
	public function index()
    {  
		$txnStatuses = Transaction::select('txn_status')
		->groupBy('txn_status')
		->pluck('txn_status');
 
        return view('user.transaction.transaction-list', compact('txnStatuses'));
    }
	
	public function transactionAjax(Request $request)
	{
		if ($request->ajax()) {
			// Define the columns for ordering and searching
			$columns = ['id', 'platform_name', 'order_id', 'fees', 'txn_amount', 'unit_convert_exchange', 'comments', 'notes', 'refund_reason', 'status', 'created_at', 'created_at', 'action'];

			 // Global search value
			$start = $request->input('start'); // Offset for pagination
			$limit = $request->input('length'); // Limit for pagination
			$orderColumnIndex = $request->input('order.0.column', 0);
			$orderDirection = $request->input('order.0.dir', 'asc'); // Default order direction
			//$search = $request->input('search.value'); 
			$search = $request->input('search');

			$query = Transaction::where('user_id', auth()->user()->id);

			// Apply filters dynamically based on request inputs
			if ($request->filled('platform_name')) {
				$query->where('platform_name', $request->platform_name);
			}

			if ($request->filled(['start_date', 'end_date'])) {
				if ($request->start_date === $request->end_date) {
					// If both dates are the same, use 'whereDate' for exact match
					$query->whereDate('created_at', $request->start_date);
				} else {
					// Otherwise, use 'whereBetween' for the range
					$query->whereBetween('created_at', [$request->start_date, $request->end_date]);
				}
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
						->orWhere('transaction_type', 'LIKE', "%{$search}%")
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
				 
				$data[] = [
					'id' => $i,
					'platform_name' => $value->platform_name,
					'order_id' => $value->order_id,
					'fees' => Helper::decimalsprint($value->fees, 2).' '.config('setting.default_currency'),
					'transaction_type' => '<span class="text-' . ($value->transaction_type == 'debit' ? 'danger' : 'success') . '">' . e($value->transaction_type) . '</span>', 
					'txn_amount' => '<span class="text-' . ($value->transaction_type == 'debit' ? 'danger' : 'success') . '">' . Helper::decimalsprint($value->txn_amount, 2) . ' ' . (config('setting.default_currency') ?? '') . '</span>', 
					'unit_convert_exchange' => $value->rates ? Helper::decimalsprint($value->rates, 2) : "1.00",
					'comments' => $value->comments ?? 'N/A',
					'notes' => $value->notes,
					'refund_reason' => $value->refund_reason,
					'status' => $value->txn_status,
					'created_at' => $value->created_at->format('M d, Y H:i:s'),
					'action' => '', // Initialize action buttons
				];

				// Manage actions with permission checks
				$actions = [];
				$actions[] = '<div class="d-flex align-items-center gap-2">';
				
				$actions[] = '<a href="' . route('transaction.receipt', ['id' => $value->id]) . '" class="btn btn-sm btn-primary" onclick="viewReceipt(this, event)" data-toggle="tooltip" data-placement="bottom" title="view receipt"><i class="bi bi-info-circle"></i></a>';
				 
				$actions[] = '<a href="' . route('transaction.receipt-pdf', ['id' => $value->id]) . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="bottom" title="download pdf receipt"><i class="bi bi-file-earmark-pdf"></i></a>';
				
				/* if (
					strtolower($value->platform_name) === "transfer to bank" && 
					strtolower($value->platform_provider) === "lightnet" && 
					strtolower($value->txn_status) === "pending"
				) {
					$actions[] = sprintf(
						'<a href="%s" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Commit the required transaction" onclick="commitTransaction(this, event)"><i class="bi bi-arrow-left-right"></i></a>',
						route('transfer-to-bank.commit-transaction', ['id' => $value->id])
					);
				}  */
				$actions[] = '</div>';
				 
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
	
	public function transactionReceipt($transactionId)
	{
		$transaction = Transaction::with(['user', 'receive'])->findOrFail($transactionId); 
		$view = view('user.transaction.transaction-reciept', compact('transaction'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	
	public function transactionReceiptPdf($transactionId)
	{ 
		$transaction = Transaction::with(['user', 'receive'])->findOrFail($transactionId);
		//return view('user.transaction.transaction-receipt-pdf', compact('transaction'));
		 
		$pdf = Pdf::loadView('user.transaction.transaction-receipt-pdf', compact('transaction')); 
		$pdf->set_option('isHtml5ParserEnabled', true)
		->set_option('isPhpEnabled', true)
		->set_option('isHtml5Parse', true)
		->set_option('isCssFloatEnabled', true)
		->set_option('isImageEnabled', true);
		return $pdf->download($transaction->order_id.'-receipt.pdf');
	}
	
	// Geopay To Geopay Wallet
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
			
			$fromComment = 'You have successfully transferred ' . $txnAmount . ' USD to ' . $toUser->first_name . ' ' . $toUser->last_name . ' via Wallet-to-Wallet. Thank you for choosing GEOPAY for secure wallet transfers.';
			$toComment = $user->first_name . ' ' . $user->last_name . ' has sent you ' . $txnAmount . ' USD to your wallet.';
		 
			$orderId = "GPWW-".$user->id."-".time();
			// Create a transaction record
			$creditTransaction = Transaction::create([
				'user_id' => $toUser->id,
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
	 
}
