<h5 class="heading-4 fw-normal mb-4 text-center">Please upload all the required documents. </h5>
<div class="row mb-5">
	<div class="col-lg-6">
		<div class="mb-2">
			<label for="company_director_id" class="required text-black content-3 fw-normal mb-2">Director <span class="text-danger">*</span></label>
			<select id="company_director_id" name="company_director_id" class="form-control form-control-lg bg-light border-light select2">
				<option value="">Select Director</option> 
			</select>    
		</div>
		<div class="mb-3">
			<label for="document_type_id" class="required text-black content-3 fw-normal mb-2">Document <span class="text-danger">*</span></label>
			<select id="document_type_id" name="document_type_id" class="form-control form-control-lg bg-light border-light select2">
				<option value="">Select Document</option> 
			</select>    
		</div>
		<div class="card">
			<div class="card-body"> 
				<div>
					<label for="upload-files" class="required text-black content-3 fw-normal mb-2">Upload your all Documents <span class="text-danger">*</span></label> 
					<label class="image_upload_form__container" id="upload-container">
						<i class="bi bi-cloud-upload fs-1"></i>
						<span class="content-1 text-dark">Choose or Drag & Drop Files</span>
						<input class="form__file" id="upload-files" type="file" accept="image/*,application/pdf" multiple="multiple"/>
						<span class="content-4 text-muted opacity-50">JPEG, PNG, JPG, PDF formats, up to 2 MB</span>
						<span class="btn btn-light border mt-3">Browse File</span>
					</label>
					<div class="form__files-container" id="files-list-container"></div> 
				</div>
				<div class="d-flex justify-content-end">
					<button type="button" id="submit-btn" class="btn btn-primary w-fit px-4 d-flex align-items-center">
						Add 
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 kyc-document-column">
	@livewire('company-director-documents', ['companyDetailId' => $companyDetail->id])

	{{-- @if($companyDetail && $companyDetail->companyDirectors->isNotEmpty())
			@php
				// Group documents by director ID and document type ID
				$groupedDocuments = $companyDetail->companyDocuments
					->groupBy(function ($doc) {
						return $doc->company_director_id . '_' . $doc->document_type_id;
					});
			@endphp
  
			@foreach($companyDetail->companyDirectors as $companyDirector)
				<div class="card card-body">
					<div class="mb-4">
						<h5 class="heading-6 fw-normal mb-2">
							{{ $companyDirector->name }} Documents 
							@php
								// Check if all documents are uploaded for this director
								$allDocumentsUploaded = true;
								foreach($documentTypes as $documentType) {
									$key = $companyDirector->id . '_' . $documentType->id;
									if (!$groupedDocuments->has($key)) {
										$allDocumentsUploaded = false;
										break;
									}
								}
							@endphp
							@if($allDocumentsUploaded)
								<span class="badge bg-success">Document Uploaded</span>
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
												<i id="check_{{ $companyDirector->id }}_{{ $documentType->id }}" 
												   class="bi bi-check-circle-fill text-success me-2"></i>
											@else
												<i id="check_{{ $companyDirector->id }}_{{ $documentType->id }}" 
												   class="bi bi-x-circle-fill text-muted opacity-50 me-2 not_completed"></i>
											@endif
											{{ $documentType->label }}
										</span>
										@if($groupedDocuments->has($key))
											<a href="javascript:;" id="edit_{{ $companyDirector->id }}_{{ $documentType->id }}" data-company_director_id="{{$companyDirector->id}}" data-document_type_id="{{$documentType->id}}" onclick="editDocument(this, event)">
												<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
											</a>
										@else
											<a href="javascript:;" id="edit_{{ $companyDirector->id }}_{{ $documentType->id }}" data-company_director_id="{{$companyDirector->id}}" data-document_type_id="{{$documentType->id}}" onclick="editDocument(this, event)" style="display:none;">
												<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
											</a>
										@endif
									</div>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			@endforeach

		@endif --}}
	</div>

</div>

<div class="d-flex align-items-center justify-content-between">
	<button type="button" class="btn btn-secondary prev-step"><i class="bi bi-arrow-left me-1"></i>Previous</button>
	@if($user->is_upload_document == 0)
		<button type="button" class="btn btn-primary submit-final">Submit </button>
	@endif
</div>
<script>
	 
	var selectedFiles = [];  
	var fileList = [];
	
	document.getElementById('submit-btn').addEventListener('click', function () 
	{ 
		// Form validation variables
		var isValid = true;
		const directorSelect = document.getElementById('company_director_id');
		const documentSelect = document.getElementById('document_type_id');
		const fileInput = document.getElementById('upload-files');
		const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
		const maxFileSize = 2 * 1024 * 1024; // 2 MB

		// Clear any previous error messages
		document.querySelectorAll('.error-message').forEach(el => el.remove());

		// Validate director selection
		if (!directorSelect.value) {
			isValid = false;
			addErrorMessage(directorSelect, 'Please select a person.');
		}

		// Validate document selection
		if (!documentSelect.value) {
			isValid = false;
			addErrorMessage(documentSelect, 'Please select a document.');
		}

		// Validate file input
		var files = selectedFiles; 
		if (files.length === 0) {
			isValid = false;
			addErrorMessage(fileInput, 'Please upload at least one file.');
		} else if (files.length > 2) {
			isValid = false;
			addErrorMessage(fileInput, 'You can upload a maximum of 2 files.');
		} else {
			for (const file of files) {
				const fileExtension = file.name.split('.').pop().toLowerCase();
				if (!allowedExtensions.includes(fileExtension)) {
					isValid = false;
					addErrorMessage(fileInput, `Invalid file type. Only ${allowedExtensions.join(', ')} are allowed.`);
				}
				if (file.size > maxFileSize) {
					isValid = false;
					addErrorMessage(fileInput, 'File size must be less than 2 MB.');
				}
			}
		}
		console.log(files)
		// If all validations pass, submit the form or show success message
		if (isValid) {
			// Step 1: Prepare FormData
			files = [];  
			var formDataInput = {}; 
			formDataInput['company_director_id'] = directorSelect.value;
			formDataInput['document_type_id'] = documentSelect.value;
			formDataInput['company_details_id'] = "{{ $companyDetail ? $companyDetail->id : '' }}";
			const encrypted_data = encryptData(JSON.stringify(formDataInput));
		  
		  
			var formData = new FormData(); 
			formData.append('encrypted_data', encrypted_data);  
			formData.append('_token', "{{ csrf_token() }}"); 
		 
			if(selectedFiles.length === 0)	
			{ 
				formData.append('documents[]', selectedFiles);
			}
			else
			{
				selectedFiles.forEach(function(file) {
					formData.append('documents[]', file);
				});
			} 
			
			$('#submit-btn').html('<div class="spinner-border text-light during-verify" role="status"></div>').prop('disabled', true); 
			
			$.ajax({
				url: '{{ route("corporate.kyc.document-store") }}', 
				type: 'POST',
				data: formData,
				processData: false, 
				contentType: false, 
				success: function(res)
				{  	
					$('#submit-btn').html('Add').prop('disabled', false);  
					$('#check_' + directorSelect.value + '_' + documentSelect.value)
						.removeClass()
						.addClass('bi bi-x-circle-fill text-muted opacity-50 me-2 not_completed');
					$('#edit_' + directorSelect.value + '_' + documentSelect.value).hide();
					  
					if (res.status === "success") 
					{
						var result = decryptData(res.response);
						toastrMsg(res.status, res.message);  
						$('#company_director_id').val(null).trigger('change'); // Reset director dropdown
						$('#document_type_id').val(null).trigger('change');    // Reset document type dropdown
						$('#upload-files').val(null);                         // Reset file input
						$('#files-list-container').html(''); // Clear file previews
						 
						fileList = [];
						selectedFiles  = [];
						// Update the status icon to success
						$('#check_' + result.data.company_director_id + '_' + result.data.document_type_id)
							.removeClass()
							.addClass('bi bi-check-circle-fill text-success me-2');
						$('#edit_' + result.data.company_director_id + '_' + result.data.document_type_id).show(); 
						fetchDirectors("{{ $companyDetail ? $companyDetail->id : '' }}");
						Livewire.dispatch('refreshCompanyDocuments');
					} else if (res.status === "validation") {
						// Display validation errors
						toastrMsg(res.status, res.errors);
					} else {
						// Display a generic error message
						toastrMsg(res.status, res.message);
					} 
				} 
			});
		}
	});
	
	// Helper function to add error messages
	function addErrorMessage(element, message) {
		const error = document.createElement('div');
		error.classList.add('error-message', 'text-danger', 'content-4', 'mt-1');
		error.innerText = message;
		element.parentNode.appendChild(error);
	}
	
	// Function to fetch documents for the selected director
	var fetchDocuments = (directorId) => {
		if (!directorId) {
			$('#document_type_id').html('<option value="">Select Document</option>');
			return;
		}

		// AJAX request to fetch documents
		$.ajax({
			url: "{{ url('corporate/document-type')}}/"+directorId,
			method: 'GET',
			success: function (data) {
				// Clear existing options
				$('#document_type_id').html('<option value="">Select Document</option>');

				// Populate dropdown with remaining documents
				data.remainingDocuments.forEach(doc => {
					$('#document_type_id').append(
						$('<option></option>').val(doc.id).text(doc.label)
					);
				});
			},
			error: function (xhr, status, error) {
				console.error('Error fetching documents:', error);
			}
		});
	};
	 
	function fetchDirectors(companyDetailId) {
		$('#company_director_id').html('<option value="">Select Director</option>');

		// AJAX request to fetch directors
		$.ajax({
			url: "{{ url('corporate/director')}}/" + companyDetailId,
			method: 'GET',
			success: function (res) {
				console.log(res);  // Log response to check if it's correct

				// Ensure the response has 'remainingDirectors' as an array
				if (Array.isArray(res.remainingDirectors)) {
					// Clear existing options
					$('#company_director_id').html('<option value="">Select Director</option>');

					// Populate dropdown with directors
					res.remainingDirectors.forEach(doc => {
						$('#company_director_id').append(
							$('<option></option>').val(doc.id).text(doc.name)
						);
					});
				} else {
					console.error('Expected "remainingDirectors" to be an array, but got:', res.remainingDirectors);
				}
			},
			error: function (xhr, status, error) {
				console.error('Error fetching directors:', error);
			}
		});
	}

   
	// Initialize when the page is loaded
	document.addEventListener('DOMContentLoaded', () => {
		initializeFileUpload();
		fetchDirectors("{{ $companyDetail ? $companyDetail->id : '' }}");
		$('.select2').select2({
			width: "100%"
		});	
	});
	
	function initializeFileUpload() {
		const inputFile = document.querySelector('#upload-files');
		const inputContainer = document.querySelector('#upload-container');
		var filesListContainer = $('#files-list-container');
		 
		/**
		 * Attach multiple events to an element
		 * @param {HTMLElement} element - The element to attach events
		 * @param {string[]} events - Array of event names
		 * @param {function} listener - The event listener function
		 */
		const attachEvents = (element, events, listener) => {
			events.forEach(event => element.addEventListener(event, listener, false));
		};

		/**
		 * Update the preview of uploaded files
		 */
		const updatePreview = () => {
			filesListContainer.html(''); // Clear existing previews

			if (fileList.length === 0) {
				if (inputFile) inputFile.value = ''; // Reset input if no files
				return;
			}

			fileList.forEach((file, index) => {
				let previewHTML = '';

				if (file.type.startsWith("image/")) {
					// Preview image
					previewHTML = `
						<div class="form__image-container js-remove-file" data-index="${index}">
							<img class="form__image" src="${file.url}" alt="${file.name}"> 
						</div> 
						`;
				} else if (file.type === "application/pdf") {
					// Preview PDF
					previewHTML = `
						<div class="form__image-container js-remove-file" data-index="${index}">
							<iframe class="form__pdf" src="${file.url}" frameborder="0" style="max-width: 100%;max-height: 100%;"></iframe>
							<div class="file-name">${file.name}</div>
						</div>
					`;
				} else if (
					file.type === "application/msword" ||
					file.type === "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
				) {
					// Preview Word document
					previewHTML = `
						<div class="form__image-container js-remove-file" data-index="${index}">
							<a href="${file.url}" target="_blank" class="form__word">
								<img src="word-icon.png" alt="Word File">
								<div class="file-name">${file.name}</div>
							</a>
						</div>
					`;
				} else {
					// Unsupported file type
					previewHTML = `
						<div class="form__image-container js-remove-file" data-index="${index}">
							<div class="file-name text-danger">Unsupported file: ${file.name}</div>
						</div>
					`;
				}

				filesListContainer.prepend(previewHTML);
			});

			attachRemoveListeners(); // Attach listeners to remove buttons
		};

		/**
		 * Handle file upload and validation
		 */
		const handleFileUpload = () => {
			if (!inputFile) return;
			const files = Array.from(inputFile.files);

			files.forEach(file => {
				// Validate file type
				if (!file.type.startsWith("image/") && file.type !== "application/pdf" &&
					file.type !== "application/msword" &&
					file.type !== "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
					alert(`${file.name} is not a valid file type.`);
					return;
				}

				// Add file to the list if valid
				fileList.push({
					name: file.name,
					type: file.type,
					url: URL.createObjectURL(file),
				});
				selectedFiles.push(file);
			});

			updatePreview(); // Refresh the file previews
		};

		/**
		 * Attach remove functionality to preview elements
		 */
		const attachRemoveListeners = () => {
			const removeButtons = document.querySelectorAll(".js-remove-file");

			removeButtons.forEach(button => {
				button.addEventListener('click', () => {
					const fileIndex = button.getAttribute('data-index');
					fileList.splice(fileIndex, 1); // Remove file from the list

					if (fileIndex !== -1) {
						selectedFiles.splice(fileIndex, 1);
					}
					updatePreview(); // Refresh the preview
				});
			});
		};

		/**
		 * Add drag and drop visual feedback
		 */
		if (inputFile && inputContainer) {
			attachEvents(inputFile, ['click', 'dragstart', 'dragover'], () => {
				inputContainer.classList.add('active');
			});

			attachEvents(inputFile, ['dragleave', 'dragend', 'drop', 'change', 'blur'], () => {
				inputContainer.classList.remove('active');
			});

			// Handle file selection and upload
			inputFile.addEventListener('change', handleFileUpload);
		}
	}

	 
	
	function editDocument(obj, event)
	{
		event.preventDefault();
		var companyDirectorId = $(obj).data('company_director_id');
		var documentTypeId = $(obj).data('document_type_id');
		 
		$('#company_director_id').val(companyDirectorId).trigger('change');
		$('#document_type_id').val(documentTypeId).trigger('change');
	}

</script>