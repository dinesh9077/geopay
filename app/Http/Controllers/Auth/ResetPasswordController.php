<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{
	Hash, Validator, DB, App, Mail, Auth, Session
};
use Spatie\Activitylog\Facades\Activity;
use App\Helpers\Helper;
use App\Models\{
	LoginLog, User, Otp
}; 
use App\Mail\{
	PasswordResetOtp, PasswordResetSuccess
};  
use App\Http\Traits\WebResponseTrait;

class ResetPasswordController extends Controller
{
	use WebResponseTrait;
	
	// Show the OTP request form
    public function showOtpRequestForm()
    {
        return view('user.auth.passwords.reset');
    }
	
	// Send OTP to the user's email
    public function sendOtp(Request $request)
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
				
				$view = view('user.auth.passwords.verify-otp')->render();
				$extraData = ['view' => $view, 'email' => $email];
				return $this->successResponse('Verification code sent to your email.', $extraData);
			}, 3); // Retry transaction 3 times if it fails
		} 
		catch (\Throwable $e) 
		{
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	}
	
	public function resendOtp(Request $request)
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
			
			$view = view('user.auth.passwords.confirm')->render();
			$extraData = ['view' => $view, 'email' => $email];
				
			// If OTP is correct, consider it verified
			return $this->successResponse('OTP verified successfully. You can now reset your password.', $extraData);
		} 
		catch (\Throwable $e) 
		{
			DB::rollBack();
			return $this->errorResponse($e->getMessage());
		}
	}
	
	public function resetPassword(Request $request)
	{ 
		$user = User::where('email', $request->email)->first();
		if (!$user) {
			return $this->errorResponse('User not found.');
		}
		
		$validator = Validator::make($request->all(), [
			'email' => 'required|email',
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
		]);

		if ($validator->fails()) {
			return $this->validateResponse($validator->errors());
		} 
		
		try {
			
			DB::beginTransaction();
			$email = $request->email; 
			$newPassword = $request->password;
  
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
