<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtp extends Mailable
{
    use Queueable, SerializesModels;
	public $otpCode;
    /**
     * Create a new message instance.
     */
	 
    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }
	
	/**
	* Build the message.
	*
	* @return $this
	*/
    public function build()
    {
        return $this->subject('Password Reset Verification Code')
                    ->view('emails.password_reset_otp')
                    ->with([
                        'otpCode' => $this->otpCode,
                    ]);
    }
}
