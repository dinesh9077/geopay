<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InternationalAirtimeTransaction extends Mailable
{
   use Queueable, SerializesModels;

    public $user;
    public $transaction;

    public function __construct($user, $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction; 
    }

    public function build()
    {
        return $this->subject('Recharge for '.$this->transaction->api_response['product']['operator']['name'] ?? 'N/A')->view('emails.international_airtime');
    }
}
