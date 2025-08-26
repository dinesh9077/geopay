<!DOCTYPE html>
<html lang="en">
	<head> 
		<title>{{ config('setting.site_name') }} - Admin Login</title>
		<link rel="shortcut icon" href="{{ url('storage/setting', config('setting.fevicon_icon')) }}" />
		<!-- color-modes:js -->
		<script src="{{ asset('admin/js/color-modes.js') }}"></script>
		<!-- endinject -->
		
		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
		<!-- End fonts -->
		
		<!-- core:css -->
		<link rel="stylesheet" href="{{ asset('admin/vendors/core/core.css') }}">
		<!-- endinject -->
		 
		<!-- inject:css -->
		<link rel="stylesheet" href="{{ asset('admin/fonts/feather-font/css/iconfont.css') }}">
		<!-- endinject -->
		
		<!-- Layout styles -->  
		<link rel="stylesheet" href="{{ asset('admin/css/demo1/style.css') }}">
		<!-- End layout styles --> 
		<link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
	</head>
	<body>
		<div class="main-wrapper">
			<div class="page-wrapper full-page">
				<div class="page-content d-flex justify-content-center">
					
					<div class="row w-100 mx-0 auth-page">
						<div class="col-md-10 col-lg-8 col-xl-6 mx-auto">
							<div class="card m-auto mt-sm-7" style="max-width:500px">  
								<div class="auth-form-wrapper px-4 py-5">
									<a href="{{ url('/') }}" class="nobleui-logo d-block mb-2">{{ config('setting.site_name') }}</a>
									<h5 class="text-secondary fw-normal mb-4">Welcome back! Log in to your account.</h5>
									<form class="forms-sample" id="loginForm" action="{{ route('admin.login.submit') }}" method="POST">
										<div class="mb-3">
											<label for="userEmail" class="form-label">Email address</label>
											<input type="email" id="email" name="email" class="form-control"  placeholder="Email">
										</div>
										<div class="mb-3">
											<label for="userPassword" class="form-label">Password</label>
											<input type="password" id="password" name="password" class="form-control" autocomplete="current-password" placeholder="Password">
										</div> 
										<div>
											<button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Login</button> 
										</div> 
									</form>
								</div> 
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
		 
		<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}" ></script>
		 
		<script src="{{ asset('admin/vendors/feather-icons/feather.min.js') }}"></script> 
		
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" />
		
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
						if(res.status === "success")
						{ 
							toastrMsg(res.status,res.message); 
							const decryptRes = decryptData(res.response);
						 
							window.location.href = decryptRes.url;
						}
						else if(res.status == "validation")
						{  
							$.each(res.errors, function(key, value) {
								var inputField = $('#' + key);
								var errorSpan = $('<span>')
									.addClass('error_msg') // Add other classes if necessary
									.addClass('text-danger') // Explicitly add Tailwind class
									.attr('id', key + 'Error')
									.text(value[0]);
								inputField.parent().append(errorSpan);
							}); 
						}
						else
						{ 				
							toastrMsg(res.status, res.message);  
						}
					} 
				});
			});
		</script> 
	</body>
</html>