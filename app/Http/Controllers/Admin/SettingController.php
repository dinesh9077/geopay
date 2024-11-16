<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Setting;
	use App\Models\Banner;
	use App\Http\Traits\WebResponseTrait; 
	use Validator, DB, Auth, ImageManager;
	
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
			
			
			$data = $request->except('_token', 'fevicon_icon', 'site_logo');
			
			try {
				DB::beginTransaction();
				
				// Bulk update or create for general settings
				foreach ($data as $key => $value) {
					Setting::updateOrCreate(
					['name' => $key],
					['value' => $value, 'updated_at' => now()]
					);
				}
				
				// Handle file uploads
				$this->handleFileUpload($request, 'fevicon_icon', 'setting');
				$this->handleFileUpload($request, 'login_logo', 'setting');
				$this->handleFileUpload($request, 'site_logo', 'setting');
				
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
				$fileName = ImageManager::imgUpdate($directory, config('setting.'. $fieldName), $extension, $file);
				
				Setting::updateOrCreate(
				['name' => $fieldName],
				['value' => $fileName, 'updated_at' => now()]
				);
			}
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
				foreach ($values as $value)
				{
					$data[] = [
						'id' => $user->id,
						'name' => $user->name,
						'email' => $user->email,
						'created_at' => $user->created_at->format('Y-m-d H:i:s'),
						'action' => '<a href="/users/' . $user->id . '" class="btn btn-sm btn-primary">View</a>',
					];
					
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
		
	}
