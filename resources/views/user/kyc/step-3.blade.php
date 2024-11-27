<h5 class="heading-4 fw-normal mb-4 text-center">Please upload all the required documents. </h5>
<div class="row mb-5">
	<div class="col-lg-6">
		<div class="mb-2">
			<label for="company_director_id" class="required text-black content-3 fw-normal mb-2">Director <span class="text-danger">*</span></label>
			<select id="company_director_id" name="company_director_id" class="form-control form-control-lg bg-light border-light select2">
				<option value="">Select Director</option>
				@if($companyDetail && $companyDetail->companyDirectors->isNotEmpty())
					@foreach($companyDetail->companyDirectors as $companyDirector)
						<option value="{{ $companyDirector->id }}">{{ $companyDirector->name }}</option>
					@endforeach
				@endif
			</select>    
		</div>
		<div class="mb-3">
			<label for="document_type_id" class="required text-black content-3 fw-normal mb-2">Document <span class="text-danger">*</span></label>
			<select id="document_type_id" name="document_type_id" class="form-control form-control-lg bg-light border-light select2">
				<option value="">Select Document</option>
				@foreach($documentTypes as $documentType)
					<option value="{{ $documentType->id }}">{{ $documentType->label }}</option>
				@endforeach
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
		@if($companyDetail && $companyDetail->companyDirectors->isNotEmpty())
			@php
				// Group documents by director ID and document type ID
				$groupedDocuments = $companyDetail->companyDocuments
					->groupBy(function ($doc) {
						return $doc->company_director_id . '_' . $doc->document_type_id;
					});
			@endphp

			@foreach($companyDetail->companyDirectors as $companyDirector)
				<div class="mb-4">
					<h5 class="heading-6 fw-normal mb-2">{{ $companyDirector->name }} Documents</h5>
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
											   class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>
										@endif
										{{ $documentType->label }}
									</span>
									@if($groupedDocuments->has($key))
										<a href="javascript:;" data-company_director_id="{{$companyDirector->id}}" data-document_type_id="{{$documentType->id}}" onclick="editDocument(this, event)"><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></a>
									@endif
								</div>
							</li>
						@endforeach
					</ul>
				</div>
			@endforeach
		@endif
	</div>

</div>

<div class="d-flex align-items-center justify-content-between">
	<button type="button" class="btn btn-secondary prev-step"><i class="bi bi-arrow-left me-1"></i>Previous</button>
	<button type="button" class="btn btn-primary next-step">Next <i class="bi bi-arrow-right ms-1"></i></button>
</div>
<script>
	@if(!isset($isSelect))
		$('.select2').select2({
			width: "100%"
		});
	@endif
	var selectedFiles = []; // Store selected files
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
						.addClass('bi bi-x-circle-fill text-muted opacity-50 me-2');
					  
					if (res.status === "success") {
						var result = decryptData(res.response);
						toastrMsg(res.status, res.message);
					
						// Reset the form inputs
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
				const previewHTML = `
					<div class="form__image-container js-remove-image" data-index="${index}">
						<img class="form__image" src="${file.url}" alt="${file.name}">
					</div>
				`;
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
				if (!file.type.startsWith("image/")) {
					alert(`${file.name} is not a valid image file.`);
					return;
				}

				// Add file to the list if valid
				fileList.push({
					name: file.name,
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
			const removeButtons = document.querySelectorAll(".js-remove-image");

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

	// Initialize when the page is loaded
	document.addEventListener('DOMContentLoaded', () => {
		initializeFileUpload();
	});

	function editDocument(obj, event)
	{
		event.preventDefault();
		var companyDirectorId = $(obj).data('company_director_id');
		var documentTypeId = $(obj).data('document_type_id');
		 
		$('#company_director_id').val(companyDirectorId).trigger('change');
		$('#document_type_id').val(documentTypeId).trigger('change');
	}

</script>