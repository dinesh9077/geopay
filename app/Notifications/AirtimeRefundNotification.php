<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AirtimeRefundNotification extends Notification
{
    protected $txnAmount;
    protected $transactionId; 
    protected $comments;
    protected $status;
    protected $notes;
	public $receiver;
  
    // Constructor to initialize notification details
    public function __construct(User $receiver, $txnAmount, $transactionId, $comments, $notes, $status)
    {
		$this->receiver = $receiver;
        $this->txnAmount = $txnAmount;
        $this->transactionId = $transactionId; 
        $this->comments = $comments; 
        $this->notes = $notes; 
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
		$receiverProfile = $this->receiver->profile_image ? url('storage/profile', $this->receiver->profile_image): '';
        return new DatabaseMessage([ 
            'receiver_name' => $this->receiver->first_name.' '.$this->receiver->last_name,
            'receiver_profile' => $receiverProfile,
            'amount' => $this->txnAmount,
            'comment' => $this->comments,
            'notes' => $this->notes,
            'transaction_id' => $this->transactionId, 
            'transaction_status' => $this->status,
        ]);
    } 
    
}
