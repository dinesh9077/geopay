<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Setting;
	use App\Models\Banner;
	use App\Models\Faq;
	use App\Http\Traits\WebResponseTrait; 
	use Validator, DB, Auth, ImageManager, Hash;
	
	class SettingController extends Controller
	{
		use WebResponseTrait;
		
		public function generalSetting()
		{
			return view('admin.setting.index');
		}
		
		public function generalSettingUpdate(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'site_name' => 'required|string|max:255',
				'default_currency' => 'required|string|max:10',
				'site_logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048', 
				'fevicon_icon' => 'nullable|file|mimes:jpg,jpeg,png,ico|max:1024', 
				'login_logo' => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048', 
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try {
				DB::beginTransaction();
				
				$data = $request->except('_token', 'fevicon_icon', 'login_logo', 'site_logo');
				
				// Bulk update or create for general settings
				foreach ($data as $key => $value) {
					Setting::updateOrCreate(
					['name' => $key],
					['value' => $value, 'updated_at' => now()]
					);
				}
				
				$images = $request->only('fevicon_icon', 'login_logo', 'site_logo');
				if($images)
				{
					foreach($images as $key => $image)
					{
						$fileName = $this->handleFileUpload($request, $key, 'setting'); 
						Setting::updateOrCreate(
							['name' => $key],
							['value' => $fileName, 'updated_at' => now()]
						);
					}
				}
				
				
				DB::commit();
				
				return $this->successResponse('Settings have been successfully updated.');
				} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		private function handleFileUpload(Request $request, string $fieldName, string $directory)
		{
			if ($request->hasFile($fieldName)) {
				$file = $request->file($fieldName);
				$extension = $file->getClientOriginalExtension();
				$fileName = ImageManager::imgUpdate($directory, (config('setting.'. $fieldName) ?? ''), $extension, $file);
				return $fileName; 
			}
			return null; 
		}
		
		public function banner()
		{
			return view('admin.banner.index');
		}
		
		public function bannerAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'title', 'image', 'status', 'created_at', 'action'];
				  
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = Banner::query();
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('title', 'LIKE', "%{$search}%")
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

					// Initialize the row data
					$data[] = [
						'id' => $i, // Use $key for indexing
						'title' => $value->title,
						'image' => '<img src="' . url('storage/banner', $value->image) . '" style="height:70px;width:70px">',
						'status' => "<span class=\"badge bg-{$statusClass}\">{$statusText}</span>",
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '', // Initialize action
					];

					// Manage actions with permission checks
					$actions = [];
					if (config('permission.banner.edit')) {
						$actions[] = '<a href="' . route('admin.banner.edit', ['id' => $value->id]) . '" onclick="editBanner(this, event)" class="btn btn-sm btn-primary">Edit</a>';
					}
					if (config('permission.banner.delete')) {
						$actions[] = '<a href="javascript:;" data-url="' . route('admin.banner.delete', ['id' => $value->id]) . '" data-message="Are you sure you want to delete this item?" onclick="deleteConfirmModal(this, event)" class="btn btn-sm btn-danger">Delete</a>';
					}

					// Assign actions to the row if available
					$data[$key]['action'] = implode(' ', $actions);
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
		
		public function bannerCreate()
		{
			$view = view('admin.banner.create')->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function bannerStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'title' => 'required|string|max:255', 
				'image' => 'required|file|mimes:jpg,jpeg,png|max:2048', 
				'status' => 'required|in:1,0', 
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try {
				DB::beginTransaction();
				
				$data = $request->only('title', 'status'); 
				$fileName = $this->handleFileUpload($request, 'image', 'banner');
				if($fileName)
				{
					$data['image'] = $fileName;
				}
				
				Banner::create($data);
				 
				DB::commit();
				
				return $this->successResponse('The banner has been created successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		public function bannerEdit($bannerId)
		{
			$banner = Banner::find($bannerId);
			$view = view('admin.banner.edit', compact('banner'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function bannerUpdate(Request $request, $id)
		{  
			$validator = Validator::make($request->all(), [
				'title' => 'required|string|max:255', 
				'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', 
				'status' => 'required|in:1,0', 
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try {
				DB::beginTransaction();
				
				$data = $request->only('title', 'status'); 
				$fileName = $this->handleFileUpload($request, 'image', 'banner');
				if($fileName)
				{
					$data['image'] = $fileName;
				}
				
				$banner = Banner::find($id);
				$banner->update($data);
				 
				DB::commit();
				
				return $this->successResponse('The banner have been update successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		 
		public function bannerDelete($id)
		{   
			try {
				DB::beginTransaction();
				 
				$banner = Banner::find($id);
				if(!$banner)
				{
					return $this->errorResponse('The banner not found.');
				}
				
				if($banner->image)
				{ 
					ImageManager::imgDelete('banner/'.$banner->image);
				}
				$banner->delete(); 
				DB::commit();
				
				return $this->successResponse('The banner has been delete successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		//Faqs
		public function faqs()
		{
			return view('admin.faqs.index');
		}
		
		public function faqsAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'title', 'description', 'status', 'created_at', 'action'];
				  
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = Faq::query();
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('title', 'LIKE', "%{$search}%")
						->where('description', 'LIKE', "%{$search}%")
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

					// Build the row data
					$data[] = [
						'id' => $i, // Use $key for dynamic indexing
						'title' => $value->title,
						'description' => $value->description,
						'status' => "<span class=\"badge bg-{$statusClass}\">{$statusText}</span>",
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '', // Initialize action
					];

					// Initialize actions with permission checks
					$actions = [];
					if (config('permission.faqs.edit')) {
						$actions[] = '<a href="' . route('admin.faqs.edit', ['id' => $value->id]) . '" onclick="editFaq(this, event)" class="btn btn-sm btn-primary">Edit</a>';
					}
					if (config('permission.faqs.delete')) {
						$actions[] = '<a href="javascript:;" data-url="' . route('admin.faqs.delete', ['id' => $value->id]) . '" data-message="Are you sure you want to delete this item?" onclick="deleteConfirmModal(this, event)" class="btn btn-sm btn-danger">Delete</a>';
					}

					// Assign actions to the row if available
					$data[$key]['action'] = implode(' ', $actions);
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
		
		public function faqsCreate()
		{
			$view = view('admin.faqs.create')->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function faqsStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'title' => 'required|string|max:255', 
				'description' => 'required|string', 
				'status' => 'required|in:1,0', 
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try {
				DB::beginTransaction();
				
				$data = $request->only('title', 'description', 'status');  
				Faq::create($data); 
				
				DB::commit(); 
				return $this->successResponse('The faqs has been created successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		public function faqsEdit($bannerId)
		{
			$faq = Faq::find($bannerId);
			$view = view('admin.faqs.edit', compact('faq'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function faqsUpdate(Request $request, $id)
		{  
			$validator = Validator::make($request->all(), [
				'title' => 'required|string|max:255', 
				'description' => 'required|string', 
				'status' => 'required|in:1,0', 
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try {
				DB::beginTransaction();
				
				$data = $request->only('title', 'description', 'status');   
				$faq = Faq::find($id);
				$faq->update($data);
				 
				DB::commit();
				
				return $this->successResponse('The faq have been update successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		 
		public function faqsDelete($id)
		{   
			try {
				
				DB::beginTransaction(); 
				
				$faq = Faq::find($id);
				if(!$faq)
				{
					return $this->errorResponse('The faq not found.');
				} 
				$faq->delete();
				
				DB::commit(); 
				return $this->successResponse('The faq has been delete successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		// Third Party keys
		public function ThirdPartyKey()
		{
			return view('admin.setting.third-party');
		}
		
		public function ThirdPartyKeyUpdate(Request $request)
		{  
			// Define your validation rules dynamically based on the request input keys
			$rules = collect($request->all())->mapWithKeys(function ($value, $key) {
				return [$key => 'nullable|string']; // Adjust rules dynamically as needed
			})->toArray();

			// Validate the request
			$validator = Validator::make($request->all(), $rules);

			// Check if validation fails
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try 
			{
				DB::beginTransaction();
				
				$data = $request->except('_token');
				
				// Bulk update or create for general settings
				foreach ($data as $key => $value) {
					Setting::updateOrCreate(
						['name' => $key],
						['value' => $value, 'updated_at' => now()]
					);
				}
				 
				DB::commit();
				
				return $this->successResponse('The data have been added or update successfully.');
			} 
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		// Profile
		public function profile()
		{ 
			$admin = auth()->guard('admin')->user();
			return view('admin.setting.profile', compact('admin'));
		}
		
		public function profileUpdate(Request $request)
		{
			$admin = auth()->guard('admin')->user();
			
			$validator = Validator::make($request->all(), [
				'name' => 'required|string|max:255', 
				'email' => 'required|email|unique:admins,email,' . $admin->id, 
				'mobile' => 'required|string|unique:admins,mobile,' . $admin->id
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			 
			try {
				DB::beginTransaction();

				// Collect only the necessary fields
				$data = $request->except(['_token', 'profile', 'password']);
				$data['dob'] = $data['dob'] ? $data['dob'] : null;
				if($request->filled('password'))
				{
					$data['password'] = Hash::make($request->password);
					$data['xps'] = base64_encode($request->password);
				}
				
				$fileName = $this->handleFileUpload($request, 'profile', 'admin_profile');
				if($fileName)
				{
					$data['profile'] = $fileName;
				} 
				// Update admin profile
				$admin->update($data);
				
				DB::commit();

				return $this->successResponse('The profile has been updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to update profile. ' . $e->getMessage());
			}
		}

	}
