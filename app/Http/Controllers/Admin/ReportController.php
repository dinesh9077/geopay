<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Admin;
use App\Services\MasterService;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use DB;
class ReportController extends Controller
{

    protected $masterService;

    public function __construct(MasterService $masterService)
    {
        $this->masterService = $masterService;
    }
 
    public function transactionHistory()
    {
        $users = $this->masterService->getUsers();
		$txnStatuses = Transaction::distinct()->pluck('txn_status'); 
		
        return view('admin.report.transaction-history', compact('users', 'txnStatuses'));
    }

    public function transactionReportAjax(Request $request)
    { 
        if ($request->ajax()) {
            $columns = ['id', 'platform_name', 'platform_provider', 'order_id', 'fees', 'txn_amount', 'unit_convert_exchange', 'comments', 'notes', 'status', 'created_at', 'created_at', 'action'];

            $start = $request->input('start');
            $limit = $request->input('length');
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');
            $search = $request->input('search');

            // $query = Transaction::where('user_id', auth()->user()->id);
            $query = Transaction::with('user');

            if ($request->filled('platform_name')) {
                $query->where('platform_name', $request->platform_name);
            }
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
			}

            if ($request->filled(['start_date', 'end_date'])) {
				if ($request->start_date === $request->end_date) {
					// If both dates are the same, use 'whereDate' for exact match
					$query->whereDate('created_at', $request->start_date);
				} else {
					// Otherwise, use 'whereBetween' for the range
					$query->whereBetween('created_at', [$request->start_date, $request->end_date]);
				}
			}

            if ($request->filled('txn_status')) {
                $query->where('txn_status', $request->txn_status);
            }

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->orWhere('platform_name', 'LIKE', "%{$search}%")
                        ->orWhere('platform_provider', 'LIKE', "%{$search}%")
                        ->orWhere('order_id', 'LIKE', "%{$search}%")
                        ->orWhere('comments', 'LIKE', "%{$search}%")
                        ->orWhere('transaction_type', 'LIKE', "%{$search}%")
                        ->orWhere('notes', 'LIKE', "%{$search}%")
                        ->orWhere('txn_amount', 'LIKE', "%{$search}%")
                        ->orWhere('created_at', 'LIKE', "%{$search}%");
                });
            }

            $totalData = $query->count();
            $totalFiltered = $totalData;

            $values = $query
                ->orderBy($columns[$orderColumnIndex] ?? 'id', $orderDirection)
                ->offset($start)
                ->limit($limit)
                ->get();

            $data = [];
            $i = $start + 1;
            foreach ($values as $value) {

                $data[] = [
                    'id' => $i,
                    'user_name' => $value->user->first_name ?? 'N/A',
                    'platform_name' => $value->platform_name,
                    'platform_provider' => $value->platform_provider,
                    'order_id' => $value->order_id,
                    'fees' => Helper::decimalsprint($value->fees, 2) . ' ' . config('setting.default_currency'),
                    'transaction_type' => '<span class="text-' . ($value->transaction_type == 'debit' ? 'danger' : 'success') . '">' . e($value->transaction_type) . '</span>', 
					'txn_amount' => '<span class="text-' . ($value->transaction_type == 'debit' ? 'danger' : 'success') . '">' . Helper::decimalsprint($value->txn_amount, 2) . ' ' . (config('setting.default_currency') ?? '') . '</span>',  
                    'unit_convert_exchange' => $value->rates ? Helper::decimalsprint($value->rates, 2) : "1.00",
                    'comments' => $value->comments ?? 'N/A',
                    'notes' => $value->notes,
                    'status' => $value->txn_status,
                    'created_at' => $value->created_at->format('M d, Y H:i:s'),
                    'action' => '',
                ];

                $actions = [];
				$actions[] = '<div class="d-flex align-items-center gap-2">';
				
                $actions[] = '<a href="' . route('admin.transaction.receipt', ['id' => $value->id]) . '" class="btn btn-sm btn-primary" onclick="viewReceipt(this, event)" data-toggle="tooltip" data-placement="bottom" title="view receipt">View Receipt</a>';
				
                $actions[] = '<a href="' . route('admin.transaction.receipt-pdf', ['id' => $value->id]) . '" class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="bottom" title="download pdf receipt">Download Pdf</a>';
				
				$actions[] = '</div>';
				
                $data[$i - $start - 1]['action'] = implode(' ', $actions);
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
	
	public function adminLogHistory()
    {
        $users = Admin::all();
		$modules = Activity::select('log_name')->distinct()->get();
		$title = "Admin Log History";
		$causer_type = "admin";
        return view('admin.report.log-history', compact('users', 'title', 'modules', 'causer_type'));
    }
	
	public function userLogHistory()
    {
        $users = $this->masterService->getUsers();
		$modules = Activity::select('log_name')->distinct()->get();
		$title = "User Log History";
		$causer_type = "user";
        return view('admin.report.log-history', compact('users', 'title', 'modules', 'causer_type'));
    }
	
	public function adminUserLogAjax(Request $request)
    { 
        if ($request->ajax()) {
            $columns = ['id', 'created_by', 'log_name', 'description', 'event', 'created_at', 'action'];

            $start = $request->input('start');
            $limit = $request->input('length');
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');
            $search = $request->input('search');
			$causerType = $request->causer_type == "admin" ? Admin::class : User::class;
			
            $query =  Activity::select('activity_log.*', DB::raw("CONCAT(u.first_name, ' ', u.last_name) as created_by"));
			$query->join('users as u','u.id','=','activity_log.causer_id');     
			$query->where('activity_log.causer_type', $causerType);
			
			if ($request->filled('transaction_type')) {
				$query->where('activity_log.log_name', $request->transaction_type);
			}
			
			if ($request->filled('event')) {
				$query->where('activity_log.event', $request->event);
			}
 
			if ($request->filled('user_id')) {
				$query->where('activity_log.causer_id', $request->user_id);
			}
			
			if ($request->filled('start_date') && $request->filled('end_date')) {
				$query->whereBetween(DB::raw('DATE(activity_log.created_at)'), [$request->start_date, $request->end_date]);
			}
			
			if (!empty($search))
			{
				$query = $query->where(function ($que) use ($search) {
					$que->where('u.first_name', 'like', '%' . $search . '%')
					->orWhere('last_name', 'like', '%' . $search . '%')
					->orWhere('module_id', 'like', '%' . $search . '%')
					->orWhere('subject_id', 'like', '%' . $search . '%')
					->orWhere('log_name', 'like', '%' . $search . '%')
					->orWhere('event', 'like', '%' . $search . '%')
					->orWhere('activity_log.created_at', 'like', '%' . $search . '%')
					->orWhere('activity_log.description', 'like', '%' . $search . '%');
				});
			}

            $totalData = $query->count();
            $totalFiltered = $totalData;

            $values = $query 
                ->offset($start)
                ->limit($limit) 
				->orderBy('activity_log.' . $columns[$orderColumnIndex] ?? 'id', $orderDirection)
				->orderBy('activity_log.created_at','desc')->orderBy('activity_log.subject_id','desc')
                ->get();
				  
            
			$data = [];
			if (!empty($values)) {
				$i = $start + 1;
				foreach ($values as $value) {
					$transactionData = [
						'id' => $i,
						'created_by' => $value->created_by,
						'log_name' => '#'.($value->module_id ?? $value->subject_id).'<br>'.$value->log_name,
						'description' => $value->description,
						'event' => $value->event,
						'created_at' => date("Y-m-d H:i:s", strtotime($value->created_at)), 
						'action' => '<a href="'.url('admin/reports/log-view', $value->id).'" onclick="viewProperties(this,event)"><button type="button" class="btn btn-primary">view</button></a>',
					];
					$data[] = $transactionData;
					$i++;
				}
			}

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalData,
                'recordsFiltered' => $totalFiltered,
                'data' => $data,
            ]);
        }
    }
	
	public function adminUserLogView($id)
	{ 
		$activity = Activity::find($id); 

		if (!$activity) {
			return response()->json(['status' => 'error', 'message' => 'Log not found'], 404);
		}

		$json = json_encode($activity->properties, JSON_PRETTY_PRINT);  
		$view = view('admin.report.log-history-view', compact('activity', 'json'))->render(); 

		return response()->json(['status' => 'success', 'view' => $view]);
	}

}
