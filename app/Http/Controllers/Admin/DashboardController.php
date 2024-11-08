<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
	Folder, User, File
};
use Validator, DB, Hash, Storage, Auth;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
   
    public function dashboard()
    { 
		$totalUsers = User::whereStatus(1)->count();
		$totalFolders = Folder::whereStatus(1)->count();
		$totalFiles = File::count();
        return view('admin.dashboard', compact('totalUsers', 'totalFolders', 'totalFiles')); 
    }
	
    public function profile()
    { 
		$user = Auth::guard('admin')->user();
        return view('admin.profile', compact('user')); 
    }
	
	
	public function profileUpdate(Request $request)
	{  
		$validator = Validator::make($request->all(), [
			'name' => 'required|string',
			'email' => 'required|email', 
		]);
			 
		if ($validator->fails()) {
			return response()->json([
			'status' => 'validation',
			'errors' => $validator->errors()
			]);
		}
		
		try
		{  
			DB::beginTransaction();
			
			$user = Auth::guard('admin')->user();
			
			$data = $request->only('name', 'email', 'mobile');
			$data['updated_at'] = now();  
			
			if($request->password)
			{
				$data['password'] = Hash::make($request->password); 
			}
			
			if ($request->hasFile('profile')) {
				  
			 // Check if user has an existing profile image and remove it if necessary
				if ($user->profile && Storage::disk('public')->exists($user->profile)) { 
					Storage::disk('public')->delete($user->profile);
				}
				// Get the uploaded file
				$file = $request->file('profile');

				// Generate a unique file name
				$fileName = time() . '.' . $file->getClientOriginalExtension();
 
				// Move the file to the specified location
				$file->move(public_path('storage'), $fileName); 
 
				// Update the user's profile_image attribute
				$data['profile'] = $fileName;
			}
			echo '<pre>';
print_r($data);
echo '</pre>';
die;
			$user->update($data);	

			DB::commit();
			return response()->json(['status'=>'success','msg'=>'The user profile have been updated successfully.']);
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['status' => 'error', 'msg' => $message]);
		}
	}
	
	// User
	public function user()
    {   
		$users = User::whereStatus(1)->orderByDesc('id')->get(); 
        return view('admin.user', compact('users')); 
    }
	 
	public function userStore(Request $request)
	{ 
		$validator = Validator::make($request->all(), [
			'name' => 'required|string',
			'email' => 'required|email|unique:users', // Correct for creation
			'password' => 'required', 
			'status' => 'required|in:1,0', 
		]);
			 
		if ($validator->fails()) {
			return response()->json([
			'status' => 'validation',
			'errors' => $validator->errors()
			]);
		}
		
		try
		{  
			DB::beginTransaction();
			$data = $request->only('name', 'email', 'mobile', 'status');
			// Create the folder (You can adjust according to your database logic)
			$data = array_merge($data, [
				'password' => Hash::make($request->password),
				'txn_password' => $request->password,
				'created_at' => now(),
				'updated_at' => now()
			]);
			
			if ($request->hasFile('profile_image')) {
				  
				// Get the uploaded file
				$file = $request->file('profile_image');

				// Generate a unique file name
				$fileName = time() . '.' . $file->getClientOriginalExtension();

				// Define the path to save the image (e.g., 'profile_image/')
				$filePath = 'profile_image/' . $fileName;

				// Move the file to the specified location
				$file->move(public_path('storage/profile_image'), $fileName); // Move the file to the 'public/storage/profile_image' directory
 
				// Update the user's profile_image attribute
				$data['profile_image'] = $fileName; // Ensure this is the correct path
			}
			User::create($data);

			DB::commit();
			return response()->json(['status'=>'success','msg'=>'The user has been successfully added.']);
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['status' => 'error', 'msg' => $message]);
		}
	}
	 
	public function userUpdate(Request $request, $userId)
	{ 
		 // Validation rules
		$validator = Validator::make($request->all(), [
			'name' => 'required|string',
			'email' => [
				'required',
				'email',
				// Exclude the current user's email from the unique check
				Rule::unique('users')->ignore($userId),
			],
			'password' => 'nullable', // Optional: Only require if you're updating the password
			'status' => 'required|in:1,0',
		]);
			 
		if ($validator->fails()) {
			return response()->json([
			'status' => 'validation',
			'errors' => $validator->errors()
			]);
		}
		
		try
		{  
			DB::beginTransaction();
			$data = $request->only('name', 'email', 'mobile', 'status');
			// Create the folder (You can adjust according to your database logic)
			$data = array_merge($data, [
				'password' => Hash::make($request->password),
				'txn_password' => $request->password, 
				'updated_at' => now()
			]);
			
			$user = User::find($userId);
			  // Check if a new file was uploaded
			if ($request->hasFile('profile_image')) {
				// Check if user has an existing profile image and remove it if necessary
				if ($user->profile_image && Storage::disk('public')->exists('profile_image/'.$user->profile_image)) { 
					Storage::disk('public')->delete('profile_image/'.$user->profile_image);
				}

				// Get the uploaded file
				$file = $request->file('profile_image');

				// Generate a unique file name
				$fileName = time() . '.' . $file->getClientOriginalExtension();

				// Define the path to save the image (e.g., 'profile_image/')
				$filePath = 'profile_image/' . $fileName;

				// Move the file to the specified location
				$file->move(public_path('storage/profile_image'), $fileName); // Move the file to the 'public/storage/profile_image' directory
 
				// Update the user's profile_image attribute
				$data['profile_image'] = $fileName; // Ensure this is the correct path
			}
			$user->update($data);

			DB::commit();
			return response()->json(['status'=>'success','msg'=>'The user have been updated successfully.']);
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['status' => 'error', 'msg' => $message]);
		}
	}
	
	public function userDelete($userId)
	{
		try
		{  
			DB::beginTransaction();
		
			$user = User::find($userId); 
			
			if ($user->profile_image && Storage::disk('public')->exists('profile_image/'.$user->profile_image)) { 
				Storage::disk('public')->delete('profile_image/'.$user->profile_image);
			} 
			
			$user->delete();
			
			DB::commit();
			
			session()->flash('success', 'The user has been deleted successfully.');
			return redirect()->route('admin.user')->withInput();
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			session()->flash('error', $message);
			return redirect()->route('admin.user')->withInput();
		}
	}
	
	// Folder Creation Code 
    public function folder(Request $request)
    { 
		$folders = Folder::with(['user:id,name'])->whereStatus(1)->orderByDesc('id')->get();
		$user = User::whereStatus(1);
		if($request->user_id)
		{
			$user->where('id', $request->user_id);
		}
		$users = $user->orderByDesc('id')->get(); 
        return view('admin.folder', compact('folders', 'users')); 
    }
	 
	public function folderStore(Request $request)
	{ 
		$validator = Validator::make($request->all(), [
				'user_id' => 'required|numeric',
				'name' => [
					'required',
					'string',
					'max:255',
					Rule::unique('folders')->where(function ($query) use ($request) {
						return $query->where('user_id', $request->user_id);
					}),
				],
				'folder_color' => 'required|string',
				'text_color' => 'required|string',
				'status' => 'required|in:1,0', 
			], [
				'name.unique' => 'The folder name has already been taken for this user. Please choose a different name.', 
		]);
		
		
		if ($validator->fails()) {
			return response()->json([
			'status' => 'validation',
			'errors' => $validator->errors()
			]);
		}
		 
		try
		{  
			DB::beginTransaction();
			$data = $request->only('user_id', 'name', 'folder_color', 'text_color', 'status');
			// Create the folder (You can adjust according to your database logic)
			Folder::create(array_merge($data, [
				'created_at' => now(),
				'updated_at' => now()
			]));

			DB::commit();
			return response()->json(['status'=>'success','msg'=>'The folder has been successfully added.']);
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['status' => 'error', 'msg' => $message]);
		}
	}
	
	public function folderUpdate(Request $request, $folderId)
	{ 
		$validator = Validator::make($request->all(), [
			'user_id' => 'required|numeric',
			'name' => [
				'required',
				'string',
				'max:255',
				Rule::unique('folders')->where(function ($query) use ($request) {
					return $query->where('user_id', $request->user_id);
				})->ignore($folderId), // Ignore the current record
			],
			'folder_color' => 'required|string',
			'text_color' => 'required|string',
			'status' => 'required|in:1,0', 
		], [
			'name.unique' => 'The folder name has already been taken for this user. Please choose a different name.',
		]);
			 
		if ($validator->fails()) {
			return response()->json([
			'status' => 'validation',
			'errors' => $validator->errors()
			]);
		}
		
		try
		{  
			DB::beginTransaction();
			$data = $request->only('user_id', 'name', 'folder_color', 'text_color', 'status');
			 
			Folder::find($folderId)->update(array_merge($data, [
				'created_at' => now(),
				'updated_at' => now()
			]));

			DB::commit();
			return response()->json(['status'=>'success','msg'=>'The folder has been successfully added.']);
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['status' => 'error', 'msg' => $message]);
		}
	}
	
	public function folderDelete($folderId)
	{
		try
		{  
			DB::beginTransaction();
		
			$folder = Folder::find($folderId);  
			$folder->delete();
			
			DB::commit();
			
			session()->flash('success', 'The folder has been deleted successfully.');
			return redirect()->route('admin.folder')->withInput();
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			session()->flash('error', $message);
			return redirect()->route('admin.folder')->withInput();
		}
	}
	
	
	// File Creation Code 
    public function file(Request $request)
    {  
		$folder = Folder::with(['user:id,name'])->whereStatus(1);
		if($request->folder_id)
		{
			$folder->where('id', $request->folder_id);
		}
		$folders = $folder->orderByDesc('id')->get(); 
		
		$files = File::with(['folder:id,name,user_id', 'folder.user:id,name'])->whereStatus(1)->get(); 
        return view('admin.file', compact('folders', 'files')); 
    }
	 
	public function fileStore(Request $request)
	{ 
		// Validate the request input
		$validator = Validator::make($request->all(), [
			'folder_id' => 'required|numeric',
			'title' => 'required|string|max:250',
			'file' => 'required|file|mimes:jpg,png,gif,pdf,doc,docx,csv,xls,xlsx|max:2048', // Added size limit
			'status' => 'required|in:1,0',
		]);
		 
		// Return validation errors if any
		if ($validator->fails()) {
			return response()->json([
				'status' => 'validation',
				'errors' => $validator->errors()
			]);
		}
		 
		try
		{  
			DB::beginTransaction();

			// Extract the validated data
			$data = $request->only('folder_id', 'title', 'status');
			
			// Process the file upload
			if ($request->hasFile('file')) {
				// Get the uploaded file
				$file = $request->file('file');

				// Generate a unique file name
				$fileName = time() . '.' . $file->getClientOriginalExtension();

				// Define the file path to save the file
				$filePath = 'files/' . $request->folder_id . '/';

				// Create the directory if it doesn't exist
				if (!Storage::disk('public')->exists($filePath)) {
					Storage::disk('public')->makeDirectory($filePath);
				}

				// Move the file to the specified location
				$file->storeAs($filePath, $fileName, 'public'); // Use 'public' disk for storage

				// Store the file name in the data array
				$data['file'] = $fileName; 
			}

			// Create the file entry in the database
			File::create($data);

			DB::commit();
			return response()->json(['status' => 'success', 'msg' => 'The file has been successfully added.']);
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			return response()->json(['status' => 'error', 'msg' => $message]);
		}
	}

	 
	public function fileDelete($fileId)
	{
		try
		{  
			DB::beginTransaction(); 
		
			$file = File::find($fileId); 
			
			if ($file->file && Storage::disk('public')->exists('files/'.$file->folder_id.'/'.$file->file)) { 
				Storage::disk('public')->delete('files/'.$file->folder_id.'/'.$file->file);
			} 
			
			$file->delete();
			
			DB::commit();
			
			session()->flash('success', 'The file has been deleted successfully.');
			return redirect()->route('admin.file')->withInput(); 
		}
		catch (\Throwable $e)
		{
			DB::rollBack();
			$message = $e->getMessage();
			session()->flash('error', $message);
			return redirect()->route('admin.folder')->withInput();
		}
	}
}
