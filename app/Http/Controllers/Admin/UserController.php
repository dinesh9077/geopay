<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use DB, Auth, Helper, Hash, Validator;
use App\Http\Traits\WebResponseTrait;  
use App\Services\MasterService;

class UserController extends Controller
{
	use WebResponseTrait;
	protected $masterService;
	
	public function __construct()
	{
		$this->masterService = new MasterService();
	}
	
    public function userActive()
	{
		return view('admin.users.active');
	}
	 
    public function userPending()
	{
		return view('admin.users.pending');
	} 
    public function userBlock()
	{
		return view('admin.users.block');
	}
	
	public function userAjax(Request $request)
	{
		if ($request->ajax()) {
			// Define the columns for ordering and searching
			$columns = ['id', 'name', 'email', 'mobile', 'country.name',  'balance', 'is_kyc_verify', 'is_email_verify', 'is_mobile_verify', 'status', 'created_at'];

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
				->where('is_company', 0)
				->where('is_kyc_verify', $is_kyc_verify);
			if($page_status == "pending")
			{
				$query->whereIn('status', [1, 0]);
			}
			else
			{
				$query->where('status', $status);
			}
 
			// Apply search filter if present
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->where('first_name', 'LIKE', "%{$search}%")
						->orWhere('last_name', 'LIKE', "%{$search}%")
						->orWhere('email', 'LIKE', "%{$search}%")
						->orWhere('mobile_number', 'LIKE', "%{$search}%")
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
					'balance' => Helper::decimalsprint(($value->balance ?? 0), 2) ,
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
				if (config('permission.user_edit.edit')) {
					$actions[] = '<a href="' . route('admin.user.edit', ['id' => $value->id]) . '" class="btn btn-sm btn-primary">Edit Details</a>';
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
	 
	public function userLoginHistory($companyId)
	{ 
		return view('admin.companies.login-history', compact('companyId')); 
	}
	
	public function userEdit($companyid)
	{ 
		$company = User::find($companyid); 
		$txnStatuses = Transaction::select('txn_status')
		->groupBy('txn_status')
		->pluck('txn_status');
		
		$userLimits = $this->masterService->getUserLimits(1);
		return view('admin.users.edit', compact('company', 'txnStatuses', 'userLimits')); 
	}
	
	public function userUpdate(Request $request, $companyid)
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
			
			return $this->successResponse('User have been successfully updated.');
		} 
		catch (\Throwable $e)
		{
			DB::rollBack();
			return $this->errorResponse('Failed. ' . $e->getMessage());
		}
	}
	
	public function userViewKyc($userId)
	{
		$user = User::with('userKyc')->find($userId);
		$kyc = $user->userKyc;
		if(!$user || !$kyc)
		{ 
			return $this->errorResponse('User or kyc details not found.');
		}
		
		$view = view('admin.users.view', compact('user', 'kyc'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
}
