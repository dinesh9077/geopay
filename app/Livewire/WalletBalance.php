<?php

namespace App\Livewire;  
use Livewire\Component;

class WalletBalance extends Component
{
    public $balance;

    protected $listeners = ['updateBalance'];

    public function mount()
    {
       // Fetch the initial balance (replace with actual logic)
       $this->updateBalance();
    }

    public function updateBalance()
    {
        $this->balance = auth()->user()->balance;
    }

    public function render()
    {
        return view('livewire.wallet-balance');
    }
}
