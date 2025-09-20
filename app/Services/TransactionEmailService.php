<?php
	namespace App\Services;
	
	use Illuminate\Support\Facades\Mail;
	use App\Mail\{
		MobileMoneyTransaction, DepositPaymentTransaction, TransferToMobileTransaction, GeopayToGeopayTransaction,
		InternationalAirtimeTransaction, TransferToBankTransaction
	};
	
	class TransactionEmailService
	{
		protected $map = [
			'add_funds_mobile'   	=> MobileMoneyTransaction::class,
			'add_funds_deposit'  	=> DepositPaymentTransaction::class,
			'transfer_to_mobile' 	=> TransferToMobileTransaction::class,
			'geopay_to_geopay'   	=> GeopayToGeopayTransaction::class,
			'international_airtime' => InternationalAirtimeTransaction::class,
			'transfer_to_bank'   	=> TransferToBankTransaction::class,
		];
		
		public function send($user, $transaction, $transactionModule)
		{
			if (isset($this->map[$transactionModule])) 
			{
				$mailableClass = $this->map[$transactionModule]; 
				$email = $transactionModule === "geopay_to_geopay" ? $user->send_email: $user->email; 
				Mail::to($email)->send(new $mailableClass($user, $transaction)); 
			}
		} 
	}
