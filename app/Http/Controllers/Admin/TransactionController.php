<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\MasterService;
use Illuminate\Http\Request;
use DB, Validator;
use App\Http\Traits\WebResponseTrait;

class TransactionController extends Controller
{ 
	use WebResponseTrait;	
    protected $masterService; 
    public function __construct(MasterService $masterService)
    {
        $this->masterService = $masterService;
    }

    public function mobileMoneyOnafric()
    {
		$title = "Transaction Mobile Money Onafric";
		$platform_name = "transfer to mobile";
		$platform_provider = "onafric";
		 
		$txnStatuses = Transaction::distinct()
		->where('platform_name', $platform_name)
		->where('platform_provider', $platform_provider)
		->pluck('txn_status'); 
		
		$users = $this->masterService->getUsers();
		
        return view('admin.all-transaction.index', compact('users', 'txnStatuses', 'title', 'platform_name', 'platform_provider'));
    }
	
    public function bankOnafric()
    {
		$title = "Transaction Bank Onafric";
		$platform_name = "transfer to bank";
		$platform_provider = "onafric";
		 
		$txnStatuses = Transaction::distinct()
		->where('platform_name', $platform_name)
		->where('platform_provider', $platform_provider)
		->pluck('txn_status'); 
		
		$users = $this->masterService->getUsers();
		 
        return view('admin.all-transaction.index', compact('users', 'txnStatuses', 'title', 'platform_name', 'platform_provider'));
    }
	
    public function bankLightnet()
    { 
		$title = "Transaction Bank Lightnet";
		$platform_name = "transfer to bank";
		$platform_provider = "lightnet";
		
		$txnStatuses = Transaction::distinct()
		->where('platform_name', $platform_name)
		->where('platform_provider', $platform_provider)
		->pluck('txn_status'); 
		
		$users = $this->masterService->getUsers();
		
        return view('admin.all-transaction.index', compact('users', 'txnStatuses', 'title', 'platform_name', 'platform_provider'));
    }

    public function transactionAjax(Request $request)
    { 
        if ($request->ajax()) {
            $columns = ['id', 'order_id', 'txn_amount', 'comments', 'notes', 'refund_reason', 'status', 'created_at', 'action'];

            $start = $request->input('start');
            $limit = $request->input('length');
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'asc');
            $search = $request->input('search');
            $platform_name = $request->input('platform_name');
            $platform_provider = $request->input('platform_provider');

            // $query = Transaction::where('user_id', auth()->user()->id);
            $query = Transaction::with('user');

            $query->where('platform_name', $platform_name)
			->where('platform_provider', $platform_provider);
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
                    'user_name' => $value->user->first_name,
                    'order_id' => $value->order_id,
                    'transaction_type' => '<span class="text-' . ($value->transaction_type == 'debit' ? 'danger' : 'success') . '">' . e($value->transaction_type) . '</span>', 
					'txn_amount' => '<span class="text-' . ($value->transaction_type == 'debit' ? 'danger' : 'success') . '">' . Helper::decimalsprint($value->txn_amount, 2) . ' ' . (config('setting.default_currency') ?? '') . '</span>',  
                    'comments' => $value->comments ?? 'N/A',
                    'notes' => $value->notes,
                    'refund_reason' => $value->refund_reason,
                    'status' => $value->txn_status,
                    'created_at' => $value->created_at->format('M d, Y H:i:s'),
                    'action' => '',
                ];

                $actions = [];
				$actions[] = '<div class="d-flex align-items-center gap-2">';
				
				if(config("permission.transaction_mobile_money_onafric.add") && $value->txn_status !== "refund" && $value->is_refunded == 0)
				{  
					$actions[] = "<a href='javascript:;' class='btn btn-sm btn-primary' data-transactionId='{$value->id}' onclick='openRefundModal(this, event)' data-toggle='tooltip' data-placement='bottom' title='refund'>Refund</a>";   
				}
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
	
	public function transactionRefund(Request $request)
	{ 
		try {
			DB::beginTransaction(); 
			   
			$validator = Validator::make($request->all(), [
				'transaction_id'  => ['required', 'integer', 'exists:transactions,id'], 
				'refund_reason'   => ['required', 'string', 'max:255'],
				'txn_status'      => ['required', 'string', 'in:refund'], 
				'include_charge'  => ['nullable', 'boolean'],
			]);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
			}

			$transactionId = $request->transaction_id;
			$refundReason = $request->refund_reason;
			$txnStatus = $request->txn_status;
			$includeCharge = $request->include_charge ?? false;  
 
			$transaction = Transaction::findOrFail($transactionId);
 
			$user = User::findOrFail($transaction->user_id); 
			 
			$refundAmount = $includeCharge ? $transaction->txn_amount : ($transaction->txn_amount - $transaction->total_charge); 
			
			$user->increment('balance', $refundAmount); 
 
			$refundTransaction = $transaction->replicate()->toArray();
		 
			$refundTransaction['transaction_type'] = 'credit';
			$refundTransaction['comments'] = "A refund of {$refundAmount} " . config('setting.default_currency') . " has been processed by the admin.";
			$refundTransaction['created_at'] = now();
			$refundTransaction['updated_at'] = now();
			$refundTransaction['refund_reason'] = $refundReason;
			$refundTransaction['is_refunded'] = 0;
			$refundTransaction['txn_status'] = $txnStatus; 
 
			$refundedTransaction = Transaction::create($refundTransaction);
 
			Helper::updateLogName($refundedTransaction->id, Transaction::class, 'Refund Transaction By Admin', auth()->guard('admin')->user()->id);
 
			$transaction->update(['is_refunded' => 1]);
			DB::commit();
			return $this->successResponse('Transaction refund successfully.');
		} catch (\Throwable $e) {
			DB::rollBack(); 
			return $this->errorResponse($e->getMessage());
		}
	}
}
