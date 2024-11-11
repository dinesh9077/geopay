<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Http\Traits\ApiResponseTrait; 
use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\{
	Log, Mail
};
use App\Mail\OtpEmail;

class EmailService
{ 
    use ApiResponseTrait; 
  
	/**
	* Resend OTP to a email
	*
	* @param string $email
	* @return \Illuminate\Http\JsonResponse
	*/
	 
    public function resendOtp($email, $isWeb = false)
    {
        try {
            // Check if an OTP already exists for the mobile number
            $otpRecord = Otp::where('email_mobile', $email)
                            ->where('expires_at', '>', Carbon::now())
                            ->latest()
                            ->first();
	 
            // If OTP exists and is not expired, you can resend the same OTP
            if ($otpRecord) {
                $otp = $otpRecord->otp;
            } else {
                // Otherwise, generate a new OTP
                $otp = rand(100000, 999999);

                // Save the new OTP to the database
                Otp::create([
                    'email_mobile' => $email,
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(5), // OTP expires after 5 minutes
                    'created_at' => now(),
                ]);
            }

            // Send the OTP to the user
            return $this->sendOtp($email, $otp, $isSend = true, $isWeb);
        } catch (\Throwable $e) 
		{
            // Log error and return response
            Log::error('Resend OTP failed: ' . $e->getMessage()); 
			$responseType =  $isWeb ? 'webErrorResponse' : 'errorResponse'; 
            return $this->{$responseType}('Resend OTP failed: ' . $e->getMessage());
        }
    }
	
    /**
     * Send OTP to email and store in database
     *
     * @param string $email
     * @param string $otp
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp($email, $otp, $isSend = false, $isWeb = false)
    {  
		// Send the verification email 
		 
		try {
			Mail::to($email)->send(new OtpEmail($otp));
			
			if(!$isSend)
			{ 
				Otp::create([
					'email_mobile' => $email,
					'otp' => $otp,
					'expires_at' => Carbon::now()->addMinutes(5), // OTP expires after 5 minutes
					'created_at' => now()
				]);
			}
			$responseType =  $isWeb ? 'webSuccessResponse' : 'successResponse';  
			return $this->{$responseType}('OTP sent to your email.');
		} 
		catch (\Exception $e)
		{
			// Log exception
            Log::error('email sending failed due to exception: ' . $e->getMessage());
			$responseType =  $isWeb ? 'webErrorResponse' : 'errorResponse'; 
			return $this->{$responseType}('Failed to send email. Please try again later.');
		}   
    } 
	
	/**
    * Verify the OTP for a given email
    *
    * @param string $email
    * @param string $otp
    * @return \Illuminate\Http\JsonResponse
    */
    public function verifyOtp($email, $otp, $isWeb = false)
    {
        try {
            // Fetch the OTP record for the given mobile number
            $otpRecord = Otp::where('email_mobile', $email)
                            ->where('otp', $otp)
                            ->first();

            // Check if OTP record exists and is not expired
            if ($otpRecord && $otpRecord->expires_at > Carbon::now())
			{ 
                $otpRecord->delete();   
				$responseType =  $isWeb ? 'webSuccessResponse' : 'successResponse';  	
                return $this->{$responseType}('OTP verified successfully.'); 
            } else {
                $responseType =  $isWeb ? 'webErrorResponse' : 'errorResponse'; 
				return $this->{$responseType}('Invalid or expired OTP.');
            }
        } catch (\Throwable $e) {
            // Log error and return response
            Log::error('OTP verification failed: ' . $e->getMessage());
			$responseType =  $isWeb ? 'webErrorResponse' : 'errorResponse'; 
			return $this->{$responseType}('OTP verification failed: ' . $e->getMessage());
        }
    }
}
