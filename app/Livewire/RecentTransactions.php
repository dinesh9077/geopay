<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class RecentTransactions extends Component
{
    public $transactions;

    // Define listeners for Livewire events 
	protected $listeners = ['refreshRecentTransactions'];

    public function refreshRecentTransactions()
    {
        // Refresh the component's data
        $this->refreshTransactions();
    }

    public function mount()
    {	
        $this->refreshTransactions(); // Load initial transactions
    }

    public function refreshTransactions()
    {
        // Fetch the authenticated user
        $user = Auth::user();

        // Ensure the user is authenticated
        if ($user) {
            $this->transactions = Transaction::where('receiver_id', $user->id) 
                ->orderByDesc('id')
                ->limit(6)
                ->get(); 
			 
        } else {
            $this->transactions = collect(); // Empty collection if no user
        }
    }

    public function render()
    {
        return view('livewire.recent-transactions');
    }
}
