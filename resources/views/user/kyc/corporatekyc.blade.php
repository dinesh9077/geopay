<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ config('setting.site_name') }} | Corporate/Company Kyc</title> 
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />
		<style>
			.kyc-form-container {
				background-color: #fff !important;
				max-width: 1000px;
			}
		</style>
	</head>
	
	<body class="kyc-page">
		<div class="container-fluid">
			<div class="row min-vh-100">
				<!-- Right Form Section -->
				<div class="d-flex align-items-center justify-content-center position-relative z-1">
					<div id="container" class="container d-flex align-items-center justify-content-center py-4">
						<div class="p-4 shadow rounded-4 register-form-container kyc-form-container z-2"> 
							@if($user->is_company == 1 && $user->is_kyc_verify == 1)
								
								<h6 class="fw-semibold text-black text-center mb-3">Your Corporate KYC Is Completed.</h6>
								<p class="caption text-muted content-3 text-center"> Thank you for completing your KYC submission! Your documents have been reviewed and approved. You can now continue using our services. </p>
								<div class="text-center">
									<a href="{{ route('home') }}" class="btn btn-primary btn-sm">Continue to use</a>
								</div>
								
							@else
								
							<h6 class="heading-4  text-black text-center mb-3">KYC Process</h6>
							<p class="caption text-muted content-3 text-center">To ensure a secure and compliant experience, please upload your KYC documents. Quick, secure, and hassle-free verification!</p> 
							<div>
								<div class="w-75 mx-auto mt-5 mb-2 stepper-container">
									<div class="progress px-1" style="height: 3px;">
										<div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<div class="step-container d-flex justify-content-between">
										<div class="step-circle"><i class="fa-solid fa-circle small"></i></div>
										<div class="step-circle"><i class="fa-solid fa-circle small"></i></div>
										<div class="step-circle"><i class="fa-solid fa-circle small"></i></div>
									</div>
								</div>
								
								<form id="multi-step-form">
									<!-- Company Form 1 -->
									<div class="step step-1" style="display:{{ $stepNumber == 1 ? 'show' : 'none' }}"> 
										@include('user.kyc.step-1')
									</div>
									
									<!-- Company Form 2 -->
									<div class="step step-2" style="display:{{ $stepNumber == 2 ? 'show' : 'none' }}">
										@include('user.kyc.step-2')
									</div>
									 
									<div class="step step-3" style="display:{{ $stepNumber == 3 ? 'show' : 'none' }}">
										@include('user.kyc.step-3') 
									</div>
								</form>
							</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div> 
		<script src="https://kit.fontawesome.com/ae360af17e.js" ></script>
		<script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>   
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/select2.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" />	
		{{-- @livewireScripts --}}
		<script src="{{ asset('vendor/livewire/livewire.js?id=38dc8241') }}"
        data-csrf="{{ csrf_token() }}"
        data-update-uri="livewire/update"
        data-navigate-once="true"></script>
		
		<!-- Stepper Script Starts -->
		<script>		  
			$(document).ready(function() 
			{
				$('.select2').select2({
					width: "100%"
				});
				
				$('#business_type_id').change(function()
				{
					var is_director = $(this).find(':selected').data('is_director'); 
					$('#no_of_director').val(1);
					$('#no_of_director').attr('readonly', false);
					if(is_director == 0)
					{
						$('#no_of_director').attr('readonly', true);
					} 
				})
				 
				var currentStep = @json($stepNumber);

				// Handle the next button click
				$(".next-step").click(function() {
					submitFormStep(currentStep);
				});

				// Handle the previous button click
				$(".prev-step").click(function() {
					showStep(currentStep - 1);
				});

				// Handle the final submit button
				$(".submit-final").click(function() {
					submitFormStep(currentStep, true); // Final step submission
				});

				// Function to display the correct step and update the progress bar
				function showStep(stepNumber) {
					$(".step").hide();
					$(".step-" + stepNumber).show();
					updateProgressBar(stepNumber);
					currentStep = stepNumber;
				}

				// Function to submit each form step via AJAX
				function submitFormStep(stepNumber, isFinal = false) 
				{ 
					var stepFields = $(".step-" + stepNumber) 
					stepFields.find('button').prop('disabled',true);   
					 
					var formDataInput = {}; 
					stepFields.find("input, select").each(function() {
						var inputName = $(this).attr('name');
						
						// For text inputs, just append the value to formData
						if ($(this).attr('type') !== 'file') { 
							formDataInput[inputName] = $(this).val();
						}
					}); 
					const encrypted_data = encryptData(JSON.stringify(formDataInput));
					
					// Collect form data excluding file inputs
					var formData = new FormData(); 
					formData.append('encrypted_data', encrypted_data);  
					formData.append('_token', "{{ csrf_token() }}");
					
					stepFields.find("input[type='file']").each(function() {
						var inputName = $(this).attr('name');
						var files = $(this)[0].files;

						// Loop through each file and append to FormData
						$.each(files, function(index, file) {
							formData.append(inputName + '[]', file); // For multiple files, append as array
						});
					});
 
					const url = isFinal
						? "{{ route('corporate.kyc.submit-final') }}"
						: "{{ route('corporate.kyc.submit-step', ['step' => '__STEP_NUMBER__']) }}".replace('__STEP_NUMBER__', stepNumber);

					$.ajax({
						url: url,
						type: "POST",
						data: formData,
						processData: false, // Important: Let FormData handle the data processing
						contentType: false, // Important: Let FormData handle the content type
						cache: false, 
						success: function(res)
						{
							stepFields.find('button').prop('disabled',false);	 
							$('.error_msg').remove(); 
							if (res.status === "success")
							{
								var result = decryptData(res.response);
								$('.step-'.stepNumber).html(result.view)
								if (!isFinal){
									showStep(stepNumber + 1); // Move to the next step if not final
								}else{
									toastrMsg(res.status, res.message);// Show success message if final
									window.location.href = "{{ route('corporate.kyc') }}";
								}
							}
							else if(res.status == "validation")
							{   
								$.each(res.errors, function(key, value) {
								
									if (key.includes('.')) 
									{ 
										fieldName = key.replace(/\./g, '[').replace(/^/, '') + ']';
										if (key.endsWith('.0') || key.endsWith('.1')) { 
											key = key.replace(/\.(0|1)$/, '');  // Replace .0 or .1 at the end of the string
											fieldName = key.replace(/\./g, '[').replace(/^/, '') + ']';
										} 
									} else { 
										fieldName = key;
									} 
								 
									// Find the input field using the converted name format
									var inputField = stepFields.find('input[name="' + fieldName + '"], select[name="' + fieldName + '"]');
									
									// Create an error span to display the error message
									var errorSpan = $('<span>')
										.addClass('error_msg text-danger') 
										.attr('id', key + 'Error') // Use the key as the unique ID
										.text(value[0]); // Use the first error message
									
									// Append the error message after the input field
									if (inputField.length > 0) {
										inputField.parent().append(errorSpan); // Append to the parent div
									}
								});

							} else {
								toastrMsg(res.status, res.message); 
							}
						} 
					});
				}

				// Function to update the progress bar
				function updateProgressBar(stepNumber) {
					var progressPercentage = ((stepNumber - 1) / 2) * 100;
					$(".progress-bar").css("width", progressPercentage + "%");
				}

				showStep(currentStep); // Show the first step initially
			});

			// File Upload JS
			const INPUT_FILE = document.querySelector('#upload-files');
			const INPUT_CONTAINER = document.querySelector('#upload-container');
			const FILES_LIST_CONTAINER = document.querySelector('#files-list-container');
			const FILE_LIST = [];
			let UPLOADED_FILES = [];

			const multipleEvents = (element, eventNames, listener) => {
			const events = eventNames.split(' ');
			
			events.forEach(event => {
				element.addEventListener(event, listener, false);
			});
			};

			const previewImages = () => {
			FILES_LIST_CONTAINER.innerHTML = '';
			if (FILE_LIST.length > 0) {
				FILE_LIST.forEach((addedFile, index) => {
				const content = `
					<div class="form__image-container js-remove-image" data-index="${index}">
					<img class="form__image" src="${addedFile.url}" alt="${addedFile.name}">
					</div>
				`;

				FILES_LIST_CONTAINER.insertAdjacentHTML('beforeEnd', content);
				});
			} else {
				console.log('empty')
				INPUT_FILE.value= "";
			}
			}

			const fileUpload = () => {
			if (FILES_LIST_CONTAINER) {
				multipleEvents(INPUT_FILE, 'click dragstart dragover', () => {
				INPUT_CONTAINER.classList.add('active');
				});
			
				multipleEvents(INPUT_FILE, 'dragleave dragend drop change blur', () => {
				INPUT_CONTAINER.classList.remove('active');
				});
			
				INPUT_FILE.addEventListener('change', () => {
				const files = [...INPUT_FILE.files];
				console.log("changed")
				files.forEach(file => {
					const fileURL = URL.createObjectURL(file);
					const fileName = file.name;
					if (!file.type.match("image/")){
					alert(file.name + " is not an image");
					console.log(file.type)
					} else {
					const uploadedFiles = {
						name: fileName,
						url: fileURL,
					};

					FILE_LIST.push(uploadedFiles);
					}
				});
				
				console.log(FILE_LIST)//final list of uploaded files
				previewImages();
				UPLOADED_FILES = document.querySelectorAll(".js-remove-image");
				removeFile();
				}); 
			}
			};

			const removeFile = () => {
			UPLOADED_FILES = document.querySelectorAll(".js-remove-image");
			
			if (UPLOADED_FILES) {
				UPLOADED_FILES.forEach(image => {
				image.addEventListener('click', function() {
					const fileIndex = this.getAttribute('data-index');

					FILE_LIST.splice(fileIndex, 1);
					previewImages();
					removeFile();
				});
				});
			} else {
				[...INPUT_FILE.files] = [];
			}
			};

			fileUpload();
			removeFile();
		</script>
		<!-- Stepper Script Ends -->
	</body>
	
</html>