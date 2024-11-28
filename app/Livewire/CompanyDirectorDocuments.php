<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CompanyDetail;
use App\Models\CompanyDirector;
use App\Models\DocumentType;

class CompanyDirectorDocuments extends Component
{
    public $companyDetailId;
    public $companyDetail;
    public $documentTypes;
    public $companyDirectors;
    public $companyDocuments;
	 
	// Define listeners for Livewire events 
	protected $listeners = ['refreshCompanyDocuments'];

    public function refreshCompanyDocuments()
    {
        // Refresh the component's data
        $this->loadCompanyDocument();
    }
	
    // Mount the component
    public function mount($companyDetailId)
    {
        $this->companyDetailId = $companyDetailId; 
        $this->loadCompanyDocument();
    }
	
	// Load the company data
    public function loadCompanyDocument()
    {
        // Fetch company details along with directors and their associated documents
        $this->companyDetail = CompanyDetail::with(['companyDirectors','companyDocuments'])->find($this->companyDetailId);
        $this->companyDirectors = $this->companyDetail->companyDirectors;
        $this->companyDocuments = $this->companyDetail->companyDocuments;
        $this->documentTypes = DocumentType::where('status', 1)->get(); // Get active document types
    }
	
	
    // Render the component's view
    public function render()
    {
        return view('livewire.company-director-documents');
	}
}
