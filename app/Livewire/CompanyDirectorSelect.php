<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CompanyDirector;  
use App\Models\DocumentType; 

class CompanyDirectorSelect extends Component
{
    public $companyDirectors = [];  
    public $companyDetailId;   
	
	// Define listeners for Livewire events 
	protected $listeners = ['refreshCompanyDirectorSelect'];

    public function refreshCompanyDirectorSelect()
    {
        // Refresh the component's data
        $this->loadCompanyDirectors();
    }
	
   /**
     * Lifecycle method called when the component is mounted.
     */
    public function mount($companyDetailId)
    {
        $this->companyDetailId = $companyDetailId; 
        $this->loadCompanyDirectors();
    }

    /**
     * Fetch company directors with missing documents.
     */
    public function loadCompanyDirectors()
    {
        // Get all active document type IDs
        $allDocumentIds = DocumentType::where('status', 1)->pluck('id')->toArray();
		
        // Fetch all directors for the specified company along with their documents
        $directors = CompanyDirector::where('company_details_id', $this->companyDetailId)
            ->with('documents')
            ->get();

        // Filter directors to find those missing required documents
        $directorsWithMissingDocs = $directors->filter(function ($director) use ($allDocumentIds) {
            // Get the document type IDs this director has already uploaded
            $uploadedDocumentIds = $director->documents->pluck('document_type_id')->toArray();

            // Check for missing document type IDs
            $missingDocumentIds = array_diff($allDocumentIds, $uploadedDocumentIds);

            return !empty($missingDocumentIds); // Include the director if any documents are missing
        });

		$response = [];
		foreach($directorsWithMissingDocs as $director)
		{
			$response[] = [
					'id' => $director->id,
					'name' => $director->name,
				];
		} 
		 
        // Transform the filtered directors into the desired response structure
        $this->companyDirectors = $response;
    }

    /**
     * Render the Livewire view.
     */
    public function render()
    {
        return view('livewire.company-director-select');
    } 
}
