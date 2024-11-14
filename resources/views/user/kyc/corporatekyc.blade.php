<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ env('APP_NAME') }} | Corporate/Company Kyc</title> 
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />
		<style>
			.kyc-container {
				background-color: #fff !important;
				max-width: 700px;
			}
		</style>
	</head>
	
	<body>
		<div class="container-fluid">
			<div class="row min-vh-100">
				<!-- Right Form Section -->
				<div class="d-flex align-items-center justify-content-center position-relative bg-white z-1">
					<div id="container" class="container d-flex align-items-center justify-content-center py-4">
						<div class="bg_overlay_3"></div>
						<div class="bg_overlay_4"></div>
						<div class="px-4 register-form-container z-2 kyc-container"> 
							@if($user->is_company == 1 && $user->is_kyc_verify == 1)
								
								<h6 class="fw-semibold text-black text-center mb-4">Your Corporate KYC Is Completed.</h6>
								<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption">
									Thank you for completing your KYC submission! Your documents have been reviewed and approved.
									You can now continue using our services.
								</p>
								<div class="text-center">
									<a href="{{ route('home') }}" class="btn btn-primary btn-sm">Continue to use</a>
								</div>
								
							@else
								
							<h6 class="fw-semibold text-black text-center mb-4">KYC Verification</h6>
							<p style="color: gray; font-size: 0.8rem;text-align: center;" class="caption">To ensure a secure and compliant experience, please upload your KYC documents. Quick, secure, and hassle-free verification!</p> 
							<div>
								<div class="w-50 mx-auto mt-5 mb-2 stepper-container w-100">
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
										<div class="row mb-3"> 
											<div class="col-md-6">
												<label for="business_licence" class="required text-black font-md mb-2">Company Registration Number <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="business_licence" name="business_licence" value="{{ $companyDetail ? $companyDetail->business_licence : '' }}"> 
											</div>
											<div class="col-md-6">
												<label for="postcode" class="required text-black font-md mb-2">Postal Code/Zip Code <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="postcode" name="postcode" value="{{ $companyDetail ? $companyDetail->postcode : '' }}"> 
											</div> 
										</div>
										
										<div class="row mb-3">  
											<div class="col-md-12">
												<label for="company_address" class="required text-black font-md mb-2">Legal registered Corporate/Company Address <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="company_address" name="company_address" value="{{ $companyDetail ? $companyDetail->company_address : '' }}"> 
											</div>
										</div>
										
										<div class="d-flex align-items-center gap-3">
											<button type="button" class="btn btn-secondary w-100 next-step">Next</button>
										</div>
									</div>
									
									<!-- Company Form 2 -->
									<div class="step step-2" style="display:{{ $stepNumber == 2 ? 'show' : 'none' }}">
										<div class="row mb-3">
											<div class="col-md-6">
												<label for="bank_name" class="required text-black font-md mb-2">Bank Name <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="bank_name" name="bank_name" value="{{ $companyDetail ? $companyDetail->bank_name : '' }}"> 
											</div>
											<div class="col-md-6">
												<label for="bank_code" class="required text-black font-md mb-2">Bank Code <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="bank_code" name="bank_code" value="{{ $companyDetail ? $companyDetail->bank_code : '' }}"> 
											</div>
										</div> 
										<div class="row mb-4">
											<div class="col-md-12">
												<label for="account_number" class="required text-black font-md mb-2">Account No <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="account_number" name="account_number" value="{{ $companyDetail ? $companyDetail->account_number : '' }}"> 
											</div>
										</div> 
										<div class="d-flex align-items-center gap-3">
											<button type="button" class="btn btn-primary w-100 prev-step">Previous</button>
											<button type="button" class="btn btn-secondary w-100 next-step">Next</button>
										</div>
									</div>
									
									<!-- Company Form 3 -->
									<div class="step step-3" style="display:{{ $stepNumber == 3 ? 'show' : 'none' }}">
										@php
											$documentFields = [
												'memorandum_articles_of_association' => 'Memorandum Articles of Association',
												'registration_of_shareholders' => 'Registration of Shareholders',
												'registration_of_directors' => 'Registration of Directors',
												'proof_of_address_shareholders' => 'Proof of Address for Shareholders (Utility bill or bank statement)',
												'proof_of_address_directors' => 'Proof of Address for Directors (Utility bill or bank statement)',
												'govt_id_shareholders' => 'Government ID for Shareholders (Passport, Driving License or National ID)',
												'govt_id_directors' => 'Government ID for Directors (Passport, Driving License or National ID)'
											];
										@endphp

										@foreach($documentFields as $fieldKey => $fieldLabel)
											@php 
												$document = $companyDocument[$fieldKey] ?? null;
											@endphp
											<div class="row mb-3">
												<div class="col-md-12"> 
													<label for="{{ $fieldKey }}" class="required text-black font-md mb-2">{{ $fieldLabel }} <span class="text-danger">*</span></label>
													<input type="file" class="form-control bg-light border-light" 
													   id="{{ $fieldKey }}" 
													   name="company_document[{{ $fieldKey }}]" 
													   style="pointer-events: {{ $document && $document['status'] == 1 ? 'none' : 'auto' }};" 
													   multiple>
 
													@if($document)
														<div class="mt-2">
															@if($document['status'] == 2)
																<span class="text-danger">Rejected: {{ $document['reason'] ?? 'No reason provided' }}</span>
															@elseif($document['status'] == 1)
																<span class="text-success">Approved: Your document has been approved.</span>
															@else
																<span class="text-warning">Pending: Your document is under review.</span>
															@endif
														</div> 
													@endif
												</div>
											</div>
										@endforeach

										<div class="d-flex align-items-center gap-3">
											<button type="button" class="btn btn-primary w-100 prev-step">Previous</button>
											<button type="button" class="btn btn-secondary w-100 submit-final">Register</button>
										</div>
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
		
		<!-- Stepper Script Starts -->
		<script>
			  
			$(document).ready(function() {
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
									var inputField = stepFields.find('input[name="' + fieldName + '"]');
									
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