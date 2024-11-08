<?php 

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Http\Traits\ApiResponseTrait; 
use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SmsService
{ 
    use ApiResponseTrait;

    protected $host;
    protected $username;
    protected $password;
    protected $sender;

    public function __construct()
    {
        // Fetch environment variables from .env file
        $this->host = env('SMS_HOST');
        $this->username = env('SMS_USERNAME');
        $this->password = env('SMS_PASSWORD');
        $this->sender = env('SMS_SENDER');
    }
 
	/**
	* Resend OTP to a mobile number
	*
	* @param string $destination
	* @return \Illuminate\Http\JsonResponse
	*/
	 
    public function resendOtp($destination)
    {
        try {
            // Check if an OTP already exists for the mobile number
            $otpRecord = Otp::where('email_mobile', $destination)
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
                    'email_mobile' => $destination,
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(5), // OTP expires after 5 minutes
                    'created_at' => now(),
                ]);
            }

            // Send the OTP to the user
            return $this->sendOtp($destination, $otp, $isSend = true);
        } catch (\Throwable $e) {
            // Log error and return response
            Log::error('Resend OTP failed: ' . $e->getMessage()); 
            return $this->errorResponse('Resend OTP failed: ' . $e->getMessage());
        }
    }
	
    /**
     * Send OTP to mobile and store in database
     *
     * @param string $destination
     * @param string $otp
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendOtp($destination, $otp, $isSend = false)
    { 
        try {
            // Construct the message
            $message = "Your OTP for login is {$otp}. Please use it to complete your verification. If you didn't request this, please ignore.";
            
            // Send the GET request to the SMS API
            $response = Http::get($this->host.'/bulksms/bulksms', [
                'username' => $this->username,
                'password' => $this->password,
                'type' => 0,
                'dlr' => 1,
                'destination' => $destination,
                'source' => $this->sender,
                'message' => $message,
            ]);
            
            // Check if the request was successful
            if ($response->successful()) 
            {
                // Extract response code
                $responseCode = explode('|', $response->body())[0];
                
                // If successful, store OTP in the database
                if ($responseCode == 1701)
                {
					if(!$isSend)
					{ 
						Otp::create([
							'email_mobile' => $destination,
							'otp' => $otp,
							'expires_at' => Carbon::now()->addMinutes(5), // OTP expires after 5 minutes
							'created_at' => now()
						]);
					}
                      
                    return $this->successResponse('OTP sent to your mobile number.');
                }
                else
                {
                    // Log error with specific API error code
                    Log::error('SMS API error', [
                        'destination' => $destination,
                        'otp' => $otp,
                        'responseCode' => $responseCode,
                        'description' => $this->getErrorMessage($responseCode),
                    ]);
                    
                    // Return the API error message
                    return $this->errorResponse('Something went wrong!');
                }
            } 
            else 
            {
                // Log error for failed request
                Log::error('SMS sending failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'destination' => $destination,
                    'otp' => $otp,
                ]);
                
                return $this->errorResponse('SMS sending failed with status: '.$response->status());
            }
        } 
        catch (\Exception $e)
        {
            // Log exception
            Log::error('SMS sending failed due to exception: ' . $e->getMessage());

            // Handle exception
            return $this->errorResponse('SMS sending failed: ' . $e->getMessage());
        }
    }
	 
    /**
     * Map response code to error message
     *
     * @param int $errorCode
     * @return string
     */
    protected function getErrorMessage($errorCode)
    {
        $errorMessages = [
            1702 => 'Invalid URL. This means that one of the parameters was not provided or left blank.',
            1703 => 'Invalid value in username or password parameter.',
            1704 => 'Invalid value in type parameter.',
            1705 => 'Invalid message.',
            1706 => 'Invalid destination.',
            1707 => 'Invalid source (Sender).',
            1708 => 'Invalid value for dlr parameter.',
            1709 => 'User validation failed.',
            1710 => 'Internal error.',
            1025 => 'Insufficient credit.',
            1715 => 'Response timeout.',
            1032 => 'DND reject.',
            1028 => 'Spam message.',
        ];

        // Return error message if found, otherwise a default message
        return $errorMessages[$errorCode] ?? 'Unknown error occurred.';
    }
	
	/**
    * Verify the OTP for a given mobile number
    *
    * @param string $mobile
    * @param string $otp
    * @return \Illuminate\Http\JsonResponse
    */
    public function verifyOtp($mobile, $otp)
    {
        try {
            // Fetch the OTP record for the given mobile number
            $otpRecord = Otp::where('email_mobile', $mobile)
                            ->where('otp', $otp)
                            ->first();

            // Check if OTP record exists and is not expired
            if ($otpRecord && $otpRecord->expires_at > Carbon::now())
			{ 
                $otpRecord->delete();   
				
				$formatted_number = '+' . ltrim($request->mobile, '+');	 
				$user = User::where('formatted_number', $formatted_number)->first();
				if($user)
				{
					$user->update(['is_mobile_verify' => 1]);
				}
                return $this->successResponse('OTP verified successfully.'); 
            } else {
                return $this->errorResponse('Invalid or expired OTP.');
            }
        } catch (\Throwable $e) {
            // Log error and return response
            Log::error('OTP verification failed: ' . $e->getMessage());
            return $this->errorResponse('OTP verification failed: ' . $e->getMessage());
        }
    }
}
