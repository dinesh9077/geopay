<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;

class RecentTransactions extends Component
{
    public $transactions;

    public function mount()
    {
        // Load recent transactions initially
        $this->transactions = Transaction::orderByDesc('id')->limit(6)->get();
    }

    public function refreshTransactions()
    {
        // Refresh the transactions list
        $this->transactions = Transaction::orderByDesc('id')->limit(6)->get();
    }

    public function render()
    {
        return view('livewire.recent-transactions');
    }
}