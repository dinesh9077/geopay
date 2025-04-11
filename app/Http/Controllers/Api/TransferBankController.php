<?php
	namespace App\Http\Controllers\Api;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{
		DB, Auth, Log, Validator, Notification
	};
	use App\Models\{
		Country, Transaction, Beneficiary, User, 
		LightnetCatalogue, LiveExchangeRate, ExchangeRate, 
		LightnetCountry
	};
	use App\Http\Traits\ApiResponseTrait;
	use App\Services\{
		LiquidNetService, OnafricService
	};
	use App\Notifications\WalletTransactionNotification;
	use Carbon\Carbon;
	use Helper;  
 
	class TransferBankController extends Controller
	{ 
		use ApiResponseTrait;  
		protected $liquidNetService;
		protected $onafricService;
		public function __construct()
		{
			$this->liquidNetService = new LiquidNetService(); 
			$this->onafricService = new OnafricService();  
		} 
		
		public function countryList()
		{
			$lightnetCountry = LightnetCountry::select('id', 'data', 'value', 'label', 'service_name', 'status', 'created_at', 'updated_at', 'markdown_type', 'markdown_charge', DB::raw("'' as iso"))
			->whereNotNull('label')
			->get(); 

			$onafricCountry = Country::select('id', 'iso3 as data', 'currency_code as value', 'nicename as label', DB::raw("'onafric' as service_name"), DB::raw("1 as status"), 'created_at', 'updated_at', DB::raw("'flat' as markdown_type"), DB::raw("0 as markdown_charge"), 'iso')
			->whereIn('nicename', $this->onafricService->bankAvailableCountry())
			->get();

			// Merge both collections
			$countries = $lightnetCountry->merge($onafricCountry)->sortBy('label')->values();
			return $this->successResponse('country fetched successfully.', $countries);
		}
	}
