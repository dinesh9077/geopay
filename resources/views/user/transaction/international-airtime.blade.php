@extends('user.layouts.app')
@section('title', config('setting.site_name').' - International Airtime')
@section('header_title', 'International Airtime')
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
					<button type="submit" class="btn btn-primary rounded-2 text-nowrap">Submit</button>
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
		$('#payableAmountHtml').html('').hide();
		
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
							if (product.name && product.id) 
							{
								// Validate wholesale and retail prices  
								const attributes = [
									{ key: "name", value: product.name },
									{ key: "retail_unit_currency", value: product.retail_unit_currency },
									{ key: "retail_unit_amount", value: product.retail_unit_amount },
									{ key: "wholesale_unit_currency", value: product.wholesale_unit_currency },
									{ key: "wholesale_unit_amount", value: product.wholesale_unit_amount },
									{ key: "retail_rates", value: product.retail_rates },
									{ key: "wholesale_rates", value: product.wholesale_rates },
									{ key: "destination_currency", value: product.destination_currency },
									{ key: "platform_fees", value: product.platform_fees },
									{ key: "destination_rates", value: product.destination_rates },
									{ key: "remit_currency", value: product.remit_currency }
								];

								const dataAttributes = attributes
									.map(attr => `data-${attr.key}="${attr.value}"`)
									.join(" ");

								const formattedAmount = parseFloat(product.retail_unit_amount).toFixed(2);

								output += `<option value="${product.id}" ${dataAttributes}>
									${product.name} - ${formattedAmount} ${product.retail_unit_currency}
								</option>`;
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
		var payableAmount = $(this).find(':selected').attr('data-retail_unit_amount');
		var currency = $(this).find(':selected').attr('data-retail_unit_currency');
		var platformFees = parseFloat($(this).find(':selected').attr('data-platform_fees'));
		var remitCurrency = $(this).find(':selected').attr('data-remit_currency');
		var destinationCurrency = $(this).find(':selected').attr('data-destination_currency');
		var destinationRates =  parseFloat($(this).find(':selected').attr('data-destination_rates'));
		var	netAmount = parseFloat(platformFees) + parseFloat(payableAmount);
		var output = ''; 
		output += `<div class="w-100 text-start mb-3 p-2 rounded-2 border g-2 removeCommission">
			<div class="w-100 row m-auto">
				<div class="col-6 col-md-4">
					<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
						Processing Fees (${remitCurrency})
						<div class="text-muted fw-normal">${platformFees.toFixed(2)}</div>
					</span>
				</div>
				<div class="col-6 col-md-4">
					<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
						Net Amount In ${remitCurrency}
						<div class="text-muted fw-normal">${netAmount.toFixed(2)}</div>
					</span>
				</div>
				<div class="text-md-end col-6 col-md-4">
					<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
						Receivable Amount In ${destinationCurrency}
						<div class="text-muted fw-normal">${destinationRates.toFixed(2)}</div>
					</span>
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
			}, 700); // Wait 500ms after the last keystroke
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
		$airtime.find('[type="submit"]')
		.prop('disabled', true) 
		.addClass('loading-span') 
		.html('<span class="spinner-border"></span>');
		
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
		var retail_unit_amount = $airtime.find('#product_id :selected').attr('data-retail_unit_amount');  
		var wholesale_unit_amount = $airtime.find('#product_id :selected').attr('data-wholesale_unit_amount');  
		var retail_rates = $airtime.find('#product_id :selected').attr('data-retail_rates');  
		var wholesale_rates = $airtime.find('#product_id :selected').attr('data-wholesale_rates');  
		var destination_rates = $airtime.find('#product_id :selected').attr('data-destination_rates');  
		var platform_fees = $airtime.find('#product_id :selected').attr('data-platform_fees');  
		 
		formData['product_name'] = $airtime.find('#product_id :selected').attr('data-name'); 
		formData['retail_unit_currency'] = $airtime.find('#product_id :selected').attr('data-retail_unit_currency'); 
		formData['wholesale_unit_currency'] = $airtime.find('#product_id :selected').attr('data-wholesale_unit_currency');  
		formData['retail_unit_amount'] = parseFloat(retail_unit_amount).toFixed(2);
		formData['wholesale_unit_amount'] = parseFloat(wholesale_unit_amount).toFixed(2);
		formData['retail_rates'] = parseFloat(retail_rates).toFixed(2);
		formData['wholesale_rates'] = parseFloat(wholesale_rates).toFixed(2);
		formData['destination_rates'] = parseFloat(destination_rates).toFixed(2); 
		formData['platform_fees'] = parseFloat(platform_fees).toFixed(2); 
		formData['destination_currency'] = $airtime.find('#product_id :selected').attr('data-destination_currency'); 
		 
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
				$airtime.find('[type="submit"]')
				.prop('disabled', false)  
				.removeClass('loading-span') 
				.html('Submit'); 
				
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status, res.message); 
					resetForm($airtime);  
					Livewire.dispatch('refreshRecentTransactions'); 
					Livewire.dispatch('updateBalance');
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