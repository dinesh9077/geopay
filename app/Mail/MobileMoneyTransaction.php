<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MobileMoneyTransaction extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $transaction;

    public function __construct($user, $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
        $this->paymentMethod = 'Mobile Money';
    }

    public function build()
    {
        return $this->subject('Funds Added via Mobile Money')->view('emails.add_funds_mobile_money');
    }
}
