<p style="text-align: center;color: red;">Note: Please Upload All Document</p>
<form id="individualRegisterFinalForm" action="{{ $url }}" method="post">
	@foreach($userData as $key => $userDetail)
		<input name="{{ $key }}" type="hidden" value="{{ $userDetail }}" class="form-control form-control-lg bg-light border-light"/> 
	@endforeach
	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="first_name" class="required content-3 text-primary">Select Id Type <span class="text-danger">*</span></label>
			<select id="id_type" name="id_type" class="form-control form-control-lg bg-light border-light select2" required>
				<option value="">Select ID Type</option>
				@foreach(App\Enums\IdType::options() as $option)
					<option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
				@endforeach
			</select>
		</div>
		<div class="col-md-6 mb-3">
			<label for="last_name" class="required content-3 text-primary">ID Number <span class="text-danger">*</span></label>
			<input id="id_number" name="id_number" type="text" class="form-control form-control-lg bg-light border-light" required /> 
		</div>
	</div>
	
	<div class="row"> 
		<div class="col-md-6 mb-3">
			<label for="email" class="required content-3 text-primary">ID Issue Date <span class="text-danger">*</span></label>
			<input id="issue_id_date" name="issue_id_date" type="date" class="form-control form-control-lg bg-light border-light"  onclick="this.showPicker()" style="cursor: pointer;" required /> 
		</div>
		<div class="col-md-6 mb-3">
			<label for="email" class="required content-3 text-primary">ID Expiry Date <span class="text-danger">*</span></label>
			<input id="expiry_id_date" name="expiry_id_date" type="date" class="form-control form-control-lg bg-light border-light"  onclick="this.showPicker()" style="cursor: pointer;" required /> 
		</div>
	</div>	
	
	<div class="row"> 
		<div class="col-md-12 mb-3">
			<label for="password" class="required content-3 text-primary">Full Residential Address <span class="text-danger">*</span></label>
			<input id="address" name="address" type="text" autocomplete="off" class="form-control form-control-lg bg-light border-light" required /> 
		</div> 
	</div>
	
	<div class="row"> 
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">City <span class="text-danger">*</span></label>
			<input id="city" name="city" type="text" autocomplete="off" class="form-control form-control-lg bg-light border-light" required /> 
		</div> 
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">State <span class="text-danger">*</span></label>
			<input id="state" name="state" type="text" autocomplete="off" class="form-control form-control-lg bg-light border-light" required /> 
		</div> 
	</div> 
	<div class="row"> 
		
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">Zip Code/Postal Code <span class="text-danger">*</span></label>
			<input id="zip_code" name="zip_code" type="text" autocomplete="off" class="form-control form-control-lg bg-light border-light" required /> 
		</div> 
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">Date Of Birth <span class="text-danger">*</span></label>
			<input id="date_of_birth" name="date_of_birth" type="date" max="{{ date('Y-m-d') }}" autocomplete="off" class="form-control form-control-lg bg-light border-light" onclick="this.showPicker()" style="cursor: pointer;"  required /> 
		</div> 
	</div>
	<div class="row"> 
		
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">Zip Code/Postal Code <span class="text-danger">*</span></label>
			<select name="gender" class="form-control form-control-lg bg-light border-light select2" id="gender" required>
				<option value="">Select Gender</option>
				<option value="Male">Male</option>
				<option value="Female">Female</option>
				<option value="Other">Other</option>
			</select>
		</div> 
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">Business Activity or Occupation <span class="text-danger">*</span></label>
			<select name="business_activity_occupation" class="form-control form-control-lg bg-light border-light select2" id="business_activity_occupation" required>
				<option value="">Select Business Activity or Occupation</option>
				@foreach(App\Enums\BusinessOccupation::options() as $option)
					<option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
				@endforeach
			</select>
		</div> 
	</div>
	<div class="row">  
		<div class="col-md-6 mb-3">
			<label for="password" class="required content-3 text-primary">Source of Fund <span class="text-danger">*</span></label>
			<select name="source_of_fund" class="form-control form-control-lg bg-light border-light select2" id="source_of_fund" required>
				<option value="">Select Source of Fund</option>
				@foreach(App\Enums\SourceOfFunds::options() as $option)
					<option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
				@endforeach
			</select>
		</div> 
	</div> 
	<div class="text-center d-flex justify-content-center">
		<button type="submit" class="btn btn-lg btn-primary w-100 font-md">Save</button>
	</div>  
</form> 
<script>
	var $individualRegisterFinalForm = $('#individualRegisterFinalForm');
	
	$individualRegisterFinalForm.find('.select2').select2({ 
		width: "100%"
	});
	
	const formType = @json($type);
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
					if(formType == 1 || formType == "1")
					{ 
						window.location.href = "{{ route('metamap.kyc') }}";  
					}
					else
					{
						window.location.href = "{{ route('corporate.kyc') }}";  
					}
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
</script>