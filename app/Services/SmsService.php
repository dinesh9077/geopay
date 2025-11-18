<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Otp;
use App\Models\User;
use App\Models\OtpBlock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class SmsService
{
    use ApiResponseTrait;

    protected $host;
    protected $username;
    protected $password;
    protected $sender;

    // max OTP send attempts in rolling 24-hour window
    protected $dailyLimit = 3;

    public function __construct()
    {
        $this->host = config('setting.sms_host');
        $this->username = config('setting.sms_username');
        $this->password = config('setting.sms_password');
        $this->sender = config('setting.sms_sender');
    }

    /**
     * Normalize mobile number to digits only (keeps country code if present).
     * Example: "+91 98765-43210" -> "919876543210"
     */
    // protected function normalizeNumber(string $number): string
    // {
    //     // Remove non-digit characters
    //     $digits = preg_replace('/\D+/', '', $number);

    //     // Remove leading zeros (optional, kept to avoid duplicates like 0987..)
    //     $digits = ltrim($digits, '0');

    //     // If empty after sanitization, keep original input fallback
    //     return $digits ?: $number;
    // }

    /**
     * DB-backed check for block/limit (rolling 24-hour window).
     * Returns [bool $blocked, string|null $message]
     */
    protected function isBlockedOrExceedLimit(string $destination)
    {
        $normalized = $destination;

        // Check block record
        $block = OtpBlock::where('email_mobile', $normalized)->first();
        if ($block && $block->blocked_until && Carbon::now()->lt($block->blocked_until)) {
            $until = $block->blocked_until->toDateTimeString();
            return [true, "You have reached the maximum OTP attempts. Try again after {$until}"];
        }

        // Count OTPs sent in last 24 hours (rolling window)
        $since = Carbon::now()->subDay();
        $count = Otp::where('email_mobile', $normalized)
            ->where('created_at', '>=', $since)
            ->count();

        if ($count >= $this->dailyLimit) {
            // create or update block for 24 hours
            $blockedUntil = Carbon::now()->addDay();
            if ($block) {
                $block->update([
                    'blocked_until' => $blockedUntil,
                    'attempts' => $count,
                ]);
            } else {
                OtpBlock::create([
                    'email_mobile' => $normalized,
                    'blocked_until' => $blockedUntil,
                    'attempts' => $count,
                ]);
            }

            return [true, "You have reached the maximum OTP attempts. You are blocked for 24 hours."];
        }

        return [false, null];
    }

    /**
     * Resend OTP to a mobile number
     *
     * @param string $destination
     * @param bool $isWeb
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendOtp($destination, $isWeb = false)
    {
        try {
            $normalized = $destination;

            // Block/limit check
            [$blocked, $blockMessage] = $this->isBlockedOrExceedLimit($normalized);
            if ($blocked) {
                $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
                return $this->{$responseType}($blockMessage);
            }

            // Find active OTP (not expired)
            $otpRecord = Otp::where('email_mobile', $normalized)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            if ($otpRecord) {
                $otp = $otpRecord->otp;
            } else {
                // Generate new OTP
                $otp = rand(100000, 999999);

                // Save normalized OTP entry
                Otp::create([
                    'email_mobile' => $normalized,
                    'otp' => $otp,
                    'expires_at' => Carbon::now()->addMinutes(5),
                    'created_at' => now(),
                ]);
            }

            // When resending we set $isSend = true so sendOtp won't duplicate create
            return $this->sendOtp($destination, $otp, $isSend = true, $isWeb);
        } catch (\Throwable $e) {
            Log::error('Resend OTP failed: ' . $e->getMessage(), ['exception' => $e]);
            $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
            return $this->{$responseType}('Resend OTP failed: ' . $e->getMessage());
        }
    }
 
    public function sendOtp($destination, $otp, $isSend = false, $isWeb = false)
    {
        try {
            $normalized = $destination;

            // Double-check block/limit
            [$blocked, $blockMessage] = $this->isBlockedOrExceedLimit($normalized);
            if ($blocked) {
                $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
                return $this->{$responseType}($blockMessage);
            }

            // Construct message
            $message = "Your OTP for login is {$otp}. Please use it to complete your verification. If you didn't request this, please ignore.";

            // Send the GET request to the SMS API
            $response = Http::get(rtrim($this->host, '/') . '/bulksms/bulksms', [
                'username' => $this->username,
                'password' => $this->password,
                'type' => 0,
                'dlr' => 1,
                'destination' => $destination, // send as originally provided to the gateway
                'source' => $this->sender,
                'message' => $message,
            ]);

            if ($response->successful()) {
                // Extract response code (API returns like: 1701|... )
                $responseBody = (string) $response->body();
                $responseParts = explode('|', $responseBody);
                $responseCode = isset($responseParts[0]) ? (int) $responseParts[0] : null;

                if ($responseCode === 1701) {
                    // Only create DB row if not already created (isSend false)
                    // Ensure DB entry uses normalized number
                    if (!$isSend) {
                        Otp::create([
                            'email_mobile' => $normalized,
                            'otp' => $otp,
                            'expires_at' => Carbon::now()->addMinutes(5),
                            'created_at' => now(),
                        ]);
                    }

                    // success response
                    $responseType = $isWeb ? 'webSuccessResponse' : 'successResponse';
                    return $this->{$responseType}('OTP sent to your mobile number.', ['mobile_number' => $destination]);
                }

                // SMS API returned an error code
                Log::error('SMS API error', [
                    'destination' => $destination,
                    'normalized' => $normalized,
                    'otp' => $otp,
                    'responseCode' => $responseCode,
                    'description' => $this->getErrorMessage($responseCode),
                    'raw_response' => $responseBody,
                ]);

                $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
                return $this->{$responseType}('Something went wrong!');
            } else {
                // HTTP call failed (non-2xx)
                Log::error('SMS sending failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'destination' => $destination,
                    'normalized' => $normalized,
                    'otp' => $otp,
                ]);

                $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
                return $this->{$responseType}('SMS sending failed with status: ' . $response->status());
            }
        } catch (Exception $e) {
            Log::error('SMS sending failed due to exception: ' . $e->getMessage(), ['exception' => $e]);
            $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
            return $this->{$responseType}('SMS sending failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify the OTP for a given mobile number
     *
     * @param string $mobile
     * @param string $otp
     * @param bool $isWeb
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp($mobile, $otp, $isWeb = false)
    {
        try {
            $normalized = $mobile;

            // Fetch the OTP record for the given mobile number
            $otpRecord = Otp::where('email_mobile', $normalized)
                ->where('otp', $otp)
                ->first();

            if ($otpRecord && $otpRecord->expires_at > Carbon::now()) {
                // Delete used OTP
                $otpRecord->delete();

                // update user if exists (uses formatted_number field in users table)
                $formatted_number = '+' . ltrim($normalized, '+'); // basic formatting
                $user = User::where('formatted_number', $formatted_number)->first();
                if ($user) {
                    $user->update(['is_mobile_verify' => 1]);
                }

                $responseType = $isWeb ? 'webSuccessResponse' : 'successResponse';
                return $this->{$responseType}('OTP verified successfully.');
            } else {
                $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
                return $this->{$responseType}('Invalid or expired OTP.');
            }
        } catch (\Throwable $e) {
            Log::error('OTP verification failed: ' . $e->getMessage(), ['exception' => $e]);
            $responseType = $isWeb ? 'webErrorResponse' : 'errorResponse';
            return $this->{$responseType}('OTP verification failed: ' . $e->getMessage());
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

        return $errorMessages[$errorCode] ?? 'Unknown error occurred.';
    }
}
