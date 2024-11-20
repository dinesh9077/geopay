<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExchangeRate;
use DB, Auth, Helper, Hash, Validator;
use App\Http\Traits\WebResponseTrait; 
use PhpOffice\PhpSpreadsheet\Shared\Date; 
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExchangeRateController extends Controller
{
	use WebResponseTrait;
	public function exchangeRate()
	{
		return view('admin.exchange-rate.index'); 
	}
	
	public function exchangeRateAjax(Request $request)
	{
		if ($request->ajax()) {
			// Define the columns for ordering and searching
			$columns = ['id', 'currency', 'exchange_rate', 'created_by', 'created_at'];

			$search = $request->input('search.value'); // Global search value
			$start = $request->input('start'); // Offset for pagination
			$limit = $request->input('length'); // Limit for pagination
			$orderColumnIndex = $request->input('order.0.column', 0);
			$orderDirection = $request->input('order.0.dir', 'asc'); // Default order direction
			
			$type = $request->input('type'); 
			 
			// Base query with relationship for country
			$query = ExchangeRate::with('createdBy:id,name')
				->where('type', $type);
 
			// Apply search filter if present
			if (!empty($search)) {
				$query->where(function ($q) use ($search) {
					$q->where('currency', 'LIKE', "%{$search}%") 
						->orWhereHas('createdBy', function ($q) use ($search) {
							$q->where('name', 'LIKE', "%{$search}%");
						})
						->orWhere('created_at', 'LIKE', "%{$search}%");
				});
			}

			$totalData = $query->count(); // Total records before pagination
			$totalFiltered = $totalData; // Total records after filtering

			// Apply ordering, limit, and offset for pagination
			$values = $query
				->orderBy($columns[$orderColumnIndex] ?? 'id', $orderDirection)
				->offset($start)
				->limit($limit)
				->get();

			// Format data for the response
			$data = [];
			$i = $start + 1;
			foreach ($values as $value) {
				$statusClass = $value->status == 1 ? 'success' : 'danger';
				$statusText = $value->status == 1 ? 'Active' : 'In-Active';

				$data[] = [
					'id' => $i,
					'currency' => $value->currency,
					'exchange_rate' => $value->exchange_rate, 
					'created_by' => $value->createdBy ? $value->createdBy->name : 'N/A', 
					'created_at' => $value->created_at->format('M d, Y H:i:s'),
					'action' => ''
				];
				
				// Manage actions with permission checks
				$actions = [];
				if (config('permission.exchange_rate.delete'))
				{ 
					$actions[] = '<a href="javascript:;" data-url="' . route('admin.exchange-rate.delete', ['id' => $value->id]) . '" data-message="Are you sure you want to delete this item?" onclick="deleteConfirmModal(this, event)" class="btn btn-sm btn-danger">Delete</a>';
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
	
	public function exchangeRateImport()
	{
		$view = view('admin.exchange-rate.import')->render();
		return $this->successResponse('success', ['view' => $view]);
	}	
	
	public function exchangeRateStore(Request $request)
	{ 
		$validator = Validator::make($request->all(), [
			'type' => 'required|in:1,2',
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
			
			foreach ($rows as $row) {
				// Combine headings with row data
				$rowData = array_combine($headings, $row);

				$currency = $rowData['currency'];
				$exchangeRate = $rowData['exchange_rate'];
				 
				// Prepare data for upsert
				$data[] = [
					'type' => $type,
					'currency' => $currency,
					'exchange_rate' => $exchangeRate,
					'admin_id' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				];
			}

			// Process each row and either insert or update based on the combination of currency and type
			foreach ($data as $row) {
				ExchangeRate::updateOrInsert(
					['currency' => $row['currency'], 'type' => $row['type']], // Unique key to check for existing records
					[
						'exchange_rate' => $row['exchange_rate'],
						'admin_id' => $row['admin_id'],
						'updated_at' => now(),
						'created_at' => $row['created_at'],
					]
				);
			}
			
			DB::commit(); 
			return $this->successResponse('File imported successfully.'); 
		} 
		catch (\Throwable $e) 
		{
			// Rollback in case of an exception
			DB::rollBack(); 
			// Return error response with the exception message
			return $this->errorResponse('Failed to update status. ' . $e->getMessage());
		}
	}
	
	public function exchangeRateDelete($exchangeId)
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
		
}
