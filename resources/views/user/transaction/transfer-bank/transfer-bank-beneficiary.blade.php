<div class="modal fade" id="addTransferBankBeneficiary" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="staticBackdropLabel">Add Bank Transfer Beneficiary</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="return $('#addTransferBankBeneficiary').remove();" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form id="transferBankBeneficiaryForm" method="post" action="{{ route('transfer-to-bank.beneficiary-store') }}">
					<div class="row">  
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Country <span class="text-danger">*</span></label>
							<select id="payoutCurrency" name="payoutCurrency" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Country</option>
								@foreach($countries as $country) 
									<option value="{{ $country['value'] }}" data-flag="{{ $country['country_flag'] ?? '' }}" data-service-name="{{ $country['service_name'] }}" data-payout-country="{{ $country['data'] }}" data-country-name="{{ $country['label'] }}" data-iso="{{ $country['iso'] }}">{{ $country['label'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Bank Name <span class="text-danger">*</span></label>
							<select id="bankId" name="bankId" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Bank Name</option>
							</select>
						</div> 
					</div>
					
					<div class="row" id="dynamicFields">
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Bank Account Number <span class="text-danger">*</span></label>
							<input id="bankaccountnumber" name="bankaccountnumber" placeholder="Enter Bank Account Number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary First Name <span class="text-danger">*</span></label>
							<input id="receiverfirstname" name="receiverfirstname" placeholder="Enter Beneficiary First Name" type="text" class="form-control form-control-lg content-3" />
						</div>
						  
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Last Name <span class="text-danger">*</span></label>
							<input id="receiverlastname" name="receiverlastname" placeholder="Enter Beneficiary Last Name" type="text" class="form-control form-control-lg content-3" />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Address <span class="text-danger">*</span></label>
							<input id="receiveraddress" name="receiveraddress" placeholder="Enter Beneficiary Address" type="text" class="form-control form-control-lg content-3" />
						</div>
						  
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Mobile Number (eg:265244476305) <span class="text-danger">*</span></label>
							<input id="receivercontactnumber" name="receivercontactnumber" placeholder="Enter Beneficiary Mobile No" type="tel" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Select Beneficiary Relationship with sender <span class="text-danger">*</span></label>
							<select id="senderbeneficiaryrelationship" name="senderbeneficiaryrelationship" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Beneficiary Relationship with sender</option>
								@foreach($relationships as $relationship)
								<option value="{{ $relationship['data'] }}" data-relation-remark="{{ $relationship['value'] }}">{{ $relationship['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Remittance Purpose <span class="text-danger">*</span></label>
							<select id="purposeofremittance" name="purposeofremittance" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Remittance purpose</option>
								@foreach($purposeRemittances as $purposeRemittance)
								<option value="{{ $purposeRemittance['data'] }}" data-purpose-remittance-remarks="{{ $purposeRemittance['value'] }}">{{ $purposeRemittance['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender Source of Fund <span class="text-danger">*</span></label>
							<select id="sendersourceoffund" name="sendersourceoffund" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Source of Fund</option>
								@foreach($sourceOfFunds as $sourceOfFund)
								<option value="{{ $sourceOfFund['data'] }}" data-sender-sourceof-fundremarks="{{ $sourceOfFund['value'] }}">{{ $sourceOfFund['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Id Type <span class="text-danger">*</span></label>
							<select id="beneficiarytype" name="beneficiarytype" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Beneficiary Id Type</option>
								@foreach($documentOfCustomers as $documentOfCustomer)
								<option value="{{ $documentOfCustomer['data'] }}">{{ $documentOfCustomer['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 mb-md-0 col-sm-6">
							<label class="content-3 mb-0">Beneficiary Id Number <span class="text-danger">*</span></label>
							<input id="receiverIdTypeRemarks" name="receiverIdTypeRemarks" placeholder="Enter Beneficiary Id Number" type="text" class="form-control form-control-lg content-3" />
						</div>
						
						<div class="col-sm-6">
							<label class="content-3 mb-0">Receiver Id Expiry Date </label>
							<input id="receiverIdExpireDate" name="receiverIdExpireDate" placeholder="Enter Receiver Id Expiry Date" type="text" class="form-control form-control-lg "/>
						</div>
						
						<div class="col-sm-6">
							<label class="content-3 mb-0">Receiver Birthdate </label>
							<input id="receiverDateOfBirth" name="receiverDateOfBirth" placeholder="Enter Receiver Birthdate" type="text" class="form-control form-control-lg "/>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="return $('#addTransferBankBeneficiary').remove();">Close</button>
				<button type="submit" form="transferBankBeneficiaryForm" id="beneficiaryStore" class="btn btn-primary">Submit</button>
			</div>
		</div>
	</div>
</div> 
<script>  
	var $beneficiaryForm = $('#transferBankBeneficiaryForm'); 
	
	// Initialize Select2 for dropdowns
	$('#transferBankBeneficiaryForm .select2').select2({
		dropdownParent: $('#addTransferBankBeneficiary'),
		width: "100%"
	});
	
	// Initialize Flatpickr for date inputs
	flatpickr("#receiverIdExpireDate, #receiverDateOfBirth", {
		dateFormat: "Y-m-d"
	});
	 
	$(document).ready(function () {
		$('#payoutCurrency').select2({
			templateResult: formatCountryOption,
			templateSelection: formatCountryOption,
			placeholder: "Select Country",
			allowClear: true,
			width: "100%",
			dropdownParent: $('#addTransferBankBeneficiary') 
		});

		function formatCountryOption(state) {
			if (!state.id) {
				return state.text;
			}
			const flag = $(state.element).data('flag');
			const name = state.text;

			if (flag) {
				return $(
					'<span><img src="' + flag + '" class="me-2" style="width: 20px; height: 15px; object-fit: cover;" />' + name + '</span>'
				);
			}
			return $('<span>' + name + '</span>');
		}
	});


	// Handle Country Change for Bank List
	$('#transferBankBeneficiaryForm #payoutCurrency').change(function() { 
		var payoutCountry = $(this).find(':selected').data('payout-country'); 
		var payoutIso = $(this).find(':selected').data('iso'); 
		var serviceName = $(this).find(':selected').data('service-name');
		var formData = { payoutCountry: payoutCountry, serviceName: serviceName, payoutIso: payoutIso };
		const encrypted_data = encryptData(JSON.stringify(formData));
		
		// Show Loading Indicator
		run_waitMe($('#transferBankBeneficiaryForm'), 1, 'facebook');
		
		// AJAX Request to Fetch Banks
		$.ajax({
			type: 'POST',
			url: "{{ route('transfer-to-bank.bank-list') }}",
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			dataType: 'json',
			success: function(res) {
				$('#transferBankBeneficiaryForm').waitMe('hide');
				var result = decryptData(res.response);
				$('#bankId').html(result.output);
			},
			error: function(err) {
				$('#transferBankBeneficiaryForm').waitMe('hide');
				toastrMsg('error', 'Error loading bank list. Please try again.'); 
			}
		});
	});
	
	$('#transferBankBeneficiaryForm #bankId').change(function()
	{ 
		var locationId = $(this).val();
		var payoutCurrency = $beneficiaryForm.find('#payoutCurrency').val();
		var payoutCountry = $beneficiaryForm.find('#payoutCurrency :selected').data('payout-country');
		var payoutIso = $beneficiaryForm.find('#payoutCurrency :selected').data('payout-iso');
		var serviceName = $beneficiaryForm.find('#payoutCurrency :selected').data('service-name');

		if (!locationId || !payoutCurrency || !payoutCountry || !serviceName) { 
			toastrMsg('error', 'Missing required data for form submission.'); 
			return;
		}

		var formData = {
			payoutCountry: payoutCountry,
			payoutCurrency: payoutCurrency,
			payoutIso: payoutIso,
			serviceName: serviceName,
			locationId: locationId
		};

		const encrypted_data = encryptData(JSON.stringify(formData));
		
		// Show Loading Indicator
		run_waitMe($beneficiaryForm, 1, 'facebook');
		
		// AJAX Request to Fetch Banks
		$.ajax({
			type: 'POST',
			url: "{{ route('transfer-to-bank.get-fields') }}",
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			dataType: 'json',
			success: function(res) {
				$beneficiaryForm.waitMe('hide');
				if(res.status == "success")
				{
					var result = decryptData(res.response);  
					$('#dynamicFields').html(result.view);
				}  
			},
			error: function(err) {
				$beneficiaryForm.waitMe('hide');
				toastrMsg('error', 'Error loading fields. Please try again.'); 
			}
		});
	});
	
	// Attach the submit event handler 
	$beneficiaryForm.submit(function(event) 
	{
		event.preventDefault();   
		 
		if(($beneficiaryForm.find('#payoutCurrency').find(':selected').data('service-name') ?? '') == 'onafric')
		{ 
			if(!$('#sender_placeofbirth').val())
			{
				toastrMsg('warning', 'The sender date of birth is required');
				return;
			}
		}

		$('#beneficiaryStore')
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
		
		formData['category_name'] = 'transfer to bank'; 
		formData['service_name'] = $beneficiaryForm.find('#payoutCurrency').find(':selected').data('service-name') ?? '';
		formData['payoutCountry'] = $beneficiaryForm.find('#payoutCurrency').find(':selected').data('payout-country') ?? '';
		formData['payoutCountryName'] = $beneficiaryForm.find('#payoutCurrency').find(':selected').data('country-name') ?? ''; 
		formData['payoutIso'] = $beneficiaryForm.find('#payoutCurrency').find(':selected').data('iso') ?? ''; 
		formData['bankName'] = $beneficiaryForm.find('#bankId').find(':selected').data('bank-name') ?? '';
		  
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
				$('#beneficiaryStore')
				.prop('disabled', false)  
				.removeClass('loading-span') 
				.html('Submit'); 
				
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status, res.message);  
					$('#transferToBankForm #country_code').trigger('change');
					$('#addTransferBankBeneficiary').modal('hide');
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $beneficiaryForm.find('#' + key);
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
	
</script>