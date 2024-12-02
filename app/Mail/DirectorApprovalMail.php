<?php
	
	namespace App\Mail;
	
	use Illuminate\Bus\Queueable;
	use Illuminate\Mail\Mailable;
	use Illuminate\Queue\SerializesModels;
	
	class DirectorApprovalMail extends Mailable
	{
		use Queueable, SerializesModels;
		
		public $director;
		
		/**
			* Create a new message instance.
		*/
		public function __construct($director)
		{
			$this->director = $director;
		}
		
		/**
			* Build the message.
		*/
		public function build()
		{
			return $this->subject('All Documents Approved')
			->view('emails.director-approval')
			->with([
			'directorName' => $this->director->name,
			'approvedAt' => now(),
			]);
		}
	}
