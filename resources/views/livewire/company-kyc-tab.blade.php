<div>
	@if($isDirectors)
		<ul class="nav nav-tabs nav-tabs-line" id="myTab" role="tablist"> 
			@foreach($companyDirectors as $key => $companyDirector)
                <li class="nav-item">
                    <a 
                        class="nav-link {{ $activeTab == $key ? 'active' : '' }}" 
                        wire:click="loadDocumentsData({{ $companyDirector->id }}, {{ $key }})"  
                        id="director{{ $key }}-tab" 
                        data-bs-toggle="tab" 
                        href="#director{{ $key }}" 
                        role="tab" 
                        aria-controls="director{{ $key }}" 
                        aria-selected="{{ $activeTab == $key ? 'true' : 'false' }}">
                        {{ $companyDirector->name }}
                    </a>
                </li>
            @endforeach
		</ul>
		<div class="tab-content border border-top-0 p-3" id="myTabContent">
			<div class="tab-pane fade show active"> 
				<form id="directorKycForm" action="{{ route('admin.companies.kyc-update')}}" method="post">
					<div class="row"> 
						@foreach ($companyDocuments as $documentTypeId => $documents)
						   <div class="col-md-6 col-lg-4 col-xl-3 grid-margin stretch-card">
								<div class="card">
									<div class="card-body"> 			
										<h4 class="card-title">{{ $documents[0]['document_type']['label'] }}</h4>
										<div class="d-flex align-items-center gap-2 mb-3">
											@foreach($documents as $document)
												<img class="rounded-4 shadow w-50" id="profileImage" src="{{ url('storage/company_documents/'.$userId, $document['document'])}}" alt="Profile Image" height="100" width="100">
											@endforeach
										</div>
										<select class="form-select mb-3" name="status[{{$documentTypeId}}]" id="status{{$documentTypeId}}" onchange="openReasonText({{$documentTypeId}})">
											<option value="0" {{ $documents[0]['status'] == 0 ? 'selected' : '' }}>Pending</option>
											<option value="1" {{ $documents[0]['status'] == 1 ? 'selected' : '' }}>Approved</option>
											<option value="2" {{ $documents[0]['status'] == 2 ? 'selected' : '' }}>Rejected</option> 
										</select>
										<textarea id="reason{{$documentTypeId}}" class="form-control" name="reason[{{$documentTypeId}}]" style="display:none;"></textarea>
										<input type="hidden" class="form-control" name="document_type_id[]" value="{{ $documentTypeId }}">
										<input type="hidden" class="form-control" name="company_director_id" value="{{ $documents[0]['company_director_id'] }}">
										<input type="hidden" class="form-control" name="company_details_id" value="{{ $documents[0]['company_details_id'] }}">
									</div>
								</div>
							</div>
						@endforeach 
						<div class="col-12">
							<div class="d-flex justify-content-end">
								<button type="submit" class="btn btn-sm btn-primary">Submit</button>
							</div>
						</div>
					</div>
				</form> 
			</div> 
		</div>
	@else
		<div class="card">
			<div class="card-body"> 	
				<h4 class="card-title mb-0 text-center opacity-50" >Kyc Details Not Found!</h4>
			</div>
		</div>
	@endif
</div>
<script>
	function openReasonText(documentId)
	{
		var status = $('#status'+documentId).val();
		$('#reason'+documentId).hide(); 
		if(status == 2)
		{ 
			$('#reason'+documentId).val('');
			$('#reason'+documentId).show();
		}    
	}
	  
</script>
