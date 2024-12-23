<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ config('setting.site_name') }} | Register</title>
		<link rel="icon" type="image/svg+xml" href="{{ url('storage/setting', config('setting.fevicon_icon')) }}">
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" /> 
	</head> 
	<body>
		<div class="container-fluid">
			<div class="row min-vh-100">
				<!-- Left Section -->
				<div class="col-lg-6 col-xxl-7 d-none d-lg-flex align-items-end justify-content-start text-white auth-left-image-banner">
					<div class="content-wrapper m-3 m-xxl-5">
						<div class="d-flex justify-content-center py-5">
							<img class="mb-xxl-4" src="{{ asset('assets/image/logo-white.png') }}" alt="" style="max-width: 150px;">
						</div>
						<div class="mb-4 mb-xxl-5 me-xxl-5 pe-xxl-5">
							<img class="mb-4" src="{{ asset('assets/image/icons/spark-icon.svg') }}" alt="">
							<h2 class="mb-4 mb-xxl-5 heading-xxl font-radio-canada">Very Good Works are waiting for you. Login now.</h2>
							<p class="content-2 text-soft-white">Access your account to explore exclusive features, personalized content, and stay up-to-date with the latest updates.</p>
						</div>
						<div class="d-flex align-items-center mb-4 mb-xxl-5">
							<div class="avatar-group me-3">
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-1.jpg') }}" class="rounded-circle" alt="Avatar 1"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-2.jpg') }}" class="rounded-circle" alt="Avatar 2"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-3.jpg') }}" class="rounded-circle" alt="Avatar 3"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-4.jpg') }}" class="rounded-circle" alt="Avatar 4"></div>
							</div>
							<div class="d-flex flex-column ms-2">
								<div class="d-flex gap-2 mb-1">
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
								</div>
								<span class="content-1">From 200+ reviews</span>
							</div>
						</div>
						<h1 class="heading-4 mb-2">Follow Us</h1>
						<div class="d-flex align-items-center mb-3 gap-3">
							<a href="{{ config('setting.social_facebook') }}" class="btn btn-light social-icon border-0 border-secondary rounded-circle d-flex align-items-center justify-content-center p-0">
								<img src="{{ asset('assets/image/icons/facebook.svg') }}" alt="Facebook Icon" class="img-fluid" style="width: 50%;">
							</a>
							<a href="{{ config('setting.social_instagram') }}"class="btn btn-light social-icon border-0 border-secondary rounded-circle d-flex align-items-center justify-content-center p-0">
								<img src="{{ asset('assets/image/icons/insta-icon.svg') }}" alt="Facebook Icon" class="img-fluid" style="width: 50%;">
							</a>
							<a href="{{ config('setting.social_linkedin') }}" class="btn btn-light social-icon border-0 border-secondary rounded-circle d-flex align-items-center justify-content-center p-0">
								<img src="{{ asset('assets/image/icons/linkedin.svg') }}" alt="Facebook Icon" class="img-fluid" style="width: 50%;">
							</a>
						</div>
					</div>
				</div>
				
				<!-- Right Form Section -->
				<div class="col-lg-6 col-xxl-5 d-flex align-items-center justify-content-center position-relative bg-white z-1">
					<div id="container" class="container d-flex align-items-center justify-content-center py-4">
						<!-- <div class="bg_overlay_3"></div>
						<div class="bg_overlay_4"></div> -->
						<div class="w-100 px-md-4 register-form-container z-2">
							<h6 class="heading-xl fw-medium text-primary text-center mb-4 mb-md-5">Register</h6>
							<ul class="nav nav-pills my-3 d-flex justify-content-center gap-2" id="pills-tab" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link px-5 active" id="register-individual-tab" data-bs-toggle="pill"
                                    data-bs-target="#register-individual" type="button" role="tab" aria-controls="register-individual"
                                    aria-selected="true">Individual</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link px-5" id="register-company-tab" data-bs-toggle="pill"
                                    data-bs-target="#register-company" type="button" role="tab" aria-controls="register-company"
                                    aria-selected="false">Corporate/Company</button>
								</li>
							</ul>
							
							<div class="tab-content" id="pills-tabContent">
								<div class="tab-pane fade show active" id="register-individual" role="tabpanel"
                                aria-labelledby="register-individual-tab"> 
									<form id="individualRegisterForm" action="{{ route('register.individual') }}" method="post">
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="first_name" class="required content-3 text-primary">First Name <span class="text-danger">*</span></label>
												<input id="first_name" name="first_name" type="text" class="form-control form-control-lg bg-light border-light"/>
											</div>
											<div class="col-md-6 mb-3">
												<label for="last_name" class="required content-3 text-primary">Last Name <span class="text-danger">*</span></label>
												<input id="last_name" name="last_name" type="text" class="form-control form-control-lg bg-light border-light"/> 
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="email" class="required content-3 text-primary">Email <span class="text-danger">*</span></label>
												<div class="input-group">
													<input id="email" name="email" type="email" class="form-control form-control-lg bg-light border-light" autocomplete="off"/>
													<input id="is_email_verify" name="is_email_verify" type="hidden" class="form-control form-control-lg bg-light border-light" value="0" >
													<button type="button" class="input-group-text border-0 btn-secondary text-white content-4" id="emailVerifyText" onclick="verifyOtp('email', event, 'individualRegisterForm')">
														<span class="before-verify">Verify</span>
														<div class="spinner-border text-light during-verify" role="status" style="display: none;"></div>
														<div class="after-verified" style="display: none;">
															<i class="bi bi-check-lg text-success me-1 content-2"></i>
															<span class="text-success">Verified</span>
														</div>
													</button>
												</div> 
											</div>
											
											<div class="col-md-6 mb-3">
												<label for="password" class="required content-3 text-primary">Password <span class="text-danger">*</span></label>
												<input id="password" name="password" type="password" autocomplete="off" class="form-control form-control-lg bg-light border-light" /> 
											</div> 
										</div>
										<div class="row">
											
											<div class="col-md-6 mb-3">
												<label for="confirmPassword" class="required content-3 text-primary">Confirm Password <span class="text-danger">*</span></label>
												<input id="password_confirmation" name="password_confirmation" type="password"
                                                class="form-control form-control-lg bg-light border-light" /> 
											</div>
											<div class="col-md-6 mb-3">
												<label for="country" class="required content-3 text-primary">Country <span class="text-danger">*</span></label>
												<select id="country_id" name="country_id" class="form-control form-control-lg bg-light border-light"> 
												</select>
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="mobile_number" class="required content-3 text-primary">Mobile Number (Without country code) <span class="text-danger">*</span></label>
												<div class="input-group">
													<input id="mobile_number" name="mobile_number" type="text" class="form-control form-control-lg bg-light border-light" autocomplete="off"  oninput="this.value = this.value.replace(/\D/g, '')"/>
													<input id="is_mobile_verify" name="is_mobile_verify" type="hidden" class="form-control form-control-lg bg-light border-light" value="0" >
													<button type="button" class="input-group-text border-0 btn-secondary text-white content-4" id="mobile_numberVerifyText" onclick="verifyOtp('mobile_number', event, 'individualRegisterForm')">
														<span class="before-verify">Verify</span>
														<div class="spinner-border text-light during-verify" role="status" style="display: none;"></div>
														<div class="after-verified" style="display: none;">
															<i class="bi bi-check-lg text-success me-1 content-2"></i>
															<span class="text-success">Verified</span>
														</div>
													</button>
												</div>
											</div>
											
											<div class="col-md-6 mb-3">
												<label for="referalcode" class="required content-3 text-primary">Promo Code</label>
												<input id="referalcode" name="referalcode" type="text" class="form-control form-control-lg bg-light border-light"/> 
											</div> 
										</div> 
										<div class="mb-3">
											<div class="d-flex">
												<input type="checkbox" id="terms" name="terms" class="me-2 content-3" value="1" />
												<label for="terms" class="d-flex text-secondary content-3"> <a target="_blank" href="{{ route('terms-and-condition') }}">I have read the User agreement and I accept it</a></label>
											</div> 
										</div> 
										<div class="text-center d-flex justify-content-center">
											<button type="submit" class="btn btn-lg btn-primary w-100 font-md">Register</button>
										</div>
									</form>
								</div>
								<div class="tab-pane fade" id="register-company" role="tabpanel" aria-labelledby="register-company-tab"> 
									<form class="mt-4" id="companyRegisterForm" action="{{ route('register.company') }}" method="post">
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="first_name" class="required content-3 text-primary">First Name <span class="text-danger">*</span></label>
												<input id="first_name" name="first_name" type="text" class="form-control form-control-lg bg-light border-light"/>
											</div>
											<div class="col-md-6 mb-3">
												<label for="last_name" class="required content-3 text-primary">Last Name <span class="text-danger">*</span></label>
												<input id="last_name" name="last_name" type="text" class="form-control form-control-lg bg-light border-light"/> 
											</div>
										</div>
									
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="email" class="required content-3 text-primary">Email <span class="text-danger">*</span></label>
												<div class="input-group">
													<input id="email" name="email" type="email" class="form-control form-control-lg bg-light border-light" autocomplete="off"/>
													<input id="is_email_verify" name="is_email_verify" type="hidden" class="form-control form-control-lg bg-light border-light" value="0" >
													<button type="button" class="input-group-text border-0 btn-secondary text-white content-4" id="emailVerifyText" onclick="verifyOtp('email', event, 'companyRegisterForm')">
														<span class="before-verify">Verify</span>
														<div class="spinner-border text-light during-verify" role="status" style="display: none;"></div>
														<div class="after-verified" style="display: none;">
															<i class="bi bi-check-lg text-success me-1 content-2"></i>
															<span class="text-success">Verified</span>
														</div>
													</button>
												</div> 
											</div>
											
											<div class="col-md-6 mb-3">
												<label for="company_name" class="required content-3 text-primary">Company Name <span class="text-danger">*</span></label>
												<input id="company_name" name="company_name" type="text" class="form-control form-control-lg bg-light border-light"/> 
											</div>  
										</div>
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="password" class="required content-3 text-primary">Password <span class="text-danger">*</span></label>
												<input id="password" name="password" type="password" autocomplete="off" class="form-control form-control-lg bg-light border-light" /> 
											</div> 
											<div class="col-md-6 mb-3">
												<label for="confirmPassword" class="required content-3 text-primary">Confirm Password <span class="text-danger">*</span></label>
												<input id="password_confirmation" name="password_confirmation" type="password"
												class="form-control form-control-lg bg-light border-light" /> 
											</div> 
										</div>
										
										<div class="row">
											<div class="col-md-6 mb-3">
												<label for="country" class="required content-3 text-primary">Country <span class="text-danger">*</span></label>
												<select id="country_id1" name="country_id" class="form-control form-control-lg bg-light border-light"> 
												</select>
											</div>
											<div class="col-md-6 mb-3">
												<label for="mobile_number" class="required content-3 text-primary">Mobile Number (Without country code) <span class="text-danger">*</span></label>
												<div class="input-group">
													<input id="mobile_number" name="mobile_number" type="text" class="form-control form-control-lg bg-light border-light" autocomplete="off"  oninput="this.value = this.value.replace(/\D/g, '')"/>
													<input id="is_mobile_verify" name="is_mobile_verify" type="hidden" class="form-control form-control-lg bg-light border-light" value="0" >
													<button type="button" class="input-group-text border-0 btn-secondary text-white content-4" id="mobile_numberVerifyText" onclick="verifyOtp('mobile_number', event, 'companyRegisterForm')">
														<span class="before-verify">Verify</span>
														<div class="spinner-border text-light during-verify" role="status" style="display: none;"></div>
														<div class="after-verified" style="display: none;">
															<i class="bi bi-check-lg text-success me-1 content-2"></i>
															<span class="text-success">Verified</span>
														</div>
													</button>
												</div>
											</div>
										</div> 
										<div class="mb-3">
											<div class="d-flex">
												<input type="checkbox" id="terms" name="terms" class="me-2 content-3" value="1"/>
												<label for="terms" class="d-flex text-secondary content-3">  <a target="_blank" href="{{ route('terms-and-condition') }}">I have read the User agreement and I accept it</a></label>
											</div> 
										</div> 
										<div class="text-center d-flex justify-content-center">
											<button type="submit" class="btn btn-lg btn-primary w-100 font-md">Register</button>
										</div> 
									</form>
								</div>
								<div class="d-flex align-items-center justify-content-center my-3">
									<hr class="flex-grow-1 hr-line text-secondary">
									<label class="d-flex text-center justify-content-end content-3 text-muted mx-2">Already have an account?</label>
									
									<hr class="flex-grow-1 hr-line text-secondary">
								</div>
								<a href="{{ route('login') }}" class="btn btn-lg btn-secondary w-100 mb-4">Login</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Email Verification Modal -->
		<div class="modal fade" id="verifyemailmodal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="d-flex justify-content-center align-items-center">
						<img src="{{ url('storage/setting', config('setting.fevicon_icon')) }}" width="80" class="modal-logo p-1">
					</div>
					 <!-- Modal Header -->
					<div class="text-end m-2">
						<button type="button" class="content-4 btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body p-4 pt-0">
						<form id="verifyOtpEmailForm" action="{{ route('verify.email-otp') }}" method="post">
							<b class="text-center d-block mb-4 heading-3 fw-medium">Verify Email Otp</b>
							<div class="mb-3"> 
								<input type="text" class="form-control form-control-lg bg-light border-light" id="email" name="email" placeholder="Enter Email">
							</div>
							<div class="mb-3"> 
								<input type="text" class="form-control form-control-lg bg-light border-light" id="otp" name="otp" placeholder="Enter OTP" oninput="this.value = this.value.replace(/\D/g, '')"> 
							</div>
							<span id="resendemailotp" class="content-3 text-secondary"></span>
							<div class="text-center d-flex justify-content-center mt-3">
								<button type="submit" class="btn btn-lg btn-primary w-100">Verify Otp</button>
							</div>
						</form>
					</div>                                                    
				</div>
			</div>
		</div>  
		
		<div class="modal fade" id="verifymobile_numbermodal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="d-flex justify-content-center align-items-center">
						<img src="{{ url('storage/setting', config('setting.fevicon_icon')) }}" width="80" class="modal-logo p-1">
					</div>
					 <!-- Modal Header -->
					<div class="text-end m-2">
						<button type="button" class="content-4 btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body p-4 pt-0">
						<form id="verifyOtpMobileForm" action="{{ route('verify.mobile-otp') }}" method="post">
							<b class="text-center d-block mb-4 heading-3 fw-medium">Verify Mobile Otp</b>
							<div class="mb-4"> 
								<input type="text" class="form-control form-control-lg bg-light border-light" id="mobile_number" name="mobile_number" placeholder="Enter Mobile">
							</div>
							<div class="mb-4"> 
								<input type="text" class="form-control form-control-lg bg-light border-light" id="otp" name="otp" placeholder="Enter OTP" oninput="this.value = this.value.replace(/\D/g, '')">
							</div>
							<span id="resendmobile_numberotp" class="content-3 text-secondary"></span>
							<div class="text-center d-flex justify-content-center mt-3">
								<button type="submit" class="btn btn-lg btn-primary w-100">Verify Otp</button>
							</div>
						</form>
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
		<script src="{{ asset('vendor/livewire/livewire.js?id=38dc8241') }}"
        data-csrf="{{ csrf_token() }}"
        data-update-uri="livewire/update"
        data-navigate-once="true"></script>
		<x-scripts :cryptoKey="$cryptoKey" />	
		
		<script>  
			var timer;
			var countdown = 60; // Set the countdown duration in seconds
			var $individualForm = $('#individualRegisterForm'); 
			var $companyForm = $('#companyRegisterForm');  
			 
			var countries = @json($countriesWithFlags);

			$(document).ready(function() {
				// Initialize Select2 for the individual form
				$individualForm.find('#country_id').select2({
					data: countries.map(country => ({
						id: country.id,
						text: country.name,
						flag: country.country_flag // Add custom data for the flag
					})),
					templateResult: formatCountry,
					templateSelection: formatCountrySelection,
					width: "100%"
				});
				
				// Initialize Select2 for the company form
				$companyForm.find('#country_id1').select2({
					data: countries.map(country => ({
						id: country.id,
						text: country.name,
						flag: country.country_flag
					})),
					templateResult: formatCountry,
					templateSelection: formatCountrySelection,
					width: "100%"
				});
				
				// Template for the dropdown items
				function formatCountry(country) {
					if (!country.id) {
						return country.text; // Default text if no id (for the placeholder option)
					}
					const flagImg = '<img src="'+country.flag+'" style="width: 20px; height: 20px; margin-right: 4px; margin-bottom: 4px;" />';
					return $('<span>'+flagImg+' '+country.text+'</span>');
				}

				// Template for the selected item
				function formatCountrySelection(country) {
					if (!country.id) {
						return country.text;
					}
					const flagImg = '<img src="'+country.flag+'" style="width: 20px; height: 20px; margin-right: 4px; margin-bottom: 4px;" />';
					return $('<span>'+flagImg+' '+country.text+'</span>');
				}
			
				// You can set a default value for both forms (e.g., country with ID 98)
				$individualForm.find('#country_id').val('98').trigger('change'); 
				$companyForm.find('#country_id1').val('98').trigger('change');
			});

			 
			// Attach the submit event handler
			$individualForm.submit(function(event) 
			{
				event.preventDefault();   
				 
				$individualForm.find('[type="submit"]')
				.prop('disabled', true) 
				.addClass('loading-span') 
				.html('<span class="spinner-border"></span>');
  
				var formData = {};
				$(this).find('input, select, checkbox').each(function() {
					var inputName = $(this).attr('name');

					if ($(this).is(':checkbox')) {
						// For checkboxes, store whether it is checked (true or false)
						formData[inputName] = $(this).is(':checked');
					} else {
						// For other inputs, use the value
						formData[inputName] = $(this).val();
					}
				});

				// Encrypt data before sending
				const encrypted_data = encryptData(JSON.stringify(formData));
 
				$.ajax({
					async: true,
					type: $(this).attr('method'),
					url: $(this).attr('action'),
					data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
					cache: false, 
					dataType: 'Json', 
					success: function (res) 
					{ 
						$individualForm.find('[type="submit"]')
						.prop('disabled', false)  
						.removeClass('loading-span') 
						.html('Register'); 
						
						$('.error_msg').remove(); 
						if(res.status === "success")
						{ 
							toastrMsg(res.status, res.message); 
							window.location.href = "{{ route('metamap.kyc') }}"; 
						}
						else if(res.status == "validation")
						{  
							$.each(res.errors, function(key, value) {
								var inputField = $individualForm.find('#' + key);
								var errorSpan = $('<span>')
								.addClass('error_msg text-danger content-4') 
								.attr('id', key + 'Error')
								.text(value[0]);  
								if(key == "email" || key == "mobile_number" || key == "terms")
								{
									inputField.parent().parent().append(errorSpan);
								}
								else
								{
									inputField.parent().append(errorSpan);
								}
								
							});
						}
						else
						{ 
							toastrMsg(res.status, res.message);
						}
					} 
				});
			});
			
			// Attach the submit event handler
			$companyForm.submit(function(event) 
			{
				event.preventDefault();   
				
				$companyForm.find('[type="submit"]')
				.prop('disabled', true) 
				.addClass('loading-span') 
				.html('<span class="spinner-border"></span>'); 
				
				var formData = {};
				$(this).find('input, select, checkbox').each(function() {
					var inputName = $(this).attr('name');

					if ($(this).is(':checkbox')) {
						// For checkboxes, store whether it is checked (true or false)
						formData[inputName] = $(this).is(':checked');
					} else {
						// For other inputs, use the value
						formData[inputName] = $(this).val();
					}
				});

				// Encrypt data before sending
				const encrypted_data = encryptData(JSON.stringify(formData));
 
				$.ajax({
					async: true,
					type: $(this).attr('method'),
					url: $(this).attr('action'),
					data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
					cache: false, 
					dataType: 'Json', 
					success: function (res) 
					{ 
						$companyForm.find('[type="submit"]')
						.prop('disabled', false)  
						.removeClass('loading-span') 
						.html('Register'); 
						$('.error_msg').remove(); 
						
						if(res.status === "success")
						{ 
							toastrMsg(res.status, res.message); 
							window.location.href = "{{ route('corporate.kyc') }}"; 
						}
						else if(res.status == "validation")
						{  
							$.each(res.errors, function(key, value) {
								var inputField = $companyForm.find('#' + key);
								var errorSpan = $('<span>')
								.addClass('error_msg text-danger content-4') 
								.attr('id', key + 'Error')
								.text(value[0]);  
								if(key == "email" || key == "mobile_number" || key == "terms")
								{
									inputField.parent().parent().append(errorSpan);
								}
								else
								{
									inputField.parent().append(errorSpan);
								}
								
							});
						}
						else
						{ 
							toastrMsg(res.status, res.message);
						}
					} 
				});
			});
			 
			$('#verifyOtpEmailForm, #verifyOtpMobileForm').submit(function(event) {
				event.preventDefault();
				 
				const $form = $(this); // Cache the current form being submitted
				$form.find('[type="submit"]')
				.prop('disabled', true) 
				.addClass('loading-span') 
				.html('<span class="spinner-border"></span>');

				// Create a JSON object from form data
				let formData = {};
				$form.find('input').each(function() {
					const inputName = $(this).attr('name');
					const inputValue = $(this).val();
					formData[inputName] = inputValue;
				});
				 
				// Encrypt data before sending
				const encrypted_data = encryptData(JSON.stringify(formData));

				$.ajax({
					async: true,
					type: $form.attr('method'),
					url: $form.attr('action'),
					data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
					cache: false,
					dataType: 'json',
					success: function(res) {
						$form.find('[type="submit"]')
						.prop('disabled', false)  
						.removeClass('loading-span') 
						.html('Verify Otp'); 
						
						$form.find('.error_msg').remove(); // Remove any previous error messages

						if (res.status === "success") 
						{ 
							toastrMsg(res.status, res.message);
							
							if ($form.attr('id') === 'verifyOtpEmailForm') 
							{ 	
								if($('#register-individual-tab').hasClass('active')) {
									$individualForm.find('#is_email_verify').val(1);
									$individualForm.find('#email').attr('readonly', true);
									$individualForm.find('#emailVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);

									var $button = $individualForm.find("#emailVerifyText"); 
									// Hide "Verify" text and show spinner
									$button.find(".before-verify").hide();
									$button.find(".during-verify").hide();
									$button.find(".after-verified").show();

								} else {
									$companyForm.find('#is_email_verify').val(1);
									$companyForm.find('#email').attr('readonly', true);
									$companyForm.find('#emailVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);
									var $button = $companyForm.find("#emailVerifyText"); 
									// Hide "Verify" text and show spinner
									$button.find(".before-verify").hide();
									$button.find(".during-verify").hide();
									$button.find(".after-verified").show();
								}
 
								$form.find('input').val('');
								$('#verifyemailmodal').modal('hide'); 
							}
							else
							{
								if($('#register-individual-tab').hasClass('active')) {
									$individualForm.find('#is_mobile_verify').val(1);
									$individualForm.find('#mobile_number').attr('readonly', true);
									$individualForm.find('#mobile_numberVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);
									var $button = $individualForm.find("#mobile_numberVerifyText"); 
									// Hide "Verify" text and show spinner
									$button.find(".before-verify").hide();
									$button.find(".during-verify").hide();
									$button.find(".after-verified").show();

								} else {
									$companyForm.find('#is_mobile_verify').val(1);
									$companyForm.find('#mobile_number').attr('readonly', true);
									$companyForm.find('#mobile_numberVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);

									var $button = $companyForm.find("#mobile_numberVerifyText"); 
									// Hide "Verify" text and show spinner
									$button.find(".before-verify").hide();
									$button.find(".during-verify").hide();
									$button.find(".after-verified").show();
								} 
								
								$form.find('input').val('');
								$('#verifymobile_numbermodal').modal('hide'); 
							}
						} else if (res.status === "validation") {
							$.each(res.errors, function(key, value) {
								const inputField = $form.find('#' + key);
								const errorSpan = $('<span>')
									.addClass('error_msg text-danger content-4')
									.attr('id', key + 'Error')
									.text(value[0]);
								inputField.parent().append(errorSpan);
							});
						} else { 
							toastrMsg(res.status, res.message);
						}
					} 
				});
			});
 
			function verifyOtp(keyId, event, formId)
			{
				event.preventDefault();   
				
				var $button = $('#'+ formId).find("#"+ keyId + "VerifyText");

				// Hide "Verify" text and show spinner
				$button.find(".before-verify").hide();
				$button.find(".during-verify").show();

				// Create a JSON object from form data
				var formData = {};
				var inputName = $('#'+ formId).find('#'+ keyId).attr('name');
				var inputValue = $('#'+ formId).find('#'+ keyId).val(); 
				formData[inputName] = inputValue;  
				 
				var countryInputName = $('#' + formId).find('#country_id, #country_id1').attr('name');  
				var countryInputValue = $('#' + formId).find('#country_id, #country_id1').val(); 
				 
				formData[countryInputName] = countryInputValue;
 
				// Encrypt data before sending
				const encrypted_data = encryptData(JSON.stringify(formData));
				
				const sendRoutes = {
					email: "{{ route('email.send') }}",
					mobile_number: "{{ route('mobile.send') }}", 
				};
 
				$.ajax({
					async: true,
					type: "POST",
					url: sendRoutes[keyId],
					data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
					cache: false, 
					dataType: 'Json', 
					success: function (res) 
					{   
						// Hide "Verify" text and show spinner
						$button.find(".before-verify").show();
						$button.find(".during-verify").hide();

						$('.error_msg').remove(); 
						if(res.status === "success")
						{ 
							$('#verify'+keyId+'modal').modal('show');
							$('#verify' + keyId + 'modal').find('input').val('');
							$('#verify' + keyId + 'modal').find('#'+ 'resend'+keyId+'otp').text('');
							if(res.response)
							{
								const result = decryptData(res.response); 
								if (result[keyId]) {
									// Fill both #email and #mobile_number fields with `result[keyId]` 
									$('#verify' + keyId + 'modal').find('form #email, form #mobile_number').val(result[keyId]);
								}
							}
							else
							{
								$('#verify' + keyId + 'modal').find('form #email, form #mobile_number').val(inputValue); 
							}
							const resendRoutes = {
								email: "{{ route('email.resend') }}",
								mobile_number: "{{ route('mobile.resend') }}", 
							};
							
							countdown = 60;
							clearInterval(timer);
							const resendUrl = resendRoutes[keyId];
							timer = setInterval(function() {
								updateTimer(keyId, 'resend'+keyId+'otp', resendUrl, $('#'+ formId));
							}, 1000);  
						}
						else if(res.status == "validation")
						{  
							$.each(res.errors, function(key, value) {
								var inputField = $('#'+ formId).find('input[name="'+key+'"]');
								var errorSpan = $('<span>')
								.addClass('error_msg text-danger content-4') 
								.attr('id', key + 'Error')
								.text(value[0]);  
								inputField.parent().parent().append(errorSpan); 
							});
						}
						else
						{    
							toastrMsg(res.status, res.message);
						}
					} 
				});
			}
			
			function resendOtp(keyId, resendId, resendUrl, commonForm) {
				// Ensure event.preventDefault() works
				event.preventDefault();

				// Clear any existing timer to avoid multiple timers running
				clearInterval(timer);

				let formData = {};
				
				const inputName = commonForm.find('#' + keyId).attr('name');
				const inputValue = commonForm.find('#' + keyId).val();
				formData[inputName] = inputValue;
				if(keyId == 'mobile_number')
				{ 
					formData['country_id'] = commonForm.find('[name="country_id"]').val(); 
				}
				// Encrypt data before sending
				const encrypted_data = encryptData(JSON.stringify(formData));

				$.ajax({
					async: true,
					type: "POST",
					url: resendUrl, // Using the dynamic URL passed to the function
					data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
					cache: false,
					dataType: 'json',
					success: function(res) {
						$('.error_msg').remove();
						if (res.status === "success") { 
							countdown = 60; // Reset countdown after successful OTP resend
							clearInterval(timer);
							timer = setInterval(function() {
								updateTimer(keyId, resendId, resendUrl, commonForm);
							}, 1000);
							toastrMsg(res.status, res.message);
						} 
						else if (res.status == "validation") 
						{
							$.each(res.errors, function(key, value) {
								const inputField = commonForm.find('#' + key);
								const errorSpan = $('<span>')
									.addClass('error_msg text-danger content-4')
									.attr('id', key + 'Error')
									.text(value[0]);
								inputField.parent().parent().append(errorSpan);
							});
						} else {
							toastrMsg(res.status, res.message)
						}
					}
				});
			}

			function updateTimer(keyId, resendId, resendUrl, commonForm) {
				const timerElement = document.getElementById(resendId);

				if (countdown > 0) {
					timerElement.textContent = `Resend in ${countdown} seconds`;
					countdown--;
				} else {
					// Enable the resend link once countdown reaches zero
					timerElement.innerHTML = '<a href="javascript:;" id="resendLink" class="content-3 text-secondary">Resend OTP</a>';

					// Attach event listener to trigger OTP resend functionality
					document.getElementById('resendLink').addEventListener('click', function(event) {
						 // Disable the link and change text to "Sending..."
						var resendLink = document.getElementById('resendLink');
						resendLink.innerHTML = 'Sending OTP, please wait...';
						resendLink.classList.add('disabled'); // Optionally add a 'disabled' class for styling (if necessary)
						resendLink.style.pointerEvents = 'none'; // Disable click events
						resendOtp(keyId, resendId, resendUrl, commonForm); // Call the function to resend OTP
					});

					// Stop the timer
					clearInterval(timer);
				}
			}
		</script> 
	</body> 
</html>
