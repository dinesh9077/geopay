<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;

class UserDetailCard extends Component
{
	public $balance;
    public $deposits;
    public $withdrawals;
    public $totalTransaction;
    public $company;
	
	protected $listeners = ['refreshData'];
    public function mount($company)
    {  
		$this->company = $company; 
		
        $this->balance = $company->balance;
        $this->deposits = $company->depositAmount(); 
        $this->withdrawals = $company->withdrawAmount();
        $this->totalTransaction = $company->totalTransaction();   
    }
	
	public function refreshData()
    {
		$this->company = User::find($this->company->id);
		
        $this->balance = $this->company->balance;
		$this->deposits = $this->company->depositAmount(); 
        $this->withdrawals = $this->company->withdrawAmount();
        $this->totalTransaction = $this->company->totalTransaction();
    }
    public function render()
    {
        return view('livewire.admin.user-detail-card');
    }
}
