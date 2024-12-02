<div>
    @if($companyDetail && $companyDirectors->isNotEmpty())
		@php
			// Group documents by director ID and document type ID
			$groupedDocuments = $companyDocuments
				->groupBy(function ($doc) {
					return $doc->company_director_id . '_' . $doc->document_type_id;
				}); 
		@endphp
        @foreach($companyDirectors as $companyDirector)
			@php
				// Check if all documents are uploaded for this director 
				$allDocumentsUploaded = true;
				$allDocumentsApproved = true;
				$hasPendingOrRejected = false;
				foreach($documentTypes as $documentType) 
				{
					$key = $companyDirector->id . '_' . $documentType->id; 
					if (!$groupedDocuments->has($key)) {
						$allDocumentsUploaded = false;
						break;
					}
					
					// Check the status of the uploaded document
					$documentStatus = $groupedDocuments[$key][0]['status'];

					if ($documentStatus != 1) {
						$allDocumentsApproved = false; // Document not approved
						if ($documentStatus == 2) {
							$hasPendingOrRejected = true; // Document is pending or rejected
						}
					}
				}
			@endphp
            <div class="card card-body {{ $allDocumentsUploaded ? 'border-success' : '' }}">
                <div class="mb-4">
                    <h5 class="heading-6 fw-normal mb-2">
                        {{ $companyDirector->name }} Documents 
                        
						@if($allDocumentsUploaded) 
							@if($allDocumentsApproved) 
								<span class="badge bg-success">Approved</span> 
							@elseif($hasPendingOrRejected)
								<span class="badge bg-danger">Attention Required</span> 
							@else    
								<span class="badge bg-success">Document Uploaded</span> 
							@endif
						@else
							<span class="badge bg-warning">Pending</span>
						@endif
                    </h5>

                    <ul class="p-0">
                        @foreach($documentTypes as $documentType)
							@php
								$key = $companyDirector->id . '_' . $documentType->id;
								 
							@endphp
							<li class="content-3 text-muted mb-2">
								<div class="d-flex justify-content-between">
									<span class="d-flex">
										@if($groupedDocuments->has($key))
											@if($groupedDocuments[$key][0]['status'] == 1)
												<i id="check_{{ $companyDirector->id }}_{{ $documentType->id }}" class="bi bi-check-circle-fill text-success me-2"></i>
											@elseif($groupedDocuments[$key][0]['status'] == 2)
												<i id="check_{{ $companyDirector->id }}_{{ $documentType->id }}" class="bi bi-x-circle-fill text-danger opacity-50 me-2 not_completed"></i> 
											@else 
												<i id="check_{{ $companyDirector->id }}_{{ $documentType->id }}" class="bi bi-file-earmark-check-fill text-success opacity-50 me-2"></i>
											@endif 
										@else
											<i id="check_{{ $companyDirector->id }}_{{ $documentType->id }}"  class="bi bi-x-circle-fill text-muted opacity-50 me-2 not_completed"></i>
										@endif
										{{ $documentType->label }}
									</span>
									@if($groupedDocuments->has($key))
										@if($groupedDocuments[$key][0]['status'] == 2 || $groupedDocuments[$key][0]['status'] == 0)	
											<a href="javascript:;" id="edit_{{ $companyDirector->id }}_{{ $documentType->id }}" data-company_director_id="{{$companyDirector->id}}" data-company_director_name="{{$companyDirector->name}}" data-document_type_id="{{$documentType->id}}"  data-document_type_label="{{$documentType->label}}" onclick="editDocument(this, event)">
												<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
											</a>
										@endif
									@else
										<a href="javascript:;" id="edit_{{ $companyDirector->id }}_{{ $documentType->id }}" data-company_director_id="{{$companyDirector->id}}" data-document_type_id="{{$documentType->id}}" onclick="editDocument(this, event)" style="display:none;">
											<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
										</a>
									@endif
								</div>
								@if($groupedDocuments->has($key) && $groupedDocuments[$key][0]['status'] == 2)
									<span class="text-danger content-4 opacity-75">{{ $groupedDocuments[$key][0]['reason'] }}</span>
								@endif
							</li>
						@endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    @endif
</div>
