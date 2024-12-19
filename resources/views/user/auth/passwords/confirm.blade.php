<h6 class="fw-semibold text-black text-center mb-4">Reset Password</h6> 
<label for="terms" class="d-flex text-center text-secondary font-md mb-3">
	Follow the instructions in the email to choose a new password and confirm password to set new password.
</label>
<form id="resetPasswordForm" action="{{ route('password.reset') }}" method="post">
	<div class="mb-4">
		<label for="email" class="required text-black font-md mb-2">Email</label>
		<div class="input-group">
			<input type="text" class="form-control border-0 bg-light" id="email" name="email" placeholder="Enter your email" readonly>
		</div> 
	</div>
	<div class="mb-4">
		<label for="otp" class="required text-black font-md mb-2">Password</label>
		<div class="input-group mb-3">
			<input type="password" class="form-control border-0 bg-light" id="password" name="password" placeholder="Enter your Password">
		</div>  
	</div>
	<div class="mb-4">
		<label for="otp" class="required text-black font-md mb-2">Password Confirmation</label>
		<div class="input-group mb-3">
			<input type="text" class="form-control border-0 bg-light" id="password_confirmation" name="password_confirmation" placeholder="Enter your Password Confirmation">
		</div>  
	</div>
	<div class="text-center d-flex justify-content-center">
		<button type="submit" class="btn btn-primary w-100">Reset Password</button>
	</div>
</form>

<script>
	var $resetPasswordForm = $('#resetPasswordForm');
	$('#resetPasswordForm').submit(function(event) 
	{
		event.preventDefault();   
		
		$resetPasswordForm.find('[type="submit"]')
		.prop('disabled', true) 
		.addClass('loading-span') 
		.html('<span class="spinner-border"></span>');
		
		// Create a JSON object from form data
		var formData = {};
		$(this).find('input').each(function() {
			var inputName = $(this).attr('name');
			var inputValue = $(this).val();
			formData[inputName] = inputValue; // Add form field to JSON object
		}); 
		 
		// Encrypt data before sending
		var encrypted_data = encryptData(JSON.stringify(formData));
		 
		$.ajax({
			async: true,
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			cache: false, 
			dataType: 'Json', 
			success: function (res) 
			{ 
				$resetPasswordForm.find('[type="submit"]')
				.prop('disabled', false)  
				.removeClass('loading-span') 
				.html('Register');  
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status,res.message);
					setTimeout(function() {
						window.location.href = "{{ route('login') }}";
					}, 1000); 
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
				}
			} 
		});
	}); 	
</script>

