<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApiCredential; 
use App\Models\LiveExchangeRate; 
use App\Models\AccessToken; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;

class ApiCredentialController extends Controller
{
	public function __construct()
	{ 
		$this->middleware('auth');
	}	
	
    public function index()
    {
		if(auth()->user()->developer_option == 0 || auth()->user()->is_merchant == 0) return abort(403);
		  
        $credential = ApiCredential::with(['user.webhook'])
		->where('user_id', Auth::id()) 
		->latest()
		->first();
		 
        return view('user.api-credentials', compact('credential'));
    }
	 
	public function store(Request $request)
	{  
		$request->validate([
			'environment' => 'required|in:sandbox,production',
		]);

		$env = $request->environment;
		$userId = Auth::id();

		$urls = [
			'sandbox'    => url('/api-test'),
			'production' => url('/api-service')
		];

		DB::beginTransaction();

		try {
			$existing = ApiCredential::where('user_id', $userId)->first();
 
			if ($existing && $existing->status === 'inactive') {
				return redirect()->back()->with('error', 'Credential issuance failed: The associated account is inactive. For further assistance, please contact your system administrator.');
			}
			
			AccessToken::where('user_id', $userId)->delete();
			 
			if (!$existing) {
				do {
					$clientId = Str::upper(Str::random(32));
				} while (ApiCredential::where('client_id', $clientId)->exists());
			} else { 
				$clientId = $existing->client_id;
			}
 
			do {
				$clientSecret = Str::random(64);
			} while (ApiCredential::where('client_secret', $clientSecret)->exists());
			
			ApiCredential::updateOrCreate(
				['user_id' => $userId],
				[
					'environment'   => $env,
					'status'        => 'active',
					'client_id'     => $clientId,
					'client_secret' => $clientSecret,
					'api_url'       => $urls[$env]
				]
			);

			DB::commit();

			return redirect()->back()->with('success', $existing ? 'Client secret regenerated successfully.' : 'API credentials generated successfully.');
		} catch (\Exception $e) {
			DB::rollBack();
			return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
		}
	} 
	
	public function exchangeRate()
    {
		$user = auth()->user(); 
		if($user->is_merchant == 0) return abort(403);
		
		$allowedCurrencies = $user->merchantCorridors->pluck('payout_currency')->unique()->toArray();
		
		$exchangeRates = LiveExchangeRate::with(['merchantRates' => function($q) use ($user){
			$q->where('user_id', $user->id)->limit(1);
		}])
		->whereIn('channel', ['lightnet', 'onafric'])
		->whereIn('currency', $allowedCurrencies)
		->get();
		 
        return view('user.exchange-rate', compact('exchangeRates'));
    }
	
	public function corridorAccess()
    {
		$user = auth()->user(); 
		if($user->is_merchant == 0) return abort(403);
		
		$corridors = $user->merchantCorridors;  
        return view('user.corridor', compact('corridors'));
    }
}