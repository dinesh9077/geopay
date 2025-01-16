<div class="modal fade" id="editTransferBankBeneficiary" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Bank Transfer Beneficiary</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="return $('#editTransferBankBeneficiary, .modal-backdrop').remove();" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form id="editTransferBankBeneficiaryForm" method="post" action="{{ route('transfer-to-bank.beneficiary-update', ['id' => $beneficiary->id]) }}">
					<div class="row">  
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Country <span class="text-danger">*</span></label>
							<select id="payoutCurrency" name="payoutCurrency" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Country</option>
								@foreach($countries as $country) 
									<option value="{{ $country['value'] }}" data-service-name="{{ $country['service_name'] }}" data-payout-country="{{ $country['data'] }}" data-country-name="{{ $country['label'] }}" {{ isset($edit['payoutCurrency']) && $edit['payoutCurrency'] === $country['value'] ? 'selected' : '' }}>{{ $country['label'] }}</option>
								@endforeach
							</select>
						</div>
						 
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Bank Name <span class="text-danger">*</span></label>
							<select id="bankId" name="bankId" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Bank Name</option>
								@foreach($banks as $bank) 
									<option value="{{ $bank['locationId'] }}" data-bank-name="{{ $bank['locationName'] }}" {{ isset($edit['bankId']) && $edit['bankId'] === $bank['locationId'] ? 'selected' : '' }}>{{ $bank['locationName'] }}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="row" id="dynamicFields">  
						@if($fieldView)
							{!! $fieldView !!}
						@else
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Bank Account Number <span class="text-danger">*</span></label>
								<input id="bankaccountnumber" name="bankaccountnumber" placeholder="Enter Bank Account Number" type="text" class="form-control form-control-lg content-3" value="{{ $edit['bankaccountnumber'] }}" oninput="this.value = this.value.replace(/\D/g, '')"/>
							</div>
							
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Beneficiary First Name <span class="text-danger">*</span></label>
								<input id="receiverfirstname" name="receiverfirstname" placeholder="Enter Beneficiary First Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['receiverfirstname'] }}"/>
							</div>
							 
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Beneficiary Last Name <span class="text-danger">*</span></label>
								<input id="receiverlastname" name="receiverlastname" placeholder="Enter Beneficiary Last Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['receiverlastname'] }}"/>
							</div>
							
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Beneficiary Address <span class="text-danger">*</span></label>
								<input id="receiveraddress" name="receiveraddress" placeholder="Enter Beneficiary Address" type="text" class="form-control form-control-lg content-3" value="{{ $edit['receiveraddress'] }}"/>
							</div>
							 
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Beneficiary Mobile Number (eg:265244476305) <span class="text-danger">*</span></label>
								<input id="receivercontactnumber" name="receivercontactnumber" placeholder="Enter Beneficiary Mobile No" type="tel" class="form-control form-control-lg content-3" value="{{ $edit['receivercontactnumber'] }}" oninput="this.value = this.value.replace(/\D/g, '')"/>
							</div>
							
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Select Beneficiary Relationship with sender <span class="text-danger">*</span></label>
								<select id="senderbeneficiaryrelationship" name="senderbeneficiaryrelationship" class="form-control form-control-lg content-3 select2" >
									<option value="">Select Beneficiary Relationship with sender</option>
									@foreach($relationships as $relationship)
									<option value="{{ $relationship['data'] }}" data-relation-remark="{{ $relationship['value'] }}" {{ isset($edit['senderbeneficiaryrelationship']) && $edit['senderbeneficiaryrelationship'] === $relationship['data'] ? 'selected' : '' }}>{{ $relationship['value'] }}</option>
									@endforeach
								</select>
							</div>
							
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Remittance Purpose <span class="text-danger">*</span></label>
								<select id="purposeofremittance" name="purposeofremittance" class="form-control form-control-lg content-3 select2" >
									<option value="">Select Remittance purpose</option>
									@foreach($purposeRemittances as $purposeRemittance)
									<option value="{{ $purposeRemittance['data'] }}" data-purpose-remittance-remarks="{{ $purposeRemittance['value'] }}" {{ isset($edit['purposeofremittance']) && $edit['purposeofremittance'] === $purposeRemittance['data'] ? 'selected' : '' }}>{{ $purposeRemittance['value'] }}</option>
									@endforeach
								</select>
							</div>
							
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Sender Source of Fund <span class="text-danger">*</span></label>
								<select id="sendersourceoffund" name="sendersourceoffund" class="form-control form-control-lg content-3 select2" >
									<option value="">Select Source of Fund</option>
									@foreach($sourceOfFunds as $sourceOfFund)
									<option value="{{ $sourceOfFund['data'] }}" data-sender-sourceof-fundremarks="{{ $sourceOfFund['value'] }}" {{ isset($edit['sendersourceoffund']) && $edit['sendersourceoffund'] === $sourceOfFund['data'] ? 'selected' : '' }}>{{ $sourceOfFund['value'] }}</option>
									@endforeach
								</select>
							</div>
							
							<div class="mb-4 col-md-6">
								<label class="content-3 mb-0">Beneficiary Id Type <span class="text-danger">*</span></label>
								<select id="beneficiarytype" name="beneficiarytype" class="form-control form-control-lg content-3 select2" >
									<option value="">Select Beneficiary Id Type</option>
									@foreach($documentOfCustomers as $documentOfCustomer)
									<option value="{{ $documentOfCustomer['data'] }}" {{ isset($edit['beneficiarytype']) && $edit['beneficiarytype'] === $documentOfCustomer['data'] ? 'selected' : '' }}>{{ $documentOfCustomer['value'] }}</option>
									@endforeach
								</select>
							</div>
							
							<div class="mb-4 mb-md-0 col-sm-6">
								<label class="content-3 mb-0">Beneficiary Id Number <span class="text-danger">*</span></label>
								<input id="receiverIdTypeRemarks" name="receiverIdTypeRemarks" placeholder="Enter Beneficiary Id Number" type="text" class="form-control form-control-lg content-3" value="{{ $edit['receiverIdTypeRemarks'] }}"/>
							</div>
							
							<div class="col-sm-6">
								<label class="content-3 mb-0">Receiver Id Expiry Date </label>
								<input id="receiverIdExpireDate" name="receiverIdExpireDate" placeholder="Enter Receiver Id Expiry Date" type="text" class="form-control form-control-lg " value="{{ $edit['receiverIdExpireDate'] }}"/>
							</div>
							
							<div class="col-sm-6">
								<label class="content-3 mb-0">Receiver Birthdate </label>
								<input id="receiverDateOfBirth" name="receiverDateOfBirth" placeholder="Enter Receiver Birthdate" type="text" class="form-control form-control-lg " value="{{ $edit['receiverDateOfBirth'] }}"/>
							</div>
						@endif
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="return $('#editTransferBankBeneficiary, .modal-backdrop').remove();">Close</button>
				<button type="submit" form="editTransferBankBeneficiaryForm" id="beneficiaryUpdate" class="btn btn-primary">Update</button>
			</div>
		</div>
	</div>
</div> 
<script> 
	// Initialize Select2 for dropdowns
	$('#editTransferBankBeneficiaryForm .select2').select2({
		dropdownParent: $('#editTransferBankBeneficiary'),
		width: "100%"
	});
	
	// Initialize Flatpickr for date inputs
	flatpickr("#receiverIdExpireDate, #receiverDateOfBirth", {
		dateFormat: "Y-m-d"
	});
	
	// Handle Country Change for Bank List
	$('#editTransferBankBeneficiaryForm #payoutCurrency').change(function() { 
		var payoutCountry = $(this).find(':selected').data('payout-country');
		var formData = { payoutCountry: payoutCountry };
		const encrypted_data = encryptData(JSON.stringify(formData));
		
		// Show Loading Indicator
		run_waitMe($('#editTransferBankBeneficiaryForm'), 1, 'facebook');
		
		// AJAX Request to Fetch Banks
		$.ajax({
			type: 'POST',
			url: "{{ route('transfer-to-bank.bank-list') }}",
			data: { encrypted_data: encrypted_data, '_token': "{{ csrf_token() }}" },
			dataType: 'json',
			success: function(res) {
				$('#editTransferBankBeneficiaryForm').waitMe('hide');
				var result = decryptData(res.response);
				$('#bankId').html(result.output);
			},
			error: function(err) {
				$('#editTransferBankBeneficiaryForm').waitMe('hide');
				toastrMsg('error', 'Error loading bank list. Please try again.'); 
			}
		});
	});
	 
	$('#editTransferBankBeneficiaryForm #bankId').change(function()
	{ 
		var locationId = $(this).val();
		var payoutCurrency = $beneficiaryForm.find('#payoutCurrency').val();
		var payoutCountry = $beneficiaryForm.find('#payoutCurrency :selected').data('payout-country');
		var serviceName = $beneficiaryForm.find('#payoutCurrency :selected').data('service-name');

		if (!locationId || !payoutCurrency || !payoutCountry || !serviceName) { 
			toastrMsg('error', 'Missing required data for form submission.'); 
			return;
		}

		var formData = {
			payoutCountry: payoutCountry,
			payoutCurrency: payoutCurrency,
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
	var $beneficiaryForm = $('#editTransferBankBeneficiaryForm'); 
	$beneficiaryForm.submit(function(event) 
	{
		event.preventDefault();   
		$('#beneficiaryUpdate')
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
				$('#beneficiaryUpdate')
				.prop('disabled', false)  
				.removeClass('loading-span') 
				.html('Register'); 
				
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status, res.message);  
					$('#editTransferBankBeneficiary').modal('hide');
					$('#editTransferBankBeneficiary, .modal-backdrop').remove();
					$('#transferToBankForm #beneficiaryId').trigger('change');
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