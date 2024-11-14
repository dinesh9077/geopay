<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ env('APP_NAME') }} | Meta Kyc</title> 
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />
	</head>
	
	<body>
		<div class="container-fluid">
			<div class="row min-vh-100">
				<!-- Right Form Section -->
				<div class="d-flex align-items-center justify-content-center position-relative bg-white z-1">
					<div id="container" class="container d-flex align-items-center justify-content-center py-4"> 
						<div class="bg_overlay_3"></div>
						<div class="bg_overlay_4"></div>
						<div class="w-100 px-4 register-form-container z-2 kyc-container" id="kyc_response_html">
							@if(!$userKyc)
								<h6 class="fw-semibold text-black text-center mb-4">Meta KYC Verification</h6>
								<p class="caption text-center">Our partner, MetaMap, provides a seamless and secure verification process, ensuring that your data is handled with the utmost care. Simply follow the steps below:</p>

								<ul class="caption text-center" style="color: gray; font-size: 0.8rem;">
									<li><b>Upload Documents:</b> Choose and upload clear images of your government-issued ID, such as a passport or driver's license.</li>
									<li><b>Selfie Capture:</b> Take a quick selfie to match your ID photo for further verification.</li>
									<li><b>Quick Processing:</b> Once your documents are submitted, MetaMap will process your KYC data securely and quickly, often within minutes.</li>
								</ul>

								<div class="text-center">
									<script src="https://web-button.metamap.com/button.js"></script>
									<div id="metamap-button-container"></div>
								</div> 
							@else
								@if($userKyc->status == "verified")
									<h6 class="fw-semibold text-black text-center mb-4">Your Meta KYC Is Completed.</h6>
									<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption">
										Thank you for completing your KYC submission! Your documents have been reviewed and approved.
										You can now continue using our services.
									</p>
									<div class="text-center">
										<a href="{{ route('home') }}" class="btn btn-primary btn-sm">Continue to use</a>
									</div> 
								@elseif($userKyc->status == "rejected")
									<h6 class="fw-semibold text-black text-center mb-4">Your Meta verification was rejected.</h6>
									<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption">
										Your verification has been rejected. Please follow the instructions below to reverify.
									</p>
									<ul style="color: gray; font-size: 0.8rem; text-align: center;">
										<li><b>Upload Documents:</b> Upload clear images of a government-issued ID, such as a passport or driver's license.</li>
										<li><b>Selfie Capture:</b> Take a selfie to match your ID photo for further verification.</li>
										<li><b>Quick Processing:</b> Once submitted, MetaMap will process your KYC data securely and quickly.</li>
									</ul>
									<div class="text-center">
										<div id="metamap-button-container"></div>
									</div> 
								@else
									<h6 class="fw-semibold text-black text-center mb-4">Thank you for Meta KYC</h6>
									<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption">
										Your documents are under review to ensure they meet our verification requirements. We will notify you once the process is complete.
									</p>
								@endif
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
		@include('components.scripts')
		<script>
			@if(!$userKyc)
				// Securely pass MetaMap data from the backend
				var encryptedData = @json($encryptedData);
				const decryptRes = decryptData(encryptedData);
				var metaClientId = decryptRes.metaClientId;
				var metaFlowId = decryptRes.metaFlowId; 
				
				// Dynamically create the MetaMap button and append it to the DOM
				var metamapButtonContainer = document.getElementById('metamap-button-container');
				var metamapButton = document.createElement('metamap-button');
				
				// Set the necessary attributes for the button
				metamapButton.setAttribute('clientid', metaClientId);
				metamapButton.setAttribute('flowId', metaFlowId);
				metamapButton.setAttribute('metadata', '{"user_id": "{{ $user->id }}","user_email": "{{ $user->email }}"}');  // You can pass other metadata here
				
				// Append the MetaMap button to the container
				metamapButtonContainer.appendChild(metamapButton);
				 
				// You could also trigger the KYC verification here based on user actions. 
				metamapButton.addEventListener('metamap:userFinishedSdk', async ({ detail }) => {
					//console.log('MetaMap response: ', detail);
					
					// Extract necessary fields from the detail object
					const { identityId, verificationId } = detail;
					
					// Validate the extracted data before sending to the API
					if (!identityId || !verificationId) { 
						toastrMsg('error', 'Missing required KYC data: identityId or verificationId.')
						return;
					}
					 
					try {
						
						// Prepare form data to send to backend
						var formData = {};
						formData['verification_id'] = verificationId;
						formData['identification_id'] = identityId;
						formData['response_data'] = JSON.stringify(detail);
						 
						// Encrypt data before sending
						const encrypted_data = encryptData(JSON.stringify(formData));
					
						$.ajax({
							async: true,
							type: "post",
							url: "{{ route('metamap.kyc-finished') }}",
							data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
							cache: false, 
							dataType: 'Json', 
							success: function (res) 
							{   
								if(res.status === "error")
								{ 
									toastrMsg(res.status, res.message);
								} 
								else
								{ 
									toastrMsg(res.status, res.message); 
									window.location.href = "{{ route('metamap.kyc') }}";
								}
							} 
						});
					} catch (error) { 
						console.error('Error during API request:', error);
						toastrMsg('error', 'An error occurred during the verification process. Please try again later.') 
					}
				});
				
			@else
				@if(!in_array($userKyc->status, ['verified', 'rejected']))
					// Define a variable to hold the interval ID
					let checkKycInterval = setInterval(() => {
						$.ajax({
							async: true,
							type: "get",
							url: "{{ route('metamap.kyc-check-status') }}", 
							cache: false, 
							dataType: 'json', 
							success: function (res) {   
								if (res.status === "success") { 
									toastrMsg(res.status, res.message); 
									const decryptRes = decryptData(res.response); 
									$('#kyc_response_html').html(decryptRes.output);

									// Stop the interval if the response status is "success"
									clearInterval(checkKycInterval);
								}  
							}
						});
					}, 2000); // 2000 ms (2 seconds) 
				@endif  
			@endif  
		</script>
	</body>
</html>														