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
		<link rel="stylesheet" href="{{ asset('admin/vendors/sweetalert2/sweetalert2.min.css') }}">
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
					<a href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary position-absolute top-0 end-0 m-3 d-none d-lg-block"><i class="bi bi-power ms-1"></i> Logout </a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
						@csrf
					</form>
					<div id="container" class="container d-flex align-items-center justify-content-center py-4 "> 
						@if(!$companyDetail)
							<div class="p-4 shadow rounded-4 register-form-container kyc-form-container z-2 position-relative"> 
								<a href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary position-absolute top-0 end-0 m-3 d-lg-none"><i class="bi bi-power ms-1"></i></a>
								<div>  
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
									 
								</div>
							</div>
						@else
							@if(!$companyDetail->isUploadDocuments() || $companyDetail->is_update_kyc == 0 || $user->is_upload_document == 0)
								<div class="p-4 shadow rounded-4 register-form-container kyc-form-container z-2 position-relative"> 
									<a href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary position-absolute top-0 end-0 m-3 d-lg-none"><i class="bi bi-power ms-1"></i></a>
									<div>  
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
									</div>
								</div>   
							@else 
								@if($user->is_company == 1 && $user->is_kyc_verify == 1) 
									<div class="p-4 shadow rounded-4 text-center register-form-container kyc-form-container z-2 position-relative">
										<img class="mb-xxl-4 mb-3" src="{{ url('storage/setting', config('setting.site_logo')) }}" alt="" style="max-width: 100px;">
										<div class="card card-body p-4 kyc-result-contents border-opacity-10">
											<h6 class="heading-4 mb-2 fw-semibold text-success">KYC Verified</h6>
											<p class="caption text-muted content-3 mb-4">Thank you for completing your KYC submission! Your documents have been reviewed and approved. You can now continue using our services.</p>
											<div class="d-flex justify-content-center">
												<a href="{{ route('home') }}" id="submit-btn" class="btn btn-primary w-fit px-4"> Dashabord </a>
											</div>
										</div>
									</div> 
								@else
									<div class="p-4 shadow rounded-4 text-center register-form-container kyc-form-container z-2 position-relative">
										<img class="mb-xxl-4 mb-3" src="{{ url('storage/setting', config('setting.site_logo')) }}" alt="" style="max-width: 100px;">
										<div class="card card-body p-4 kyc-result-contents border-opacity-10">
											<h6 class="heading-4 mb-2 fw-semibold text-warning">KYC Underway</h6>
											<p class="caption text-muted content-3 mb-4">Your KYC process is currently underway. We will keep you updated via email with further details.</p>
											<h6 class="heading-4  text-black mb-1">Thank You</h6>
											<p class="caption text-muted content-3">{{ config('setting.site_name') }} Team</p>
										</div>
									</div> 
								@endif
							@endif
						@endif
					</div>
				</div>
			</div>
		</div>  
		<script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>   
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/select2.min.js')}}" ></script>
		<script src="{{ asset('admin/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" />	
		{{-- @livewireScripts --}}
		<script src="{{ asset('vendor/livewire/livewire.js') }}?v={{ \Carbon\Carbon::now()->timestamp }}"
        data-csrf="{{ csrf_token() }}"
        data-update-uri="{{ url('livewire/update') }}"
        data-navigate-once="true"></script>
 
		<!-- Stepper Script Starts -->
		<script>		
			 
			$(document).ready(function() 
			{
				select2()
				function select2()
				{
					$('.select2').select2({
						width: "100%"
					});
				}
				
				$('#business_type_id').change(function()
				{
					var is_director = $(this).find(':selected').data('is_director'); 
					$('#no_of_director').val(1);
					$('#no_of_director').attr('readonly', false);
					if(is_director == 0)
					{
						$('#no_of_director').attr('readonly', true);
					} 
					
					$('#director_html').html('<hr><div class="col-md-12 mb-3"><label for="postcode" class="required text-black font-md mb-2">Director 1  <span class="text-danger">*</span></label><input type="text" class="form-control bg-light border-light" id="director_name_0" name="director_name[0]" value="" placeholder="Enter Director Name"></div><input type="hidden" id="id_0" name="id" value="">');
				})
				
				$('#no_of_director').keyup(function () {
					var noofdirector = parseInt($(this).val()); // Parse the input value as an integer
					var html = '';

					// Validate that the input is a positive number
					if (!isNaN(noofdirector) && noofdirector > 0) {
						for (var i = 0; i < noofdirector; i++) {
							if (i === 0) {
								html += `<hr>`;
							}

							// Attempt to retrieve the existing value; if undefined, set as an empty string
							var director_name = $(`#director_name_${i}`).val() || '';
							var id = $(`#director_id_${i}`).val() || '';

							html += `
								<div class="col-md-12 mb-3">
									<label for="director_name_${i + 1}" class="required text-black font-md mb-2">
										Director ${i + 1} <span class="text-danger">*</span>
									</label>
									<input 
										type="text" 
										class="form-control bg-light border-light" 
										name="director_name[${i}]" 
										id="director_name_${i}" 
										placeholder="Enter Director Name"
										value="${director_name}"
									>
								</div>
								<input type="hidden" id="director_id_${i}" name="director_id[${i}]" value="${id}">
							`;
						}
					}

					// Update the director fields container
					$('#director_html').html(html);
				});
				  
				var currentStep = @json($stepNumber);

				// Handle the next button click
				$(document).on('click', ".next-step", function() {
					submitFormStep(currentStep);
				});

				// Handle the previous button click 
				$(document).on('click', ".prev-step", function() {
					showStep(currentStep - 1);
				});
		
				// Handle the final submit button 
				$(document).on('click', ".submit-final", function() {
					 
					if ($('.not_completed').length > 0) {
						toastrMsg('warning', 'Please upload all the required documents.');
						return false; // Prevent further execution
					}
					// Final step submission if validation passes
					submitFormStep(currentStep, true); 
				});
 
				$(document).on('change', "#company_director_id", function () {
					var directorId = $(this).val(); 
					fetchDocuments(directorId); // Fetch documents for the selected director
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
					var buttonClass = isFinal ? '.submit-final' : '.next-step';
					stepFields.find(buttonClass)
					.prop('disabled', true) 
					.addClass('loading-span') 
					.html('<span class="spinner-border"></span>');
					 
					var formDataInput = {}; 
					stepFields.find("input, select").each(function () {
						var inputName = $(this).attr('name');
						
						// Skip processing if the name is empty
						if (!inputName) return;

						// Handle text inputs and other non-file inputs
						if ($(this).attr('type') !== 'file') {
							// Check for array-like inputs (e.g., director_name[])
							if (inputName.includes('director_name')) {
								// Initialize the array if not already done
								if (!formDataInput['director_name']) {
									formDataInput['director_name'] = [];   
								}
								// Append the value to the array
								formDataInput['director_name'].push($(this).val()); 
							} 
							else if (inputName.includes('director_id')) {
								// Initialize the array if not already done
								if (!formDataInput['director_id']) {
									formDataInput['director_id'] = [];   
								}
								// Append the value to the array
								formDataInput['director_id'].push($(this).val()); 
							} else {
								// For single inputs
								formDataInput[inputName] = $(this).val();
							}
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
							var buttontext = isFinal ? 'Submit' : 'Next <i class="bi bi-arrow-right ms-1"></i>';
							stepFields.find('[type="button"]')
							.prop('disabled', false)  
							.removeClass('loading-span') 
							.html(buttontext);  
							 
							$('.error_msg').remove(); 
							if (res.status === "success")
							{
								var result = decryptData(res.response); 
								$('.step-'+parseInt(stepNumber + 1)).html(result.view) 
								initializeFileUpload();  
								select2()
								if (!isFinal){
									showStep(stepNumber + 1); // Move to the next step if not final
								}else{ 
									Swal.fire({
										icon: res.status,
										text: res.message,
										showDenyButton: false,
										showCancelButton: false,
										confirmButtonText: "Ok", 
									}).then((result) => { 
										if (result.isConfirmed) {
											Livewire.navigate("{{ route('corporate.kyc') }}");
										}  
									});
								}
							}
							else if(res.status == "validation")
							{   
								$.each(res.errors, function(key, value) {
									
									if (key.includes('.') && key.includes('director_name')) {
										var lastIndex = key.split('.').pop(); // Extracts '0' or '1'
										
										// Corrected field name using array indexing format
										fieldName = 'director_name[' + lastIndex + ']';
										
										// Correctly calculate the index for human-readable error message
										value[0] = 'The director ' + (parseInt(lastIndex) + 1) + ' name field is required.';
									} else { 
										// For other keys, just assign as usual
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
 
		</script>
		<!-- Stepper Script Ends -->
	</body>
	
</html>