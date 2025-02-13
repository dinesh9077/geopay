<?php
	namespace App\Http\Controllers\Api;

	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\{ 
		User, Banner, Faq, Setting , Country 
	};
	use Illuminate\Support\Facades\{Http, Storage, Hash, DB, Log, Auth};
	use App\Http\Traits\ApiResponseTrait;
	use Validator;
	use ImageManager;
 
	class SettingController extends Controller
	{ 
		use ApiResponseTrait;  
		public function userProfileUpdate(Request $request)
		{
			// Validate the input
			$token = $request->bearerToken();   
			$user = Auth::user(); 

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
	 
			try 
			{
				DB::beginTransaction(); 
				$user->first_name = $request->input('first_name');
				$user->last_name = $request->input('last_name');
 
				if($request->hasFile('profile_image'))
				{
					$file = $request->file('profile_image');
					$storedFile = ImageManager::imgUpdate('profile', $user->profile_image, $file->getClientOriginalExtension(), $file);
					$user->profile_image = $storedFile;
				} 
				$user->save(); 
				$user->profile_image = $user->profile_image ? url('storage/profile', $user->profile_image) : url('admin/default-profile.png');
				DB::commit();  
				return $this->successResponse('Your profile has been updated successfully.', $user);
			} catch (\Throwable $e) {
				DB::rollBack(); 
				return $this->errorResponse('An error occurred while updating the profile. Please try again later.');
			}
		}
		
		public function userResetPassword(Request $request)
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
		
		public function commonDetails()
		{
			$banners = Banner::where('status', 1)
			->select('id', 'title', 'image')
			->get()
			->map(function ($banner) {
				$banner->image = url('storage/banner/' . $banner->image);
				return $banner;
			});
			
			$faqs = Faq::select('title', 'description')->where('status', 1) 
			->get(); 
			 
			$data = [
				'banners' => $banners,
				'faqs' => $faqs,
				'aboutus' => config('setting.aboutus') ?? '',
				'site_name' => config('setting.site_name') ?? '',
				'contact_address' => config('setting.contact_address') ?? '',
				'contact_website' => config('setting.contact_website') ?? '',
				'contact_email' => config('setting.contact_email') ?? '',
				'social_whatsapp' => config('setting.social_whatsapp') ?? '',
				'social_instagram' => config('setting.social_instagram') ?? '',
				'social_facebook' => config('setting.social_facebook') ?? '',
				'social_linkedin' => config('setting.social_linkedin') ?? '',
			]; 
			return $this->successResponse('data fetched.', $data);
		}
		
		public function countryList()
		{
			$countries = Country::all();

			$countriesWithFlags = $countries->transform(function ($country) {
				if ($country->country_flag) {
					$country->country_flag = asset('country/' . $country->country_flag);
				} 
				return $country;
			});
			return $this->successResponse('country fetched.', $countriesWithFlags);
		}
	}
