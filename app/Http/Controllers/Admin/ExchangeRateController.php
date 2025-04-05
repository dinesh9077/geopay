<?php
	
	namespace App\Http\Controllers\Admin;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use App\Models\ExchangeRate;
	use App\Models\LightnetCountry;
	use App\Models\LiveExchangeRate;
	use DB, Auth, Helper, Hash, Validator;
	use App\Http\Traits\WebResponseTrait; 
	use PhpOffice\PhpSpreadsheet\Shared\Date; 
	use Maatwebsite\Excel\Facades\Excel;
	use PhpOffice\PhpSpreadsheet\IOFactory; 
	use App\Services\{ 
		LiquidNetService, OnafricService, MasterService
	};
	 
	class ExchangeRateController extends Controller
	{
		use WebResponseTrait;
		protected $liquidNetService;
		protected $onafricService;
		protected $masterService;
		
		public function __construct()
		{
			$this->liquidNetService = new LiquidNetService();
			$this->onafricService = new OnafricService(); 
			$this->masterService = new MasterService(); 
		}
		
		public function manualExchangeRate()
		{
			return view('admin.exchange-rate.manual'); 
		}
		
		public function manualExchangeRateAjax(Request $request)
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
				->where('type', $type);
				 
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
				foreach ($values as $value) {
					$statusClass = $value->status == 1 ? 'success' : 'danger';
					$statusText = $value->status == 1 ? 'Active' : 'In-Active';
					
					$data[] = [
					'id' => $i,
						'created_by' => $value->createdBy ? $value->createdBy->name : 'N/A', 
						'service_name' => ucfirst(str_replace('_', ' ', $value->service_name)),
						'country_name' => $value->country_name,
						'currency' => $value->currency,
						'exchange_rate' => $value->exchange_rate, 
						'aggregator_rate' => $value->aggregator_rate, 
						'markdown_charge' => $value->markdown_charge,  
						'updated_at' => $value->updated_at->format('M d, Y H:i:s'),
						'action' => ''
					];
					
					// Manage actions with permission checks
					$actions = [];
					if (config('permission.manual_exchange_rate.edit'))
					{ 
						$actions[] = '<a href="'.route('admin.manual.exchange-rate.edit', ['id' => $value->id]).'" onclick="editManualRate(this, event)" class="btn btn-sm btn-primary">Edit</a>';
					} 
					if (config('permission.manual_exchange_rate.delete'))
					{ 
						$actions[] = '<a href="javascript:;" data-url="' . route('admin.manual.exchange-rate.delete', ['id' => $value->id]) . '" data-message="Are you sure you want to delete this item?" onclick="deleteConfirmModal(this, event)" class="btn btn-sm btn-danger">Delete</a>';
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
		
		public function manualExchangeRateImport()
		{
			$view = view('admin.exchange-rate.import')->render();
			return $this->successResponse('success', ['view' => $view]);
		}	
		
		public function manualExchangeRateStore(Request $request)
		{ 
			$validator = Validator::make($request->all(), [
			'type' => 'required|in:1,2',
			'service_name' => 'required',
			'file_import' => 'required|mimes:xlsx,csv|max:10240', // max file size 10MB (10240 KB)
			]);
			
			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()); // Returns the first validation error
			}
			
			try
			{
				DB::beginTransaction();
				$admin = auth()->guard('admin')->user();
				$file = $request->file('file_import'); 
				
				$reader = IOFactory::createReaderForFile($file);
				$reader->setReadDataOnly(true);
				$spreadsheet = $reader->load($file); 
				$sheet = $spreadsheet->getActiveSheet(); 
				
				// Get all rows as an array
				$rows = $sheet->toArray();
				 
				// Extract headings from the first row
				$headings = array_shift($rows);
				
				// Prepare data for bulk insert or update
				$data = [];
				$type = $request->type;	
				$serviceName = $request->service_name;	
				
				foreach ($rows as $key => $row) {
					// Combine headings with row data
					$rowData = array_combine($headings, $row);
					 
					$countryName = $rowData['country_name'] ?? '';
					$currency = $rowData['currency'] ?? '';
					$markdown_type = $rowData['markdown_type'] ?? $rowData['markdown_type (flat/percentage)'];
					$markdown_charge = $rowData['markdown_charge'];
					
					$rate = $rowData['aggregator_rate'];
					
					$markdownCharge = $markdown_type === "flat"
							? max($markdown_charge, 0) // Ensure flat fee is non-negative
							: max(($rate * $markdown_charge / 100), 0); // Ensure percentage fee is non-negative
					
					$markdownRate = $rate - $markdownCharge; 
					 
					// Prepare data for upsert
					$data[] = [
						'type' => $type,
						'service_name' => $serviceName,
						'country_name' => $countryName,
						'currency' => $currency,
						'exchange_rate' => $markdownRate,
						'admin_id' => $admin->id,
						'aggregator_rate' => $rate,
						'markdown_type' => $markdown_type,
						'markdown_charge' => $markdown_charge,
						'status' => 1, 
						'created_at' => now(),
						'updated_at' => now(),
					];
				}
				
				// Process each row and either insert or update based on the combination of currency and type
				foreach ($data as $row) 
				{
					ExchangeRate::updateOrInsert(
						['country_name' => $row['country_name'], 'currency' => $row['currency'], 'type' => $row['type']], // Unique key to check for existing records
						[
						'country_name' => $row['country_name'],
						'exchange_rate' => $row['exchange_rate'],
						'admin_id' => $row['admin_id'],
						'aggregator_rate' => $row['aggregator_rate'],
						'markdown_type' => $row['markdown_type'],
						'markdown_charge' => $row['markdown_charge'],
						'status' => $row['status'],
						'service_name' => $row['service_name'],
						'created_at' => $row['created_at'], 
						'updated_at' => $row['updated_at'], 
						]
					);
				}
				Helper::multipleDataLogs('created', ExchangeRate::class, 'Manual Exchange Rate', $module_id = NULL, $data); 
				
				DB::commit(); 
				return $this->successResponse('File imported successfully.'); 
			} 
			catch (\Throwable $e) 
			{ 
				DB::rollBack();  
				return $this->errorResponse('Failed to update status. ' . $e->getMessage());
			}
		}
		
		public function manualExchangeRateEdit($id)
		{
			$manualRate = ExchangeRate::find($id);
			if(!$manualRate)
			{
				return $this->errorResponse('Exchange rate not found!');
			}
			
			$view = view('admin.exchange-rate.manual-rate-edit', compact('manualRate'))->render();
			return $this->successResponse('success', ['view' => $view]);
		}
		
		public function manualExchangeRateUpdate(Request $request, $id)
		{
			$validator = Validator::make($request->all(), [
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
					'exchange_rate' => $markdownRate,
					'aggregator_rate' => $rate,
					'markdown_type' => $request->markdown_type,
					'markdown_charge' => $request->markdown_charge,
					'status' => 1,
					'updated_at' => now(),
				];
				
				$liveRate->update($data);
				DB::commit();
				return $this->successResponse('Exchange rates updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to fetch rates. Error: ' . $e->getMessage());
			}
		}
		
		public function manualExchangeRateDelete($exchangeId)
		{   
			try {
				DB::beginTransaction();
				
				$exchangeRate = ExchangeRate::find($exchangeId);
				if(!$exchangeRate)
				{
					return $this->errorResponse('The exchange rate not found.');
				} 
				$exchangeRate->delete(); 
				DB::commit();
				
				return $this->successResponse('The exchange rate has been delete successfully.');
			}
			catch (\Throwable $e)
			{
				DB::rollBack();
				return $this->errorResponse('Failed to update settings. ' . $e->getMessage());
			}
		}
		
		//Live Exchange Rate 
		public function liveExchangeRate()
		{
			return view('admin.exchange-rate.live'); 
		}
		
		public function liveExchangeRateAjax(Request $request)
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
				foreach ($values as $value) {
					 
					$data[] = [
						'id' => '<input type="checkbox" class="rowCheckbox" data-id="'.$value->id.'">',
						'channel' => $value->channel,
						'country_name' => $value->country_name,
						'currency' => $value->currency,
						'markdown_rate' => $value->markdown_rate, 
						'aggregator_rate' => $value->aggregator_rate, 
						'markdown_charge' => $value->markdown_charge,  
						'updated_at' => $value->updated_at->format('M d, Y'),
						'action' => ''
					];
					
					// Manage actions with permission checks
					$actions = [];
					if (config('permission.live_exchange_rate.edit'))
					{ 
						$actions[] = '<a href="'.route('admin.live.exchange-rate.edit', ['id' => $value->id]).'" onclick="editLiveRate(this, event)" class="btn btn-sm btn-primary">Edit</a>';
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
		
		public function liveExchangeRateFetch(Request $request)
		{
			$channel = $request->channel;

			try {
				DB::beginTransaction();
				
				switch ($channel) {
					case 'lightnet':
						$this->getLightnetRates($channel);
						break;  // Add break to stop execution after this case
					
					case 'onafric':
						$this->getOnafricRates($channel);
						break;  // Add break to stop execution after this case

					default:
						// Optional: Handle unexpected values
						throw new \Exception("Invalid channel: $channel");
				}

				 
				DB::commit();
				return $this->successResponse('Live exchange rates fetched successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to fetch rates. Error: ' . $e->getMessage());
			}
		}
		
		public function getLightnetRates($channel)
		{
			$lightnetCountries = LightnetCountry::where('service_name', $channel)->get(); 
			$currentDate = now()->toDateString();

			foreach ($lightnetCountries as $lightnetCountry) {
				$response = $this->liquidNetService->getRateHistory(
					$lightnetCountry->data,
					$lightnetCountry->value,
					$currentDate
				);
				 
				// Validate response
				if(!$response['success']) 
				{ 
					continue;
				}
				
				if(isset($response['response']['code']) && $response['response']['code'] != 0)
				{
					continue; 
				}
				$rateHistory = $response['response']['rateHistory'][0] ?? null;
				
				if (!$rateHistory) {
					continue; // Skip if no rate history is available
				}

				$rate = $rateHistory['rate'] ?? 0;
				$updatedDate = $rateHistory['updatedDate'] ?? $currentDate;
			
				$countryName = $lightnetCountry->label;
				$currency = $lightnetCountry->value;

				// Fetch existing record or default values
				$liveExchangeRate = LiveExchangeRate::where([
					'channel' => $channel,
					'currency' => $currency,
				])->first();

				$markdownType = $liveExchangeRate->markdown_type ?? 'flat';
				$markdownTypeCharge = $liveExchangeRate->markdown_charge ?? 0;

				// Calculate markdown charge and rate
				$markdownCharge = $markdownType === "flat"
					? max($markdownTypeCharge, 0)
					: max(($rate * $markdownTypeCharge / 100), 0);

				$markdownRate = $rate - $markdownCharge;

				// Upsert the record
				LiveExchangeRate::updateOrCreate(
					[
						'channel' => $channel,
						'currency' => $currency,
					],
					[
						'country_name' => $countryName,
						'markdown_rate' => $markdownRate,
						'aggregator_rate' => $rate,
						'markdown_type' => $markdownType,
						'markdown_charge' => $markdownTypeCharge,
						'status' => 1,
						'updated_at' => $updatedDate,
					]
				);
			}
		}
		
		public function getOnafricRates($channel)
		{ 
			$africanCountries = $this->onafricService->availableCountry(); 
			$countries = $this->masterService->getCountries()->whereIn('nicename', $africanCountries)->values();
			$currentDate = now()->toDateString();
			$defaultCurrency = Config('setting.default_currency') ?? 'USD';
			foreach ($countries as $country) 
			{ 
				$response = $this->onafricService->getRates($defaultCurrency, $country->only('iso', 'currency_code'));
				
				// Validate response
				if(!$response['success']) 
				{ 
					continue;
				}
				 
				$rateHistory = $response['response'];
				

				// Validate rate history
				if (empty($rateHistory) || !isset($rateHistory['fx_rate']) || (int)$rateHistory['fx_rate'] == 0) { 
					continue;
				}
				 
				$rate = $rateHistory['fx_rate'] ?? 0;
				$updatedDate = $rateHistory['updatedDate'] ?? $currentDate;
			
				$countryName = $country->nicename;
				$currency = $country->currency_code;

				// Fetch existing record or default values
				$liveExchangeRate = LiveExchangeRate::where([
					'channel' => $channel, 
					'currency' => $currency,
				])->first();

				$markdownType = $liveExchangeRate->markdown_type ?? 'flat';
				$markdownTypeCharge = $liveExchangeRate->markdown_charge ?? 0;

				// Calculate markdown charge and rate
				$markdownCharge = $markdownType === "flat"
					? max($markdownTypeCharge, 0)
					: max(($rate * $markdownTypeCharge / 100), 0);

				$markdownRate = $rate - $markdownCharge;

				// Upsert the record
				LiveExchangeRate::updateOrCreate(
					[
						'channel' => $channel,
						'currency' => $currency,
					],
					[
						'country_name' => $countryName,
						'markdown_rate' => $markdownRate,
						'aggregator_rate' => $rate,
						'markdown_type' => $markdownType,
						'markdown_charge' => $markdownTypeCharge,
						'status' => 1,
						'updated_at' => $updatedDate,
					]
				);
			}
		}
		
		public function liveExchangeRateEdit($id)
		{
			$liverate = LiveExchangeRate::find($id);
			if(!$liverate)
			{
				return $this->errorResponse('Exchange rate not found!');
			}
			
			$view = view('admin.exchange-rate.edit-rate', compact('liverate'))->render();
			return $this->successResponse('success', ['view' => $view]);
		}
		
		public function liveExchangeRateUpdate(Request $request, $id)
		{
			$validator = Validator::make($request->all(), [
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
					'markdown_rate' => $markdownRate,
					'aggregator_rate' => $rate,
					'markdown_type' => $request->markdown_type,
					'markdown_charge' => $request->markdown_charge,
					'status' => 1,
					'updated_at' => now(),
				];
				
				$liveRate->update($data);
				DB::commit();
				return $this->successResponse('Live exchange rates updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse('Failed to fetch rates. Error: ' . $e->getMessage());
			}
		}
		
		public function liveExchangeRateBulkUpdate(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'markdown_type' => 'required|in:flat,percentage',
				'markdown_charge' => 'required|numeric', 
				'ids' => 'required|array',
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
						'channel' => $exchangeRates->get($id)->channel ?? null,
						'currency' => $exchangeRates->get($id)->currency ?? null,
						'country_name' => $exchangeRates->get($id)->country_name ?? null,
						'markdown_rate' => $markdownRate,
						'aggregator_rate' => $rate,
						'markdown_type' => $request->markdown_type,
						'markdown_charge' => $request->markdown_charge,
						'status' => 1,
						'updated_at' => now(),
					];
					$exchangeData[] = $data;
					
					LiveExchangeRate::updateOrCreate(
						['id' => $data['id']], // Match based on primary key
						$data // Update with this data
					);
				}
				
				Helper::multipleDataLogs('updated', LiveExchangeRate::class, 'Live Exchange Rate', $module_id = NULL, $exchangeData); 
 
				DB::commit();

				return $this->successResponse('Rate margins updated successfully.');
			} catch (\Throwable $e) {
				DB::rollBack(); 
				return $this->errorResponse('Failed to update rates. Please try again later.');
			}
		}
 
	}
