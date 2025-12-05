<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\ {
	Hash, Validator, DB, Mail, Auth, Storage
};
use App\Models\{ 
	User, CompanyDetail, Country 
}; 
use App\Http\Traits\ApiResponseTrait; 
use Helper; 
use Str;
use App\Services\{
	SmsService, EmailService
};
use Laravel\Passport\HasApiTokens;
use App\Enums\BusinessOccupation;
use App\Enums\SourceOfFunds;
use App\Enums\IdType;

class RegisterController extends Controller
{
    use HasApiTokens, ApiResponseTrait;  
	protected $smsService;
	protected $emailService; 
	 
    public function __construct()
    { 
		$this->smsService = new SmsService();
		$this->emailService = new EmailService();
    }
	  
	public function individualRegister(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => [
				'required',
				'string',
				'confirmed',
				'min:8',
				function ($attribute, $value, $fail) {
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

					foreach ($errors as $message) {
						$fail($message);
					}
				}
			],
			//'country_id' => 'required|integer',
			'terms' => 'required|integer|in:1',
			//'mobile_number' => 'required|integer',
		], [
			'terms.integer' => 'You must agree to the terms and conditions to proceed.'
		]);

		$validator->after(function ($validator) use ($request) {
			if ($request->input('email') && $request->input('is_email_verify') == 0) {
				$validator->errors()->add('email', 'Email verification is required before proceeding.');
			}
			// if ($request->input('mobile_number') && $request->input('is_mobile_verify') == 0) {
			// 	$validator->errors()->add('mobile_number', 'Mobile verification is required before proceeding.');
			// }
		});

		// Check if the main validator fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		try {

			DB::beginTransaction();
			// Retrieve the country based on country_id and check if it exists
			// $country = Country::find($request->country_id);

			// if (!$country) {
			// 	return $this->errorResponse('The country selection is not found.');
			// }

			//$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			$userData = $request->only('first_name', 'last_name', 'email', 'country_id', 'mobile_number', 'referalcode', 'is_email_verify', 'is_mobile_verify', 'terms', 'address');
			$userData['password'] = Hash::make($request->password);
			$userData['xps'] = base64_encode($request->password);
			//$userData['formatted_number'] = $formattedNumber;
			$userData['role'] = 'user';
			$userData['is_company'] = 0;
			$userData['is_kyc_verify'] = 0;
			$userData['verification_token'] = Str::random(64);
			//session(['user_data' => $userData]);
			$user = User::create($userData);
			$token = $user->createToken('geopay')->accessToken;
			$user->profile_image = $user->profile_image ? url('storage/profile', $user->profile_image) : url('admin/default-profile.png');
			$user->load('companyDetail'); 
			$user->token = $token; 
            DB::commit();  
            return $this->successResponse('User registered successfully.', $user); 
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
	}
	
	public function companyRegister(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => [
				'required',
				'string',
				'confirmed',
				'min:8',
				function ($attribute, $value, $fail) {
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

					foreach ($errors as $message) {
						$fail($message);
					}
				}
			],
			//'country_id' => 'required|integer',
			'terms' => 'required|integer|in:1',
			//'mobile_number' => 'required|integer',
			'company_name' => 'required|string',
		], [
			'terms.integer' => 'You must agree to the terms and conditions to proceed.'
		]);

		$validator->after(function ($validator) use ($request) {
			if ($request->input('email') && $request->input('is_email_verify') == 0) {
				$validator->errors()->add('email', 'Email verification is required before proceeding.');
			}
			// if ($request->input('mobile_number') && $request->input('is_mobile_verify') == 0) {
			// 	$validator->errors()->add('mobile_number', 'Mobile verification is required before proceeding.');
			// }
		});

		// Check if the main validator fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		try {

			DB::beginTransaction();
			// Retrieve the country based on country_id and check if it exists
			// $country = Country::find($request->country_id);

			// if (!$country) {
			// 	return $this->errorResponse('The country selection is not found.');
			// }

			//$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			$userData = $request->only('first_name', 'last_name', 'email', 'country_id', 'mobile_number', 'company_name', 'is_email_verify', 'is_mobile_verify', 'terms');
			$userData['password'] = Hash::make($request->password);
			$userData['xps'] = base64_encode($request->password);
			//$userData['formatted_number'] = $formattedNumber;
			$userData['role'] = 'user';
			$userData['is_company'] = 1;
			$userData['is_kyc_verify'] = 0;
			$userData['verification_token'] = Str::random(64);

			$user = User::create($userData);
			Helper::updateLogName($user->id, User::class, 'corporate/company user');	 
			$token = $user->createToken('geopay')->accessToken;
			$user->profile_image = $user->profile_image ? url('storage/profile', $user->profile_image) : url('admin/default-profile.png');
			$user->load('companyDetail'); 
			$user->token = $token; 
            DB::commit();  
            return $this->successResponse('User registered successfully.', $user); 
        } 
		catch (\Throwable $e)
		{ 
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
	}
	
	public function userBasicDetailsUpdate(Request $request)
	{ 
		$validator = Validator::make($request->all(), [ 
			'mobile_number' => 'required|integer',
			'country_id' => 'required|integer',
			'id_type' => 'required|in:' . implode(',', array_column(IdType::cases(), 'value')),
			'id_number' => 'required|string|max:50',
			'expiry_id_date' => 'required|date',
			'issue_id_date' => 'required|date', 
			'city' => 'required|string|max:100',
			'state' => 'required|string|max:100',
			'zip_code' => 'required|string|max:20', 
			'date_of_birth' => 'required|date',
			'gender' => 'required|in:Male,Female,Other',
			'address' => 'required|string', 
			'business_activity_occupation' => 'required|in:' . implode(',', array_column(BusinessOccupation::cases(), 'value')), 
			'source_of_fund' => 'required|in:' . implode(',', array_column(SourceOfFunds::cases(), 'value')),
		]);

		$validator->after(function ($validator) use ($request) { 
			if ($request->input('mobile_number') && $request->input('is_mobile_verify') == 0) {
				$validator->errors()->add('mobile_number', 'Mobile verification is required before proceeding.');
			}
		});

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		try {

			DB::beginTransaction();
			$user = Auth::user();
			if (!$user) {
				return $this->errorResponse('unauthorize user.');
			}
			$country = Country::find($request->country_id); 
			if (!$country) {
				return $this->errorResponse('The country selection is not found.');
			}

			$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			$userData = $request->except(keys: '_token');
			$userData['formatted_number'] = $formattedNumber;

			$user->update($userData);  

			DB::commit();
			return $this->successResponse('User registered successfully.', data: $userData);
		} catch (\Throwable $e) {
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		} 
	}

	// Email verification
	public function sendEmailOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email'
		]);

		// Check if the validation fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		  
		// Check if the email already exists in the database
		// if (User::where('email', $request->email)->exists()) {
		// 	return $this->errorResponse('The email you provided already exists.');
		// }
		
		// Generate a 6-digit OTP
		$otp = rand(100000, 999999);
		
		return $this->emailService->sendOtp($request->email, $otp, false, false);  
	}
	 
	public function resendEmailOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email'
		]);

		// Check if the validation fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		  
		// Check if the email already exists in the database
		// if (User::where('email', $request->email)->exists()) {
		// 	return $this->errorResponse('The email you provided already exists.');
		// }
		
		// Generate a 6-digit OTP
		$otp = rand(100000, 999999);
		
		return $this->emailService->sendOtp($request->email, $otp, false, false);  
	}
	
	public function verifyEmailOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email',
			'otp' => 'required|digits:6', // Adjust based on your OTP length
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		return $this->emailService->verifyOtp($request->email, $request->otp, false);
	} 
	
	// Mobile Verification
	public function sendMobileOtp(Request $request)
	{
		// Validate the request inputs
		$validator = Validator::make($request->all(), [
			'mobile_number' => 'required|numeric', // Mobile number is required and must be numeric
			'country_id' => 'required|numeric', // Country ID is required and must be numeric
		]);

		// Return validation errors if validation fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}

		// Retrieve the country based on country_id and check if it exists
		$country = Country::find($request->country_id);
	 
		if (!$country) {
			return $this->errorResponse('The country selection is not found.');
		}

		// Format the mobile number with country code
		$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');

		// Check if the mobile number is already registered
		if (User::where('formatted_number', $formattedNumber)->exists()) {
			return $this->errorResponse('The mobile number you provided already exists.');
		}

		// Generate a 6-digit OTP
		$otp = random_int(100000, 999999);

		// Send the OTP via SMS service
		return $this->smsService->sendOtp(ltrim($formattedNumber, '+'), $otp, false, false);
	}

	
	public function resendMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile_number' => 'required|numeric', // Use numeric for mobile validation
			'country_id' => 'required|numeric', // Use numeric for mobile validation
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		// Retrieve the country based on country_id and check if it exists
		$country = Country::find($request->country_id);
	 
		if (!$country) {
			return $this->errorResponse('The country selection is not found.');
		}

		// Format the mobile number with country code
		$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');

		// Check if the mobile number already exists in the database
		if (User::where('formatted_number', $formattedNumber)->exists()) {
			return $this->errorResponse('The mobile number you provided already exists.');
		}
			 
		return $this->smsService->resendOtp(ltrim($formattedNumber, '+'), false);
	}

	public function verifyMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile_number' => 'required|numeric',
			'otp' => 'required|digits:6', // Adjust based on your OTP length
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		return $this->smsService->verifyOtp($request->mobile_number, $request->otp, false);
	}
	
	
}