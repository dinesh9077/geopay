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
		// Generate QR Code with the mobile number
		$mobileNumber = auth()->user()->formatted_number ?? '';
        
		$banners = Banner::where('status', 1)->orderByDesc('id')->get();
        return view('user.home', compact('banners', 'mobileNumber'));
    }
	
	public function fetchNotifications()
    {
        $user = Auth::user();
		
		$unreadNotificationCount = $user->unreadNotifications()->count();
		
        $notifications = $user->notifications()->latest()->limit(6)->get();

        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['comment'] ?? 'No details provided',
                'time' => $notification->created_at->diffForHumans(),
                'image' => $notification->data['sender_image'] ?? 'https://via.placeholder.com/30x30',
            ];
        });
 
		return $this->successResponse('success', compact('formattedNotifications', 'unreadNotificationCount'));
    }
}
