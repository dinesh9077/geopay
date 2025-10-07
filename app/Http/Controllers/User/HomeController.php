<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ApiCredential;
use App\Models\Banner;
use App\Models\Transaction;
use App\Http\Traits\WebResponseTrait; 
use Validator, DB, Auth;
use Helper, ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Models\Notification;
use App\Enums\LightnetStatus;
use App\Enums\OnafricStatus;

class HomeController extends Controller
{
	use WebResponseTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */
    public function index()
    {
		// echo '<pre>';
		// print_r(json_decode(auth()->user()->userKyc->meta_response, true));
		// echo '</pre>';
		// die;

        //dd(json_decode(auth()->user()->userKyc->meta_response));
		// Generate QR Code with the mobile number
		
		/* $status = OnafricStatus::from('Accepted')->label();
		echo $status;
		die; */
		
		$user = auth()->user();
		if($user->is_merchant == 1)
		{ 
			$userId = $user->id;
			// Today
			$todayTransactions = Transaction::where('user_id', $userId)
				->whereIn('platform_name', ['transfer to mobile', 'transfer to bank'])
				->whereDate('created_at', today());
			
			$todayStats = [
				'count' => $todayTransactions->count(), 
				'amount' => $todayTransactions->sum('txn_amount'),
				'success' => $todayTransactions->where('txn_status', 'paid')->count(),
				'failed' => $todayTransactions->where('txn_status', 'cancelled and refunded')->count(),
			];
			$todayStats['success_rate'] = $todayStats['count'] > 0 
				? round(($todayStats['success'] / $todayStats['count']) * 100, 2) 
				: 0;

			// Monthly
			$monthTransactions = Transaction::where('user_id', $userId)
				->whereIn('platform_name', ['transfer to mobile', 'transfer to bank'])
				->whereMonth('created_at', now()->month)
				->whereYear('created_at', now()->year);
			
			$monthlyStats = [
				'count' => $monthTransactions->count(),
				'amount' => $monthTransactions->sum('txn_amount'),
				'fee' => $monthTransactions->sum('total_charge'),
			];

			// Service Breakdown
			$serviceWise = Transaction::where('user_id', $userId)
				->whereIn('platform_name', ['transfer to mobile', 'transfer to bank'])
				->select('platform_name', DB::raw('COUNT(*) as total'), DB::raw('SUM(txn_amount) as amount'))
				->groupBy('platform_name')
				->get();

			// Recent 5
			$recent = Transaction::where('user_id', $userId)
				->whereIn('platform_name', ['transfer to mobile', 'transfer to bank'])
				->latest()->take(5)->get();

			$data = [
				'today' => $todayStats,
				'month' => $monthlyStats,
				'services' => $serviceWise,
				'recent' => $recent
			];
	
			return view('user.merchant-home', $data);
		}
		else
		{
			$mobileNumber = json_encode(['mobile_number' => auth()->user()->mobile_number ?? '', 'country_id' => auth()->user()->country_id ?? '']);
			
			$banners = Banner::where('status', 1)->orderByDesc('id')->get();
			return view('user.home', compact('banners', 'mobileNumber'));
		}
    } 
	
	public function notifications()
	{ 
		// Get the authenticated user
		$user = auth()->user();
		
		// Mark all notifications as read
		$user->notifications->markAsRead();
		
		$user->notifications()->where('created_at', '<', Carbon::now()->subDays(2))->delete();
		
		 // Get recent notifications from the last 2 days
		$recentNotifications = $user->notifications()
        ->where('created_at', '>=', Carbon::now()->subDays(2))
		->orderByDesc('id')
        ->get();
		
		return view('user.notification.index', compact('recentNotifications'));
	}
	
	public function apiDocumentation()
	{
		if(auth()->user()->developer_option == 0 || auth()->user()->is_merchant == 0) return abort(403);
		  
        $credential = ApiCredential::with(['user.webhook'])
		->where('user_id', Auth::id()) 
		->latest()
		->first(); 
		return view('user.api-documantation', compact('credential'));
	}
}
