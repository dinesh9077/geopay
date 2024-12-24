<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AirtimeRefundNotification extends Notification
{
    protected $txnAmount;
    protected $transactionId; 
    protected $comments;
    protected $status;

    // Constructor to initialize notification details
    public function __construct($txnAmount, $transactionId, $comments, $status)
    {
        $this->txnAmount = $txnAmount;
        $this->transactionId = $transactionId; 
        $this->comments = $comments; 
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // You can also use 'mail' for email notifications
    }

    /**
     * Get the database notification representation.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->comments,
            'txn_amount' => $this->txnAmount,
            'transaction_id' => $this->transactionId, 
            'status' => $this->status,
        ];
    } 
    
}
