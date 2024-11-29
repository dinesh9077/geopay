<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\CompanyDetail;
use App\Models\CompanyDocument;

class CompanyKycTab extends Component
{
    public $directorId;
    public $userId;
    public $companyDocuments = [];
    public $companyDirectors;
	public $activeTab = 0;  // Add this line to track the active tab
	public $isDirectors = true;  // Add this line to track the active tab
 
    public function mount($userId)
    {
        $this->userId = $userId;
        $this->loadDirectorTab(); 
    }
	
    public function loadDirectorTab()
    { 
       $companyDetail = CompanyDetail::where('user_id', $this->userId)->first();
	   $this->isDirectors = $companyDetail->companyDirectors->isNotEmpty() ? true : false;
	   $this->companyDirectors = $companyDetail->companyDirectors;  
	   
	   $this->loadDocumentsData($companyDetail->companyDirectors[0]->id, $this->activeTab);
    }
	
    public function loadDocumentsData($directorId, $key)
    { 
		$this->activeTab = $key;
        // Fetch documents grouped by document_type_id
        $companyDocuments = CompanyDocument::with('documentType')
            ->where('company_director_id', $directorId)
            ->orderBy('document_type_id')
            ->get()
            ->groupBy('document_type_id');

        $this->companyDocuments = $companyDocuments->map(function ($documents) {
            return $documents->map(function ($document) {
                return $document;
            })->toArray();
        })->toArray();
    }
 
    public function render()
    {
        return view('livewire.company-kyc-tab');
    }
}
