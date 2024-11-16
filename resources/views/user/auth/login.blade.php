<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ config('setting.site_name') }} | Login</title>
		<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}"> 
		<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.css" /> 
	</head>
	
	<body>
		<div class="vh-100 overflow-hidden">
			<div class="row h-100">
				<!-- Left Section -->
				<div class="col-lg-7 d-none d-lg-flex align-items-end justify-content-start text-white auth-left-image-banner">
					<div class="content-wrapper m-4">
						<div class="mb-4">
							<img class="mb-4" src="{{ asset('assets/image/icons/spark-icon.svg') }}" alt="">
							<h2 class="mb-4">Very Good Works are waiting for you. Login now.</h2>
							<p class="font-sm">Access your account to explore exclusive features, personalized content, and stay up-to-date with the latest updates.</p>
						</div>
						<div class="d-flex align-items-center">
							<div class="avatar-group me-3">
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-1.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 1"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-2.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 2"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-3.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 3"></div>
								<div class="avatar-group-item"><img src="{{ asset('assets/image/avatar-4.jpg') }}" class="rounded-circle avatar-sm" alt="Avatar 4"></div>
							</div>
							<div class="d-flex flex-column ms-2">
								<div class="d-flex gap-2 mb-1">
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
									<i class="bi bi-star-fill text-warning"></i>
								</div>
								<span class="text-sm">From 200+ reviews</span>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Right Section -->
				<div class="col-lg-5 position-relative bg-white z-1">
					<div id="container" class="container d-flex vh-100 align-items-center justify-content-center">
						<div class="bg_overlay_3"></div>
						<div class="bg_overlay_4"></div>
						<div class="w-100 px-4 auth-container z-2">
							<div class="alert alert-danger" role="alert" style="display: none;">Error message here</div>
							<h6 class="fw-semibold text-black text-center mb-4">Sign in To Fintech</h6>
							
							<div class="d-flex justify-content-center mb-3 gap-3">
								<button class="btn social-icon border-1 border-secondary rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;">
									<img src="{{ asset('assets/image/icons/facebook.svg') }}" alt="Facebook Icon" class="img-fluid" style="width: 50%;">
								</button>
								<button class="btn social-icon border-1 border-secondary rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;">
									<img src="{{ asset('assets/image/icons/google.svg') }}" alt="Facebook Icon" class="img-fluid" style="width: 50%;">
								</button>
								<button class="btn social-icon border-1 border-secondary rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;">
									<img src="{{ asset('assets/image/icons/linkedin.svg') }}" alt="Facebook Icon" class="img-fluid" style="width: 50%;">
								</button>
							</div>
							
							<div class="text-center mb-3"> 
								<label class="d-flex text-center justify-content-center text-secondary font-md mb-3">Or use your {{ __('Email Address') }} account</label>
							</div>
							
							<form id="loginForm" action="{{ route('login.submit') }}" method="post">
								<div class="mb-3">
									<div class="input-group">
										<span class="input-group-text bg-light border-0"><i class="bi bi-envelope"></i></span>
										<input id="email" type="email" name="email" class="form-control border-0 bg-light" placeholder="Email">
									</div> 
								</div>
								
								<div class="mb-3">
									<div class="input-group">
										<span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill"></i></span>
										<input id="password" type="password" name="password" class="form-control border-0 bg-light" placeholder="Password">
									</div> 
								</div>
							 
								<div class="mb-3 d-flex justify-content-between"> 
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

										<label class="form-check-label" for="remember">
											{{ __('Remember Me') }}
										</label>
									</div>
									<label class="d-flex text-center justify-content-end text-secondary font-md mb-3"> <a href="{{ route('password.request') }}" >Forgot your password?</a> </label>
								</div>
								
								<div class="text-center">
									<button type="submit" class="btn btn-primary w-100">Login</button>
								</div>
								<div class="d-flex align-items-center justify-content-center my-3">
									<hr class="flex-grow-1 hr-line text-secondary">
									<label class="d-flex text-center justify-content-end text-muted font-md mx-2">Or with sign up</label>
									
									<hr class="flex-grow-1 hr-line text-secondary">
								</div>
								<a href="{{ route('register') }}" class="btn btn-secondary w-100">Sign up</a>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body> 
	<script src="https://kit.fontawesome.com/ae360af17e.js" ></script>
	<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
	<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
	<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
	@include('components.scripts')
	<script>  
		$('#loginForm').submit(function(event) 
		{
			event.preventDefault();   
			
			$(this).find('button').prop('disabled',true);   
			// Create a JSON object from form data
			var formData = {};
			$(this).find('input').each(function() {
				var inputName = $(this).attr('name');
				var inputValue = $(this).val();
				formData[inputName] = inputValue; // Add form field to JSON object
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
					$('#loginForm').find('button').prop('disabled',false);	 
					$('.error_msg').remove(); 
					if(res.status === "error")
					{ 
						toastrMsg(res.status,res.message);
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) {
							var inputField = $('#' + key);
							var errorSpan = $('<span>')
							.addClass('error_msg text-danger') 
							.attr('id', key + 'Error')
							.text(value[0]);  
							inputField.parent().parent().append(errorSpan);
						});
					}
					else
					{ 
						toastrMsg(res.status,res.message); 
						const decryptRes = decryptData(res.response);
						window.location.href = decryptRes.url;
					}
				} 
			});
		});
		   
	</script> 
</html>
