<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MasterService;
use Illuminate\Http\Request;

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
		$txnStatuses = Transaction::select('txn_status')
		->groupBy('txn_status')
		->pluck('txn_status');
        return view('admin.report.transaction-history', compact('users', 'txnStatuses'));
    }

    public function transactionReportAjax(Request $request)
    { 
        if ($request->ajax()) {
            $columns = ['id', 'platform_name', 'order_id', 'fees', 'txn_amount', 'unit_convert_exchange', 'comments', 'notes', 'status', 'created_at', 'created_at', 'action'];

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
                        ->orWhere('order_id', 'LIKE', "%{$search}%")
                        ->orWhere('comments', 'LIKE', "%{$search}%")
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
                    'user_name' => $value->user->first_name,
                    'platform_name' => $value->platform_name,
                    'order_id' => $value->order_id,
                    'fees' => Helper::decimalsprint($value->fees, 2) . ' ' . config('setting.default_currency'),
                    'txn_amount' => Helper::decimalsprint($value->txn_amount, 2) . ' ' . config('setting.default_currency') ?? 0,
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
}
