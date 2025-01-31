<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request; 
	use App\Models\User; 
	use App\Models\Permission; 
	use App\Http\Traits\WebResponseTrait;  
	use Validator, DB, Auth, ImageManager, Hash;
	
	class PermissionController extends Controller
	{
		use WebResponseTrait;  
		public function index()
		{
			$permissions = Permission::orderBy('position')->get();
			return view('admin.permission.index', compact('permissions'));
		}  
		
		public function store(Request $request)
		{
			try {
				DB::beginTransaction(); 
				// Prepare data for insertion
				$data = $request->except('_token');
				$data['position'] = Permission::max('position') + 1; // Increment position
				$data['heading_position'] = 1;
				$data['created_at'] = now();
				$data['updated_at'] = now();
				
				Permission::insert($data); 
				
				DB::commit(); 
				return redirect()->back()->with('success', 'Permission added successfully!');
			} 
			catch (\Throwable $e) 
			{ 
				DB::rollBack(); 
				return redirect()->back()->with('error', 'Something went wrong. Please try again.');
			}
		}  
		
		public function update(Request $request, $permissionId)
		{
			try {
				DB::beginTransaction(); 
				// Prepare data for insertion
				$data = $request->except('_token');   
				$data['updated_at'] = now();
				
				Permission::where('id', $permissionId)->update($data); 
				
				DB::commit(); 
				return redirect()->back()->with('success', 'Permission updated successfully!');
			} 
			catch (\Throwable $e) 
			{ 
				DB::rollBack(); 
				return redirect()->back()->with('error', 'Something went wrong. Please try again.');
			}
		}    
		
		public function delete($permissionId)
		{
			try {
				DB::beginTransaction(); 
				 
				Permission::where('id', $permissionId)->delete(); 
				
				DB::commit(); 
				return redirect()->back()->with('success', 'Permission deleted successfully!');
			} 
			catch (\Throwable $e) 
			{ 
				DB::rollBack(); 
				return redirect()->back()->with('error', 'Something went wrong. Please try again.');
			}
		}
		
		public function positionUpdate(Request $request)
		{
			try {
				$positions = $request->position;  

				if (empty($positions)) {
					return redirect()->back()->with('error', 'No positions provided.');
				}

				DB::beginTransaction(); 

				// Prepare data for upsert: Each entry will be an associative array with 'id' and 'position'
				$data = [];
				foreach ($positions as $i => $position) {
					$data[] = [
						'id' => $position,
						'position' => $i + 1, // Ensure position starts from 1
					];
				}

				// Use upsert to efficiently update or insert new positions
				Permission::upsert($data, ['id'], ['position']); // 'id' is the unique identifier, 'position' is the field to update

				DB::commit();

				return;
			} 
			catch (\Throwable $e) { 
				DB::rollBack();  
				return ;
			}
		}

	}
