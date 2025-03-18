<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Banner;
use App\Http\Traits\WebResponseTrait; 
use Validator, DB, Auth;
use Helper, ImageManager;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;
use App\Models\Notification;

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
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        // CURLOPT_URL => 'https://onafriqtest.com/mttest/services/XPService.XPServiceHttpSoap11Endpoint/',
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => '',
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 0,
        // CURLOPT_FOLLOWLOCATION => true,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => 'GET',
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;

		// Generate QR Code with the mobile number
		$mobileNumber = auth()->user()->formatted_number ?? '';
        
		$banners = Banner::where('status', 1)->orderByDesc('id')->get();
        return view('user.home', compact('banners', 'mobileNumber'));
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
}
