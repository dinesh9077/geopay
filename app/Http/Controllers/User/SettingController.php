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
		$user = auth()->user();

		// Step 1: Validate everything including old password
		$validator = Validator::make($request->all(), [
			'old_password' => [
				'required',
				function ($attribute, $value, $fail) use ($user) {
					if (!Hash::check($value, $user->password)) {
						$fail('The old password is incorrect.');
					}
				}
			],
			'password' => [
				'required',
				'string',
				'confirmed',
				'min:8',
				function ($attribute, $value, $fail) use ($user) {
					$errors = [];

					// Check strength rules first
					if (!preg_match('/[A-Z]/', $value)) {
						$errors[] = 'The password must contain at least one uppercase letter.';
					}
					if (!preg_match('/[a-z]/', $value)) {
						$errors[] = 'The password must contain at least one lowercase letter.';
					}
					if (!preg_match('/\d/', $value)) {
						$errors[] = 'The password must contain at least one number.';
					}
					if (!preg_match('/[\W_]/', $value)) {
						$errors[] = 'The password must contain at least one special character.';
					}

					// Only check old == new after all other rules are valid
					if (count($errors) === 0 && Hash::check($value, $user->password)) {
						$errors[] = 'The new password must be different from the old password.';
					}

					foreach ($errors as $message) {
						$fail($message);
					}
				}
			],
		], [
			'password.confirmed' => 'The new password confirmation does not match.',
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		// Step 2: Update the password
		try {
			DB::beginTransaction();

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
		$user = auth()->user();

		$rules = [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'profile_image' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
		];

		// Conditionally add 'address' validation
		if ($user->is_company != 1) {
			$rules['address'] = 'nullable|string';
		} 
		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
 
		try {
			DB::beginTransaction();
 
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
	
	public function basicInfoUpdate(Request $request)
	{
		// Validate the input 
		$validator = Validator::make($request->all(), [
			'id_type' => 'required|string|in:Passport,National ID Card,Driving License,Voter ID,Residence Permit',
			'id_number' => 'required|string|max:50',
			'expiry_id_date' => 'required|date',

			'city' => 'required|string|max:100',
			'state' => 'required|string|max:100',
			'zip_code' => 'required|string|max:20',

			'date_of_birth' => 'required|date',
			'gender' => 'required|in:Male,Female,Other',
			'address' => 'required|string',

			'business_activity_occupation' => 'required|in:Agriculture forestry fisheries,Construction/manufacturing/marine,Government officials and Special Interest Organizations,Professional and related workers,Retired,Self-employed,Student,Unemployed',

			'source_of_fund' => 'required|in:Business profit/dividend,Income from employment (normal and/or bonus),Investments,Savings,Inheritance,Loan,Gift,Real Estate,Lottery/betting/casino winnings',
		]);
 
		// Check if the main validator fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
 
		try {
			DB::beginTransaction();

			$user = auth()->user();
			$user->fill($request->except('_token'));
			$user->save();

			DB::commit();

			return $this->successResponse('basic details has been updated successfully.');
		} catch (\Throwable $e) {
			DB::rollBack();
			return $this->errorResponse('An error occurred while updating the profile. Please try again later.');
		}

	} 

}
