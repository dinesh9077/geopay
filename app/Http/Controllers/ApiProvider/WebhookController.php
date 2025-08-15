<?php
	namespace App\Http\Controllers\ApiProvider;
	
	use App\Http\Controllers\Controller;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\{
		DB, Auth, Log, Validator
	};
	use App\Models\{
		WebhookRegister, User, Transaction
	}; 
	use App\Http\Traits\ApiServiceResponseTrait;
	use Str;
	
	class WebhookController extends Controller
	{ 
		use ApiServiceResponseTrait;  
		  
		public function webhookRegister(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'callback_url' => 'required|url|starts_with:https://',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}

			try {
				DB::beginTransaction();

				$userId = auth()->id();

				$webhook = WebhookRegister::updateOrCreate(
					['user_id' => $userId],
					[
						'url'        => $request->callback_url,
						'secret'     => WebhookRegister::where('user_id', $userId)->value('secret') ?? Str::random(40),
						'status'     => 'active',
						'updated_at' => now(),
						'created_at' => now(),
					]
				);

				DB::commit();

				return $this->successResponse('Webhook registered successfully.', [
					'url'    => $webhook->url,
					'secret' => $webhook->secret
				]);

			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse($e->getMessage(), 'ERR_INTERNAL_SERVER');
			}
		}
		
		public function webhookDelete(Request $request)
		{
			$validator = Validator::make($request->all(), [
				'secret' => 'required|string',
			]);

			if ($validator->fails()) {
				return $this->validateResponse($validator->errors()->toArray());
			}

			try {
				DB::beginTransaction();

				$userId = auth()->id();
		
				$webhook = WebhookRegister::where('user_id', $userId)->where('secret', $request->secret)->first();
				if(!$webhook)
				{
					return $this->successResponse('Webhook not found.', 'ERR_NOT_FOUND');
				}
				$webhook->delete();
				
				DB::commit();

				return $this->successResponse('Webhook deleted successfully.', []);

			} catch (\Throwable $e) {
				DB::rollBack();
				return $this->errorResponse($e->getMessage(), 'ERR_INTERNAL_SERVER');
			}
		} 
	}
