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
								
							<h6 class="heading-4  text-black text-center mb-3">KYC Verification</h6>
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
										<div class="row"> 
											<div class="col-md-6 mb-3">
												<label for="business_licence" class="required text-black font-md mb-2">Business Type <span class="text-danger">*</span></label>
												<select id="country_id1" name="country_id" class="form-control form-control-lg bg-light border-light"></select>	
											</div>
											<div class="col-md-6 mb-3">
												<label for="postcode" class="required text-black font-md mb-2">Number of Directors <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="" name=""> 
											</div> 
											<div class="col-md-6 mb-3">
												<label for="business_licence" class="required text-black font-md mb-2">Company Registration Number <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="business_licence" name="business_licence" value="{{ $companyDetail ? $companyDetail->business_licence : '' }}"> 
											</div>
											<div class="col-md-6 mb-3">
												<label for="postcode" class="required text-black font-md mb-2">Postal Code/Zip Code <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="postcode" name="postcode" value="{{ $companyDetail ? $companyDetail->postcode : '' }}"> 
											</div> 
											<div class="col-md-12 mb-5">
												<label for="company_address" class="required text-black font-md mb-2">Legal registered Corporate/Company Address <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="company_address" name="company_address" value="{{ $companyDetail ? $companyDetail->company_address : '' }}"> 
											</div>
										</div>
										
										<div class="d-flex justify-content-end">
											<button type="button" class="btn btn-primary next-step">Next <i class="bi bi-arrow-right ms-1"></i></button>
										</div>
									</div>
									
									<!-- Company Form 2 -->
									<div class="step step-2" style="display:{{ $stepNumber == 2 ? 'show' : 'none' }}">
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="bank_name" class="required text-black font-md mb-2">Bank Name <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="bank_name" name="bank_name" value="{{ $companyDetail ? $companyDetail->bank_name : '' }}"> 
											</div>
											<div class="col-md-6 mb-3">
												<label for="bank_code" class="required text-black font-md mb-2">Bank Code <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="bank_code" name="bank_code" value="{{ $companyDetail ? $companyDetail->bank_code : '' }}"> 
											</div>
											<div class="col-md-12 mb-5">
												<label for="account_number" class="required text-black font-md mb-2">Account No <span class="text-danger">*</span></label>
												<input type="text" class="form-control bg-light border-light" id="account_number" name="account_number" value="{{ $companyDetail ? $companyDetail->account_number : '' }}"> 
											</div>
										</div> 

										<div class="d-flex align-items-center justify-content-between">
											<button type="button" class="btn btn-secondary prev-step"><i class="bi bi-arrow-left me-1"></i>Previous</button>
											<button type="button" class="btn btn-primary next-step">Next <i class="bi bi-arrow-right ms-1"></i></button>
										</div>
									</div>
									
									<!-- Company Form 3 -->
									<!-- <div class="step step-3" style="display:{{ $stepNumber == 3 ? 'show' : 'none' }}">
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
									</div>  -->
									<div class="step step-3" style="display:{{ $stepNumber == 2 ? 'show' : 'none' }}">
										<h5 class="heading-4 fw-normal mb-4 text-center">Please upload these mendetory Documents</h5>
										<div class="row mb-5">
											<div class="col-lg-6">
												<div class="mb-2">
													<label for="business_licence" class="required text-black content-3 fw-normal mb-2">Select Person <span class="text-danger">*</span></label>
													<select id="country_id1" name="country_id" class="form-control form-control-lg bg-light border-light"></select>	
												</div>
												<div class="mb-3">
													<label for="bank_code" class="required text-black content-3 fw-normal mb-2">Select Document <span class="text-danger">*</span></label>
													<select id="country_id1" name="country_id" class="form-control form-control-lg bg-light border-light"></select>	
												</div>
												<div class="card">
													<div class="card-body">
														<div class="mb-2">
															<label for="business_licence" class="required text-black content-3 fw-normal mb-2">Name (as per Document) <span class="text-danger">*</span></label>
															<input type="text" class="form-control bg-light border-light" placeholder="Enter Full Name" id="" name=""> 
														</div>
														<div>
															<label for="business_licence" class="required text-black content-3 fw-normal mb-2">Upload your all Documents <span class="text-danger">*</span></label>
															<form class="form">
																<label class="image_upload_form__container" id="upload-container">
																	<i class="bi bi-cloud-upload fs-1"></i>
																	<span class="content-1 text-dark">Choose or Drag & Drop Files</span>
																	<input class="form__file" id="upload-files" type="file" accept="image/*" multiple="multiple"/>
																	<span class="content-4 text-muted opacity-50">JPEG, PNG, PDG, and MP4 formats, up to 50MB</span>
																	<span class="btn btn-light border mt-3">Browse File</span>
																</label>
																<div class="form__files-container" id="files-list-container"></div>
															</form>
														</div>
														<div class="d-flex justify-content-end">
															<button type="button" class="btn btn-primary w-fit px-4">Add</button>
														</div>
													</div>
												</div>
											</div>
											<div class="col-lg-6 kyc-document-column">
												<div class="mb-4">
													<h5 class="heading-6 fw-normal mb-2">Person 1 Documents</h5>
													<ul class="p-0">
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Memorandum Articles of Association</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>
															<span class="text-danger content-4 opacity-75">Document not verified</span>
														</li>
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Registration of Shareholders</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>	
														</li>
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Registration of Directors</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>	
														</li>
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Shareholders (Utility bill or bank statement)</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>	
														</li>
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Directors (Utility bill or bank statement)</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>	
														</li>
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Shareholders (Passport, Driving License or National ID)</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>	
														</li>
														<li class="content-3 text-muted mb-2">
															<div class="d-flex justify-content-between">
																<span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Directors (Passport, Driving License or National ID)</span>
																<i class="bi bi-pencil-square opacity-75 fw-semibold"></i>
															</div>	
														</li>
													</ul>
												</div>
												<div class="mb-4">
													<h5 class="heading-6 fw-normal mb-2">Person 2 Documents</h5>
													<ul class="p-0 lh-base">
													<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Memorandum Articles of Association</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Registration of Shareholders</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Registration of Directors</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Shareholders (Utility bill or bank statement)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Directors (Utility bill or bank statement)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Shareholders (Passport, Driving License or National ID)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Directors (Passport, Driving License or National ID)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
													</ul>
												</div>
												<div class="mb-4">
													<h5 class="heading-6 fw-normal mb-2">Person 2 Documents</h5>
													<ul class="p-0 lh-base">
													<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Memorandum Articles of Association</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Registration of Shareholders</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Registration of Directors</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Shareholders (Utility bill or bank statement)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Directors (Utility bill or bank statement)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Shareholders (Passport, Driving License or National ID)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Directors (Passport, Driving License or National ID)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
													</ul>
												</div>
												<div class="mb-4">
													<h5 class="heading-6 fw-normal mb-2">Person 2 Documents</h5>
													<ul class="p-0 lh-base">
													<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Memorandum Articles of Association</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-check-circle-fill text-success me-2"></i>Registration of Shareholders</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Registration of Directors</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Shareholders (Utility bill or bank statement)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Proof of Address for Directors (Utility bill or bank statement)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Shareholders (Passport, Driving License or National ID)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
														<li class="content-3 d-flex justify-content-between text-muted mb-2"><span class="d-flex"><i class="bi bi-x-circle-fill text-muted opacity-50 me-2"></i>Government ID for Directors (Passport, Driving License or National ID)</span><i class="bi bi-pencil-square opacity-75 fw-semibold"></i></li>
													</ul>
												</div>
											</div>
										</div>

										<div class="d-flex align-items-center justify-content-between">
											<button type="button" class="btn btn-secondary prev-step"><i class="bi bi-arrow-left me-1"></i>Previous</button>
											<button type="button" class="btn btn-primary next-step">Next <i class="bi bi-arrow-right ms-1"></i></button>
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