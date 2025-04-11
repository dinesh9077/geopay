<?php
	
	namespace App\Http\Controllers\Api\Auth;
	 
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{
		Hash, Validator, DB, App, Mail, Auth
	};
	use Laravel\Passport\HasApiTokens;
	use App\Http\Traits\ApiResponseTrait;
	use Spatie\Activitylog\Facades\Activity;
	use App\Helpers\Helper;
	use App\Models\{
		LoginLog, User, Otp
	}; 
	use App\Mail\{
		PasswordResetOtp, PasswordResetSuccess
	}; 
	
	class LoginController extends Controller
	{
		use HasApiTokens, ApiResponseTrait; 
		
		public function login(Request $request)
		{   
			$validator = Validator::make($request->all(), [
				'email' => 'required|string|email|max:255',
				'password' => 'required|string',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try 
			{ 
				$user = User::where('email', $request->email)->first();
				 
				if (!$user) {
					return $this->errorResponse('User not found.');
				}
				
				// Check user status and verification
				$messages = [
					'status' => 'This user account is inactive. Please reach out to the administrator for further details.',
					'is_email_verify' => 'This email was not verified. Please reach out to the administrator for further details.',
					'is_mobile_verify' => 'This mobile number was not verified. Please reach out to the administrator for further details.',
					'is_kyc_verify' => 'Your KYC verification is pending. Please contact the administrator for assistance.',
				];
				
				foreach ($messages as $key => $message) { 
					if ($user->$key == 0) {
						return $this->errorResponse($message);
					}
				} 
				
				if (!Hash::check($request->password, $user->password)) {
					return $this->errorResponse('Invalid credentials.');
				}
				
				$token = $user->createToken('geopay')->accessToken;
				$user->load('companyDetail'); 
				$user->profile_image = $user->profile_image ? url('storage/profile', $user->profile_image) : url('admin/default-profile.png');
				$user->token = $token;

				Helper::loginLog('login', $user, 'App'); 

				return $this->successResponse('User logged in successfully.', $user);
			}
			catch (\Throwable $e)
			{
				return $this->errorResponse($e->getMessage());
			}
		}
		
		public function logout(Request $request)
		{ 
			try 
			{ 
				Helper::loginLog('logout', $request->user(), 'App');	
				
				$token = $request->user()->token(); 
				$token->revoke();  
				return $this->successResponse('User logout successfully');
			}
			catch (\Throwable $e) 
			{ 
				return $this->errorResponse($e->getMessage());
			}
		}
		
		public function userDetails(Request $request)
		{
			try 
			{ 
				$token = $request->bearerToken();  
				
				$user = Auth::user(); 
				$user->profile_image = $user->profile_image ? url('storage/profile', $user->profile_image) : url('admin/default-profile.png');
				$user->load('companyDetail'); 
				$user->token = $token;
 
				return $this->successResponse('User details fetched successfully', $user);
			}
			catch (\Throwable $e) 
			{ 
				return $this->errorResponse($e->getMessage());
			}
		}
  
		public function forgotPassword(Request $request)
		{   
			$validator = Validator::make($request->all(), [
				'email' => 'required|string|email', 
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try {
				
				$email = $request->input('email');

				// Check if the user exists with the provided email
				$user = User::where('email', $email)->first();
				
				if (!$user) {
					return $this->errorResponse('Your email is not registered with us!');
				}
				
				// Generate a random 6-digit OTP
				$otpCode = mt_rand(100000, 999999);

				// Rate limit check (optional)
				$existingOtp = Otp::where('email_mobile', $email)
								  ->where('created_at', '>', now()->subMinute())
								  ->first();
								  
				if ($existingOtp) {
					return $this->errorResponse('OTP was recently sent. Please try again after some time.');
				}

				// Begin transaction
				return DB::transaction(function () use ($email, $otpCode) {
					// Send the OTP via Laravel Mail
					try {
						Mail::to($email)->send(new PasswordResetOtp($otpCode));
					} catch (\Exception $e) {
						return $this->errorResponse('Failed to send email. Please try again later.');
					}

					// Store or update the OTP in the database
					Otp::updateOrCreate(
						['email_mobile' => $email],
						['otp' => $otpCode, 'expires_at' => now()->addMinutes(10), 'created_at' => now()] // 10-minute expiration
					);
					 
					return $this->successResponse('Verification code sent to your email.');
				}, 3); // Retry transaction 3 times if it fails
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			}
		} 
		
		public function forgotResendOtp(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'email' => 'required|string|email', 
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try {
				
				$email = $request->input('email');

				// Check if the user exists with the provided email
				$user = User::where('email', $email)->exists();
				
				if (!$user) {
					return $this->errorResponse('Your email is not registered with us!');
				}
				
				// Generate a random 6-digit OTP
				$otpCode = mt_rand(100000, 999999);

				// Rate limit check (optional)
				$existingOtp = Otp::where('email_mobile', $email)
								  ->where('created_at', '>', now()->subMinute())
								  ->first();
								  
				if ($existingOtp) {
					return $this->errorResponse('OTP was recently sent. Please try again after some time.');
				}
				
				// Begin transaction
				return DB::transaction(function () use ($email, $otpCode) {
					// Send the OTP via Laravel Mail
					try {
						Mail::to($email)->send(new PasswordResetOtp($otpCode));
					} catch (\Exception $e) {
						return $this->errorResponse('Failed to send email. Please try again later.');
					}

					// Store or update the OTP in the database
					Otp::updateOrCreate(
						['email_mobile' => $email],
						['otp' => $otpCode, 'expires_at' => now()->addMinutes(10), 'created_at' => now()] // 10-minute expiration
					);
					 
					return $this->successResponse('A new OTP has been sent to your email.');
				}, 2); // Retry transaction 3 times if it fails
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			}
		}
	
		public function verifyEmailOtp(Request $request)
		{  
			$validator = Validator::make($request->all(), [
				'email' => 'required|email',
				'otp' => 'required|digits:6',
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			} 
			
			try {
				
				$email = $request->email;
				$otp = $request->otp;

				// Fetch OTP record
				$otpRecord = Otp::where('email_mobile', $email)->first();
				 
				// Check if OTP exists and is not expired
				if (!$otpRecord || $otpRecord->otp !== $otp || $otpRecord->expires_at < now()) {
					return $this->errorResponse('Invalid or expired OTP.');
				}

				// If OTP is correct, consider it verified
				return $this->successResponse('OTP verified successfully. You can now reset your password.');
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			}
		}
 
		public function resetPassword(Request $request)
		{ 
			$validator = Validator::make($request->all(), [
				'email' => 'required|email',
				'password' => [
					'required',
					'string',
					'min:8',  // Minimum 8 characters
					'confirmed',  // Check if password_confirmation matches
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
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			} 
			
			try {
				
				DB::beginTransaction();
				$email = $request->email; 
				$newPassword = $request->password;
	 
				// Fetch user by email
				$user = User::where('email', $email)->first();
				if (!$user) {
					return $this->errorResponse('User not found.');
				}

				// Update user's password
				$user->password = Hash::make($newPassword); // Hash the password securely
				$user->save();
				 
				// Delete OTP record to prevent reuse
				$otpRecord = Otp::where('email_mobile', $email)->first(); 
				if($otpRecord)
				{
					$otpRecord->delete();
				}
				
				DB::commit();  
				// Send password reset success email
				try {
					Mail::to($user->email)->send(new PasswordResetSuccess($user));
				} catch (\Throwable $e) {
					// Rollback changes if email sending fails
					return $this->errorResponse('Password reset successful, but failed to send confirmation email.');
				} 
				
				return $this->successResponse('Password has been reset successfully. A confirmation email has been sent to your email address.');
			} 
			catch (\Throwable $e) 
			{
				DB::rollBack();
				return $this->errorResponse($e->getMessage());
			} 
		}  
	}