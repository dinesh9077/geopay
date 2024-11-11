<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRole;
use App\Models\User;
use App\Models\LoginLog;
use App\Models\TransactionLimit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Services\MasterService;

class UserController extends Controller
{
    use ApiResponseTrait;
	protected $masterService;

	public function __construct()
	{
		$this->masterService = new MasterService();
	}

	public function userRoles()
    {
        $roles = $this->masterService->getUserRoles(1);
        return $this->successResponse('User role data fetched successfully.', $roles);
    }

	public function companyList()
    {
        $user = $this->masterService->getCompanies();
        return $this->successResponse('user company details fetch successfully', $user);
    }

	public function userList()
    {
        $user = $this->masterService->getUsers();
        return $this->successResponse('User List fetch successfully', $user);
    }

	public function userData()
	{
		try {
			// Get the authenticated user
			$user = App::make("authUser");

			// Eager load userRole and transactionLimits to minimize database queries
			$userRole = $user->userRole()->with('transactionLimits')->first();

			if (!$userRole) {
				return $this->errorResponse('User role not found.');
			}

			if (!$userRole->transactionLimits->isNotEmpty()) {
				return $this->errorResponse('Transaction limit not found.');
			}

			// Format transaction limits
			$formattedLimits = $userRole->transactionLimits->map(function ($limit) use ($userRole) {
				return [
					'id' => $limit->id,
					'role_id' => $limit->role_id,
					'slug' => $limit->slug,
					'role_name' => $userRole->role_name,
					'daily_max_limit' => $limit->daily_max_limit,
					'monthly_max_limit' => $limit->monthly_max_limit,
					'max_amount_limit' => $limit->max_amount_limit,
					'min_amount_limit' => $limit->min_amount_limit,
				];
			})->groupBy('slug');

			// Prepare response data
			$responseData = [
				'user' => [
					'id' => $user->id,
					'email' => $user->email,
					'balance' => $user->balance,
					'transaction_limit' => $formattedLimits,
				]
			];
			return $this->successResponse('User transaction limit fetched successfully.', $responseData);
		} catch (\Throwable $e) {
			// Handle exceptions
			return $this->errorResponse('An error occurred while fetching user data: ' . $e->getMessage());
		}
	}

    public function update(Request $request)
    {
        $user = App::make("authUser");

        $decodedData = json_decode(base64_decode($request->input('data')), true);

        if (!$decodedData || !is_array($decodedData)) {
            return $this->errorResponse('Invalid input data');
        }

        $validator = Validator::make($decodedData, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }

        DB::beginTransaction();

        try {
            $user->update([
                'first_name' => $decodedData['first_name'],
                'last_name' => $decodedData['last_name']
            ]);

            DB::commit();

            return $this->successResponse('user', $user, 'User data updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->errorResponse('Failed to update user data.');
        }
    }

    public function userStatus(Request $request)
    {
        $user = App::make("authUser");


        if ($user->role !== 'admin') {
            return $this->errorResponse('You are not an authorized person.');
        }

        $decodedData = json_decode(base64_decode($request->input('data')), true);

        if (!$decodedData || !is_array($decodedData)) {
            return $this->errorResponse('Invalid input data.');
        }

        $validator = Validator::make($decodedData, [
            'id' => 'required|string|max:255|exists:users,id',
            'status' => 'required|integer|in:0,1,2,3',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }

        DB::beginTransaction();

        try {

            $updated = User::where('id', $decodedData['id'])->update(['status' => $decodedData['status']]);


            DB::commit();

            if ($updated) {
                $updatedUser = User::find($decodedData['id']);
                return $this->successResponse('user', $updatedUser, 'User status updated successfully');
            } else {

                return $this->errorResponse('User status update failed.');
            }
        } catch (\Throwable $e) {

            DB::rollBack();
            return $this->errorResponse('Failed to update user status.');
        }
    }


    public function getLoginLogs(Request $request)
    {
        $decodedData = json_decode(base64_decode($request->input('data')), true);

        if ($decodedData === null || !is_array($decodedData)) {
            return $this->errorResponse('Invalid input data.');
        }

        $validator = Validator::make($decodedData, [
            'user_id' => 'sometimes|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->validateResponse($validator->errors());
        }

        $query = LoginLog::with(['user' => function ($query) {
            $query->select('id', 'first_name', 'last_name', 'email', 'mobile_number', 'formatted_number', 'role', 'balance');
        }]);



        if (isset($decodedData['user_id'])) {
            $query->where('user_id', $decodedData['user_id']);
        }

        $logs = $query->get();

        return $this->successResponse('login_logs', $logs, 'User login logs fetch successfully');
    }


    public function test()
    {

        $user = App::make("authUser");
        $transactionLimits = TransactionLimit::where('role_id', '1')->get();


        return $this->successResponse('transactionLimits', $transactionLimits, 'transactionLimits fetch successfully');
    }
}
