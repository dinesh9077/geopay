<!-- meta tags and other links -->
<!DOCTYPE html>
<html class="" lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ env('APP_NAME') }} - Admin Login</title>
		<link rel="icon" type="image/png" href="{{ asset('admin/images/favicon.png') }}" sizes="16x16">
		<!-- google fonts -->
		<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
		<!-- remix icon font css  -->
		<link rel="stylesheet" href="{{ asset('admin/css/remixicon.css') }}">
		
		<link rel="stylesheet" href="{{ asset('assets/css/toastr.min.css') }}">
		<!-- main css -->
		<link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
	</head>
	<body class="dark:bg-neutral-800 bg-neutral-100 dark:text-white">
		
		<section class="bg-white dark:bg-dark-2 flex flex-wrap min-h-[100vh]">  
			 
			<div class="lg:w-1/2 py-8 px-6 flex flex-col justify-center" style="margin: auto;">
				<div class="lg:max-w-[464px] mx-auto w-full">
					<div> 
						<h4 class="mb-3">Sign In to your Account</h4>
						<p class="mb-8 text-secondary-light text-lg">Welcome back! please enter your detail</p>
					</div>
					<form id="loginForm" action="{{ route('admin.login.submit') }}" method="POST">
						<div class="icon-field mb-4 relative">
							<span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
								<iconify-icon icon="mage:email"></iconify-icon>
							</span>
							<input type="email" id="email" name="email" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="Email address" autocomplete="off" required>
						</div>
						<div class="relative mb-5">
							<div class="icon-field">
								<span class="absolute start-4 top-1/2 -translate-y-1/2 pointer-events-none flex text-xl">
									<iconify-icon icon="solar:lock-password-outline"></iconify-icon>
								</span> 
								<input type="password" id="password" name="password" class="form-control h-[56px] ps-11 border-neutral-300 bg-neutral-50 dark:bg-dark-2 rounded-xl" placeholder="Password" required>
							</div>
							<span class="toggle-password ri-eye-line cursor-pointer absolute end-0 top-1/2 -translate-y-1/2 me-4 text-secondary-light" data-toggle="#password"></span>
						</div>  
						<button type="submit" class="btn btn-primary justify-center text-sm btn-sm px-3 py-4 w-full rounded-xl mt-8"> Log In</button> 
					</form>
				</div>
			</div>
		</section>
		
		<!-- jQuery library js -->
		<script src="{{ asset('admin/js/lib/jquery-3.7.1.min.js') }}"></script> 
		<!-- Iconify Font js -->
		<script src="{{ asset('admin/js/lib/iconify-icon.min.js') }}"></script>
		<!-- jQuery UI js -->
		<script src="{{ asset('admin/js/lib/jquery-ui.min.js') }}"></script>
		<script src="{{ asset('assets/js/toastr.min.js')}}" ></script>
		<script src="{{ asset('assets/js/crypto-js.min.js')}}" ></script>
		<x-scripts :cryptoKey="$cryptoKey" />
		
		<script> 
			function initializePasswordToggle(toggleSelector) {
				$(toggleSelector).on('click', function() {
					$(this).toggleClass("ri-eye-off-line");
					var input = $($(this).attr("data-toggle"));
					if (input.attr("type") === "password") {
						input.attr("type", "text");
						} else {
						input.attr("type", "password");
					}
				});
			} 
			initializePasswordToggle('.toggle-password'); 
			
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
									.addClass('text-danger-600') // Explicitly add Tailwind class
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
