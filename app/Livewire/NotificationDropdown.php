<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications = [];
    public $unreadCount = 0; 

    public function mount()
    {
       $this->fetchUnreadNotifications();
    }

    public function fetchUnreadNotifications()
    { 
        $user = Auth::user();

        // Fetch unread notifications 
		$allNotifications = $user->notifications()->latest()->limit(6)->get();
        // Format notifications for display
        $this->notifications =  $allNotifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['comment'] ?? 'No details provided',
                'time' => $notification->created_at->diffForHumans(),
                'image' => $notification->data['sender_image'] ?? 'https://via.placeholder.com/30x30', // Adjust sender_image field
            ];
        })->toArray();

        $this->unreadCount = $user->unreadNotifications()->count();
    }
  
    public function render()
    {
        return view('livewire.notification-dropdown');
    }
}
