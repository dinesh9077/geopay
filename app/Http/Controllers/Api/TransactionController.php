<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use App\Models\Transaction;
use App\Models\TransactionLimit;
use App\Models\User;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\App;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\Activity;

class TransactionController extends Controller
{
    use ApiResponseTrait;
    //
    public function store(Request $request)
    {
        $user = App::make("authUser");
        DB::beginTransaction();

        try {

            $decodedData = json_decode(base64_decode($request->input('data')), true);

            if ($decodedData === null || !is_array($decodedData)) {
                return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
            }

            // Validate the decoded data
            $validator = Validator::make($decodedData, [
                'receiver_id' => 'required|string|max:255',
                'invoice_id' => 'required|string|max:255',
                'transaction_id' => 'required',
                'platform_name' => 'required|string|max:255',
                'platform_provider' => 'required|string|max:255',
                'country_id' => 'required|integer',
                'transaction_type' => 'required|string',
                'total_amount' => 'required|numeric',
                'requested_amount' => 'required|numeric',
                'commission_amount' => 'required|numeric',
                'transaction_status' => 'required|string|max:255',
                'comments' => 'nullable|string',
                'remarks' => 'required|string',
                'image' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $totalAmount = $decodedData['total_amount'];
            $balance = $user->balance;

            if ($decodedData['remarks'] != 'add_money' && $totalAmount > $balance) {
                return $this->errorResponse("Insufficient balance.");
            }

            // Check transaction limits
            $transactionLimit = TransactionLimit::where('role_id', $user->user_role_id)
                ->where('slug', $decodedData['remarks'])
                ->first();

            if (!$transactionLimit) {
                return $this->errorResponse("Transaction limit not found.");
            }

            if ($totalAmount < $transactionLimit->min_amount_limit || $totalAmount > $transactionLimit->daily_max_limit) {
                return $this->errorResponse("Check minimum amount limit or daily limit.");
            }

            $todaySum = Transaction::where('transaction_type', $decodedData['transaction_type'])
                ->where('remarks', $decodedData['remarks'])
                ->where('transaction_status', 'success')
                ->whereDate('created_at', now())
                ->sum('total_amount') + $totalAmount;

            if ($todaySum > $transactionLimit->daily_max_limit) {
                return $this->errorResponse("Your daily limit is over.");
            }

            $monthSum = Transaction::where('transaction_type', $decodedData['transaction_type'])
                ->where('remarks', $decodedData['remarks'])
                ->where('transaction_status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount') + $totalAmount;

            if ($monthSum > $transactionLimit->monthly_max_limit) {
                return $this->errorResponse("Your monthly limit is over.");
            }

            if ($decodedData['remarks'] == 'add_money' && isset($decodedData['image'])) {
                $image = base64_decode($decodedData['image']);
                if ($image === false) {
                    return $this->errorResponse('Invalid image data.');
                }

                // Generate a unique filename for screenshot
                $ImageName = Str::random(40) . '.png'; // Adjust the extension based on your actual image format
                $ImagePath = 'transactions/' . $ImageName;
                Storage::disk('public')->put($ImagePath, $ImageName);
                $data['image'] = $ImagePath;
            }

            if ($decodedData['transaction_type'] != 'debit') {
                $current_amount = $balance + $totalAmount;
            } else {
                $current_amount = $balance - $totalAmount;
            }

            $data = [
                'user_id' => $user->id,
                'receiver_id' => $decodedData['receiver_id'],
                'wallet_id' => $user->id,
                'invoice_id' => $decodedData['invoice_id'],
                'platform_name' => $decodedData['platform_name'],
                'platform_provider' => $decodedData['platform_provider'],
                'country_id' => $decodedData['country_id'],
                'transaction_type' => $decodedData['transaction_type'],
                'previous_amount' => $balance,
                'current_amount' => $current_amount,
                'total_amount' => $totalAmount,
                'requested_amount' => $decodedData['requested_amount'],
                'commission_amount' => $decodedData['commission_amount'],
                'transaction_status' => $decodedData['transaction_status'],
                'comments' => $decodedData['comments'],
                'remarks' => $decodedData['remarks'],
                'status' => $decodedData['transaction_status'] ?? 'in-progress',
                'transaction_id' => $decodedData['transaction_id']
            ];

            $transaction = Transaction::create($data);
            activity()
                ->causedBy($transaction)  // Specify who performed the action
                ->performedOn($transaction)  // Specify on which model the action was performed
                ->withProperties(['attributes' => $decodedData]) // Optionally log the old and new password details
                ->log('Transaction updated successfully');

            if ($transaction->transaction_status == 'success') {
                $user_data = ['balance' => $current_amount];
                $transaction->user = User::where('id', $user->id)->update($user_data);
            }


            if ($decodedData['remarks'] == 'wallet_to_wallet_transaction' || $decodedData['remarks'] == 'direct_company_transfer') {
                if ($decodedData['transaction_type'] == 'debit') {
                    $transaction_type = 'credit';
                    $current_amount2 = $balance + $totalAmount;
                } else {
                    $transaction_type = 'debit';
                    $current_amount2 = $balance - $totalAmount;
                }
                $data2 = [
                    'user_id' => $user->id,
                    'receiver_id' => $decodedData['receiver_id'],
                    'wallet_id' => $decodedData['receiver_id'],
                    'invoice_id' => $decodedData['invoice_id'],
                    'platform_name' => $decodedData['platform_name'],
                    'platform_provider' => $decodedData['platform_provider'],
                    'country_id' => $decodedData['country_id'],
                    'transaction_type' => $transaction_type,
                    'previous_amount' => $balance,
                    'current_amount' => $current_amount2,
                    'total_amount' => $totalAmount,
                    'requested_amount' => $decodedData['requested_amount'],
                    'commission_amount' => $decodedData['commission_amount'],
                    'transaction_status' => $decodedData['transaction_status'],
                    'comments' => $decodedData['comments'],
                    'remarks' => $decodedData['remarks'],
                    'status' => $decodedData['transaction_status'] ?? 'in-progress',
                    'transaction_id' => $decodedData['transaction_id']
                ];

                $transaction2 = Transaction::create($data2);

                if ($transaction2->transaction_status == 'success') {
                    $user_data = ['balance' => $current_amount];
                    $transaction2->user = User::where('id', $decodedData['receiver_id'])->update($user_data);
                }
                activity()
                    ->causedBy($transaction2)  // Specify who performed the action
                    ->performedOn($transaction2)  // Specify on which model the action was performed
                    ->withProperties(['attributes' => $decodedData]) // Optionally log the old and new password details
                    ->log('Transaction Reciever updated successfully');
            }



            DB::commit();
            return $this->successResponse('transaction', $transaction, 'Transaction created successfully');
        } catch (Exception $e) {

            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function userTransaction(Request $request)
    {
        $user = App::make("authUser");

        $decodedData = json_decode(base64_decode($request->input('data')), true);

        if ($decodedData === null || !is_array($decodedData)) {
            return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
        }
        $validator = Validator::make($decodedData, [
            'user_id' => 'required|string|max:255|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }

        $user_transactions = Transaction::where('user_id', $decodedData['user_id'])->get();

        $usertransactionList = $user_transactions->transform(function ($user_transaction) {
            if ($user_transaction->image) {
                $user_transaction->image = asset('/storage/' . $user_transaction->image);
            }

            return $user_transaction;
        });
        return $this->successResponse('user_transaction_list', $usertransactionList, 'User transaction fetch successfully');
    }

    public function pendingTransaction()
    {
        $user = App::make("authUser");

        if ($user->role !== 'admin') {
            return $this->errorResponse('You are not an authorized person.');
        }

        $pending_transactions = Transaction::where('transaction_status', 'like', '%in-progress%')->get();

        $pendingtransactionList = $pending_transactions->transform(function ($pending_transaction) {
            if ($pending_transaction->image) {
                $pending_transaction->image = asset('/storage/' . $pending_transaction->image);
            }
            return $pending_transaction;
        });
        return $this->successResponse('pending_transaction_list', $pendingtransactionList, 'Pending transaction fetch successfully');
    }

    public function verifyWalletLimits(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = App::make("authUser");
            $decodedData = json_decode(base64_decode($request->input('data')), true);

            if (!is_array($decodedData)) {
                return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
            }

            $validator = Validator::make($decodedData, [
                'formatted_number' => 'required|string',
                'total_amount' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if ($user->formatted_number == $decodedData['formatted_number']) {
                return $this->errorResponse("You can't add into your wallet so add reciever number.");
            }

            $reciever = User::where('formatted_number', $decodedData['formatted_number'])->first();

            if (!$reciever) {
                return $this->errorResponse("User not found.");
            }

            $totalAmount = $decodedData['total_amount'];
            $balance = $user->balance;

            if ($totalAmount > $balance) {
                return $this->errorResponse("Insufficient balance.");
            }

            $transactionLimit = TransactionLimit::where('role_id', $user->user_role_id)
                ->where('slug', 'wallet_to_wallet_transaction')
                ->first();

            if (!$transactionLimit) {
                return $this->errorResponse("Transaction limit not found.");
            }

            $minLimit = $transactionLimit->min_amount_limit;
            $dailyLimit = $transactionLimit->daily_max_limit;
            $monthlyLimit = $transactionLimit->monthly_max_limit;

            $totalAmount = $decodedData['total_amount'];
            if ($totalAmount < $minLimit || $totalAmount > $dailyLimit) {
                return $this->errorResponse("Check minimum amount limit or daily limit.");
            }

            $todaySum = Transaction::where('transaction_type', 'debit')
                ->where('remarks', 'wallet_to_wallet_transaction')
                ->where('transaction_status', 'success')
                ->whereDate('created_at', now())
                ->sum('total_amount') + $totalAmount;

            if ($todaySum > $dailyLimit) {
                return $this->errorResponse("Your daily limit is over.");
            }

            $monthSum = Transaction::where('transaction_type', 'debit')
                ->where('remarks', 'wallet_to_wallet_transaction')
                ->where('transaction_status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount') + $totalAmount;

            if ($monthSum > $monthlyLimit) {
                return $this->errorResponse("Your monthly limit is over.");
            }

            DB::commit();
            $reciever_data = ['id' => $reciever->id, 'first_name' => $reciever->first_name, 'last_name' => $reciever->last_name, 'email' => $reciever->email, 'formatted_number' => $reciever->formatted_number];

            return $this->successResponse('receiver_data', $reciever_data, 'You are verified to proceed with your transaction.');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Failed to verify transaction', 'details' => $e->getMessage()], 500);
        }
    }

    public function verifyLimits(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = App::make("authUser");
            $decodedData = json_decode(base64_decode($request->input('data')), true);

            if (!is_array($decodedData)) {
                return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
            }

            $validator = Validator::make($decodedData, [
                'transaction_type' => 'required|string',
                'total_amount' => 'required|numeric',
                'remarks' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $transactionLimit = TransactionLimit::where('role_id', $user->user_role_id)
                ->where('slug', $decodedData['remarks'])
                ->first();

            if (!$transactionLimit) {
                return $this->errorResponse("Transaction limit not found.");
            }

            $minLimit = $transactionLimit->min_amount_limit;
            $dailyLimit = $transactionLimit->daily_max_limit;
            $monthlyLimit = $transactionLimit->monthly_max_limit;

            $totalAmount = $decodedData['total_amount'];
            if ($totalAmount < $minLimit || $totalAmount > $dailyLimit) {
                return $this->errorResponse("Check minimum amount limit or daily limit.");
            }

            $todaySum = Transaction::where('transaction_type', $decodedData['transaction_type'])
                ->where('remarks', $decodedData['remarks'])
                ->where('transaction_status', 'success')
                ->whereDate('created_at', now())
                ->sum('total_amount') + $totalAmount;

            if ($todaySum > $dailyLimit) {
                return $this->errorResponse("Your daily limit is over.");
            }

            $monthSum = Transaction::where('transaction_type', $decodedData['transaction_type'])
                ->where('remarks', $decodedData['remarks'])
                ->where('transaction_status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount') + $totalAmount;

            if ($monthSum > $monthlyLimit) {
                return $this->errorResponse("Your monthly limit is over.");
            }

            DB::commit();

            return $this->successResponse('', '', 'You are verified to proceed with your transaction.');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Failed to verify transaction', 'details' => $e->getMessage()], 500);
        }
    }

    public function benificaryAdd(Request $request)
    {
        DB::beginTransaction();

        try {
            $decodedData = json_decode(base64_decode($request->input('data')), true);
            if ($decodedData === null || !is_array($decodedData)) {
                return $this->errorResponse('Invalid input data');
            }
            $validator = Validator::make($decodedData, [
                'user_id' => 'required|string|max:255',
                'type' => 'required|in:1,2',
                'country_id' => 'required|string|max:255',
                'bank_name' => 'required|string',
                'account_number' => 'required|string',
                'b_first_name' => 'required|string',
                'b_middle_name' => 'nullable|string',
                'b_last_name' => 'required|string',
                'b_address' => 'required|string',
                'b_state' => 'nullable|string|max:255',
                'b_mobile' => 'required|string',
                'b_email' => 'required|string|email',
                'relations' => 'required|in:1,2,3',
                'remittance_purpose' => 'required|string',
                'beneficiary_id' => 'required|string',
                'receiver_id_expiry' => 'required|string',
                'receiver_dob' => 'required|string|date',
            ]);


            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $beneficiaryData = $validator->validated();

            if (isset($decodedData['other_remarks'])) {
                $beneficiaryData['other_remarks'] = $decodedData['other_remarks'];
            }

            $beneficiary = Beneficiary::create($beneficiaryData);

            DB::commit();

            return $this->successResponse('beneficiary', $beneficiary, 'Beneficiary add successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getBenificiaryList()
    {

        $user = App::make("authUser");


        $benificiaryList = Beneficiary::where('user_id', $user->id)->get();


        return $this->successResponse('benificiary_list', $benificiaryList, 'Benificiary list data fetch successfully');
    }

    public function searchTransactionList(Request $request, $search)
    {
        $user = App::make("authUser");

        if ($user->role !== 'admin') {
            return $this->errorResponse('You are not an authorized person.');
        }
        echo '<pre>';
        print_r($search);
        echo '</pre>';
        die;

        $decodedData = json_decode(base64_decode($request->input('data')), true);

        echo '<pre>';
        print_r($decodedData);
        echo '</pre>';
        die;

        if ($decodedData === null || !is_array($decodedData)) {
            return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
        }
        $validator = Validator::make($decodedData, [
            'user_id' => 'required|string|max:255|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }


        $pending_transactions = Transaction::where('transaction_status', 'like', '%in-progress%')->get();

        $pendingtransactionList = $pending_transactions->transform(function ($pending_transaction) {
            if ($pending_transaction->image) {
                $pending_transaction->image = asset('/storage/' . $pending_transaction->image);
            }
            return $pending_transaction;
        });
        return $this->successResponse('pending_transaction_list', $pendingtransactionList, 'Pending transaction fetch successfully');
    }

    public function walletTransaction(Request $request)
    {
        $user = App::make("authUser");
        DB::beginTransaction();

        try {

            $decodedData = json_decode(base64_decode($request->input('data')), true);

            if ($decodedData === null || !is_array($decodedData)) {
                return response()->json(['success' => false, 'message' => 'Invalid input data'], 400);
            }

            // Validate the decoded data
            $validator = Validator::make($decodedData, [
                'receiver_id' => 'required|string|max:255',
                'invoice_id' => 'required|string|max:255',
                'transaction_id' => 'required',
                'platform_name' => 'required|string|max:255',
                'platform_provider' => 'required|string|max:255',
                'country_id' => 'required|integer',
                'total_amount' => 'required|numeric',
                'requested_amount' => 'required|numeric',
                'commission_amount' => 'required|numeric',
                'transaction_status' => 'required|string|max:255',
                'comments' => 'nullable|string',
                'remarks' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validateResponse($validator->errors());
            }

            $totalAmount = $decodedData['total_amount'];
            $balance = $user->balance;

            if ($totalAmount > $balance) {
                return $this->errorResponse("Insufficient balance.");
            }
            // Check transaction limits
            $transactionLimit = TransactionLimit::where('role_id', $user->user_role_id)
                ->where('slug', $decodedData['remarks'])
                ->first();

            if (!$transactionLimit) {
                return $this->errorResponse("Transaction limit not found.");
            }

            if ($totalAmount < $transactionLimit->min_amount_limit || $totalAmount > $transactionLimit->daily_max_limit) {
                return $this->errorResponse("Check minimum amount limit or daily limit.");
            }

            $todaySum = Transaction::where('transaction_type', 'debit')
                ->where('remarks', $decodedData['remarks'])
                ->where('transaction_status', 'success')
                ->whereDate('created_at', now())
                ->sum('total_amount') + $totalAmount;

            if ($todaySum > $transactionLimit->daily_max_limit) {
                return $this->errorResponse("Your daily limit is over.");
            }

            $monthSum = Transaction::where('transaction_type', 'debit')
                ->where('remarks', $decodedData['remarks'])
                ->where('transaction_status', 'success')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount') + $totalAmount;

            if ($monthSum > $transactionLimit->monthly_max_limit) {
                return $this->errorResponse("Your monthly limit is over.");
            }




            $current_amount = $balance - $totalAmount;


            $data = [
                'user_id' => $user->id,
                'receiver_id' => $decodedData['receiver_id'],
                'wallet_id' => $user->id,
                'invoice_id' => $decodedData['invoice_id'],
                'platform_name' => $decodedData['platform_name'],
                'platform_provider' => $decodedData['platform_provider'],
                'country_id' => $decodedData['country_id'],
                'transaction_type' => 'debit',
                'previous_amount' => (float) $balance, // Cast to float
                'current_amount' => (float) $current_amount, // Cast to float
                'total_amount' => (float) $totalAmount, // Cast to float
                'requested_amount' => (float) $decodedData['requested_amount'], // Cast to float
                'commission_amount' => (float) $decodedData['commission_amount'], // Cast to float
                'transaction_status' => $decodedData['transaction_status'],
                'comments' => $decodedData['comments'],
                'remarks' => $decodedData['remarks'],
                'status' => $decodedData['transaction_status'] ?? 'in-progress',
                'transaction_id' => $decodedData['transaction_id']
            ];

            $transaction = Transaction::create($data);
            activity()
                ->causedBy($transaction)  // Specify who performed the action
                ->performedOn($transaction)  // Specify on which model the action was performed
                ->withProperties(['attributes' => $decodedData]) // Optionally log the old and new password details
                ->log('Transaction updated successfully');

            if ($transaction->transaction_status == 'success') {
                $user_data = ['balance' => $current_amount];
                $transaction->user = User::where('id', $user->id)->update($user_data);
            }


            if ($decodedData['remarks'] == 'wallet_to_wallet_transaction' || $decodedData['remarks'] == 'direct_company_transfer') {

                $reciever_data = User::find($decodedData['receiver_id']);
                $reciever_balance = $reciever_data->balance;
                $current_amount2 = $reciever_balance + $totalAmount;

                $data2 = [
                    'user_id' => $user->id,
                    'receiver_id' => $decodedData['receiver_id'],
                    'wallet_id' => $decodedData['receiver_id'],
                    'invoice_id' => $decodedData['invoice_id'],
                    'platform_name' => $decodedData['platform_name'],
                    'platform_provider' => $decodedData['platform_provider'],
                    'country_id' => $decodedData['country_id'],
                    'transaction_type' => 'credit',
                    'previous_amount' => (float) $reciever_balance, // Cast to float
                    'current_amount' => (float) $current_amount2, // Cast to float
                    'total_amount' => (float) $totalAmount, // Cast to float
                    'requested_amount' => (float) $decodedData['requested_amount'], // Cast to float
                    'commission_amount' => (float) $decodedData['commission_amount'], // Cast to float
                    'transaction_status' => $decodedData['transaction_status'],
                    'comments' => $decodedData['comments'],
                    'remarks' => $decodedData['remarks'],
                    'status' => $decodedData['transaction_status'] ?? 'in-progress',
                    'transaction_id' => $decodedData['transaction_id']
                ];

                $transaction2 = Transaction::create($data2);

                if ($transaction2->transaction_status == 'success') {
                    $user_data = ['balance' => $current_amount2];
                    $transaction2->user = User::where('id', $decodedData['receiver_id'])->update($user_data);
                }
                activity()
                    ->causedBy($transaction2)  // Specify who performed the action
                    ->performedOn($transaction2)  // Specify on which model the action was performed
                    ->withProperties(['attributes' => $decodedData]) // Optionally log the old and new password details
                    ->log('Transaction Reciever updated successfully');
            }



            DB::commit();
            return $this->successResponse('transaction', $transaction, 'Transaction created successfully');
        } catch (Exception $e) {

            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
    public function getuserTransaction()
    {
        $user = App::make("authUser");

        $user_transactions = Transaction::where('user_id', $user->id)->get();

        $usertransactionList = $user_transactions->transform(function ($user_transaction) {
            if ($user_transaction->image) {
                $user_transaction->image = asset('/storage/' . $user_transaction->image);
            }

            return $user_transaction;
        });
        return $this->successResponse('user_transaction_list', $usertransactionList, 'User transaction fetch successfully');
    }
}
