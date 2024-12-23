<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Banner;
use App\Models\Faq;
use App\Http\Traits\WebResponseTrait; 
use Validator, DB, Auth, Hash;
use Helper, ImageManager;

class SettingController extends Controller
{ 
	use WebResponseTrait;
	
    public function __construct()
    {
        $this->middleware('auth');
    }
 
    public function index()
    { 
		$faqs = Faq::orderByDesc('id')->get();
		$user = auth()->user();
        return view('user.setting.index', compact('faqs', 'user'));
    }
	
	public function changePassword(Request $request)
	{
		// Validate the input
		$validator = Validator::make($request->all(), [
			'old_password' => 'required',
			'password' => [
				'required',
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
		], [
			'password.confirmed' => 'The new password confirmation does not match.',
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		try {
			DB::beginTransaction();

			$user = auth()->user();

			// Check if the old password matches
			if (!Hash::check($request->old_password, $user->password)) {
				return $this->errorResponse('The old password is incorrect.');
			}

			// Update the user's password
			$user->password = Hash::make($request->password);
			$user->password_changed_at = now();
			$user->xps = base64_encode($request->password);
			$user->save();

			DB::commit();

			return $this->successResponse('Your password has been updated successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();

			return $this->errorResponse('An error occurred while updating the password. Please try again later.');
		}
	}
	
	public function profileUpdate(Request $request)
	{
		// Validate the input
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'profile_image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		try {
			DB::beginTransaction();

			$user = auth()->user();
			$user->first_name = $request->input('first_name');
			$user->last_name = $request->input('last_name');

			// Handle profile image upload
			if ($request->hasFile('profile_image')) {
				$file = $request->file('profile_image');
				$storedFile = ImageManager::imgUpdate('profile', $user->profile_image, $file->getClientOriginalExtension(), $file);
				$user->profile_image = $storedFile;
			}

			$user->save();

			DB::commit();

			return $this->successResponse('Your profile has been updated successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();

			return $this->errorResponse('An error occurred while updating the profile. Please try again later.');
		}
	}


}
