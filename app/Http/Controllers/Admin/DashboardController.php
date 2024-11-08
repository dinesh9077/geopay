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
}
