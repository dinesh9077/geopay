<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransactionLimit;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;

class TransactionLimitController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $user = App::make("authUser"); 
        $userRoleId = $user->user_role_id;

        $transactionLimits = TransactionLimit::where('role_id', $userRoleId)->join('user_roles','user_roles.id','=','transaction_limits.role_id');
        $transactionLimits = $transactionLimits->select('transaction_limits.*','user_roles.role_name')->get();

        $formattedLimits = [];
        foreach ($transactionLimits as $limit) {
            $formattedLimits[$limit->slug] = [
                'id' => $limit->id,
                'role_id' => $limit->role_id,
                'role_name'=>$limit->role_name,
                'daily_max_limit' => $limit->daily_max_limit,
                'monthly_max_limit' => $limit->monthly_max_limit,
                'max_amount_limit' => $limit->max_amount_limit,
                'min_amount_limit' => $limit->min_amount_limit,
            ];
        }
        return $this->successResponse('transaction limits data fetch successfully', 'transaction_limits', $formattedLimits); 
    }

    public function updateTransactionLimits(Request $request)
    {
        // Get the authenticated user
        $user = App::make("authUser");

        // Decode the base64-encoded input data
        $decodedData = json_decode(base64_decode($request->input('data')), true);

        // Validate the decoded data
        if (!$decodedData || !is_array($decodedData)) {
            return $this->errorResponse('Invalid input data.');
        } 

        $validator = Validator::make($decodedData, [
            '*.id' => 'required|integer|exists:transaction_limits,id',
            '*.daily_max_limit' => 'required|numeric',
            '*.monthly_max_limit' => 'required|numeric',
            '*.max_amount_limit' => 'required|numeric',
            '*.min_amount_limit' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }

        DB::beginTransaction();

        try {
            // Loop through each transaction limit and update the data
            foreach ($decodedData as $limitData) {
                TransactionLimit::where('id', $limitData['id'])->update([
                    'daily_max_limit' => $limitData['daily_max_limit'],
                    'monthly_max_limit' => $limitData['monthly_max_limit'],
                    'max_amount_limit' => $limitData['max_amount_limit'],
                    'min_amount_limit' => $limitData['min_amount_limit'],
                ]);
            }

            DB::commit();
            return $this->successResponse('transactionLimits', $decodedData, 'Transaction limits updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update transaction limits.');
        }
    }


    // public function update(Request $request)
    // {
    //     // Decode base64 encoded request data
    //     $decodedData = base64_decode($request->getContent());
    //     $data = json_decode($decodedData, true);

    //     // Validate decoded data
    //     $validatedData = $this->validate($request, [
    //         'role_id' => 'required|string|max:255',
    //         'slug' => 'required|string|max:255',
    //         'daily_max_limit' => 'required|numeric',
    //         'monthly_max_limit' => 'required|numeric',
    //         'max_amount_limit' => 'required|numeric',
    //         'min_amount_limit' => 'required|numeric',
    //     ]);

    //     try {
    //         // Start a database transaction
    //         DB::beginTransaction();

    //         // Find the transaction limit entry
    //         $transactionLimit = TransactionLimit::where('role_id', $data['role_id'])
    //                                            ->where('slug', $data['slug'])
    //                                            ->first();

    //         if (!$transactionLimit) {
    //             // Rollback the transaction if not found
    //             DB::rollBack();
    //             return response()->json(['message' => 'Transaction limit not found'], 404);
    //         }

    //         // Update the transaction limit
    //         $transactionLimit->update($validatedData);

    //         // Commit the transaction
    //         DB::commit();

    //         return response()->json(['message' => 'Transaction limit updated successfully'], 200);
    //     } catch (\Exception $e) {
    //         // Rollback the transaction on error
    //         DB::rollBack();

    //         return response()->json(['message' => 'An error occurred while updating the transaction limit'], 500);
    //     }
    // }
}
