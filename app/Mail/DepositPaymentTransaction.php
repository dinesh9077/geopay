<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DepositPaymentTransaction extends Mailable
{
   use Queueable, SerializesModels;

    public $user;
    public $transaction;
    public $paymentMethod;

    public function __construct($user, $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction; 
        $this->paymentMethod = 'card payment'; 
    }

    public function build()
    {
        return $this->subject('Funds added to wallet via card')->view('emails.add_fund_via_card');
    }
}
