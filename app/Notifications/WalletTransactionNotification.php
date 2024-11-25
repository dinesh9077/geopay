<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class WalletTransactionNotification extends Notification
{
    use Queueable;

    public $sender;
    public $receiver;
    public $amount;
    public $comment;
    public $notes;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\User  $sender
     * @param  \App\Models\User  $receiver
     * @param  float  $amount
     * @param  string  $notes
     * @return void
     */
    public function __construct(User $sender, User $receiver, $amount, $comment, $notes)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->amount = $amount;
        $this->comment = $comment;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];  
    }
 
    public function toDatabase($notifiable)
    {
		$receiverProfile = $this->receiver->profile_image ? url('storage/profile', $this->receiver->profile_image): '';
        return new DatabaseMessage([
            'sender_name' => $this->sender->first_name.' '.$this->sender->last_name,
            'receiver_name' => $this->receiver->first_name.' '.$this->receiver->last_name,
            'receiver_profile' => $receiverProfile,
            'amount' => $this->amount,
            'comment' => $this->comment,
            'notes' => $this->notes,
            'transaction_status' => 'success',
        ]);
    }
}
