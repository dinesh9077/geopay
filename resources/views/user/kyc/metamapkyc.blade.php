<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ config('setting.site_name') }} | Meta Kyc</title> 
		<link rel="stylesheet" href="{{ asset('assets/css/animate.min.css') }}" /> 
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" />
	</head>
 
	<body>
		<div class="container-fluid kyc-page">
			<div class="row min-vh-100">
				<!-- Right Form Section -->
				<div class="d-flex align-items-center justify-content-center position-relative z-1">
					<a href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary position-absolute top-0 end-0 m-3 d-none d-lg-block"><i class="bi bi-power ms-1"></i> Logout </a>
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
						@csrf
					</form>
					<div id="container" class="container d-flex align-items-center justify-content-center py-4">
						<div class="w-100 p-4 shadow rounded-4 register-form-container z-2 kyc-container text-center position-relative" id="kyc_response_html"> 
							<a href="{{ route('logout') }}"  onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary position-absolute top-0 end-0 m-3 d-lg-none"><i class="bi bi-power ms-1"></i></a>
							<img class="mb-xxl-4 mb-3" src="{{ url('storage/setting', config('setting.site_logo')) }}" alt="" style="max-width: 100px;">
							<div class="card card-body mb-0 kyc-result-contents mw-100">
								@if(!$userKyc || ($userKyc && $userKyc->verification_status == "pending"))
									<h6 class="fw-semibold text-black text-center mb-4">Meta KYC Verification</h6>
									<p class="caption text-center px-0">Our partner, MetaMap, provides a seamless and secure verification process, ensuring that your data is handled with the utmost care. Simply follow the steps below:</p>
	
									<ul class="caption text-muted content-3 px-0">
										<li class="d-md-flex align-items-start mb-3"><b class="text-nowrap me-2 text-dark d-flex align-items-center"><span class="number">1</span> Upload Documents:</b> Choose and upload clear images of your government-issued ID, such as a passport or driver's license.</li>
										<li class="d-md-flex align-items-start mb-3"><b class="text-nowrap me-2 text-dark d-flex align-items-center"><span class="number">2</span> Selfie Capture:</b> Take a quick selfie to match your ID photo for further verification.</li>
										<li class="d-md-flex align-items-start mb-3"><b class="text-nowrap me-2 text-dark d-flex align-items-center"><span class="number">3</span> Quick Processing:</b> Once your documents are submitted, MetaMap will process your KYC data securely and quickly, often within minutes.</li>
									</ul>
	
									<div class="text-center">
										<script src="https://web-button.metamap.com/button.js"></script>
										<div id="metamap-button-container"></div>
									</div>  
								@else
									@if($userKyc->verification_status == "verified")
										<h6 class="fw-semibold text-success text-center mb-4">Meta Kyc Verified</h6>
										<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption">
											Thank you for completing your KYC submission! Your documents have been reviewed and approved.
											You can now continue using our services.
										</p>
										<div class="text-center">
											<a href="{{ route('home') }}" class="btn btn-primary btn-sm">Dashbaord</a>
										</div> 
										{{-- @elseif($userKyc->verification_status == "rejected")
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
										</div> --}}
									@else
										<h6 class="fw-semibold text-warning text-center mb-4">Thank you for Meta KYC</h6>
										<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption"> 
											You’re documents are still under review, If you think the process is taking longer than expected, please reach out to us on the following:  
										</p>
										<p style="color: gray; font-size: 0.8rem; text-align: center;" class="caption"> 
											Email : support@geopayments.co   
										</p>
										<!--<h6 class="heading-4  text-black mb-1 text-center">Thank You</h6>
										<p class="caption text-muted content-3 text-center">{{ config('setting.site_name') }} Team</p>-->
									@endif
								@endif
							</div>
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
		<script>
			@if(!$userKyc || ($userKyc && $userKyc->verification_status == "pending"))
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
								if(res.status === "success")
								{  
									toastrMsg(res.status, res.message);
									window.location.href = "{{ route('metamap.kyc') }}";
								} 
								else
								{  
									toastrMsg(res.status, res.message);  
								}
							} 
						}); 
						 
					} catch (error) {  
						window.location.href = "{{ route('metamap.kyc') }}";
					} 
				}); 
			@else
				@if(!in_array($userKyc->verification_status, ['verified', 'rejected']))
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