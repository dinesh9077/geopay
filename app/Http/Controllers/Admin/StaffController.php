<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Role;
	use App\Models\RoleGroup;
	use App\Models\Permission;
	use App\Models\RolePermission;
	use App\Models\Admin;
	use App\Http\Traits\WebResponseTrait; 
	use Validator, DB, Auth, ImageManager, Hash;
	use App\Services\MasterService;
	use Carbon\Carbon;
	class StaffController extends Controller
	{
		use WebResponseTrait;
		protected $masterService;
		public function __construct()
		{
			$this->masterService = new MasterService();
		}
		
		public function roles()
		{
			return view('admin.roles.index');
		}
		
		public function rolesAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'name', 'status', 'created_at', 'action'];
				
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = Role::query();
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('name', 'LIKE', "%{$search}%")
						->orWhere('created_at', 'LIKE', "%{$search}%");
					}); 
				}
				
				$totalData = $query->count();
				$totalFiltered = $totalData;
				
				// Get data with limit and offset for pagination
				$values = $query->offset($start)->limit($limit)
				->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
				->get();
				
				// Format response
				$data = [];
				$i = $start + 1;
				foreach ($values as $key => $value) {
					$statusClass = $value->status == 1 ? 'success' : 'danger';
					$statusText = $value->status == 1 ? 'Active' : 'In-Active';

					$appendData = [
						'id' => $i, // Use $key instead of manually tracking $i
						'name' => $value->name,
						'status' => "<span class=\"badge bg-{$statusClass}\">{$statusText}</span>",
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '',
					];

					if ($value->name !== 'admin') {
						$actions = [];
						if (config('permission.role.edit')) {
							$actions[] = "<a href=\"" . route('admin.roles.edit', ['id' => $value->id]) . "\" onclick=\"editRoles(this, event)\" class=\"btn btn-sm btn-primary\">Edit</a>";
							}
						if (config('permission.role.delete')) {
							$actions[] = "<a href=\"javascript:;\" data-url=\"" . route('admin.roles.delete', ['id' => $value->id]) . "\" data-message=\"Are you sure you want to delete this item?\" onclick=\"deleteConfirmModal(this, event)\" class=\"btn btn-sm btn-danger\">Delete</a>";
						}
						$appendData['action'] = implode(' ', $actions);
					}

					$data[] = $appendData;
					$i++;
				}
 
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			}
		}
		
		public function rolesCreate()
		{
			$permissions = Permission::where('status', 1)
			//->orderBy('heading_position', 'asc')
			->orderBy('position', 'asc')
			->get();
			//->groupBy('heading');
			
			$view = view('admin.roles.create', compact('permissions'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function rolesStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [
			'name' => [
			'required', 
			'string', 
			'unique:roles,name' // Explicitly specify the column to avoid confusion
			],
			'status' => [
			'required', 
			'integer', // Use "integer" for numbers (instead of "numeric")
			'in:0,1'   // Ensures the value is either 0 or 1
			]
			]);
			
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try
			{
				DB::beginTransaction();
				
				if(!$request->permission)
				{ 
					return $this->errorResponse('You have not select any checkbox.');
				}
				$admin = auth()->guard('admin')->user();
				
				$currentTime = now(); 
				$data = $request->except('_token','permission'); 
				$data['admin_id'] = $admin->id; 
				$data['created_at'] = $currentTime;
				$data['updated_at'] = $currentTime;
				
				$role = Role::create($data);
				
				$permissions = $request->permission;
				
				// Prepare data for bulk insert
				$data = [];
				foreach ($permissions as $key => $permission) {
					$data[] = [
					'role_id' => $role->id,
					'name' => $key,
					'value' => $permission,
					'created_at' => $currentTime,
					'updated_at' => $currentTime,
					];
				}
				
				// Chunk size for batch insertion (adjust as needed)
				$chunkSize = 200;
				$dataChunks = array_chunk($data, $chunkSize); 
				foreach ($dataChunks as $chunk) 
				{
					// Bulk insert using insert method
					RoleGroup::insert($chunk);
				}
				
				DB::commit();
				
				return $this->successResponse('The role has been created successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		public function rolesEdit($roleId)
		{
			$role = Role::find($roleId);
			if(!$role)
			{
				return $this->errorResponse('Role not found.');
			}
			
			$permissions = Permission::where('status', 1) 
			->orderBy('position', 'asc')
			->get(); 
			
			$roleper = RoleGroup::where('role_id', $roleId)
			->pluck('name')
			->toArray();
			
			$view = view('admin.roles.edit', compact('permissions', 'role', 'roleper'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function rolesUpdate(Request $request, $id)
		{
			// Validate input
			$validator = Validator::make($request->all(), [
			'name' => [
            'required', 
            'string', 
            'unique:roles,name,' . $id // Make sure the unique validation excludes the current role
			],
			'status' => [
            'required', 
            'integer', // Use "integer" for numbers (instead of "numeric")
            'in:0,1'   // Ensures the value is either 0 or 1
			]
			]);
			
			// Return validation errors if validation fails
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try {
				DB::beginTransaction();
				
				// Find the role by ID
				$role = Role::find($id);
				if (!$role) {
					return $this->errorResponse('Role not found.');
				}
				
				// Ensure at least one permission is provided
				if (!$request->permission) {
					return $this->errorResponse('You have not selected any permissions.');
				}
				
				// Get the current authenticated admin
				$admin = auth()->guard('admin')->user();
				
				// Prepare data for updating the role
				$currentTime = now();
				$data = $request->except('_token', 'permission', 'old_name'); 
				$data['admin_id'] = $admin->id;
				$data['updated_at'] = $currentTime;
				
				// Update the role
				$role->update($data);
				
				Admin::whereRole($request->old_name)->update(['role' => $request->name]);
				
				// Fetch users and permissions
				$users = Admin::whereRole($request->name)->get();
				$permissions = $request->permission;
				$permissionNames = array_keys($permissions);
				
				// Delete old role permissions not in current permissions
				RolePermission::whereIn('admin_id', $users->pluck('id'))
				->whereNotIn('name', $permissionNames)
				->delete();
				
				// Delete old role groups
				RoleGroup::whereRole_id($id)->delete();
				
				// Bulk insert role groups and role permissions
				$roleGroups = [];
				$rolePermissions = [];
				
				foreach ($permissions as $key => $permission) {
					$roleGroups[] = [
					'role_id' => $id,
					'name' => $key,
					'value' => $permission,
					'created_at' => $currentTime,
					'updated_at' => $currentTime
					];
					
					foreach ($users as $user) {
						// Check if role permission exists for user
						$exists = RolePermission::where('admin_id', $user->id)
						->where('name', $key)
						->exists();
						
						if (!$exists) {
							$rolePermissions[] = [
							'admin_id' => $user->id,
							'name' => $key,
							'value' => $permission,
							'created_at' =>$currentTime,
							'updated_at' => $currentTime
							];
						}
					}
				}
				
				// Chunk size for bulk insert operations (adjust as needed)
				$chunkSize = 100; // Example chunk size
				
				// Insert role groups in chunks
				foreach (array_chunk($roleGroups, $chunkSize) as $chunk) {
					RoleGroup::insert($chunk);
				}
				
				// Insert role permissions in chunks
				foreach (array_chunk($rolePermissions, $chunkSize) as $chunk) {
					RolePermission::insert($chunk);
				}
				
				// Commit the transaction
				DB::commit();
				
				// Return success response
				return $this->successResponse('The role has been updated successfully.');
				
				} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to update the role. ' . $e->getMessage());
			}
		}
		
		
		public function rolesDelete($id)
		{   
			try {
				DB::beginTransaction();
				
				$role = Role::find($id); 
				if (!$role) {
					return $this->errorResponse('The role not found.');
				}  
				$role->roleGroups()->delete();  
				$role->delete();
				
				DB::commit();
				
				return $this->successResponse('Role and its associated role groups deleted successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		 
		public function staff()
		{
			return view('admin.staff.index');
		}
		
		public function staffAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'profile', 'name', 'email', 'mobile', 'dob', 'role', 'status', 'created_at', 'action'];
				
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = Admin::query();
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('name', 'LIKE', "%{$search}%")
						->orwhere('email', 'LIKE', "%{$search}%")
						->orwhere('mobile', 'LIKE', "%{$search}%")
						->orwhere('dob', 'LIKE', "%{$search}%")
						->orwhere('role', 'LIKE', "%{$search}%")
						->orWhere('created_at', 'LIKE', "%{$search}%");
					}); 
				}
				
				$totalData = $query->count();
				$totalFiltered = $totalData;
				
				// Get data with limit and offset for pagination
				$values = $query->offset($start)->limit($limit)
				->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
				->get();
				
				// Format response
				$data = [];
				$i = $start + 1;
				foreach ($values as $key => $value) {
					// Determine profile image
					$profileImage = $value->profile 
						? url('storage/admin_profile', $value->profile) 
						: url('admin/default-profile.png');

					// Build the data row
					$appendData = [
						'id' => $i, // Use $key for index
						'profile' => '<img src="' . $profileImage . '" style="height:36px;width:36px">', 
						'name' => $value->name, 
						'email' => $value->email, 
						'mobile' => $value->mobile, 
						'dob' => $value->dob, 
						'role' => $value->role, 
						'status' => '<span class="badge bg-' . ($value->status == 1 ? 'success' : 'danger') . '">' . ($value->status == 1 ? 'Active' : 'In-Active') . '</span>',
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '', // Initialize action
					];

					// Check role and permissions
					if ($value->role !== 'admin') {
						$actions = [];

						// Edit permission
						if (config('permission.staff.edit')) {
							$actions[] = '<a href="' . route('admin.staff.edit', ['id' => $value->id]) . '" onclick="editStaff(this, event)" class="btn btn-sm btn-primary">Edit</a>';
						}

						// Delete permission
						if (config('permission.staff.delete')) {
							$actions[] = '<a href="javascript:;" data-url="' . route('admin.staff.delete', ['id' => $value->id]) . '" data-message="Are you sure you want to delete this item?" onclick="deleteConfirmModal(this, event)" class="btn btn-sm btn-danger">Delete</a>';
						}

						// Permission management permission
						if (config('permission.staff.add')) {
							$actions[] = '<a href="' . route('admin.staff.permission', ['id' => $value->id]) . '" onclick="editPermission(this, event)" class="btn btn-sm btn-info">Permission</a>';
						}

						// Combine actions into the action column
						$appendData['action'] = implode(' ', $actions);
					}

					// Append data row to output
					$data[] = $appendData;
					$i++;
				}

				
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			}
		}
		
		public function staffCreate()
		{ 
			$roles = $this->masterService->getRoles(1);
			$view = view('admin.staff.create', compact('roles'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function rolesGroups($id)
		{
			// Fetch permissions, ordered by heading_position and position, and group them by heading
			$permissions = Permission::where('status', 1) 
				->orderBy('position', 'asc')
				->get();

			// Fetch role permissions and map to names
			$roleper = RoleGroup::where('role_id', $id)
				->pluck('name')
				->toArray();

			// Render the view and return the JSON response
			$view = view('admin.roles.role-group', compact('permissions', 'roleper'))->render();

			return $this->successResponse('success', ['view' => $view])	;
		}
		
		public function staffStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [ 
				'name' => 'required|string', 
				'email' => 'required|email|unique:admins,email',
				'password' => 'required|string',
				'mobile' => 'required|string|unique:admins,mobile', 
				'role_id' => 'required|integer',  
				'role' => 'required|string',  
				'status' => 'required|in:1,0',  
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try
			{
				DB::beginTransaction();
				
				 
				$admin = auth()->guard('admin')->user();
				
				$currentTimestamp = Carbon::now();
			
				$data = $request->except('_token', 'permission', 'password');
				$data['password'] = Hash::make($request->password);
				$data['xps'] = base64_encode($request->password);
				$data['email'] = strtolower($request->email);
				$data['assign_by'] = $admin->id; 
				$data['dob'] = $request->filled('dob') ? $request->input('dob') : null;
				$data['created_at'] = $currentTimestamp;
				$data['updated_at'] = $currentTimestamp;  
				
				$staff = Admin::create($data);
				
				if ($request->role != "admin")
				{
					$permissions = $request->permission;
					
					// Prepare data for bulk insert
					$data = [];
					foreach ($permissions as $key => $permission) {
						$data[] = [
						'admin_id' => $staff->id,
						'name' => $key,
						'value' => $permission,
						'created_at' => $currentTimestamp,
						'updated_at' => $currentTimestamp,
						];
					}
					
					// Use chunking for bulk insert to improve performance
					$chunks = array_chunk($data, 200); // Chunk size can be adjusted based on your needs
					
					foreach ($chunks as $chunk) 
					{
						RolePermission::insert($chunk);
					} 
				}
				
				DB::commit();
				
				return $this->successResponse('The staff has been created successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		public function staffEdit($staffId)
		{
			$staff = Admin::find($staffId);
			if(!$staff)
			{
				return $this->errorResponse('Staff not found.');
			} 
			
			$view = view('admin.staff.edit', compact('staff'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function staffUpdate(Request $request, $staffId)
		{ 
			$validator = Validator::make($request->all(), [ 
				'name' => 'required|string', 
				'email' => 'required|email|unique:admins,email,' . $staffId, // Ignore the current admin's email
				'password' => 'nullable|string', // Nullable since you may not want to update the password
				'mobile' => 'required|string|unique:admins,mobile,' . $staffId, // Ignore the current admin's mobile 
				'status' => 'required|in:1,0',  
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}

			try {
				DB::beginTransaction();

				// Fetch the staff member to update
				$staff = Admin::findOrFail($staffId);

				$admin = auth()->guard('admin')->user();
				$currentTimestamp = Carbon::now();

				// Update only the fields provided in the request
				$data = $request->except('_token', 'password');
				if ($request->filled('password')) {
					$data['password'] = Hash::make($request->password); // Hash the new password if provided
					$data['xps'] = base64_encode($request->password);  // Base64 encode the password
				}
				$data['email'] = strtolower($request->email); // Normalize email to lowercase
				$data['dob'] = $request->filled('dob') ? $request->input('dob') : null;
				$data['assign_by'] = $admin->id; 
				$data['updated_at'] = $currentTimestamp; // Update the timestamp

				// Update the staff details
				$staff->update($data);

				DB::commit();

				return $this->successResponse('The staff details have been updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to update staff details. ' . $e->getMessage());
			}
		}
		
		public function staffPermission($staffId)
		{
			$staff = Admin::find($staffId);
			if(!$staff)
			{
				return $this->errorResponse('Staff not found.');
			} 
			
			$roles = $this->masterService->getRoles(1);
			
			// Fetch permissions in a single query and organize them by heading
			$permissions = Permission::whereStatus(1) 
				->orderBy('position')
				->get();

			// Retrieve role permissions for the user
			$roleper = RolePermission::whereAdmin_id($staffId)
				->pluck('name')
				->toArray();
				
			$view = view('admin.staff.permission', compact('staff', 'roleper', 'permissions', 'roles'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function staffPermissionUpdate(Request $request, $staffId)
		{  
			$validator = Validator::make($request->all(), [  
				'role_id' => 'required|numeric',  
				'role' => 'required|string',  
			]);
			 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try
			{  
				DB::beginTransaction(); 
				
				$currentTime = now();
				if ($request->role != "admin")
				{
					$permissions = $request->permission;
					
					// Delete permissions not included in the current request 
					RolePermission::where('admin_id', $staffId)->delete();
					
					// Prepare data for bulk insert
					$data = [];
					foreach ($permissions as $key => $permission) {
						$data[] = [
							'admin_id' => $staffId,
							'name' => $key,
							'value' => $permission,
							'created_at' => $currentTime,
							'updated_at' => $currentTime,
						];
					}
					
					// Use chunking for bulk insert to improve performance
					$chunks = array_chunk($data, 200); // Chunk size can be adjusted based on your needs
					
					foreach ($chunks as $chunk) 
					{
						RolePermission::insert($chunk);
					} 
				}
				else if ($request->role != "admin")
				{
					MstRolePermission::where('admin_id', $staffId)->delete();
				}
				
				Admin::whereId($staffId)->update($request->only('role_id', 'role'));
			 
				DB::commit();
				
				return $this->successResponse('The staff permission been updated successfully.'); 
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update staff details. ' . $e->getMessage()); 
			}  
		}
		
		public function staffDelete($staffId)
		{
			try
			{
				DB::beginTransaction();
  
				RolePermission::whereAdmin_id($staffId)->delete(); 
				Admin::find($staffId)->delete();
 
				DB::commit();
				return $this->successResponse('The staff has been successfully deleted.');  
			}
			catch (\Throwable $e)
			{
				DB::rollBack(); 
				return $this->errorResponse('Failed to update staff details. ' . $e->getMessage()); 
			}
		}
	}
