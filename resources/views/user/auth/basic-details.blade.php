<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('setting.site_name') }} | Basic Info</title>
    <link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
    <link rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />
	<style>
		.content-3.text-primary {
			display: flex;
		}
		.select2-container .select2-selection--single .select2-selection__rendered {
			display: flex;
			padding-left: 8px;
			padding-right: 20px;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}

		.error_msg
		{
			display: flex;
		}
	</style>
</head>

<body>
    <div class="container-fluid kyc-page">
        <div class="row min-vh-100">
            <!-- Right Form Section -->
            <div class="d-flex align-items-center justify-content-center position-relative z-1">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="btn btn-primary position-absolute top-0 end-0 m-3 d-none d-lg-block"><i
                        class="bi bi-power ms-1"></i> Logout </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                <div id="container" class="container d-flex align-items-center justify-content-center py-4">
                    <div class="w-100 p-4 shadow rounded-4 register-form-container z-2 kyc-container text-center position-relative"
                        id="kyc_response_html">
                        <p style="text-align: center;color: red;">Note: Please fill out all fields.</p>
                        <form id="individualRegisterFinalForm" action="{{ route('user.basic-details.update') }}" method="post">
                            <div class="row">
								<div class="col-md-6 mb-3">
									<label for="email" class="required content-3 text-primary">Email <span class="text-danger">*</span></label>
									<div class="input-group">
										<input id="email" name="email" readonly autocomplete="off" type="email" class="form-control form-control-lg bg-light border-light" value="{{ $user->email }}" autocomplete="off"/>
										<input id="is_email_verify"  name="is_email_verify" type="hidden" class="form-control form-control-lg bg-light border-light" value="0" >
										<button type="button" class="input-group-text border-0 btn-secondary text-white content-4" id="emailVerifyText" onclick="verifyOtp('email', event, 'individualRegisterFinalForm')">
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
										<label for="country" class="required content-3 text-primary">Country <span class="text-danger">*</span></label>
										<select id="country_id" name="country_id" class="form-control form-control-lg bg-light border-light"> 
										</select>
									</div> 
								<div class="col-md-6 mb-3">
									<label for="mobile_number" class="required content-3 text-primary">Mobile Number (Without country code) <span class="text-danger">*</span></label>
									<div class="input-group">
										<input id="mobile_number" name="mobile_number" type="text" class="form-control form-control-lg bg-light border-light" autocomplete="off"  oninput="this.value = this.value.replace(/\D/g, '')"/>
										<input id="is_mobile_verify" name="is_mobile_verify" type="hidden" class="form-control form-control-lg bg-light border-light" value="0" >
										<button type="button" class="input-group-text border-0 btn-secondary text-white content-4" id="mobile_numberVerifyText" onclick="verifyOtp('mobile_number', event, 'individualRegisterFinalForm')">
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
                                    <label for="first_name" class="required content-3 text-primary">Select Id Type <span
                                            class="text-danger">*</span></label>
                                    <select id="id_type" name="id_type"
                                        class="form-control form-control-lg bg-light border-light select2">
                                        <option value="">Select ID Type</option>
                                        @foreach (App\Enums\IdType::options() as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="required content-3 text-primary">ID Number <span
                                            class="text-danger">*</span></label>
                                    <input id="id_number" name="id_number" type="text"
                                        class="form-control form-control-lg bg-light border-light"  />
                                </div>
                            
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="required content-3 text-primary">ID Issue Date <span
                                            class="text-danger">*</span></label>
                                    <input id="issue_id_date" name="issue_id_date" type="date"
                                        class="form-control form-control-lg bg-light border-light"
                                        onclick="this.showPicker()" style="cursor: pointer;"  />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="required content-3 text-primary">ID Expiry Date <span
                                            class="text-danger">*</span></label>
                                    <input id="expiry_id_date" name="expiry_id_date" type="date"
                                        class="form-control form-control-lg bg-light border-light"
                                        onclick="this.showPicker()" style="cursor: pointer;"  />
                                </div>
                             
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">Full Residential
                                        Address <span class="text-danger">*</span></label>
                                    <input id="address" name="address" type="text" autocomplete="off"
                                        class="form-control form-control-lg bg-light border-light"  />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">City <span
                                            class="text-danger">*</span></label>
                                    <input id="city" name="city" type="text" autocomplete="off"
                                        class="form-control form-control-lg bg-light border-light"  />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">State <span
                                            class="text-danger">*</span></label>
                                    <input id="state" name="state" type="text" autocomplete="off"
                                        class="form-control form-control-lg bg-light border-light"  />
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">Zip Code/Postal Code
                                        <span class="text-danger">*</span></label>
                                    <input id="zip_code" name="zip_code" type="text" autocomplete="off"
                                        class="form-control form-control-lg bg-light border-light"  />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">Date Of Birth <span
                                            class="text-danger">*</span></label>
                                    <input id="date_of_birth" name="date_of_birth" type="date"
                                        max="{{ date('Y-m-d') }}" autocomplete="off"
                                        class="form-control form-control-lg bg-light border-light"
                                        onclick="this.showPicker()" style="cursor: pointer;"  />
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">Zip Code/Postal Code
                                        <span class="text-danger">*</span></label>
                                    <select name="gender"
                                        class="form-control form-control-lg bg-light border-light select2"
                                        id="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">Business Activity or
                                        Occupation <span class="text-danger">*</span></label>
                                    <select name="business_activity_occupation"
                                        class="form-control form-control-lg bg-light border-light select2"
                                        id="business_activity_occupation" >
                                        <option value="">Select Business Activity or Occupation</option>
                                        @foreach (App\Enums\BusinessOccupation::options() as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="required content-3 text-primary">Source of Fund <span
                                            class="text-danger">*</span></label>
                                    <select name="source_of_fund"
                                        class="form-control form-control-lg bg-light border-light select2"
                                        id="source_of_fund" >
                                        <option value="">Select Source of Fund</option>
                                        @foreach (App\Enums\SourceOfFunds::options() as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="text-center d-flex justify-content-center">
                                <button type="submit" class="btn btn-lg btn-primary w-100 font-md">Save</button>
                            </div>
                        </form>
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

    <script src="https://kit.fontawesome.com/ae360af17e.js"></script>
    <script src="{{ asset('assets/js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/crypto-js.min.js') }}"></script>
    <x-scripts :cryptoKey="$cryptoKey" />
	<script>
		var $individualRegisterFinalForm = $('#individualRegisterFinalForm');
		var timer;
		var countdown = 60;
		$individualRegisterFinalForm.find('.select2').select2({ 
			width: "100%"
		});

		var countries = @json($countriesWithFlags);  
		$(document).ready(function() {
			// Initialize Select2 for the individual form
			$individualRegisterFinalForm.find('#country_id').select2({
				data: countries.map(country => ({
					id: country.id,
					text: country.name,
					flag: country.country_flag // Add custom data for the flag
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
			$individualRegisterFinalForm.find('#country_id').val('99').trigger('change');  
		});
		
		$individualRegisterFinalForm.submit(function(event) 
		{
			event.preventDefault();   
			
			$individualRegisterFinalForm.find('[type="submit"]')
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
					$individualRegisterFinalForm.find('[type="submit"]')
					.prop('disabled', false)  
					.removeClass('loading-span') 
					.html('Save'); 
					
					$('.error_msg').remove(); 
					if(res.status === "success")
					{ 
						toastrMsg(res.status, res.message);  
						setTimeout(() => {
							location.href = "{{ url('home') }}";
						}, 1000);
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value)
						{
							if(key === "password")
							{
								var inputField = $individualRegisterFinalForm.find('#' + key);
								var existingList = $individualRegisterFinalForm.find('#' + key + 'ErrorList');

								// Remove previous error list
								if (existingList.length) {
									existingList.remove();
								}

								// Create a new <ul> list to hold error <li>s
								var errorList = $('<ul style="padding-left: 1rem;">')
									.addClass('error_msg text-danger')
									.attr('id', key + 'ErrorList');

								// Add each error as <li>
								$.each(value, function(i, msg) {
									errorList.append($('<li style="list-style: disc;" class="content-4">').text(msg));
								});

								// Append the list after the input field
								inputField.parent().append(errorList);
							}else{
								var inputField = $individualRegisterFinalForm.find('#' + key);
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
		
		document.querySelectorAll('input[type="date"]').forEach(input => {
			input.addEventListener('focus', function () {
				this.showPicker?.();
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
								$individualRegisterFinalForm.find('#is_email_verify').val(1);
								$individualRegisterFinalForm.find('#email').attr('readonly', true);
								$individualRegisterFinalForm.find('#emailVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);

								var $button = $individualRegisterFinalForm.find("#emailVerifyText"); 
								// Hide "Verify" text and show spinner
								$button.find(".before-verify").hide();
								$button.find(".during-verify").hide();
								$button.find(".after-verified").show();

							} else {
								$individualRegisterFinalForm.find('#is_email_verify').val(1);
								$individualRegisterFinalForm.find('#email').attr('readonly', true);
								$individualRegisterFinalForm.find('#emailVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);
								var $button = $individualRegisterFinalForm.find("#emailVerifyText"); 
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
								$individualRegisterFinalForm.find('#is_mobile_verify').val(1);
								$individualRegisterFinalForm.find('#mobile_number').attr('readonly', true);
								$individualRegisterFinalForm.find('#mobile_numberVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);
								var $button = $individualRegisterFinalForm.find("#mobile_numberVerifyText"); 
								// Hide "Verify" text and show spinner
								$button.find(".before-verify").hide();
								$button.find(".during-verify").hide();
								$button.find(".after-verified").show();

							} else {
								$individualRegisterFinalForm.find('#is_mobile_verify').val(1);
								$individualRegisterFinalForm.find('#mobile_number').attr('readonly', true);
								$individualRegisterFinalForm.find('#mobile_numberVerifyText').removeClass('btn-secondary').addClass('btn-light').attr('disabled', true);

								var $button = $individualRegisterFinalForm.find("#mobile_numberVerifyText"); 
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
