<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">View KYC Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"> 
				<div class="row mb-4 kyc-details">
					<!-- User Information -->
					<div class="col-md-12 mb-3">
						<h6>User Information</h6>
						<p><strong>Email:</strong> {{ $kyc->email ?? '' }}</p>
						<p><strong>Verification Status:</strong> {{ $kyc->verification_status ?? '' }}</p>
						<p><strong>Identification ID:</strong> {{ $kyc->identification_id ?? '' }}</p>
						<p><strong>Verification ID:</strong> {{ $kyc->verification_id ?? '' }}</p>
					</div>

					<!-- Documents (Handle both images and files) -->
					<div class="col-md-12 mb-3">
						<h6>Documents</h6>
						@if ($kyc && $kyc->document)
							@php
								$files = json_decode($kyc->document, true); // Assuming the document column stores a JSON array
							@endphp
							<ul class="list-group">
								@foreach ($files as $file)
									@php
										$fileUrl = $file; 
										$fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
									@endphp
									<li class="list-group-item">
										@if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']))
											<!-- Display Image -->
											<img src="{{ $fileUrl }}" alt="{{ $fileUrl }}" class="img-fluid" />
										@elseif (in_array(strtolower($fileExtension), ['pdf', 'doc', 'docx', 'txt']))
											<!-- Display Document Link -->
											<a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a>
										@else
											<!-- Fallback for other file types -->
											<a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a>
										@endif
									</li>
								@endforeach
							</ul>
						@else
							<p>No documents uploaded.</p>
						@endif
					</div>

					<!-- Videos -->
					<div class="col-md-12">
						<h6>Videos</h6>
						@if ($kyc && $kyc->video) 
							<ul class="list-group"> 
								<li class="list-group-item">
									<video controls width="100%">
										<source src="{{ $kyc->video }}" type="video/mp4">
										Your browser does not support the video tag.
									</video>
								</li> 
							</ul>
						@else
							<p>No videos uploaded.</p>
						@endif
					</div>
				</div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
