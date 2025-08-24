<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request; 
	use App\Models\Admin;
	use App\Models\MerchantFund;
	use App\Models\MerchantIpWhitelist;
	use App\Models\MerchantCommission;
	use App\Models\AccessToken;
	use App\Models\ApiCredential;
	use App\Models\ExchangeRate;
	use App\Models\LiveExchangeRate;
	use App\Models\MerchantTransactionLimit;
	use App\Models\MerchantExchangeRate;
	use App\Models\MerchantCorridor;
	use App\Models\LightnetCountry;
	use App\Models\Country;
	use App\Models\User;
	use App\Http\Traits\WebResponseTrait; 
	use Validator, DB, Auth, ImageManager, Hash, Helper;
	use App\Services\MasterService;
	use App\Services\OnafricService;
	use Carbon\Carbon;
	use Storage;
	use Str;
	
	class MerchantController extends Controller
	{
		use WebResponseTrait;
		protected $masterService;
		protected $onafricService; 
		
		public function __construct()
		{
			$this->masterService = new MasterService();
			$this->onafricService = new OnafricService();  
		}
		 
		public function index()
		{ 
			return view('admin.merchant.index');
		}
		
		public function merchantAjax(Request $request)
		{
			if ($request->ajax())
			{
				$columns = ['id', 'company_name', 'name', 'email', 'mobile_number', 'address', 'date_of_birth', 'balance', 'status', 'created_at', 'action'];
				
				$search = $request->input('search.value');
				$start = $request->input('start');
				$limit = $request->input('length');
				
				// Base query
				$query = User::where('is_merchant', 1);
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('first_name', 'LIKE', "%{$search}%")
						->orwhere('last_name', 'LIKE', "%{$search}%")
						->orwhere('company_name', 'LIKE', "%{$search}%")
						->orwhere('email', 'LIKE', "%{$search}%")
						->orwhere('mobile_number', 'LIKE', "%{$search}%")
						->orwhere('address', 'LIKE', "%{$search}%")
						->orwhere('date_of_birth', 'LIKE', "%{$search}%") 
						->orwhere('balance', 'LIKE', "%{$search}%") 
						->orWhere('created_at', 'LIKE', "%{$search}%");
					}); 
				}
				
				$totalData = $query->count();
				$totalFiltered = $totalData;
				
				// Get data with limit and offset for pagination
				$values = $query->offset($start)->limit($limit)
				->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
				->get();
				
				// Format response
				$data = [];
				$i = $start + 1;
				foreach ($values as $key => $value) { 
					// Build the data row
					$appendData = [
						'id' => $i, // Use $key for index 
						'company_name' => $value->company_name, 
						'name' => $value->full_name, 
						'email' => $value->email, 
						'mobile_number' => $value->formatted_number, 
						'address' => $value->address, 
						'date_of_birth' => $value->date_of_birth, 
						'balance' => Helper::decimalsprint($value->balance, 2), 
						'status' => '<span class="badge bg-' . ($value->status == 1 ? 'success' : 'danger') . '">' . ($value->status == 1 ?'Active' : 'In-Active') . '</span>',
						'created_at' => $value->created_at->format('Y-m-d H:i:s'),
						'action' => '',
					];
 
					$actions = [];  
					$dropdown = '
					<div class="btn-group" role="group"> 
						<div class="btn-group" role="group">
							<button id="btnGroupDrop' . $value->id . '" type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Action
							</button>
							<div class="dropdown-menu" aria-labelledby="btnGroupDrop' . $value->id . '">';

							// Edit permission
							if (config('permission.merchant.edit')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.edit', ['id' => $value->id]) . '" onclick="editMerchant(this, event)">Edit</a>';
							}

							// Add Fund
							if (config('permission.merchant_fund.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.fund', ['id' => $value->id]) . '">Add Fund</a>';
							} 
							
							// Ip Whitelist
							if (config('permission.merchant_ip_whitelist.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.ipwhitelist', ['id' => $value->id]) . '">Ip Whitelist</a>';
							} 

							// Api Activate Deactivate
							if (config('permission.api_activate_deactive.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.api-activation', ['id' => $value->id]) . '" onclick="apiActivation(this, event)">Api Activate/Deactivate</a>';
							} 

							// Merchnat Commision
							if (config('permission.merchant_commission.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.commission', ['id' => $value->id]) . '" onclick="merchantCommission(this, event)">Commission Flat/%</a>';
							} 
							
							//Transaction Limit
							if (config('permission.merchant_daily_transaction_limit.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.transaction-limit', ['id' => $value->id]) . '"  onclick="transactionLimit(this, event)">Daily Transaction Limit</a>'; 
							} 
							
							// Fx Rates
							if (config('permission.merchant_exchange_rate.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.exchange-rate', ['id' => $value->id]) . '">Exchange Rate</a>'; 
							} 
							// Country Access Service Wise
							if (config('permission.merchant_corridor_access.view')) {
								$dropdown .= '<a class="dropdown-item" href="' . route('admin.merchant.corridor', ['id' => $value->id]) . '">Corridor Access</a>'; 
							} 

							$dropdown .= '
							</div>
						</div>
					</div>';

					$appendData['action'] = $dropdown;  
 
					$data[] = $appendData;
					$i++;
				}

				
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			}
		}
		
		public function merchantCreate()
		{  
			$countries = Country::all(); 
			$view = view('admin.merchant.create', compact('countries'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		 
		public function merchantStore(Request $request)
		{  
			$validator = Validator::make($request->all(), [ 
				'company_name' => 'required|string', 
				'first_name' => 'required|string', 
				'last_name' => 'required|string', 
				'email' => 'required|email|unique:users,email',
				'password' => [
					'required',
					'string', 
					'min:8',
					function ($attribute, $value, $fail) {
						$errors = [];

						// Check strength rules first
						if (!preg_match('/[A-Z]/', $value)) {
							$errors[] = 'The password must contain at least one uppercase letter.';
						}
						if (!preg_match('/[a-z]/', $value)) {
							$errors[] = 'The password must contain at least one lowercase letter.';
						}
						if (!preg_match('/\d/', $value)) {
							$errors[] = 'The password must contain at least one number.';
						}
						if (!preg_match('/[\W_]/', $value)) {
							$errors[] = 'The password must contain at least one special character.';
						}
	 
						foreach ($errors as $message) {
							$fail($message);
						}
					}
				],
				'country_id' => 'required|integer',
				'mobile_number' => 'required|string|unique:users,mobile_number',   
				'status' => 'required|in:1,0',  
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
			
			try
			{
				DB::beginTransaction();
				  
				$currentTimestamp = Carbon::now();
				
				$country = Country::find($request->country_id); 
				if (!$country) {
					return $this->errorResponse('The country selection is not found.');
				}
				  
				$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			
				$data = collect($request->except('_token', 'password'))
				->map(function ($value) {
					return is_string($value) ? trim($value) : $value;
				})
				->toArray();
				$data['password'] = Hash::make($request->password);
				$data['xps'] = base64_encode($request->password);
				$data['email'] = strtolower($request->email); 
				$data['formatted_number'] = $formattedNumber; 
				$data['date_of_birth'] = $request->filled('date_of_birth') ? $request->input('date_of_birth') : null;
				$data['role'] = "merchant";
				$data['user_role_id'] = 1;
				$data['is_email_verify'] = 1;
				$data['is_mobile_verify'] = 1;
				$data['is_kyc_verify'] = 1;
				$data['is_merchant'] = 1;
				$data['terms'] = 1;
				$data['balance'] = 0;
				$data['created_at'] = $currentTimestamp;
				$data['updated_at'] = $currentTimestamp;  
				
				$user = User::create($data); 
				
				Helper::updateLogName($user->id, User::class, 'Merchant Create');
				DB::commit();
				
				return $this->successResponse('The merchant has been created successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		public function merchantEdit($staffId)
		{
			$merchant = User::find($staffId);
			if(!$merchant)
			{
				return $this->errorResponse('Merchant not found.');
			} 
			$countries = Country::all(); 
			
			$view = view('admin.merchant.edit', compact('merchant', 'countries'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function merchantUpdate(Request $request, $merchantId)
		{  
			$validator = Validator::make($request->all(), [ 
				'company_name' => 'required|string', 
				'first_name' => 'required|string', 
				'last_name' => 'required|string', 
				'email' => 'required|email|unique:users,email,' . $merchantId,
				'password' => [
					'nullable',
					'string', 
					'min:8',
					function ($attribute, $value, $fail) {
						$errors = [];

						// Check strength rules first
						if (!preg_match('/[A-Z]/', $value)) {
							$errors[] = 'The password must contain at least one uppercase letter.';
						}
						if (!preg_match('/[a-z]/', $value)) {
							$errors[] = 'The password must contain at least one lowercase letter.';
						}
						if (!preg_match('/\d/', $value)) {
							$errors[] = 'The password must contain at least one number.';
						}
						if (!preg_match('/[\W_]/', $value)) {
							$errors[] = 'The password must contain at least one special character.';
						}
	 
						foreach ($errors as $message) {
							$fail($message);
						}
					}
				],
				'country_id' => 'required|integer',
				'mobile_number' => 'required|string|unique:users,mobile_number,' . $merchantId, 
				'status' => 'required|in:1,0',  
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}

			try {
				DB::beginTransaction();
				  
				$currentTimestamp = Carbon::now();
				$merchant = User::find($merchantId); 
				
				$country = Country::find($request->country_id); 
				if (!$country) {
					return $this->errorResponse('The country selection is not found.');
				}
				  
				$formattedNumber = '+' . ltrim(($country->isdcode ?? '') . $request->mobile_number, '+');
			
				$data = collect($request->except('_token', 'password'))
				->map(function ($value) {
					return is_string($value) ? trim($value) : $value;
				})
				->toArray();
				
				if(!empty($request->password))
				{
					$data['password'] = Hash::make($request->password);
					$data['xps'] = base64_encode($request->password);
				}
				$data['email'] = strtolower($request->email); 
				$data['formatted_number'] = $formattedNumber; 
				$data['date_of_birth'] = $request->filled('date_of_birth') ? $request->input('date_of_birth') : null;
				$data['role'] = "merchant";
				$data['user_role_id'] = 1;
				$data['is_email_verify'] = 1;
				$data['is_mobile_verify'] = 1;
				$data['is_kyc_verify'] = 1;
				$data['is_merchant'] = 1;
				$data['terms'] = 1; 
				$data['updated_at'] = $currentTimestamp;  
				
				$merchant->update($data);
				DB::commit();

				return $this->successResponse('The merchant details have been updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to update merchant details. ' . $e->getMessage());
			}
		}
		  
		public function merchantDelete($merchantId)
		{
			try
			{
				DB::beginTransaction();  
				user::find($merchantId)->delete();  
				DB::commit();
				return $this->successResponse('The merchant has been successfully deleted.');  
			}
			catch (\Throwable $e)
			{
				DB::rollBack(); 
				return $this->errorResponse('Failed to update merchant details. ' . $e->getMessage()); 
			}
		}
		
		//Merchant Fund
		public function merchantFund()
		{ 
			return view('admin.merchant.fund');
		}
		
		public function merchantFundAjax(Request $request)
		{
			if ($request->ajax()) {
				$columns = [
					'id',
					'amount',
					'payment_mode',
					'transaction_id',
					'date',
					'remarks',
					'receipt',
					'created_at',
				];

				$search = $request->input('search.value');
				$start  = $request->input('start');
				$limit  = $request->input('length');

				// Base query: Get funds with merchant info
				$query = MerchantFund::with('user')
				->where('user_id', $request->user_id);

				// Apply search filter
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('amount', 'LIKE', "%{$search}%")
						  ->orWhere('payment_mode', 'LIKE', "%{$search}%")
						  ->orWhere('transaction_id', 'LIKE', "%{$search}%")
						  ->orWhere('remarks', 'LIKE', "%{$search}%")
						  ->orWhere('date', 'LIKE', "%{$search}%")
						  ->orWhereHas('user', function ($uq) use ($search) {
							  $uq->where('company_name', 'LIKE', "%{$search}%")
								 ->orWhere('first_name', 'LIKE', "%{$search}%")
								 ->orWhere('last_name', 'LIKE', "%{$search}%")
								 ->orWhere('email', 'LIKE', "%{$search}%")
								 ->orWhere('mobile_number', 'LIKE', "%{$search}%");
						  });
					});
				}

				$totalData     = $query->count();
				$totalFiltered = $totalData;

				// Apply pagination + order
				$values = $query->offset($start)
					->limit($limit)
					->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
					->get();

				// Format response
				$data = [];
				$i    = $start + 1;

				foreach ($values as $fund) {
					$appendData = [
						'id'             => $i,
						'amount'         => number_format($fund->amount, 2),
						'payment_mode'   => ucfirst($fund->payment_mode),
						'transaction_id' => $fund->transaction_id,
						'date'           => $fund->date ? $fund->date->format('Y-m-d') : '',
						'remarks'        => $fund->remarks,
						'receipt'        => $fund->receipt ? '<a href="' . asset('storage/' . $fund->receipt) . '" target="_blank">View</a>' : '-', 
						'created_at'     => $fund->created_at->format('Y-m-d H:i:s'),
						'action'         => '',
					];

					$actions = [];
					/* if (config('permission.merchant_fund.delete')) {
						$actions[] = '<a href="javascript:;" data-url="' . route('admin.merchant.fund.delete', ['id' => $fund->id]) . '" data-message="Are you sure you want to delete this item?" onclick="deleteConfirmModal(this, event)" class="btn btn-sm btn-danger">Delete</a>';
					} */

					$appendData['action'] = $actions;

					$data[] = $appendData;
					$i++;
				}

				return response()->json([
					'draw'            => intval($request->input('draw')),
					'recordsTotal'    => $totalData,
					'recordsFiltered' => $totalFiltered,
					'data'            => $data,
				]);
			}
		}
		
		public function merchantFundStore(Request $request)
		{ 
			$validator = Validator::make($request->all(), [
				'user_id' => 'required|integer',
				'amount' => 'required|numeric|min:0.01',
				'payment_mode' => 'required|string',
				'transaction_id' => 'nullable|string',
				'date' => 'required|date',
				'remarks' => 'nullable|string',
				'receipt' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}

			DB::beginTransaction();

			try {
				$data = $request->only(['user_id', 'amount','payment_mode','transaction_id','date','remarks']); 
				
				$user = User::find($request->user_id);
				if(!$user)
				{
					return $this->errorResponse('Merchant Not Found!'); 
				}
				
				if ($request->hasFile('receipt')) {
					$data['receipt'] = $request->file('receipt')->store('receipts', 'public');
				} 
				$user->increment('balance', $request->amount);
				$merchant = MerchantFund::create($data);
				
				Helper::updateLogName($merchant->id, MerchantFund::class, 'Merchant Fund', $merchant->user_id);
				
				DB::commit();
				
				return $this->successResponse('Fund added successfully!'); 

			} catch (\Exception $e) {
				DB::rollBack();

				return $this->errorResponse('Failed to update merchant fund. ' . $e->getMessage()); 
			} 
		}
		
		public function merchantFundDelete($fundId)
		{
			try
			{
				DB::beginTransaction();

				$merchantFund = MerchantFund::findOrFail($fundId);
				$userId = $merchant->user_id;
				// Delete receipt file if exists
				if ($merchantFund->receipt && Storage::disk('public')->exists($merchantFund->receipt)) {
					Storage::disk('public')->delete($merchantFund->receipt);
				}

				// Decrement balance
				$user = User::findOrFail($merchantFund->user_id);
				$user->decrement('balance', $merchantFund->amount);

				// Delete record
				$merchantFund->delete();
				Helper::updateLogName($fundId, MerchantFund::class, 'Merchant Fund', $userId);
				DB::commit();
				return $this->successResponse('The merchant fund has been successfully deleted.');  
			}
			catch (\Throwable $e)
			{
				DB::rollBack(); 
				return $this->errorResponse('Failed to update merchant details. ' . $e->getMessage()); 
			}
		}	
		
		//Merchant IP Whitelist
		public function merchantIpWhitelist()
		{ 
			return view('admin.merchant.ip-whitelist');
		}
		
		public function merchantIpWhitelistAjax(Request $request)
		{
			if ($request->ajax()) {
				$columns = ['id', 'ip_address', 'status', 'created_at','action'];

				$search = $request->input('search.value');
				$start  = $request->input('start');
				$limit  = $request->input('length');

				// Base query: Get funds with merchant info
				$query = MerchantIpWhitelist::with('user')
				->where('user_id', $request->user_id);

				// Apply search filter
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->where('ip_address','like',"%$search%")
						  ->orWhere('created_at','like',"%$search%")
						  ->orWhereHas('user', function ($uq) use ($search) {
							  $uq->where('company_name', 'LIKE', "%{$search}%")
								 ->orWhere('first_name', 'LIKE', "%{$search}%")
								 ->orWhere('last_name', 'LIKE', "%{$search}%")
								 ->orWhere('email', 'LIKE', "%{$search}%")
								 ->orWhere('mobile_number', 'LIKE', "%{$search}%");
						  });
					});
				}

				$totalData   = $query->count();
				$totalFiltered = $totalData;

				// Apply pagination + order
				$values = $query->offset($start)
					->limit($limit)
					->orderBy($columns[$request->input('order.0.column')], $request->input('order.0.dir'))
					->get();

				// Format response
				$data = [];
				$i    = $start + 1;

				foreach ($values as $value) {
					$appendData = [
						'id'             => $i,
						'ip_address'     => $value->ip_address,
						'status'   		 => '<span class="badge bg-' . ($value->status == 1 ? 'success' : 'danger') . '">' . ($value->status == 1 ?'Active' : 'In-Active') . '</span>', 
						'created_at'     => $value->created_at->format('Y-m-d H:i:s'),
						'action'         => '',
					];

					$actions = [];

					if (config('permission.merchant_ip_whitelist.edit')) {
						$actions[] = '<a href="javascript:;" 
										data-id="' . e($value->id) . '" 
										data-ip="' . e($value->ip_address) . '" 
										data-status="' . e($value->status) . '" 
										onclick="editIpWhitelist(this, event)" 
										class="btn btn-sm btn-primary">Edit</a>';
					}

					if (config('permission.merchant_ip_whitelist.delete')) {
						$actions[] = '<a href="javascript:;" 
										data-url="' . e(route('admin.merchant.ipwhitelist.delete', ['id' => $value->id])) . '" 
										data-message="Are you sure you want to delete this item?" 
										onclick="deleteConfirmModal(this, event)" 
										class="btn btn-sm btn-danger">Delete</a>';
					}


					$appendData['action'] = implode(' ', $actions);

					$data[] = $appendData;
					$i++;
				}

				return response()->json([
					'draw'            => intval($request->input('draw')),
					'recordsTotal'    => $totalData,
					'recordsFiltered' => $totalFiltered,
					'data'            => $data,
				]);
			}
		}
		
		public function merchantIpWhitelistStore(Request $request)
		{ 
			$validator = Validator::make($request->all(), [
				'ip_address' => 'required|ip', 
				'user_id'    => 'required|exists:users,id',
				'status'  => 'required|in:1,0',
				'id'         => 'nullable|exists:merchant_ip_whitelists,id', 
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}

			DB::beginTransaction();

			try { 
				$data = collect($request->only(['user_id', 'ip_address', 'status']))
				->map(function ($value) {
					return is_string($value) ? trim($value) : $value;
				})
				->toArray();
				 
				$unique = MerchantIpWhitelist::where('ip_address', $data['ip_address'])->where('user_id', $data['user_id'] ?? null);
				if (!empty($request->id)) { $unique->where('id','!=',$request->id); }
				if ($unique->exists()) {
					return $this->errorResponse('IP already exists for this merchant'); 
				}
 
				if (!empty($request->id)) {
					$ipwhitelist = MerchantIpWhitelist::findOrFail($request->id);
					$ipwhitelist->update($data);
				} else {
					$ipwhitelist = MerchantIpWhitelist::create($data);
				}
				
				Helper::updateLogName($ipwhitelist->id, MerchantIpWhitelist::class, 'Merchant Ip Whitelist', $request->user_id);
				
				DB::commit();
				
				return $this->successResponse('Ip added successfully!'); 

			} catch (\Exception $e) {
				DB::rollBack();

				return $this->errorResponse('Failed to update merchant fund. ' . $e->getMessage()); 
			} 
		}
		
		public function merchantIpWhitelistDelete($ipId)
		{
			try
			{
				DB::beginTransaction();

				$ipwhitelist = MerchantIpWhitelist::findOrFail($ipId);
				$userId = $ipwhitelist->user_id;
				
				$ipwhitelist->delete();
				
				Helper::updateLogName($ipId, MerchantIpWhitelist::class, 'Merchant Ip Whitelist', $userId);
				DB::commit();
				return $this->successResponse('The ip has been successfully deleted.');  
			}
			catch (\Throwable $e)
			{
				DB::rollBack(); 
				return $this->errorResponse('Failed to update merchant details. ' . $e->getMessage()); 
			}
		}
		
		//Api Activation
		public function merchantApiActivation($merchantId)
		{
			$merchant = User::find($merchantId);
			if(!$merchant)
			{
				return $this->errorResponse('Merchant not found.');
			} 
			
			$apiCredential = ApiCredential::where('user_id', $merchant->id)->first();
			
			$view = view('admin.merchant.api-activation', compact('merchant', 'apiCredential'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function merchantApiActivationGenerate(Request $request)
		{   
			$validator = Validator::make($request->all(), [
				'services'   => 'required|array',
				'services.*' => 'in:mobile_money,bank_transfer',
				'environment' => 'required|in:sandbox,production',
				'user_id' => 'required|exists:users,id',
				'status' => 'required|in:active,inactive',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors());
			}
 
			$env = $request->environment;
			$userId = $request->user_id;

			$urls = [
				'sandbox'    => url('/api-test'),
				'production' => url('/api-service')
			];

			DB::beginTransaction();

			try {
				$existing = ApiCredential::where('user_id', $userId)->first();
	   
				if($request->status === "inactive")
				{
					AccessToken::where('user_id', $userId)->delete();
				}
				
				if (!$existing) {
					do {
						$clientId = Str::upper(Str::random(32));
					} while (ApiCredential::where('client_id', $clientId)->exists());
				} else { 
					$clientId = $existing->client_id;
				}
				
				if (!$existing) { 
					do {
						$clientSecret = Str::random(64);
					} while (ApiCredential::where('client_secret', $clientSecret)->exists());
				} else { 
					$clientSecret = $existing->client_secret;
				}
				
				$apiCredential = ApiCredential::updateOrCreate(
					['user_id' => $userId],
					[
						'environment'   => $env,
						'status'        => $request->status,
						'client_id'     => $clientId,
						'client_secret' => $clientSecret,
						'api_url'       => $urls[$env],
						'services' => $request->services ?? [],
					]
				);

				DB::commit();
				
				return $this->successResponse('API credentials generated successfully.', ['client' => $apiCredential]);  
			} catch (\Exception $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to generate API details. ' . $e->getMessage()); 
			}
		}
		
		//Merchant Commission
		public function merchantCommission($merchantId)
		{
			$merchant = User::find($merchantId);
			if(!$merchant)
			{
				return $this->errorResponse('Merchant not found.');
			} 
			
			$mobileMoney = MerchantCommission::where('user_id', $merchant->id)->where('service', 'mobile_money')->first();
			$bankTransfer = MerchantCommission::where('user_id', $merchant->id)->where('service', 'bank_transfer')->first();
			
			$view = view('admin.merchant.commission', compact('merchant', 'mobileMoney', 'bankTransfer'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function merchantCommissionStore(Request $request)
		{   
			$validator = Validator::make($request->all(), [
				'user_id' => 'required|exists:users,id',
				'charge_type.mobile_money' => 'required|in:flat,percentage',
				'charge_type.bank_transfer' => 'required|in:flat,percentage',
				'charge_value.mobile_money'        => 'required|numeric|min:0',
				'status.mobile_money'        => 'required|in:1,0',
				'charge_value.bank_transfer'       => 'required|numeric|min:0',
				'status.bank_transfer'        => 'required|in:1,0',
			]);

			if ($validator->fails()) {
				return $this->errorResponse($validator->errors()->first());
			}
  

			try {
				DB::beginTransaction();

				$merchantId = $request->user_id; // adjust as needed

				foreach (['mobile_money', 'bank_transfer'] as $service) {
					MerchantCommission::updateOrCreate(
						[
							'user_id' => $merchantId,
							'service'     => $service,
						],
						[
							'charge_type' => $request->input("charge_type.$service"),
							'charge_value'        => $request->input("charge_value.$service"),
							'status'        => $request->input("status.$service"),
						]
					);
				}

				DB::commit();
 
				return $this->successResponse('Flat rate settings saved successfully!');  

			} catch (\Exception $e) {
				DB::rollBack();

					return $this->errorResponse('Failed to Flat rate details. ' . $e->getMessage()); 
			}
		}
		
		//Merchant Commission
		public function merchantTransactionLimit($merchantId)
		{
			$merchant = User::find($merchantId);
			if(!$merchant)
			{
				return $this->errorResponse('Merchant not found.');
			} 
			
			$mobileMoneyLimit = MerchantTransactionLimit::where('user_id', $merchant->id)->where('service', 'mobile_money')->value('daily_limit');
			$bankTransferLimit = MerchantTransactionLimit::where('user_id', $merchant->id)->where('service', 'bank_transfer')->value('daily_limit');
			
			$view = view('admin.merchant.transaction-limit', compact('merchant', 'mobileMoneyLimit', 'bankTransferLimit'))->render();
			return $this->successResponse('success', ['view' => $view])	;
		} 
		
		public function merchantTransactionLimitStore(Request $request)
		{   
			$validator = Validator::make($request->all(), [
				'user_id' => 'required|exists:users,id', 
				'limits'  => 'required|array',
				'limits.*' => 'nullable|numeric|min:0',
			]);

			if ($validator->fails()) {
				return $this->errorResponse($validator->errors()->first());
			}
    
			try {
				DB::beginTransaction();

				$merchantId = $request->user_id; // adjust as needed

				foreach ($request->limits as $service => $dailyLimit) {
					MerchantTransactionLimit::updateOrCreate(
						[
							'user_id' => $merchantId,
							'service' => $service
						],
						[
							'daily_limit' => $dailyLimit ?? 0
						]
					);
				}

				DB::commit();
 
				return $this->successResponse('Service limits saved successfully.');  

			} catch (\Exception $e) {
				DB::rollBack(); 
				return $this->errorResponse('Failed to limits details. ' . $e->getMessage()); 
			}
		}
		
		//Merchant Corridor
		public function merchantCorridor($merchantId)
		{
			$merchant = User::find($merchantId);
			if(!$merchant)
			{
				return $this->errorResponse('Merchant not found.');
			}  
			
			$services = []; 
			$services['mobile_money'] = $this->mobileMoneyCountry();
			$services['transafer_bank'] = $this->transferBankCountry();
			
			$merchantCorridor = MerchantCorridor::where('user_id', $merchant->id)
			->get()
			->groupBy('service')
			->map(function ($items) {
				return $items->pluck('payout_country', 'payout_country')->toArray();
			})
			->toArray();
			 
			return view('admin.merchant.corridor-access', compact('services', 'merchant', 'merchantCorridor')); 
		} 
		
		private function transferBankCountry()
		{ 
			$lightnetCountry = LightnetCountry::select(
				'id',
				'data as payout_country',
				'value as payout_currency',
				'label',
				DB::raw("1 as service"),
				DB::raw("'' as iso")
			)
			->whereNotNull('label')
			->where('status', 1)
			->get();
			  
			$onafricCountry = Country::select('id', 'iso3 as payout_country', 'currency_code as payout_currency', 'nicename as label', DB::raw("2 as service_"), 'iso')
			->whereIn('nicename', $this->onafricService->bankAvailableCountry())
			->get();
			 
			$countriesWithFlags = $lightnetCountry->merge($onafricCountry)->sortBy('label')->values(); 
			return $countriesWithFlags;  
		}
		
		private function mobileMoneyCountry()
		{ 
			$africanCountries = $this->onafricService->availableCountry();
			
			$countries = Country::with('channels:id,country_id,channel')
			->select('id', 'nicename as label', 'iso', 'iso3 as payout_country', 'currency_code as payout_currency')
			->whereHas('channels')
			->whereIn('nicename', $africanCountries)
			->get(); 
			  
			return $countries;
		} 
		
		public function merchantCorridorStore(Request $request)
		{
			DB::beginTransaction();

			try {
				$settings = $request->input('settings', []);

				// Example: clear old and reinsert
				MerchantCorridor::where('user_id', $request->user_id)->delete();

				$insertData = [];

				foreach ($settings as $service => $countries) {
					foreach ($countries as $country => $data) {
						if (!empty($data['enabled'])) {
							$insertData[] = [
								'user_id'     => $request->user_id,
								'service'         => $service,
								'payout_country'  => $data['payout_country'],
								'payout_currency' => $data['payout_currency'] ?? null,
								'created_at'      => now(),
								'updated_at'      => now(),
							];
						}
					}
				}

				if (!empty($insertData)) {
					MerchantCorridor::insert($insertData);
				}

				DB::commit();

				return redirect()->back()->with('success', 'Corridor access saved successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();  
				return redirect()->back()->with('error', 'Failed to save corridor access. Please try again.'. $e->getMessage());
			}  
		}
		
		//Merchant Exchange Rate
		public function merchantExchangeRate()
		{ 
			$userId = request('id');
			
			if(!$userId){
				return redirect()->route('admin.merchant.index')->with('error', 'Something went wrong.');
			} 
			 
			return view('admin.merchant.exchange-rate.index', compact('userId'));
		}
		
		public function merchantExchangeRateAjax(Request $request)
		{
			if ($request->ajax()) {
				// Define the columns for ordering and searching
				$columns = ['id', 'channel', 'country_name', 'currency', 'markdown_rate', 'aggregator_rate', 'markdown_charge', 'updated_at', 'action'];
				
				$search = $request->input('search'); // Global search value
				$start = $request->input('start'); // Offset for pagination
				$limit = $request->input('length'); // Limit for pagination
				$orderColumnIndex = $request->input('order.0.column', 0);
				$orderDirection = $request->input('order.0.dir', 'asc'); // Default order direction
				
				$channel = $request->input('channel'); 
				
				// Base query with relationship for country
				$query = LiveExchangeRate::query()
				->where('channel', $channel);
				
				// Apply search filter if present
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->orWhere('currency', 'LIKE', "%{$search}%") 
						->orWhere('country_name', 'LIKE', "%{$search}%") 
						->orWhere('channel', 'LIKE', "%{$search}%")  
						->orWhere('markdown_rate', 'LIKE', "%{$search}%") 
						->orWhere('aggregator_rate', 'LIKE', "%{$search}%") 
						->orWhere('markdown_charge', 'LIKE', "%{$search}%") 
						->orWhere('updated_at', 'LIKE', "%{$search}%");
					});
				}
				
				$totalData = $query->count(); // Total records before pagination
				$totalFiltered = $totalData; // Total records after filtering
				
				// Apply ordering, limit, and offset for pagination
				$values = $query
				->orderBy($columns[$orderColumnIndex] ?? 'id', $orderDirection) 
				->get();
				
				// Format data for the response
				$data = [];
				$i = $start + 1;
				
				$mechantRates = MerchantExchangeRate::where('user_id', $request->user_id)->where('type', 'live')
				->get()
				->keyBy('referance_id');
				
				foreach ($values as $value) {
					 
					$merchantRate = $mechantRates->has($value->id) ? $mechantRates->get($value->id) : null;
					 
					$data[] = [
						'id' => '<input type="checkbox" class="rowCheckbox" data-id="'.$value->id.'">',
						'channel' => $value->channel,
						'country_name' => $value->country_name,
						'currency' => $value->currency,
						'aggregator_rate' => $value->aggregator_rate, 
						'markdown_rate' => $merchantRate ? $merchantRate->markdown_rate : $value->aggregator_rate, 
						'markdown_charge' => $merchantRate ? $merchantRate->markdown_charge : 0, 
						'updated_at' => $value->updated_at->format('M d, Y'),
						'action' => ''
					];
					
					// Manage actions with permission checks
					$actions = [];
					if (config('permission.merchant_exchange_rate.edit'))
					{ 
						$actions[] = '<a href="'.route('admin.merchant.exchange-rate.edit', ['id' => $value->id]).'?user_id='.$request->user_id.'" onclick="editLiveRate(this, event)" class="btn btn-sm btn-primary">Edit</a>';
					} 
					
					// Assign actions to the row if permissions exist
					$data[$i - $start - 1]['action'] = implode(' ', $actions);
					
					$i++;
				}
				
				// Return JSON response
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			} 
		}
		
		public function merchantExchangeRateBulkUpdate(Request $request)
		{ 
			$validator = Validator::make($request->all(), [
				'user_id'        => 'required|integer|exists:users,id',
				'markdown_type'  => 'required|in:flat,percentage',
				'markdown_charge'=> 'required|numeric',
				'ids'            => 'required|array',
				'ids.*'          => 'integer',
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()); // Returns the first validation error
			}

			try {
				DB::beginTransaction();

				$ids = $request->ids;

				// Fetch exchange rates by IDs
				$exchangeRates = LiveExchangeRate::whereIn('id', $ids)->get()->keyBy('id');

				$data = [];
				$exchangeData = [];
				foreach ($ids as $id)
				{
					if (!$exchangeRates->has($id)) {
						continue;
					}

					$rate = $exchangeRates->get($id)->aggregator_rate;

					// Calculate markdown charge based on type
					$markdownCharge = $request->markdown_type === "flat"
						? $request->markdown_charge
						: ($rate * $request->markdown_charge / 100);

					$markdownRate = $rate - $markdownCharge;
					 
 
					$data = [
						'id' => $id,
						'user_id' => $request->user_id,
						'type' => 'live',
						'referance_id' => $id, 
						'markdown_rate' => $markdownRate,
						'markdown_type' => $request->markdown_type,
						'markdown_charge' => $request->markdown_charge,  
						'updated_at' => now(),
					];
					//$exchangeData[] = $data;
					
					MerchantExchangeRate::updateOrCreate(
						[
							'referance_id' => $data['referance_id'],
							'user_id' => $data['user_id'],
							'type' => $data['type']
						], 
						$data  
					);
				}
				
				//Helper::multipleDataLogs('updated', LiveExchangeRate::class, 'Live Exchange Rate', $module_id = NULL, $exchangeData); 
 
				DB::commit();

				return $this->successResponse('Rate margins updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack(); 
				return $this->errorResponse('Failed to update rates. Please try again later.'. $e->getMessage());
			}
		}
		
		public function merchantExchangeRateEdit($exchangeRateId)
		{
			$userId = request('user_id'); 
			$liverate = LiveExchangeRate::with(['merchantRates' => function($q) use ($userId){
				$q->where('user_id', $userId)->limit(1);
			}])
			->find($exchangeRateId);
			
			if(!$liverate)
			{
				return $this->errorResponse('Exchange rate not found!');
			}
			$merchantRate = $liverate->merchantRates->isNotEmpty() ? $liverate->merchantRates->first() : null;
			$view = view('admin.merchant.exchange-rate.edit-rate', compact('liverate', 'userId', 'merchantRate'))->render();
			return $this->successResponse('success', ['view' => $view]);
		}
		
		public function merchantExchangeRateUpdate(Request $request, $id)
		{
			$validator = Validator::make($request->all(), [
				'user_id'        => 'required|integer|exists:users,id',
				'markdown_type' => 'required|in:flat,percentage',
				'markdown_charge' => 'required|numeric', 
				'aggregator_rate' => 'required|numeric',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()); // Returns the first validation error
			}
			
			try {
				DB::beginTransaction();
				 
				// Upsert the record
				$liveRate = LiveExchangeRate::find($id);
				if(!$liveRate)
				{
					return $this->errorResponse('Exchange rate not found!');
				}
				
				$rate = $request->aggregator_rate;
				 
				$markdownCharge = $request->markdown_type === "flat"
						? max($request->markdown_charge, 0) // Ensure flat fee is non-negative
						: max(($rate * $request->markdown_charge / 100), 0); // Ensure percentage fee is non-negative
				
				$markdownRate = $rate - $markdownCharge;
				 
				$data = [ 	
					'user_id' => $request->user_id,
					'referance_id' => $liveRate->id,
					'type' => 'live',
					'markdown_rate' => $markdownRate,
					'markdown_type' => $request->markdown_type,
					'markdown_charge' => $request->markdown_charge,  
					'updated_at' => now(),
				];
				
				MerchantExchangeRate::updateOrCreate(
					[
						'referance_id' => $data['referance_id'],
						'user_id' => $data['user_id'],
						'type' => $data['type']
					], 
					$data  
				);
				 
				DB::commit();
				return $this->successResponse('Exchange rates updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to fetch rates. Error: ' . $e->getMessage());
			} 
		}
		
		//Manual Exchange Rate
		public function merchantExchangeRateManualAjax(Request $request)
		{
			if ($request->ajax()) { 
				$columns = ['id', 'created_by', 'service_name', 'country_name', 'currency', 'exchange_rate', 'aggregator_rate',  'markdown_charge', 'updated_at', 'action'];
				
				$search = $request->input('search');  
				$start = $request->input('start');  
				$limit = $request->input('length'); 
				$orderColumnIndex = $request->input('order.0.column', 0);
				$orderDirection = $request->input('order.0.dir', 'asc'); 
				
				$type = $request->input('type'); 
				 
				$query = ExchangeRate::with('createdBy:id,name')
				->where('type', 2);
				 
				if (!empty($search)) {
					$query->where(function ($q) use ($search) {
						$q->orWhere('currency', 'LIKE', "%{$search}%") 
						->orWhere('service_name', 'LIKE', "%{$search}%") 
						->orWhere('country_name', 'LIKE', "%{$search}%") 
						->orWhere('exchange_rate', 'LIKE', "%{$search}%") 
						->orWhere('aggregator_rate', 'LIKE', "%{$search}%") 
						->orWhere('markdown_charge', 'LIKE', "%{$search}%") 
						->orWhereHas('createdBy', function ($q) use ($search) {
							$q->where('name', 'LIKE', "%{$search}%");
						})
						->orWhere('updated_at', 'LIKE', "%{$search}%");
					});
				}
				
				$totalData = $query->count(); 
				$totalFiltered = $totalData; 
				
				// Apply ordering, limit, and offset for pagination
				$values = $query
				->orderBy($columns[$orderColumnIndex] ?? 'id', $orderDirection) 
				->get();
				
				// Format data for the response
				$data = [];
				$i = $start + 1;
				
				$mechantRates = MerchantExchangeRate::where('user_id', $request->user_id)->where('type', 'manual')
				->get()
				->keyBy('referance_id');
				
				foreach ($values as $value) {
					 
					$merchantRate = $mechantRates->has($value->id) ? $mechantRates->get($value->id) : null;
					
					$statusClass = $value->status == 1 ? 'success' : 'danger';
					$statusText = $value->status == 1 ? 'Active' : 'In-Active';
					
					$data[] = [
						'id' => '<input type="checkbox" class="rowManualCheckbox" data-id="'.$value->id.'">',
						'created_by' => $value->createdBy ? $value->createdBy->name : 'N/A', 
						'service_name' => ucfirst(str_replace('_', ' ', $value->service_name)),
						'country_name' => $value->country_name,
						'currency' => $value->currency,
						'aggregator_rate' => $value->aggregator_rate, 
						'exchange_rate' => $merchantRate ? $merchantRate->markdown_rate : $value->aggregator_rate, 
						'markdown_charge' => $merchantRate ? $merchantRate->markdown_charge : 0,   
						'updated_at' => $value->updated_at->format('M d, Y H:i:s'),
						'action' => ''
					];
					
					// Manage actions with permission checks
					$actions = [];
					if (config('permission.merchant_exchange_rate.edit'))
					{ 
						$actions[] = '<a href="'.route('admin.merchant.exchange-rate.manual-edit', ['id' => $value->id]).'?user_id='.$request->user_id.'" onclick="editManualRate(this, event)" class="btn btn-sm btn-primary">Edit</a>';
					} 
					 
					// Assign actions to the row if permissions exist
					$data[$i - $start - 1]['action'] = implode(' ', $actions);
					
					$i++;
				}
				
				// Return JSON response
				return response()->json([
				'draw' => intval($request->input('draw')),
				'recordsTotal' => $totalData,
				'recordsFiltered' => $totalFiltered,
				'data' => $data,
				]);
			} 
		}
		
		public function merchantExchangeRateManualEdit($exchangeRateId)
		{
			$userId = request('user_id'); 
			$liverate = ExchangeRate::with(['merchantRates' => function($q) use ($userId){
				$q->where('user_id', $userId)->limit(1);
			}])
			->find($exchangeRateId);
			
			if(!$liverate)
			{
				return $this->errorResponse('Exchange rate not found!');
			}
			 
			$merchantRate = $liverate->merchantRates->isNotEmpty() ? $liverate->merchantRates->first() : null;
			
			$view = view('admin.merchant.exchange-rate.manual-edit-rate', compact('liverate', 'userId', 'merchantRate'))->render();
			return $this->successResponse('success', ['view' => $view]);
		}
		
		public function merchantExchangeRateManualUpdate(Request $request, $id)
		{
			$validator = Validator::make($request->all(), [
				'user_id'        => 'required|integer|exists:users,id',
				'markdown_type' => 'required|in:flat,percentage',
				'markdown_charge' => 'required|numeric', 
				'aggregator_rate' => 'required|numeric',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()); // Returns the first validation error
			}
			
			try {
				DB::beginTransaction();
				 
				// Upsert the record
				$liveRate = ExchangeRate::find($id);
				if(!$liveRate)
				{
					return $this->errorResponse('Exchange rate not found!');
				}
				
				$rate = $request->aggregator_rate;
				 
				$markdownCharge = $request->markdown_type === "flat"
						? max($request->markdown_charge, 0) // Ensure flat fee is non-negative
						: max(($rate * $request->markdown_charge / 100), 0); // Ensure percentage fee is non-negative
				
				$markdownRate = $rate - $markdownCharge;
				 
				$data = [ 	
					'user_id' => $request->user_id,
					'referance_id' => $liveRate->id,
					'type' => 'manual',
					'markdown_rate' => $markdownRate,
					'markdown_type' => $request->markdown_type,
					'markdown_charge' => $request->markdown_charge,  
					'updated_at' => now(),
				];
				
				MerchantExchangeRate::updateOrCreate(
					[
						'referance_id' => $data['referance_id'],
						'user_id' => $data['user_id'],
						'type' => $data['type']
					], 
					$data  
				);
				 
				DB::commit();
				return $this->successResponse('Exchange rates updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to fetch rates. Error: ' . $e->getMessage());
			} 
		}
		
		public function merchantExchangeRateBulkManualUpdate(Request $request)
		{ 
			$validator = Validator::make($request->all(), [
				'user_id'        => 'required|integer|exists:users,id',
				'markdown_type'  => 'required|in:flat,percentage',
				'markdown_charge'=> 'required|numeric',
				'ids'            => 'required|array',
				'ids.*'          => 'integer',
			]);
 
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()); // Returns the first validation error
			}

			try {
				DB::beginTransaction();

				$ids = $request->ids;

				// Fetch exchange rates by IDs
				$exchangeRates = ExchangeRate::whereIn('id', $ids)->get()->keyBy('id');

				$data = [];
				$exchangeData = [];
				foreach ($ids as $id)
				{
					if (!$exchangeRates->has($id)) {
						continue;
					}

					$rate = $exchangeRates->get($id)->aggregator_rate;

					// Calculate markdown charge based on type
					$markdownCharge = $request->markdown_type === "flat"
						? $request->markdown_charge
						: ($rate * $request->markdown_charge / 100);

					$markdownRate = $rate - $markdownCharge;
					 
 
					$data = [
						'id' => $id,
						'user_id' => $request->user_id,
						'type' => 'manual',
						'referance_id' => $id, 
						'markdown_rate' => $markdownRate,
						'markdown_type' => $request->markdown_type,
						'markdown_charge' => $request->markdown_charge,  
						'updated_at' => now(),
					];
					//$exchangeData[] = $data;
					
					MerchantExchangeRate::updateOrCreate(
						[
							'referance_id' => $data['referance_id'],
							'user_id' => $data['user_id'],
							'type' => $data['type']
						], 
						$data  
					);
				}
				
				//Helper::multipleDataLogs('updated', LiveExchangeRate::class, 'Live Exchange Rate', $module_id = NULL, $exchangeData); 
 
				DB::commit();

				return $this->successResponse('Rate margins updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack(); 
				return $this->errorResponse('Failed to update rates. Please try again later.'. $e->getMessage());
			}
		}
	}
