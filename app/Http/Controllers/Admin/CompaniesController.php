<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CompanyDetail;
use App\Models\CompanyDocument;
use App\Models\Transaction;
use App\Models\LoginLog;
use App\Models\CompanyDirector;
use DB, Auth, Helper, Hash, Validator;
use App\Http\Traits\WebResponseTrait; 
use App\Mail\KycRejectionMail;
use App\Mail\DirectorApprovalMail;
use Illuminate\Support\Facades\Mail;
use ImageManager;
use App\Notifications\WalletTransactionNotification;
use Illuminate\Support\Facades\Notification;

class CompaniesController extends Controller
{
	use WebResponseTrait;
    public function companiesActive()
	{
		return view('admin.companies.active');
	}
	
    public function companiesPending()
	{
		return view('admin.companies.pending');
	}
	
    public function companiesBlock()
	{
		return view('admin.companies.block');
	}
	
	public function companiesAjax(Request $request)
	{
		if ($request->ajax())
		{
			// Define the columns for ordering and searching
			$columns = ['id', 'name', 'email', 'mobile', 'country.name', 'is_kyc_verify', 'is_email_verify', 'is_mobile_verify', 'status', 'created_at'];

			$search = $request->input('search.value'); // Global search value
			$start = $request->input('start'); // Offset for pagination
			$limit = $request->input('length'); // Limit for pagination
			$orderColumnIndex = $request->input('order.0.column', 0);
			$orderDirection = $request->input('order.0.dir', 'asc'); // Default order direction
			
			$is_kyc_verify = $request->input('is_kyc_verify'); // Offset for pagination
			$status = $request->input('status'); // Limit for pagination
			$page_status = $request->input('page_status'); // Limit for pagination
			 
			// Base query with relationship for country
			$query = User::with('country:id,name')
				->where('is_company', 1)
				->where('is_kyc_verify', $is_kyc_verify);

			if ($page_status === 'pending') {
				$query->whereIn('status', [1, 0]); // Allow both active and inactive statuses
			} else {
				$query->where('status', $status); // Apply specific status
			}
 
			// Apply search filter if present
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->where('first_name', 'LIKE', "%{$search}%")
						->orWhere('last_name', 'LIKE', "%{$search}%")
						->orWhere('email', 'LIKE', "%{$search}%")
						->orWhere('mobile', 'LIKE', "%{$search}%")
						->orWhereHas('country', function ($q) use ($search) {
							$q->where('name', 'LIKE', "%{$search}%");
						})
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
				$statusClass = $value->status == 1 ? 'success' : 'danger';
				$statusText = $value->status == 1 ? 'Active' : 'In-Active';

				$data[] = [
					'id' => $i,
					'name' => $value->first_name.' '.$value->last_name,
					'email' => $value->email,
					'mobile' => $value->formatted_number,
					'country' => $value->country ? $value->country->name : 'N/A',
					'is_kyc_verify' => $value->is_kyc_verify ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>',
					'is_email_verify' => $value->is_email_verify ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>',
					'is_mobile_verify' => $value->is_mobile_verify ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>', 
					'status' => '<div class="form-check form-switch">
						<input 
							class="form-check-input companyActiveInactive" 
							type="checkbox" 
							role="switch" 
							id="flexSwitchCheckDefault" 
							data-id="'.$value->id.'" 
							'.($value->status ? "checked" : "").'
						>
						<label class="form-check-label" for="flexSwitchCheckDefault"></label>
					</div>
					',
					'created_at' => $value->created_at->format('M d, Y H:i:s'),
					'action' => '', // Initialize action buttons
				];

				// Manage actions with permission checks
				$actions = [];
				if (config('permission.active_company.edit') || config('permission.pending_company.edit') || config('permission.block_company.edit')) {
					$actions[] = '<a href="' . route('admin.companies.edit', ['id' => $value->id]) . '" class="btn btn-sm btn-primary">View Details</a>';
				} 
				if (config('permission.active_company.edit') || config('permission.pending_company.edit') || config('permission.block_company.edit')) 
				{  
					$actions[] = '<a href="' . route('admin.companies.view-kyc', ['id' => $value->id]) . '"  class="btn btn-sm btn-warning">View Kyc Data</a>';
				} 

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
	
	public function companiesUpdateStatus(Request $request)
	{ 
		$validator = Validator::make($request->all(), [
			'id' => 'required|exists:users,id',
			'status' => 'required|in:1,0',
		]);
		
		if ($validator->fails()) {
			return $this->errorResponse($validator->errors()->first());
		}
		
		try {
			DB::beginTransaction();
			
			$user = User::find($request->id); 
			$user->update(['status' => $request->status]);
			
			// Generate the updated button HTML dynamically
			$status = $user->status;
			$blockText = $status == 1 ? 'Block' : 'Unblock';
			$blockMsg = $status == 1
				? 'If you block this account, they will not be able to access their dashboard.'
				: 'If you unblock this account, they will be able to access their dashboard.';
			$newStatus = $status == 1 ? 0 : 1;
			$icon = $status == 1 ? 'slash' : 'key';

			// Build the output HTML
			$output = view('components.user-status-button', compact('blockText', 'blockMsg', 'newStatus', 'icon', 'status'))->render();
			
			DB::commit(); 
			return $this->successResponse('Status updated successfully.', ['output' => $output]); 
		} 
		catch (\Throwable $e) 
		{
			// Rollback in case of an exception
			DB::rollBack(); 
			// Return error response with the exception message
			return $this->errorResponse('Failed to update status. ' . $e->getMessage());
		}
	}
 
	public function companiesEdit($companyid)
	{
		$company = User::find($companyid);
		if(!$company)
		{
			return back()->withError('Company details not found.');
		}
		return view('admin.companies.edit', compact('company')); 
	}
	
	public function companiesUpdate(Request $request, $companyid)
	{
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',  
			'password' => [
				'nullable',
				'string',
				'confirmed',
				'min:8',  // Minimum 8 characters 
				function ($attribute, $value, $fail) {
					if (!preg_match('/[A-Z]/', $value)) {
						return $fail('The ' . $attribute . ' must contain at least one uppercase letter.');
					}
					if (!preg_match('/[a-z]/', $value)) {
						return $fail('The ' . $attribute . ' must contain at least one lowercase letter.');
					}
					if (!preg_match('/\d/', $value)) {
						return $fail('The ' . $attribute . ' must contain at least one number.');
					}
					if (!preg_match('/[\W_]/', $value)) {
						return $fail('The ' . $attribute . ' must contain at least one special character.');
					}
				},
			],
			'company_name' => 'required|string',  
		]);
		
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		try {
			DB::beginTransaction();
			
			$data = $request->except('_token', 'password', 'profile_image');
			if($request->filled('password'))
			{
				$data['password'] = Hash::make($request->password);
				$data['xps'] = base64_encode($request->password);
			}
			
			$user = User::find($companyid);
			
			// Handle profile image upload
			if ($request->hasFile('profile_image')) {
				$file = $request->file('profile_image');
				$storedFile = ImageManager::imgUpdate('profile', $user->profile_image, $file->getClientOriginalExtension(), $file);
				$data['profile_image'] = $storedFile;
			}
			 
			$user->update($data);
			  
			DB::commit();
			
			return $this->successResponse('Company have been successfully updated.');
		} 
		catch (\Throwable $e)
		{
			DB::rollBack();
			return $this->errorResponse('Failed. ' . $e->getMessage());
		}
	}
	
	public function companiesIncrementBalance($userId)
	{	
		$users = User::where('id', '!=', $userId)->whereStatus(1)->get();
		$view = view('admin.companies.increment-balance', compact('userId', 'users'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	
	public function storeIncrementBalance(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' => 'required|integer',
			'amount' => 'required|numeric|min:1',
			'remark' => 'required|string|max:255',
		]);
		
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		DB::beginTransaction();

		try {
			$txnAmount = $request->amount;
			$senderId = $request->user_id; // Sender user ID
			$recipientId = $request->id; // Recipient user ID
			$remark = $request->remark;
		  
			// Retrieve the sender and recipient users
			$sender = User::find($senderId);
			$recipient = User::find($recipientId);
	
			// Validate recipient and sender existence
			if (!$recipient || !$sender) {
				return $this->errorResponse('User not found.');
			}

			// Check if sender has sufficient balance
			if ($sender->balance < $txnAmount) {
				return $this->errorResponse('Insufficient balance.');
			}
 
			// Update sender's balance (debit the amount)
			$sender->decrement('balance', $txnAmount);

			// Update recipient's balance (credit the amount)
			$recipient->increment('balance', $txnAmount);
			
			// Construct comments for transactions
			$senderComment = 'You successfully transferred ' . $txnAmount . ' USD to ' 
							 . $recipient->first_name . ' ' . $recipient->last_name . ' by admin.';
			$recipientComment = 'You received ' . $txnAmount . ' USD from ' 
                        . $sender->first_name . ' ' . $sender->last_name . '.';
			
			// Record credit transaction for recipient
			$creditTransaction = Transaction::create([
				'user_id' => $recipient->id,
				'receiver_id' => $recipient->id,
				'platform_name' => 'Admin Transfer',
				'platform_provider' => 'Admin',
				'transaction_type' => 'credit',
				'country_id' => $recipient->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success',
				'comments' => $recipientComment,
				'notes' => $remark,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			Helper::updateLogName($creditTransaction->id, Transaction::class, 'wallet transaction', $recipient->id);

			// Record debit transaction for sender
			$debitTransaction = Transaction::create([
				'user_id' => $recipient->id,
				'receiver_id' => $sender->id,
				'platform_name' => 'Admin Transfer',
				'platform_provider' => 'Admin',
				'transaction_type' => 'debit',
				'country_id' => $sender->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success',
				'comments' => $senderComment,
				'notes' => $remark,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			Helper::updateLogName($debitTransaction->id, Transaction::class, 'wallet transaction', $sender->id);

			// Send notifications
			Notification::send($sender, new WalletTransactionNotification(
				$sender, $recipient, $txnAmount, $senderComment, $remark
			));
			Notification::send($recipient, new WalletTransactionNotification(
				$sender, $recipient, $txnAmount, $recipientComment, $remark
			));
 
			DB::commit();
			return $this->successResponse('Transaction successful.');
		} 
		catch (\Throwable $e)
		{
			DB::rollBack();
			return $this->errorResponse('Failed. ' . $e->getMessage());
		}
	}
	
	public function companiesDecrementBalance($userId)
	{ 
		$users = User::where('id', '!=', $userId)->whereStatus(1)->get();
		$view = view('admin.companies.decrement-balance', compact('userId', 'users'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	
	public function storeDecrementBalance(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user_id' => 'required|integer',
			'amount' => 'required|numeric|min:1',
			'remark' => 'required|string|max:255',
		]);
		
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		DB::beginTransaction();

		try {
		
			$txnAmount = $request->amount;
			$senderId = $request->id; // Sender user ID
			$recipientId = $request->user_id; // Recipient user ID
			$remark = $request->remark;
		  
			// Retrieve the sender and recipient users
			$sender = User::find($senderId);
			$recipient = User::find($recipientId);
	
			// Validate recipient and sender existence
			if (!$recipient || !$sender) {
				return $this->errorResponse('User not found.');
			}

			// Check if sender has sufficient balance
			if ($sender->balance < $txnAmount) {
				return $this->errorResponse('Insufficient balance.');
			}
 
			// Update sender's balance (debit the amount)
			$sender->decrement('balance', $txnAmount);

			// Update recipient's balance (credit the amount)
			$recipient->increment('balance', $txnAmount);
			
			// Construct comments for transactions
			$senderComment = 'You successfully transferred ' . $txnAmount . ' USD to ' 
							 . $recipient->first_name . ' ' . $recipient->last_name . ' by admin.';
			$recipientComment = 'You received ' . $txnAmount . ' USD from ' 
                        . $sender->first_name . ' ' . $sender->last_name . '.';
			
			// Record credit transaction for recipient
			$creditTransaction = Transaction::create([
				'user_id' => $recipient->id,
				'receiver_id' => $recipient->id,
				'platform_name' => 'Admin Transfer',
				'platform_provider' => 'Admin',
				'transaction_type' => 'credit',
				'country_id' => $recipient->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success',
				'comments' => $recipientComment,
				'notes' => $remark,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			Helper::updateLogName($creditTransaction->id, Transaction::class, 'wallet transaction', $recipient->id);

			// Record debit transaction for sender
			$debitTransaction = Transaction::create([
				'user_id' => $recipient->id,
				'receiver_id' => $sender->id,
				'platform_name' => 'Admin Transfer',
				'platform_provider' => 'Admin',
				'transaction_type' => 'debit',
				'country_id' => $sender->country_id,
				'txn_amount' => $txnAmount,
				'txn_status' => 'success',
				'comments' => $senderComment,
				'notes' => $remark,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			Helper::updateLogName($debitTransaction->id, Transaction::class, 'wallet transaction', $sender->id);

			// Send notifications
			Notification::send($sender, new WalletTransactionNotification(
				$sender, $recipient, $txnAmount, $senderComment, $remark
			));
			Notification::send($recipient, new WalletTransactionNotification(
				$sender, $recipient, $txnAmount, $recipientComment, $remark
			));
 
			DB::commit();
			return $this->successResponse('Transaction successful.');
		} 
		catch (\Throwable $e)
		{
			DB::rollBack();
			return $this->errorResponse('Failed. ' . $e->getMessage());
		}
	}
	public function companiesViewKyc($userId)
	{ 
		return view('admin.companies.view-kyc', compact('userId'));
	}
	
	public function companiesKycUpdate(Request $request)
	{
		try {
			DB::beginTransaction();
			
			// Retrieve variables from the request
			$documentTypeIds = $request->document_type_id;
			$companyDirectorId = $request->company_director_id;
			$companyDetailsId = $request->company_details_id;
			$userId = $request->user_id;
			
			// Ensure that documentTypeIds are not empty
			if (count($documentTypeIds) == 0) {
				return $this->errorResponse('No document types provided.');
			}
			
			foreach($documentTypeIds as $documentTypeId)
			{
				$status = $request->status[$documentTypeId];
				$reason = $request->reason[$documentTypeId] ?? '';
				
				CompanyDocument::where('company_director_id', $companyDirectorId)
				->where('document_type_id', $documentTypeId)
				->update(['status' => $status, 'reason' => $reason]);
			}
			
			if(CompanyDocument::where('company_details_id', $companyDetailsId)->whereStatus(2)->exists())
			{
				CompanyDetail::where('id', $companyDetailsId)->update(['is_update_kyc' => 0]); 
				// Fetch rejected documents for the director
				$rejectedDocuments = CompanyDocument::where('company_director_id', $companyDirectorId)
					->where('status', 2) // Rejected status
					->get(['document_type_id', 'reason']) 
					->map(function ($doc) {
						return [
							'documentType' => $doc->documentType->label, // Assuming documentType relationship exists
							'reason' => $doc->reason,
						];
					})
					->toArray();

				// Check if two or more documents are rejected
				if (count($rejectedDocuments) >= 2)
				{
					// Fetch director's name and email
					$director = CompanyDirector::find($companyDirectorId);
					$user = User::find($userId);
					$user->update(['is_upload_document' => 0]); 
					// Send email
					Mail::to($user->email)->send(new KycRejectionMail($director->name, $rejectedDocuments));
				}
			}
			
			// Check if all documents are approved
			$allApproved = CompanyDocument::where('company_director_id', $companyDirectorId)
            ->where('status', '<>', 1)
            ->doesntExist(); // If no documents exist with a status other than 1, all are approved
			
			if($allApproved)
			{  
				// Fetch director's name and email
				$director = CompanyDirector::find($companyDirectorId);
				$user = User::find($userId);
				Mail::to($user->email)->send(new DirectorApprovalMail($director));
			}
			
			// Check if all documents are approved
			$allDirectorApproved = CompanyDocument::where('company_details_id', $companyDetailsId)
            ->where('status', '<>', 1)
            ->doesntExist(); // If no documents exist with a status other than 1, all are approved
			
			$user = User::find($userId);
			$user->update(['is_kyc_verify' => 0]);
			if($allDirectorApproved)
			{ 
				$user->update(['is_kyc_verify' => 1]); 
				CompanyDetail::where('id', $companyDetailsId)->update(['is_update_kyc' => 1]); 
			}
			DB::commit(); 
			return $this->successResponse('The director KYC has been updated successfully.');
		} 
		catch (\Throwable $e)
		{
			DB::rollBack();
			return $this->errorResponse('Failed. ' . $e->getMessage());
		}
	}
	
	public function companiesloginHistory($companyId)
	{
		//$loginLogs = LoginLog::where('user_id', $companyid)->orderByDesc('id')->get(); 
		return view('admin.companies.login-history', compact('companyId')); 
	}
	
	public function companiesloginHistoryAjax(Request $request)
	{
		if ($request->ajax())
		{
			// Define the columns for ordering and searching
			$columns = ['id', 'type', 'ip_address', 'device', 'browser', 'source', 'created_at'];

			$search = $request->input('search.value'); // Global search value
			$start = $request->input('start'); // Offset for pagination
			$limit = $request->input('length'); // Limit for pagination
			$orderColumnIndex = $request->input('order.0.column', 0);
			$orderDirection = $request->input('order.0.dir', 'asc'); // Default order direction
			 
			$userId = $request->input('userId'); // Limit for pagination
			 
			// Base query with relationship for country
			$query = LoginLog:: where('user_id', $userId);
 
			// Apply search filter if present
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->where('type', 'LIKE', "%{$search}%")
						->orWhere('device', 'LIKE', "%{$search}%")
						->orWhere('browser', 'LIKE', "%{$search}%")
						->orWhere('source', 'LIKE', "%{$search}%")
						->orWhere('ip_address', 'LIKE', "%{$search}%") 
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
					'type' => $value->type,
					'ip_address' => $value->ip_address,
					'device' => $value->device, 
					'browser' => $value->browser, 
					'source' => $value->source, 
					'created_at' => $value->created_at->format('M d, Y H:i:s')
				]; 
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
}
