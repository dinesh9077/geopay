<?php
	
	namespace App\Models;
	
	use Illuminate\Database\Eloquent\Factories\HasFactory;
	use Illuminate\Database\Eloquent\Model;
	use Spatie\Activitylog\Traits\LogsActivity;
	use Spatie\Activitylog\LogOptions;
	
	class CompanyDetail extends Model
	{
		use HasFactory, LogsActivity;
		
		protected $table = 'company_details';
		
		protected $fillable = [
        'user_id', 
        'business_licence', 
        'tin', 
        'vat', 
        'company_address', 
        'postcode', 
        'bank_name', 
        'account_number', 
        'bank_code',
        'step_number',
        'business_type_id',
        'no_of_director',
        'is_update_kyc'
		];
		
		protected static $recordEvents = ['created', 'deleted', 'updated'];
		
		public function getActivitylogOptions(string $logName = 'company details'): LogOptions
		{  
			$user_name = auth()->check() ? auth()->user()->name : 'Unknown User'; // Fixed ternary operator
			return LogOptions::defaults()
			->logOnly(['*', 'user.name'])
			->logOnlyDirty()
			->dontSubmitEmptyLogs()
			->useLogName($logName)
			->setDescriptionForEvent(function (string $eventName) use ($logName, $user_name) {
				return "The {$logName} has been {$eventName} by {$user_name}";
			});
		}
		
		public function user()
		{
			return $this->belongsTo(User::class);
		}
		
		public function companyDocuments()
		{
			return $this->hasMany(CompanyDocument::class, 'company_details_id');
		}
		
		public function businessTypes()
		{
			return $this->belongsTo(BusinessType::class, 'business_type_id');
		}
		
		public function companyDirectors()
		{
			return $this->hasMany(CompanyDirector::class, 'company_details_id');
		}
		
		public function isUploadDocuments()
		{
			// Ensure the current instance of CompanyDetail has related directors and documents
			if ($this && $this->companyDirectors->isNotEmpty()) {
				
				// Fetch all active document types
				$documentTypes = DocumentType::where('status', 1)->get();
				
				// Group company documents by director ID and document type ID
				$groupedDocuments = $this->companyDocuments
				->groupBy(function ($doc) {
					return $doc->company_director_id . '_' . $doc->document_type_id;
				});
				
				// Check if all required documents are uploaded for each director
				foreach ($this->companyDirectors as $companyDirector) {
					foreach ($documentTypes as $documentType) {
						$key = $companyDirector->id . '_' . $documentType->id;
						
						// If any required document is missing, return false
						if (!$groupedDocuments->has($key)) {
							return false;
						}
					}
				}
				
				// If all required documents are uploaded for all directors, return true
				return true;
			}
			
			// If no directors or invalid CompanyDetail instance, return false
			return false;
		}
		
	}
