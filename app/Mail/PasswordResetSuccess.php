<?php
	namespace App\Mail;
	
	use Illuminate\Bus\Queueable;
	use Illuminate\Mail\Mailable;
	use Illuminate\Queue\SerializesModels;
	use Illuminate\Contracts\Queue\ShouldQueue;
	
	class PasswordResetSuccess extends Mailable
	{
		use Queueable, SerializesModels;
		
		public $user;
		
		/**
			* Create a new message instance.
			*
			* @return void
		*/
		public function __construct($user)
		{
			$this->user = $user;
		}
		
		/**
			* Build the message.
			*
			* @return $this
		*/
		public function build()
		{
			return $this->subject('Password Reset Successful')
			->view('emails.password_reset_success')
			->with([
				'userName' => $this->user ? $this->user->first_name. ' ' .$this->user->last_name : 'Unknown user',
			]);
		}
	}
	
