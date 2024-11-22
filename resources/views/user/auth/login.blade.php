<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
	
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ config('setting.site_name') }} | Login</title>
		<link rel="icon" type="image/svg+xml" href="{{ url('storage/setting', config('setting.fevicon_icon')) }}">
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
				
				<!-- Right Section -->
				<div class="col-lg-5 position-relative bg-white z-1">
					<div id="container" class="container d-flex vh-100 align-items-center justify-content-center">
						<!-- <div class="bg_overlay_3"></div>
						<div class="bg_overlay_4"></div> -->
						<div class="w-100 px-4 auth-container z-2">
							<h6 class="heading-xl fw-medium text-primary text-center mb-5">Sign in To {{ config('setting.site_name') }}</h6>							
							<form id="loginForm" action="{{ route('login.submit') }}" method="post">
								<div class="mb-3">
									<label for="email" class="required content-3 text-primary">Email <span class="text-danger">*</span></label>
									<div class="input-group">
										<span class="input-group-text bg-light border-0"><img class="in-svg" src="{{ asset('assets/image/icons/envelope-icon.svg') }}" height="17" alt=""></span>
										<input id="email" type="email" name="email" class="form-control form-control-lg border-0 bg-light" placeholder="Email">
									</div> 
								</div>
								
								<div class="mb-3">
									<label for="password" class="required content-3 text-primary">Password <span class="text-danger">*</span></label>
									<div class="input-group">
										<span class="input-group-text bg-light border-0"><img class="in-svg" src="{{ asset('assets/image/icons/lock-icon.svg') }}" height="19" alt=""></span>
										<input id="password" type="password" name="password" class="form-control form-control-lg border-0 bg-light" placeholder="Password">
									</div> 
								</div>
							 
								<div class="mb-4 d-flex justify-content-between"> 
									<div class="d-flex align-items-center">
										<input class="form-check-input m-0 me-1" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

										<label class="form-check-label content-3" for="remember">
											{{ __('Remember Me') }}
										</label>
									</div>
									<label class="d-flex text-center justify-content-end text-secondary font-md"> <a class="content-3 text-primary" href="{{ route('password.request') }}" >Forgot your password?</a> </label>
								</div>
								
								<div class="text-center">
									<button type="submit" class="btn btn-lg btn-primary w-100">Login</button>
								</div>
								<div class="d-flex align-items-center justify-content-center my-3">
									<hr class="flex-grow-1 hr-line text-secondary">
									<label class="d-flex text-center justify-content-end content-3 text-muted mx-2">Don't have an account?</label>
									
									<hr class="flex-grow-1 hr-line text-secondary">
								</div>
								<a href="{{ route('register') }}" class="btn btn-lg btn-secondary w-100">Sign up</a>
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
							.addClass('error_msg text-danger content-4') 
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
