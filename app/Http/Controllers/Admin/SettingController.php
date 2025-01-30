<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Setting;
	use App\Models\Banner;
	use App\Models\Faq;
	use App\Models\UserLimit;
	use App\Models\LightnetCountry;
	use App\Models\OnafricChannel;
	use App\Models\LightnetCatalogue;
	use App\Http\Traits\WebResponseTrait; 
	use App\Services\LiquidNetService; 
	use App\Services\OnafricService; 
	use App\Services\MasterService; 
	use Validator, DB, Auth, ImageManager, Hash;
	
	class SettingController extends Controller
	{
		use WebResponseTrait;
		protected $liquidNetService;
		protected $onafricService;
		
		public function __construct()
		{
			$this->onafricService = new OnafricService(); 
			$this->liquidNetService	= new LiquidNetService();
			$this->masterService	= new MasterService();
		}
		
		public function generalSetting()
		{
			$userLimits = UserLimit::all();
			return view('admin.setting.index', compact('userLimits'));
		}
		
		public function generalSettingUpdate(Request $request)
		{   
			// Define validation rules dynamically for flexibility
			$validationRules = [
				'site_name'        => 'nullable|string|max:255',
				'default_currency' => 'nullable|string|max:3', 
				'site_logo'        => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
				'fevicon_icon'     => 'nullable|file|mimes:jpg,jpeg,png,ico|max:1024',
				'login_logo'       => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
			];
			
			// Validate the incoming request
			$validator = Validator::make($request->all(), $validationRules);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try
			{
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
			}
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		public function thirdPartyKeyLightnetUpdate(Request $request)
		{ 
			$validationRules = [
				'lightnet_url'       => 'required|string|max:255', 
				'lightnet_apikey'    => 'required|string|max:255', 
				'lightnet_secretkey' => 'required|string|max:255', 
			];
			
			// Validate the incoming request
			$validator = Validator::make($request->all(), $validationRules);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try
			{
				DB::beginTransaction();
				
				$data = $request->except('_token');
				
				$response = $this->liquidNetService->getEcho($data);
				
				if (!$response['success']) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
					throw new \Exception($errorMsg);
				}
				
				if(($response['response']['code'] ?? -1) != 0)
				{
					$errorMsg = $response['response']['message'] ?? 'An error occurred.';
					throw new \Exception($errorMsg);
				} 
				// Bulk update or create for general settings
				foreach ($data as $key => $value) {
					Setting::updateOrCreate(
					['name' => $key],
					['value' => $value, 'updated_at' => now()]
					);
				}
				
				Setting::updateOrCreate(
					['name' => 'lightnet'],
					['value' => 1, 'updated_at' => now()]
				);
				
				DB::commit(); 
				return $this->successResponse('Settings have been successfully updated.');
			}
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse('Failed to connect api. ' . $e->getMessage());
			}
		}
		
		public function thirdPartyKeyLightnetView()
		{	 
			if (!config('setting.lightnet')) {
				return $this->successResponse('success', ['view' => '']);
			}
			
			// Check for existing LightnetCountry records
			$lightnetCountries = LightnetCountry::where('service_name', 'lightnet')->get();
		
			if ($lightnetCountries->isEmpty())  
			{
				$timestamp = time();
				$body =  [
					'agentSessionId' => (string) $timestamp,
					'catalogueType' => 'CTY',
				];
				
				$response = $this->liquidNetService->serviceApi('post', '/GetCatalogue', $timestamp, $body);
				
				if ($response['success'] && $response['response']['code'] == 0) {
					// Process the API result
					$countries = $response['response']['result'] ?? [];

					if (!empty($countries)) {
						// Add 'service_name' and 'status' to each element dynamically
						$countries = array_map(function($country) {
							$country['service_name'] = 'lightnet';
							$country['status'] = 1;
							$country['created_at'] = now(); // Adding timestamp
							$country['updated_at'] = now(); // Adding timestamp
							return $country;
						}, $countries);

						// Insert the countries into the database
						LightnetCountry::insert($countries);

						// Re-fetch the data to include the newly inserted records
						$lightnetCountries = LightnetCountry::where('service_name', 'lightnet')->get();
					}
				}
			}    
			
			$view = view('admin.setting.lightnet-view', compact('lightnetCountries'))->render();
			return $this->successResponse('success', ['view' => $view]);
		}
		 
		public function thirdPartyKeyCountryUpdate(Request $request)
		{
			try
			{
				DB::beginTransaction();
				
				$lightnetCountry = LightnetCountry::findOrFail($request->id);

				// Update the status and label
				$lightnetCountry->update([
					'status' => $request->status,
					'label' => $request->label,
					'markdown_type' => $request->markdown_type ?? 'flat',
					'markdown_charge' => $request->markdown_charge ?? 0,
					'updated_at' => now(),
				]);

				DB::commit(); 
				return $this->successResponse('Country information updated successfully.');
			}
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse('An error occurred while updating the country information.');
			} 
		}
		
		public function thirdPartyKeySyncCatalogue()
		{	
			try
			{
				DB::beginTransaction();
				
				// Define catalogues with keys and descriptions
				$catalogues = [
					'OCC' => 'Get Occupation',
					'CTY' => 'Get Country',  
					'SOF' => 'Get Source of Fund',
					'REL' => 'Get Relationship List',
					'POR' => 'Get Purpose of Remittance',
					'DOC' => 'Get Customer Document ID Type',
				];
				
				foreach($catalogues as $key => $catalogue)
				{ 
					$timestamp = time();
					$body =  [
						'agentSessionId' => (string) $timestamp,
						'catalogueType' => (string) $key,
					];
					
					$response = $this->liquidNetService->serviceApi('post', '/GetCatalogue', $timestamp, $body);
					
					if (!$response['success']) {
						$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
						throw new \Exception($errorMsg);
					}
					
					if(($response['response']['code'] ?? -1) != 0)
					{
						$errorMsg = $response['response']['message'] ?? 'An error occurred.';
						throw new \Exception($errorMsg);
					} 
					
					$data = $response['response']['result'];
					$filteredData = array_filter($data, function ($item) {
						return $item['value'] !== 'Other';
					});
					// Update or create the catalogue record in the database
					LightnetCatalogue::updateOrCreate(
						['service_name' => 'lightnet', 'catalogue_type' => $key],
						[
							'category_name' => 'transfer to bank',
							'service_name' => 'lightnet',
							'catalogue_type' => $key,
							'catalogue_description' => $catalogue,
							'data' => $filteredData,
							'updated_at' => now(),
						]
					);
				}
				
				DB::commit(); 
				return $this->successResponse('Catalogues synced successfully.');
			}
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse('Failed to sync catalogues. ' . $e->getMessage());
			} 
		}
		
		public function thirdPartyKeySyncCountries()
		{	
			try
			{
				DB::beginTransaction();
				
				$timestamp = time();
				$body =  [
					'agentSessionId' => (string) $timestamp,
					'catalogueType' => 'CTY',
				];
				
				$response = $this->liquidNetService->serviceApi('post', '/GetCatalogue', $timestamp, $body);
				if (!$response['success']) {
					$errorMsg = $response['response']['errors'][0]['message'] ?? 'An error occurred.';
					throw new \Exception($errorMsg);
				}
				
				if(($response['response']['code'] ?? -1) != 0)
				{
					$errorMsg = $response['response']['message'] ?? 'An error occurred.';
					throw new \Exception($errorMsg);
				} 
				 
				// Process the API result
				$countries = $response['response']['result'] ?? [];

				if (!empty($countries)) {
					// Add 'service_name' and 'status' to each element dynamically
					$countries = array_map(function($country) {
						$country['service_name'] = 'lightnet';
						$country['status'] = 1;
						$country['created_at'] = now(); // Adding timestamp
						$country['updated_at'] = now(); // Adding timestamp
						return $country;
					}, $countries);
					
					foreach($countries as $country)
					{	 
						$this->getLightnetState($country['data']);
						LightnetCountry::updateOrCreate(
							['service_name' => 'lightnet', 'value' => $country['value']],
							['data' => $country['data'], 'updated_at' => now()]
						);
					}
				}
				
				DB::commit(); 
				return $this->successResponse('Country synced successfully.');
			}
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse('Failed to sync Country. ' . $e->getMessage());
			} 
		}
		
		public function getLightnetState($payoutCountry)
		{
			$timestamp = time();
			$body =  [
				'agentSessionId' => (string) $timestamp,
				'catalogueType' => 'STA',
				'additionalField1' => (string) $payoutCountry,
			];
			
			$response = $this->liquidNetService->serviceApi('post', '/GetCatalogue', $timestamp, $body);
			if (!$response['success']) {
				return;
			}
			
			if(($response['response']['code'] ?? -1) != 0)
			{
				return;
			} 
			
			$result = $response['response']['result'] ?? [];
			
			LightnetCatalogue::updateOrCreate(
				['service_name' => 'lightnet', 'catalogue_type' => 'STA', 'additionalField1'=> $payoutCountry],
				['category_name' => 'transfer to bank', 'catalogue_description' => 'Get States', 'data'=> $result, 'additionalField1'=> $payoutCountry, 'updated_at' => now()]
			);
			return;
		}
		
		//Onafric Mobile
		public function thirdPartyKeyOnafricMobileView()
		{	  
			$africanCountries = $this->onafricService->availableCountry(); 
			$onafricCuntries = $this->masterService->getCountries()->whereIn('nicename', $africanCountries)->values();  
			$view = view('admin.setting.onafric-mobile-view', compact('onafricCuntries'))->render();
			return $this->successResponse('success', ['view' => $view]);
		}
		
		public function thirdPartyKeyOnafricMobileUpdate(Request $request)
		{
			DB::beginTransaction();
			
			try {
				$insertData = [];
				$updateData = [];
				$batch = 100; // Size of batch to insert at once

				// Loop through the channels input data
				foreach ($request->input('channel') as $countryId => $channels) 
				{
					foreach ($channels as $index => $channel) {
						// Skip if the channel is empty
						if (empty($channel)) {
							continue;
						}

						$fees = $request->input('fees')[$countryId][$index] ?? 0;
						$commissionType = $request->input('commission_type')[$countryId][$index] ?? 'flat';
						$commissionCharge = $request->input('commission_charge')[$countryId][$index] ?? 0;
						$channelId = $request->input('channel_id')[$countryId][$index] ?? null;

						// Prepare the channel data
						$channelData = [
							'country_id' => $countryId,
							'channel' => $channel,
							'fees' => $fees,
							'commission_type' => $commissionType,
							'commission_charge' => $commissionCharge,
							'status' => 1,  // Set as active
							'updated_at' => now(),
						];

						// If the channel_id exists, add it to the update data
						if ($channelId) {
							$updateData[] = array_merge($channelData, ['id' => $channelId]);
							
							// If insertData exceeds batch size, perform a batch insert
							if (count($updateData) >= $batch) {
								OnafricChannel::upsert($updateData, ['id'], ['country_id', 'channel', 'fees', 'commission_type', 'commission_charge', 'status', 'updated_at']);
								$updateData = []; // Clear insert data after batch insert
							} 
							
						} else {
							// Otherwise, add it to the insert data
							$insertData[] = array_merge($channelData, [
								'created_at' => now(),
							]);

							// If insertData exceeds batch size, perform a batch insert
							if (count($insertData) >= $batch) {
								OnafricChannel::insert($insertData);  // Insert batch of channels
								$insertData = []; // Clear insert data after batch insert
							}
						}
					}
				}

				// Insert remaining data if any
				if (!empty($insertData)) {
					OnafricChannel::insert($insertData);
				}

				// Perform batch update if there are any update records
				if (!empty($updateData)) {
					OnafricChannel::upsert($updateData, ['id'], ['country_id', 'channel', 'fees', 'commission_type', 'commission_charge', 'status', 'updated_at']);
				}

				// Commit the transaction if everything went well
				DB::commit();
				
				// Return success response
				return response()->json(['status' => 'success', 'message' => 'Channels updated successfully']);
				
			} catch (\Exception $e) {
				// Rollback the transaction in case of any errors
				DB::rollBack();

				// Log the error message
				Log::error('Error updating channels: ' . $e->getMessage());

				// Return error response
				return response()->json(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
			}
		}

		
		public function UserLimitUpdate(Request $request)
		{    
			$validationRules = [
			'id' => 'required|numeric',
			'name' => 'required|string|max:255',
			'daily_add_limit' => 'required|string',
			'daily_pay_limit' => 'required|string'
			];
			
			// Validate the incoming request
			$validator = Validator::make($request->all(), $validationRules);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try {
				DB::beginTransaction();
				
				$data = $request->only('name', 'daily_add_limit', 'daily_pay_limit');
				
				$userLimit = UserLimit::find($request->id);
				$userLimit->update($data);
				
				DB::commit();
				
				return $this->successResponse('user limit have been successfully updated.', ['data' => $request->all()]);
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
