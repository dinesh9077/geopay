<h6 class="fw-semibold text-black text-center mb-4">Verify OTP</h6> 
<label for="terms" class="d-flex text-center text-secondary font-md mb-3">
	Please enter the OTP sent to your email to verify your identity.
</label>
<form id="verifyOtpForm" action="{{ route('password.verifyOtp') }}" method="post">
	<div class="mb-4">
		<label for="email" class="required text-black font-md mb-2">Email</label>
		<div class="input-group">
			<input type="text" class="form-control border-0 bg-light" id="email" name="email" placeholder="Enter your email" readonly>
		</div> 
	</div>
	<div class="mb-4">
		<label for="otp" class="required text-black font-md mb-2">Enter OTP</label>
		<div class="input-group mb-3">
			<input type="text" class="form-control border-0 bg-light" id="otp" name="otp" placeholder="Enter your OTP">
		</div> 
		<span id="resendOtp"></span>
	</div>
	<div class="text-center">
		<button type="submit" class="btn btn-primary w-100">Verify OTP</button>
	</div>
</form>

<script>
	$('#verifyOtpForm').submit(function(event) 
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
				$('#verifyOtpForm').find('button').prop('disabled',false);	 
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
					const result = decryptData(res.response); 
					$('#response-view').html(result.view);
					$('#email').val(result.email); 
				}
			} 
		});
	}); 	
</script>
	

