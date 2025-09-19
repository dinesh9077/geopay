@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transfer To Bank')
@section('header_title', 'Transfer To Bank')
@section('content')
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<div class="d-flex justify-content-end">
				<button type="button" class="btn btn-primary mb-4" onclick="addTransferBankBeneficiary(this, event)">Add Recipient Details</button>
			</div>

			<form id="transferToBankForm" action="{{ route('transfer-to-bank.store') }}" method="post" class="animate__animated animate__fadeIn g-2">
				<input id="is_password" name="is_password" type="hidden" value="0"> 
				<div class="mb-1 row">
					<div class="col-12 mb-3" style="display:none;"> 
						<label for="country_code" class="form-label">Country <span class="text-danger">*</span></label>
						<select id="country_code" name="country_code" class="form-control form-control-lg content-3 default-input select3">
							<option value="">Select Country</option>
							@foreach($countries as $country) 
								<option 
									value="{{ $country['value'] }}" 
									data-flag="{{ $country['country_flag'] ?? '' }}"
									data-service-name="{{ $country['service_name'] }}" 
									data-country-name="{{ $country['label'] }}" 
									data-payout-country="{{ $country['data'] }}" 
									data-iso="{{ $country['iso'] }}">
									{{ $country['label'] }}
								</option>
							@endforeach
						</select>
					</div>
					
					<div class="col-12 mb-3"> 
						<label for="beneficiaryId" class="form-label">Recipient <span class="text-danger">*</span></label>
						<select id="beneficiaryId" name="beneficiaryId" class="form-control form-control-lg default-input content-3 select3">
							<option value="">Select Recipient</option>  
							@foreach($beneficiaries as $beneficiary)
								@php 
									$dataArr   = $beneficiary->data ?? [];
									$firstName = $dataArr['receiverfirstname'] ?? ($dataArr['recipient_name'] ?? '');
									$lastName  = $dataArr['receiverlastname'] ?? ($dataArr['recipient_surname'] ?? '');
									$bankName  = $dataArr['bankName'] ?? '';
									$bankName  = $dataArr['bankName'] ?? '';
									$payoutCurrency  = $dataArr['payoutCurrency'] ?? '';
									$countryFlag = $beneficiary->country() ? url('country', $beneficiary->country()->country_flag) : '';
								@endphp

								@if(!empty($firstName))
									<option value="{{ $beneficiary->id }}" data-flag="{{ $countryFlag ?? '' }}" data-payout-currency="{{ $payoutCurrency }}">
										{{ $firstName }} {{ $lastName }} ({{ $bankName }})
									</option>
								@endif
							@endforeach
						</select> 
					</div>
					  
					<div class="col-12 mb-3 showAfterConfirm"  style="display:none"> 
						<label for="txnAmount" class="form-label">Amount <span class="text-danger">*</span></label>
						<input id="txnAmount" name="txnAmount" class="form-control form-control-lg content-3 default-input"  placeholder="Enter Amount in {{config('setting.default_currency')}} (eg : 100 or eg : 0.0)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));"> 
					</div>
					  
					<div class="col-12 showAfterConfirm" id="commissionHtml" ></div>
					  
					<div class="col-12 mb-3 showAfterConfirm" style="display:none">
						<label for="txnAmount" class="form-label">Notes </label>
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
<!-- Password Confirmation Modal -->
<div class="modal fade" id="passwordConfirmModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Confirm Password</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<label for="confirmPassword" class="form-label">Enter your password</label>
				<input 
				type="password" 
				id="confirmPassword" 
				class="form-control" 
				placeholder="Password" 
				autocomplete="new-password" 
				autocapitalize="off" 
				spellcheck="false">

				<span class="text-danger small d-none" id="passwordError">Invalid password</span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="confirmPasswordBtn">Confirm</button>
			</div>
		</div>
	</div>
</div>


@endsection
@push('js')
<script>
	$('#transferToBankForm .select3').select2({ 
		width: "100%"
	});

	$(document).ready(function() {
		$('#country_code').select2({
			templateResult: formatCountryOption,
			templateSelection: formatCountryOption,
			placeholder: "Select Country"
		});

		function formatCountryOption (state) {
			if (!state.id) {
				return state.text;
			}

			var flag = $(state.element).data('flag');
			var countryName = state.text;

			if (flag) {
				return $(
					'<span><img src="' + flag + '" class="me-2" style="width: 20px; height: 20px; object-fit: cover;" />' + countryName + '</span>'
				);
			} else {
				return $(
					'<span>' + countryName + '</span>'
				);
			}
		}
		
		$('#beneficiaryId').select2({
			templateResult: formatCountryOption,
			templateSelection: formatCountryOption,
			placeholder: "Select Recipient",
			allowClear: true,
			width: "100%", 
		});
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
	
	$('#transferToBankForm #beneficiaryId').on('change', function() 
	{ 
		const payoutCurrency = $(this).find(':selected').data('payout-currency'); 
		$('#transferToBankForm').find('#country_code').val(payoutCurrency).change();
		$('#transferToBankForm').find('#txnAmount').val('');
		$('#transferToBankForm').find('#commissionHtml').empty();
		$('#transferToBankForm').find('#notes').val('');
	});
	
	/* $('#transferToBankForm #country_code').on('change', function() 
	{ 
		// Retrieve selected country payout data
		if(!$(this).val())
		{
			return;
		}
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
						$('#transferToBankForm #beneficiaryId').html(result.output);
					} else { 
						$('#transferToBankForm #beneficiaryId').html('<option value="">No beneficiary found</option>');
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
	}); */
	  
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
				$('#transferToBankForm').find('#netAmount, #totalCharges, #platformCharge, #serviceCharge, #payoutCurrencyAmount, #aggregatorCurrencyAmount, #exchangeRate, #aggregatorRate').remove();

				if (response.status === "success") {
					const result = decryptData(response.response);

					// Safely parse values
					const platformCharge = parseFloat(result.platformCharge) || 0;
					const serviceCharge = parseFloat(result.serviceCharge) || 0;
					const payoutCurrencyAmount = parseFloat(result.payoutCurrencyAmount) || 0;
					const aggregatorCurrencyAmount = parseFloat(result.aggregatorCurrencyAmount) || 0;
					const txnAmountFloat = parseFloat(txnAmount) || 0;

					// Calculate totals
					const aggregatorRate = parseFloat(result.aggregatorRate) || 0;
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
						<input type="hidden" id="aggregatorRate" name="aggregatorRate" value="${aggregatorRate}">
						<input type="hidden" id="totalCharges" name="totalCharges" value="${totalCharges}">
						<input type="hidden" id="platformCharge" name="platformCharge" value="${platformCharge}">
						<input type="hidden" id="serviceCharge" name="serviceCharge" value="${serviceCharge}">
						<input type="hidden" id="payoutCurrencyAmount" name="payoutCurrencyAmount" value="${payoutCurrencyAmount}">
						<input type="hidden" id="aggregatorCurrencyAmount" name="aggregatorCurrencyAmount" value="${aggregatorCurrencyAmount}">
					`;

					// Append all hidden fields at once
					$('#transferToBankForm').append(hiddenFields);

					// Prepare commission details HTML
					const commissionDetails = `
						<div class="w-100 text-start mb-3 p-2 rounded-2 border g-2 removeCommission">
							<div class="w-100 row m-auto">
								<div class="col-6 col-md-4">
									<span class="content-3 mb-0 text-dark fw-semibold text-nowrap">
										Processing Fee (${remitCurrency})
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

	$('#confirmPasswordBtn').on('click', function() {
		var password = $('#confirmPassword').val(); 
		if (!password) {
			$('#passwordError').removeClass('d-none').text("Password required");
			return;
		}

		$.ajax({
			url: "{{ route('password.verify') }}",
			type: "POST",
			data: {
				_token: "{{ csrf_token() }}",
				password: password
			},
			success: function(res) {
				if (res.valid) {
					$('#passwordError').addClass('d-none');
					$('#is_password').val(1); // hidden input in form
					$('#passwordConfirmModal').modal('hide'); 
					submitEncryptedForm(); // call a separate function instead of .submit()
				} else {
					$('#passwordError').removeClass('d-none').text("Invalid password");
				}
			}
		});
	});

	$transferToBankForm.submit(function(event) {
		event.preventDefault();    
		submitEncryptedForm();
	});

	function submitEncryptedForm() {
		$transferToBankForm.find('[type="submit"]')
			.prop('disabled', true) 
			.addClass('loading-span') 
			.html('<span class="spinner-border"></span>');

		var formData = {};
		$transferToBankForm.find('input, select, textarea').each(function() {
			var inputName = $(this).attr('name'); 
			formData[inputName] = $(this).val();
		});

		formData['category_name'] = 'transfer to bank'; 
		formData['service_name'] = $transferToBankForm.find('#country_code option:selected').data('service-name') ?? '';
		formData['payoutCountry'] = $transferToBankForm.find('#country_code option:selected').data('payout-country') ?? '';
		formData['payoutCountryName'] = $transferToBankForm.find('#country_code option:selected').data('country-name') ?? '';

		const encrypted_data = encryptData(JSON.stringify(formData));

		$.ajax({
			async: true,
			type: $transferToBankForm.attr('method'),
			url: $transferToBankForm.attr('action'),
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			cache: false, 
			dataType: 'json', 
			success: function (res) {
				$transferToBankForm.find('[type="submit"]')
					.prop('disabled', false)  
					.removeClass('loading-span') 
					.html('Submit'); 
				
				$('.error_msg').remove(); 
				if (res.status === "success") { 
					toastrMsg(res.status, res.message);  
					resetForm($transferToBankForm);  
					$('#is_password').val(0); // reset for next time
					Livewire.dispatch('refreshRecentTransactions'); 
					Livewire.dispatch('refreshNotificationDropdown');
					Livewire.dispatch('updateBalance');
				} else if (res.status == "validation") {  
					$.each(res.errors, function(key, value) {
						var inputField = $transferToBankForm.find('#' + key);
						var errorSpan = $('<span>')
							.addClass('error_msg text-danger content-4') 
							.attr('id', key + 'Error')
							.text(value[0]); 
						inputField.parent().append(errorSpan);
					});
				} else if (res.status == "password_confirmation") {  
					$('#passwordConfirmModal').modal('show'); 
				} else { 
					toastrMsg(res.status, res.message);
				}
			}
		});
	}
 
	
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
