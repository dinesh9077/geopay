@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transfer To Bank')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<div class="d-flex justify-content-end">
				<button type="button" class="btn btn-lg btn-primary mb-4" onclick="addTransferBankBeneficiary(this, event)">Add Beneficiary Details</button>
			</div>

			<form id="transferToBankForm" action="{{ route('transfer-to-bank.store') }}" method="post" class="animate__animated animate__fadeIn g-2">
				<div class="mb-1 row">
					<div class="col-12 mb-3"> 
						<select id="country_code" name="country_code" class="form-control form-control-lg content-3 default-input select3" >
							<option value="">Select Country</option>
							@foreach($countries as $country) 
							<option value="{{ $country['value'] }}" data-service-name="{{ $country['service_name'] }}" data-country-name="{{ $country['label'] }}" data-payout-country="{{ $country['data'] }}">{{ $country['label'] }}</option>
							@endforeach
						</select>
					</div>
					
					<div class="col-12 mb-3"> 
						<select id="beneficiaryId" name="beneficiaryId" class="form-control form-control-lg default-input content-3 select3" >
							<option value="">Select Beneficiary</option> 
						</select>
					</div>
					  
					<div class="col-12 mb-3"> 
						<input id="txnAmount" name="txnAmount" class="form-control form-control-lg content-3 default-input"  placeholder="Enter Amount in {{config('setting.default_currency')}} (eg : 100 or eg : 0.0)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));"> 
					</div>
					  
					<div class="col-12 mb-3" id="commissionHtml"></div>
					  
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
	$('#transferToBankForm .select3').select2({ 
		width: "100%"
	});
	
	function addTransferBankBeneficiary(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get("{{ route('transfer-to-bank.beneficiary') }}", function(res)
			{
				const result = decryptData(res.response);
				$('body').find('#modal-view-render').html(result.view);
				$('#addTransferBankBeneficiary').modal('show');  
			});
		} 
	}
	
	$('#transferToBankForm #country_code').on('change', function() { 
		// Retrieve selected country payout data
		const payoutCountry = $(this).find(':selected').data('payout-country');
		const serviceName = $(this).find(':selected').data('service-name');
		 
			
		// Prepare and encrypt form data
		let formData = {};
		formData['payoutCurrency'] =  $(this).val();
		formData['payoutCountry'] = payoutCountry;
		formData['serviceName'] = serviceName;
		formData['categoryName'] = 'transfer to bank';
		const encryptedData = encryptData(JSON.stringify(formData));

		// Show Loading Indicator
		run_waitMe($('body'), 1, 'facebook');
 
		$.ajax({
			type: 'POST',
			url: "{{ route('transfer-to-bank.beneficiary-list') }}",
			data: { 
				encrypted_data: encryptedData,
				_token: "{{ csrf_token() }}" 
			},
			dataType: 'json',
			success: function(response) {
				// Hide Loading Indicator
				$('body').waitMe('hide');

				try {
					// Decrypt and parse response
					const result = decryptData(response.response);
					
					// Check for valid output
					if (result.output) {
						$('#beneficiaryId').html(result.output);
					} else { 
						$('#beneficiaryId').html('<option value="">No beneficiary found</option>');
					}
				} catch (e) { 
					toastrMsg('error', 'An error occurred while processing the response.');
				}
			},
			error: function(xhr, status, error) {
				// Hide Loading Indicator
				$('body').waitMe('hide'); 
				toastrMsg('error', 'Error loading beneficiary list. Please try again.');
			}
		});
	});
	  
	$('#transferToBankForm #beneficiaryId').on('change', function() 
	{  
		// Prepare and encrypt form data
		let formData = {};
		formData['beneficiaryId'] =  $(this).val(); 
		const encryptedData = encryptData(JSON.stringify(formData));

		// Show Loading Indicator
		run_waitMe($('body'), 1, 'facebook');
 
		$.ajax({
			type: 'POST',
			url: "{{ route('transfer-to-bank.beneficiary-detail') }}",
			data: { 
				encrypted_data: encryptedData,
				_token: "{{ csrf_token() }}" 
			},
			dataType: 'json',
			success: function(response) {
				// Hide Loading Indicator
				$('body').waitMe('hide');

				if(response.status == "success")  
				{
					const result = decryptData(response.response);
					$('body').find('#modal-view-render').html(result.view);
					$('#confirmBeneficiaryModal').modal('show');  
				}
				else
				{
					toastrMsg(response.status, response.message);
					$('#transferToBankForm #beneficiaryId').val('').select2();
				}
			} 
		});
	});
	
	let debounceTimer;
	$('#transferToBankForm #txnAmount').on('input', function() 
	{  
		clearTimeout(debounceTimer);
		var txnAmount = $(this).val();
		if (txnAmount.length > 0) {
			debounceTimer = setTimeout(() => {
				commissionAmount(txnAmount);
			}, 700); // Wait 500ms after the last keystroke
		}
		
	});
	
	function commissionAmount(txnAmount)
	{
		let formData = {};
		formData['beneficiaryId'] = $('#transferToBankForm #beneficiaryId').val();   
		formData['txnAmount'] =  txnAmount; 
		const encryptedData = encryptData(JSON.stringify(formData));

		// Show Loading Indicator
		run_waitMe($('body'), 1, 'facebook');
 
		$.ajax({
			type: 'POST',
			url: "{{ route('transfer-to-bank.commission') }}",
			data: { 
				encrypted_data: encryptedData,
				_token: "{{ csrf_token() }}" 
			},
			dataType: 'json',
			success: function(response) {
				// Hide Loading Indicator
				$('body').waitMe('hide'); 
				// Remove old commission elements
				$('.removeCommission').remove();
				$('#transferToBankForm').find('#netAmount, #totalCharges, #platformCharge, #serviceCharge, #payoutCurrencyAmount, #exchangeRate').remove();

				if (response.status === "success") {
					const result = decryptData(response.response);

					// Safely parse values
					const platformCharge = parseFloat(result.platformCharge) || 0;
					const serviceCharge = parseFloat(result.serviceCharge) || 0;
					const payoutCurrencyAmount = parseFloat(result.payoutCurrencyAmount) || 0;
					const txnAmountFloat = parseFloat(txnAmount) || 0;

					// Calculate totals
					const exchangeRate = parseFloat(result.exchangeRate) || 0;
					const totalCharges = platformCharge + serviceCharge;
					const netAmount = totalCharges + txnAmountFloat;

					// Cache frequently accessed values
					const remitCurrency = result.remitCurrency || '';
					const payoutCurrency = result.payoutCurrency || '';

					// Prepare hidden input fields
					const hiddenFields = `
						<input type="hidden" id="netAmount" name="netAmount" value="${netAmount}">
						<input type="hidden" id="exchangeRate" name="exchangeRate" value="${exchangeRate}">
						<input type="hidden" id="totalCharges" name="totalCharges" value="${totalCharges}">
						<input type="hidden" id="platformCharge" name="platformCharge" value="${platformCharge}">
						<input type="hidden" id="serviceCharge" name="serviceCharge" value="${serviceCharge}">
						<input type="hidden" id="payoutCurrencyAmount" name="payoutCurrencyAmount" value="${payoutCurrencyAmount}">
					`;

					// Append all hidden fields at once
					$('#transferToBankForm').append(hiddenFields);

					// Prepare commission details HTML
					const commissionDetails = `
						<div class="w-100 text-start mb-3 p-2 rounded-2 border g-2 removeCommission">
							<div class="w-100 row m-auto">
								<div class="col-6 col-md-4">
									<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
										Service Charges + Platform Charges (${remitCurrency})
										<div class="text-muted fw-normal">${totalCharges.toFixed(2)}</div>
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
										Receivable Amount In ${payoutCurrency}
										<div class="text-muted fw-normal">${payoutCurrencyAmount.toFixed(2)}</div>
									</span>
								</div>
							</div> 
						</div>`;

					// Update the DOM efficiently
					$('#commissionHtml').html(commissionDetails);
				} 
				else
				{
					toastrMsg(response.status, response.message); 
				}
			} 
		});
	}
	 
	var $transferToBankForm = $('#transferToBankForm'); 
	$transferToBankForm.submit(function(event) 
	{
		event.preventDefault();   
		$transferToBankForm.find('button').prop('disabled',true);  
		run_waitMe($('body'), 1, 'facebook');
		var formData = {};
		$(this).find('input, select, textarea').each(function() {
			var inputName = $(this).attr('name'); 
			formData[inputName] = $(this).val();
		});
		
		formData['category_name'] = 'transfer to bank'; 
		formData['service_name'] = $transferToBankForm.find('#country_code').find(':selected').data('service-name') ?? '';
		formData['payoutCountry'] = $transferToBankForm.find('#country_code').find(':selected').data('payout-country') ?? '';
		formData['payoutCountryName'] = $transferToBankForm.find('#country_code').find(':selected').data('country-name') ?? '';
	  
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
				$transferToBankForm.find('button').prop('disabled',false);	 
				$('body').waitMe('hide');
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status, res.message);  
					resetForm($transferToBankForm);  
					Livewire.dispatch('refreshRecentTransactions'); 
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $transferToBankForm.find('#' + key);
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
		// Reset the form's values to their default state
		$form[0].reset();
		
		// Remove all elements with the class `error_msg`
		$form.find('.error_msg').remove();
		
		// Remove elements with the class `removeCommission`
		$form.find('.removeCommission').remove();
		
		// Reset all select elements and reinitialize the select2 plugin
		$form.find('select').val(null).select2();
	} 
</script>
@endpush
