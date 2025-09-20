<?php
	namespace App\Services;
	
	use Illuminate\Support\Facades\Mail;
	use App\Mail\{
		MobileMoneyTransaction, AddFundsDepositMail, TransferToMobileMail, GeopayToGeopayMail
		InternationalAirtimeMail, TransferToBankTransaction
	}
	
	class TransactionEmailService
	{
		protected $map = [
			'add_funds_mobile'   	=> MobileMoneyTransaction::class,
			'add_funds_deposit'  	=> AddFundsDepositMail::class,
			'transfer_to_mobile' 	=> TransferToMobileMail::class,
			'geopay_to_geopay'   	=> GeopayToGeopayMail::class,
			'international_airtime' => InternationalAirtimeMail::class,
			'transfer_to_bank'   	=> TransferToBankTransaction::class,
		];
		
		public function send($user, $transaction, $transactionModule)
		{
			if (isset($this->map[$transactionModule]))
			{
				$mailableClass = $this->map[$transaction->module];
				Mail::to($user->email)->send(new $mailableClass($user, $transaction));
			}
		}
	}
