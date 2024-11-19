<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use DB, Auth, Helper, Hash, Validator;
use App\Http\Traits\WebResponseTrait; 

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
		if ($request->ajax()) {
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
					'country' => $value->country->name ?? 'N/A',
					'is_kyc_verify' => $value->is_kyc_verify ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>',
					'is_email_verify' => $value->is_email_verify ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>',
					'is_mobile_verify' => $value->is_mobile_verify ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Not Verified</span>',
				//	'status' => "<span class=\"badge bg-{$statusClass}\">{$statusText}</span>",
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
				if (config('permission.active_company.edit')) {
					$actions[] = '<a href="' . route('admin.companies.edit', ['id' => $value->id]) . '" onclick="editCompany(this, event)" class="btn btn-sm btn-primary">Edit</a>';
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
			
			DB::commit(); 
			return $this->successResponse('Status updated successfully.'); 
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
			return $this->errorResponse('Company not found.');
		}
		$view = view('admin.companies.edit', compact('company'))->render();
		return $this->successResponse('success', ['view' => $view]);
	}
	
	public function companiesUpdate(Request $request, $companyid)
	{
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255', 
			'password' => 'nullable|string|max:255', 
			'company_name' => 'required|string', 
			'status' => 'required|in:1,0', 
		]);
		
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		try {
			DB::beginTransaction();
			
			$data = $request->except('_token', 'password');
			if($request->filled('password'))
			{
				$data['password'] = Hash::make($request->password);
				$data['xps'] = base64_encode($request->password);
			}
			$user = User::find($companyid);
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
}
