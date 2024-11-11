<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{
	User, Country
};
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\WebResponseTrait;
use App\Services\{
	SmsService, EmailService
};

class RegisterController extends Controller
{
	 
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
	 
    use RegistersUsers, WebResponseTrait;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	 
	protected $smsService;
	protected $emailService; 
	 
    public function __construct()
    {
        $this->middleware('guest');
		$this->smsService = new SmsService();
		$this->emailService = new EmailService();
    }

    public function showRegistrationForm()
	{
		$countries = Country::select('id', 'name', 'isdcode', 'country_flag')->get();

        $countriesWithFlags = $countries->transform(function ($country) {
            if ($country->country_flag) {
                $country->country_flag = asset('country/' . $country->country_flag);
            } 
            return $country;
        });
		 
		return view('user.auth.register', compact('countriesWithFlags'));
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
			'country_id' => 'required|integer',
			'terms' => 'required|integer',
			'mobile_number' => 'required|integer',
			'is_email_verify' => 'required|integer|in:1',
			'is_mobile_verify' => 'required|integer|in:1',
			'formatted_number' => 'required|integer|unique:users', 
		], [
			'is_email_verify.in' => 'Email verification is required before proceeding.',
			'is_mobile_verify.in' => 'Mobile verification is required before proceeding.',
		]);
		
		// Check if the main validator fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
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
		if (User::where('email', $request->email)->exists()) {
			return $this->errorResponse('The email you provided already exists.');
		}
		
		// Generate a 6-digit OTP
		$otp = rand(100000, 999999);
		
		return $this->emailService->sendOtp($request->email, $otp, false, true);  
	}
	
	public function resendEmailOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'email' => 'required|string|email', 
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		// Check if the email already exists in the database
		if (User::where('email', $request->email)->exists()) {
			return $this->errorResponse('The email you provided already exists.');
		}
			 
		return $this->emailService->resendOtp($request->email, true);
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
		
		return $this->emailService->verifyOtp($request->email, $request->otp, true);
	} 
	
	//Mobile Verification
	public function sendMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile_number' => 'required|numeric', // Use numeric for mobile validation
		]);

		// Check if the validation fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		// Format the mobile number with country code (assuming the mobile doesn't have a leading '+')
		$formatted_number = '+' . ltrim($request->mobile_number, '+');
		
		// Check if the mobile number already exists in the database
		if (User::where('formatted_number', $formatted_number)->exists()) {
			return $this->errorResponse('The mobile number you provided already exists.');
		}
		
		// Generate a 6-digit OTP
		$otp = rand(100000, 999999);
		return $this->smsService->sendOtp($request->mobile_number, $otp, false, true);  
	}
	
	public function resendMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile_number' => 'required|numeric', 
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		$formatted_number = '+' . ltrim($request->mobile_number, '+');

		// Check if the mobile number already exists in the database
		if (User::where('formatted_number', $formatted_number)->exists()) {
			return $this->errorResponse('The mobile number you provided already exists.');
		}
			 
		return $this->smsService->resendOtp($request->mobile_number, true);
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
		
		return $this->smsService->verifyOtp($request->mobile, $request->otp, true);
	}
}
