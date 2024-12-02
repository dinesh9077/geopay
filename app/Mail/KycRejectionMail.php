<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KycRejectionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $directorName;
    public $rejectedDocuments;

    /**
     * Create a new message instance.
     *
     * @param string $directorName
     * @param array $rejectedDocuments
     */
    public function __construct($directorName, $rejectedDocuments)
    {
        $this->directorName = $directorName;
        $this->rejectedDocuments = $rejectedDocuments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('KYC Rejection Notification')
                    ->view('emails.kyc-rejection')
                    ->with([
                        'directorName' => $this->directorName,
                        'rejectedDocuments' => $this->rejectedDocuments,
                    ]);
    }
}
