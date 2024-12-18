@extends('user.layouts.app')
@section('title', config('setting.site_name').' - International Airtime')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 international-airtime-section"> 
			<form id="airtimeForm" action="{{ route('international-airtime.store') }}" method="post" class="animate__animated animate__fadeIn g-2">
				<div class="mb-1 row">
					<div class="col-12 mb-3"> 
						<select id="country_code" name="country_code" class="form-control form-control-lg default-input select2">
							<option value="">Select Country</option> 
							@foreach($countries as $country)
								<option value="{{ $country['iso_code'] }}">{{ $country['name'] }}</option> 
							@endforeach
						</select>
					</div>
					
					<div class="col-12 mb-3" id="operatorHtml" style="display:none;"> 
						<select id="operator_id" name="operator_id" class="form-control form-control-lg default-input select2">  
						</select>
					</div>
					
					<div class="col-12 mb-3" id="productHtml" style="display:none;"> 
						<select id="product_id" name="product_id" class="form-control form-control-lg default-input select2">  
						</select> 
					</div>
					
					<div class="col-12 mb-3" id="payableAmountHtml" style="display:none;">  
					</div>
					
					<div class="col-12 mb-3">
						<input type="number" id="mobile_number" name="mobile_number" class="form-control form-control-lg default-input mobile-number" placeholder="Enter your mobile number with country code"/>
					</div>
					 
					<div class="col-12 mb-3">
						<textarea name="notes" id="notes" class="form-control form-control-lg default-input" placeholder="Account Description"></textarea>
					</div> 
				</div>
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2"> 
					<button type="submit" class="btn btn-lg btn-primary rounded-2 text-nowrap">Submit</button>
				</div>
			</form> 
		</div>
		
		<!-- Quick Transfer Column -->
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection

@push('js')
<script>
	$('.select2').select2({
		width: "100%"
	});
	
	var $airtimeForm = $('#airtimeForm');
	$('#country_code').change(function ()
	{
		if(!$(this).val())
		{
			$('#operatorHtml').hide();
			$('#operator_id').val('').trigger('change');
			return;
		}
		let formData = {};
		const inputName = $(this).attr('name');
		const inputValue = $(this).val();
		formData[inputName] = inputValue;

		// Encrypt data before sending
		const encrypted_data = encryptData(JSON.stringify(formData));
		
		run_waitMe($('body'), 1, 'facebook')
		$.ajax({
			async: true,
			type: "POST",
			url: "{{ route('international-airtime.operator') }}", // Using the dynamic URL passed to the function
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			cache: false,
			dataType: 'json',
			success: function(res) 
			{ 
				$('body').waitMe('hide');
				if (res.status === "success") 
				{  
					// Assuming `res.response` is the encrypted JSON, decrypted into `result`.
					var result = decryptData(res.response); // Decrypt the response
					var output = ''; // Initialize an empty string for the dropdown options
					$('#operatorHtml').show();  
					if (result.length > 0) {
						output += '<option value="">Select operators</option>';
						result.forEach(function(operator) {
							// Check if the operator data is valid
							if (operator.name && operator.id) {
								output += `<option value="${operator.id}">${operator.name}</option>`;
							}
						});

						// Populate the dropdown with the generated options 
						$('#operator_id').html(output);  
					} else {
						// Handle the case where no operators are returned
						output = '<option value="">No operators available</option>';
						$('#operator_id').html(output);
					} 
				} 
				else {
					toastrMsg(res.status, res.message)
				}
			}
		});	
	});
	
	$('#operator_id').change(function ()
	{
		if(!$(this).val() || !$('#country_code').val())
		{
			$('#productHtml').hide();
			$('#product_id').val('').trigger('change');
			return;
		}
		
		let formData = {};

		// Collect form data
		formData[$(this).attr('name')] = $(this).val();
		formData[$('#country_code').attr('name')] = $('#country_code').val();

		// Encrypt data before sending
		const encrypted_data = encryptData(JSON.stringify(formData));
 
		run_waitMe($('body'), 1, 'facebook')
		$.ajax({
			async: true,
			type: "POST",
			url: "{{ route('international-airtime.product') }}", // Using the dynamic URL passed to the function
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			cache: false,
			dataType: 'json',
			success: function(res) 
			{ 
				$('body').waitMe('hide');
				if (res.status === "success") 
				{    
					var result = decryptData(res.response); 
					var output = '';  
					$('#productHtml').show();  
					if (result.length > 0) {
						output += '<option value="">Select Product</option>';
						result.forEach(function(product) 
						{ 
							if (product.name && product.id) {
								output += `<option value="${product.id}" data-name="${product.name}" data-unit="${product.unit}" data-rates="${product.rates}" data-unit_amount="${product.unit_amount}" data-unit_convert_currency="${product.unit_convert_currency}" data-unit_convert_amount="${product.unit_convert_amount}" data-unit_convert_exchange="${product.unit_convert_exchange}">${product.name} - ${product.unit_convert_amount.toFixed(2)} ${product.unit_convert_currency}</option>`;
							}
						});

						// Populate the dropdown with the generated options 
						$('#product_id').html(output);  
					} else {
						// Handle the case where no operators are returned
						output = '<option value="">No Products available</option>';
						$('#product_id').html(output);
					} 
				} 
				else {
					toastrMsg(res.status, res.message)
				}
			}
		});	
	});
	
	$('#product_id').change(function ()
	{
		if(!$(this).val())
		{
			$('#payableAmountHtml').html('').hide();
			return;
		}
		var payableAmount = $(this).find(':selected').attr('data-unit_convert_amount')
		var currency = $(this).find(':selected').attr('data-unit_convert_currency')
		var output = '';
		output += `<div class="w-100 text-start p-2 rounded-2 border g-2">
					<div class="w-100 row m-auto">
						<div class="col-6 col-md-6">
							<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">Fee(${currency}) <div class="text-muted fw-normal">0</div></span>
						</div> 
						<div class="text-md-end col-6 col-md-6">
							<span class="content-3 mb-0 text-dark fw-semibold text-nowrap"> Net Payable Amount In ${currency}  
							<div class="text-muted fw-normal">${parseFloat(payableAmount).toFixed(2)} ${currency}</div></span>
						</div>
					</div>
				</div>`;
		$('#payableAmountHtml').html(output).show();
	});
	
	let debounceTimer;
	
	var mobile_number = document.getElementById('mobile_number');

	mobile_number.addEventListener('input', function() {
	  let start = this.selectionStart;
	  let end = this.selectionEnd;
	  
	  const current = this.value
	  const corrected = current.replace(/([^+0-9]+)/gi, '');
	  this.value = corrected;
	  
	  if (corrected.length < current.length) --end;
	  this.setSelectionRange(start, end);
	});

	$('#mobile_number').on('input', function () {
		clearTimeout(debounceTimer); // Clear any previous timer
		const mobileNumber = $(this).val();
		const operatorId = $('#operator_id :selected').val(); // Assume there's an operator dropdown
		if(!operatorId)
		{
			return;
		}
		if (mobileNumber.length > 0) {
			debounceTimer = setTimeout(() => {
				validatePhoneNumber(mobileNumber, operatorId);
			}, 1000); // Wait 500ms after the last keystroke
		}
	});

	function validatePhoneNumber(mobileNumber, operatorId) 
	{
		let formData = {};

		// Collect form data
		formData['mobile_number'] = mobileNumber;
		formData['operator_id'] = operatorId;

		// Encrypt data before sending
		const encrypted_data = encryptData(JSON.stringify(formData));
 
		run_waitMe($('body'), 1, 'facebook')
		$.ajax({
			async: true,
			type: "POST",
			url: "{{ route('international-airtime.validate-phone') }}", // Using the dynamic URL passed to the function
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			cache: false,
			dataType: 'json',
			success: function(res) 
			{ 
				$('body').waitMe('hide'); // Hide the loading spinner
				$('.error_msg').remove(); // Remove any previous error messages

				// Remove any existing 'is_operator_match' hidden input
				$airtime.find('input[name="is_operator_match"]').remove();

				if (res.status === "error") {       
					// Append a hidden input with value 0
					$airtime.append('<input type="hidden" name="is_operator_match" value="0">');
					
					// Find the mobile number input field
					var inputField = $airtime.find('#mobile_number');
					
					// Create and append the error message
					if (!inputField.parent().find('.error_msg').length) {
						var errorSpan = $('<span>')
							.addClass('error_msg text-danger content-4') // Add required classes
							.text(res.message); // Set the error message text
						inputField.parent().append(errorSpan); // Append the error message
					}
				} else {
					// If success, append a hidden input with value 1
					$airtime.append('<input type="hidden" name="is_operator_match" value="1">');
				} 
			}
		});	
	}


	// Attach the submit event handler
	$airtime = $('#airtimeForm');
	$airtime.submit(function(event) 
	{
		event.preventDefault(); 
		$airtime.find('button').prop('disabled',true);  
		run_waitMe($('body'), 1, 'facebook');
		
		var formData = {};
		$airtime.find('input, select, textarea, checkbox').each(function() {
			var inputName = $(this).attr('name');

			if ($(this).is(':checkbox')) {
				// For checkboxes, store whether it is checked (true or false)
				formData[inputName] = $(this).is(':checked');
			} else {
				// For other inputs, use the value
				formData[inputName] = $(this).val();
			}
		});
		var unit_convert_amount = $airtime.find('#product_id :selected').attr('data-unit_convert_amount'); 
		formData['product_name'] = $airtime.find('#product_id :selected').attr('data-name');
		formData['unit_currency'] = $airtime.find('#product_id :selected').attr('data-unit');
		formData['rates'] = $airtime.find('#product_id :selected').attr('data-rates');
		formData['unit_amount'] = $airtime.find('#product_id :selected').attr('data-unit_amount');
		formData['unit_convert_currency'] = $airtime.find('#product_id :selected').attr('data-unit_convert_currency');
		formData['unit_convert_exchange'] = $airtime.find('#product_id :selected').attr('data-unit_convert_exchange');
		formData['unit_convert_amount'] = parseFloat(unit_convert_amount).toFixed(2);
		 
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
				$airtime.find('button').prop('disabled',false);	 
				$('body').waitMe('hide');
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status, res.message); 
					resetForm($airtime);  
					Livewire.dispatch('refreshRecentTransactions'); 
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $airtime.find('#' + key);
						var errorSpan = $('<span>')
						.addClass('error_msg text-danger content-4') 
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
	
	function resetForm($form) {
		$form[0].reset();
		$form.find('.error_msg').remove();
		$form.find('select').val(null).trigger('change');
	}
</script>
@endpush