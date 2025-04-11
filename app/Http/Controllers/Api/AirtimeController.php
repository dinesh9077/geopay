<?php
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\Country;
	use App\Notifications\AirtimeRefundNotification;
	use App\Models\Transaction;
	use App\Models\User;
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Log;
	use Illuminate\Support\Facades\Validator;
	use App\Http\Traits\ApiResponseTrait;
	use App\Notifications\WalletTransactionNotification;
	use Illuminate\Support\Facades\Notification;
	use Helper;
	use Carbon\Carbon;
	use App\Services\AirtimeService;
	use Barryvdh\DomPDF\Facade\Pdf;
	
	class AirtimeController extends Controller
	{ 
		use ApiResponseTrait;   
		protected $airtimeService;
		public function __construct()
		{
			$this->airtimeService = new AirtimeService(); 
		}	
		
		public function operator()
		{
			try 
			{ 
				$countryCode = $request->country_code;
				$response = $this->airtimeService->getOperators($countryCode); 
				if (!$response['success']) {
					$errorMsg = 'Operator not found.';
					throw new \Exception($errorMsg);
				}
				
				return $this->successResponse('Operator fetched successfully.', $response['response']);
			} 
			catch (\Throwable $e)
			{ 
				return $this->errorResponse($e->getMessage());
			} 
		}
	}
