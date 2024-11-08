<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\ {
	Hash, Validator, DB, Mail, Auth, Storage
};
use App\Models\{ 
	User, CompanyDetail 
}; 
use App\Http\Traits\ApiResponseTrait; 
use Helper; 
use Str;
use App\Services\{
	SmsService, EmailService
};

class RegisterController extends Controller
{
    use ApiResponseTrait;  
	protected $smsService;
	protected $emailService;

	public function __construct()
	{	
		$this->smsService = new SmsService();
		$this->emailService = new EmailService();
	}
	  
	protected function validateCompanyFields(Request $request)
	{
		$companyValidator = Validator::make($request->all(), [
			'company_name' => 'required|string|max:255',
			'business_licence' => 'required|string',
			'tin' => 'required|string',
			'vat' => 'required|string',
			'company_address' => 'required|string',
			'postcode' => 'required|string',
			'bank_name' => 'required|string',
			'account_number' => 'required|string',
			'bank_code' => 'required|string',
		]);

		if ($companyValidator->fails()) {
			return $this->validateResponse($companyValidator->errors());
		}
	}

    public function register(Request $request)
    {    
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|string|max:255',
			'last_name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users',
			'password' => [
				'required',
				'string',
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
			'mobile_number' => 'required|integer',
			'formatted_number' => 'required|integer|unique:users',
			'is_company' => 'required|integer|in:0,1'
		]);

		// Check if the user indicates they are a company
		if ($request->input('is_company') == 1) {
			// Validate company-specific fields
			$this->validateCompanyFields($request);
		}

		// Check if the main validator fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
        try {
			
			DB::beginTransaction();
			
            $userData = $request->only('first_name', 'last_name', 'email', 'country_id', 'mobile_number', 'formatted_number', 'is_company');
			$userData['password'] = Hash::make($request->password);
			$userData['role'] = 'user'; 
            if (isset($request->referalcode)) {
                $userData['referalcode'] = $request->referalcode;
            } 
            $user = User::create($userData);
			
			// Generate a verification token
			$verificationToken = Str::random(64);
			// Save the token in the user's record (or a separate table)
			$user->verification_token = $verificationToken;
			$user->save();

            if ($request->is_company == 1) {
				$companyDetailData = $request->only('company_name', 'business_licence', 'tin', 'vat', 'company_address', 'postcode', 'bank_name', 'account_number', 'bank_code');
				
				$companyDetailData['user_id'] = $user->id;
                $user->companyDetail()->updateOrCreate(
					['user_id' => $user->id], // This is the condition to check if the record exists
					$companyDetailData // The data to store
				); 
				  
				$moduleId = $user->companyDetail ? $user->companyDetail->id : 0;
				Helper::updateLogName($moduleId, CompanyDetail::class, 'company details', $user->id);
            } 
			$user->load('companyDetail');
			
            DB::commit();  
            return $this->successResponse('User registered successfully.', 'user', $user); 
        } 
		catch (\Throwable $e)
		{ 
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
		if (User::where('email', $request->email)->exists()) {
			return $this->errorResponse('The email you provided already exists.');
		}
		
		// Generate a 6-digit OTP
		$otp = rand(100000, 999999);
		
		return $this->emailService->sendOtp($request->email, $otp);  
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
			 
		return $this->emailService->resendOtp($request->email);
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
		
		return $this->emailService->verifyOtp($request->email, $request->otp);
	}  
	
	//Mobile Verification
	public function sendMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile' => 'required|numeric', // Use numeric for mobile validation
		]);

		// Check if the validation fails
		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		// Format the mobile number with country code (assuming the mobile doesn't have a leading '+')
		$formatted_number = '+' . ltrim($request->mobile, '+');
		
		// Check if the mobile number already exists in the database
		if (User::where('formatted_number', $formatted_number)->exists()) {
			return $this->errorResponse('The mobile number you provided already exists.');
		}
		
		// Generate a 6-digit OTP
		$otp = rand(100000, 999999);
		return $this->smsService->sendOtp($request->mobile, $otp);  
	}
	
	public function resendMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile' => 'required|numeric', 
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		 
		$formatted_number = '+' . ltrim($request->mobile, '+');

		// Check if the mobile number already exists in the database
		if (User::where('formatted_number', $formatted_number)->exists()) {
			return $this->errorResponse('The mobile number you provided already exists.');
		}
			 
		return $this->smsService->resendOtp($request->mobile);
	}

	public function verifyMobileOtp(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'mobile' => 'required|numeric',
			'otp' => 'required|digits:6', // Adjust based on your OTP length
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		}
		
		return $this->smsService->verifyOtp($request->mobile, $request->otp);
	}
	
	public function updateProfile(Request $request)
	{
		 // Define validation rules
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255', 
            'profile_image' => 'nullable|string' // Corrected regex
        ]);


        // Check validation
        if ($validator->fails()) {
            return $this->validateResponse($validator->errors()); 
        }

        // Retrieve validated data
        $data = $request->all();
 
        // Start transaction
        DB::beginTransaction();

        try {
            
			$user = Auth::user();
			
			 // Process the profile image if present
            if (!empty($data['profile_image'])) {
			
				// Check if user has an existing profile image and remove it
				if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) { 
					Storage::disk('public')->delete($user->profile_image);
				}
			
			    $fileData = explode(';base64,', $data['profile_image']);
				$fileExtension = str_replace('data:image/', '', $fileData[0]); // get file extension
				$imageContent = base64_decode($fileData[1]);

				$fileName = uniqid().'_'.time() . '.' . trim($fileExtension);
 
                // Define the storage path (optional: adjust path as needed)
                $filePath = 'profile_images/' . $user->id . '/' . $fileName;

                // Save the image to the specified path
                Storage::disk('public')->put($filePath, $imageContent);

                // Update the data array with the file path
                $data['profile_image'] = $filePath;
            }
			
            // Create or update the user record
            $user->update( 
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'profile_image' => $data['profile_image'] ?? null
                ]
            );

            // Commit the transaction
            DB::commit();
 
			return $this->successResponse("User data saved successfully!", 'user', $user);

        } catch (\Throwable $e) {
            // Rollback the transaction if something failed
            DB::rollback();

            return $this->errorResponse($e->getMessage());
        }
	}
}