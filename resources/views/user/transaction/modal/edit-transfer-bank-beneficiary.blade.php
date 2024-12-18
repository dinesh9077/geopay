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
							<label class="content-3 mb-0">Beneficiary Type <span class="text-danger">*</span></label>
							<select id="beneficiaryType" name="beneficiaryType" class="form-control form-control-lg content-3 select2">
								<option value="">Select Beneficiary Type</option>
								<option value="I" {{ isset($edit['beneficiaryType']) && $edit['beneficiaryType'] === 'I' ? 'selected' : '' }}>Individual</option>
								<option value="B" {{ isset($edit['beneficiaryType']) && $edit['beneficiaryType'] === 'B' ? 'selected' : '' }}>Business</option>
							</select>
						</div>
 
						
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
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Bank Account Number <span class="text-danger">*</span></label>
							<input id="bankAccountNumber" name="bankAccountNumber" placeholder="Enter Bank Account Number" type="text" class="form-control form-control-lg content-3" value="{{ $edit['bankAccountNumber'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary First Name <span class="text-danger">*</span></label>
							<input id="beneficiaryFirstName" name="beneficiaryFirstName" placeholder="Enter Beneficiary First Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryFirstName'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Middle Name ( Optional )</label>
							<input id="beneficiaryMiddleName" name="beneficiaryMiddleName" placeholder="Enter Beneficiary Middle Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryMiddleName'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Last Name <span class="text-danger">*</span></label>
							<input id="beneficiaryLastName" name="beneficiaryLastName" placeholder="Enter Beneficiary Last Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryLastName'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Address <span class="text-danger">*</span></label>
							<input id="beneficiaryAddress" name="beneficiaryAddress" placeholder="Enter Beneficiary Address" type="text" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryAddress'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary State</label>
							<input id="beneficiaryState" name="beneficiaryState" placeholder="Enter Beneficiary State" type="text" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryState'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Email <span class="text-danger">*</span></label>
							<input id="beneficiaryEmail" name="beneficiaryEmail" placeholder="Enter Beneficiary Email id" type="email" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryEmail'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Mobile Number (eg:+265244476305) <span class="text-danger">*</span></label>
							<input id="beneficiaryMobile" name="beneficiaryMobile" placeholder="Enter Beneficiary Mobile No" type="tel" class="form-control form-control-lg content-3" value="{{ $edit['beneficiaryMobile'] }}"/>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Select Beneficiary Relationship with sender <span class="text-danger">*</span></label>
							<select id="senderBeneficiaryRelationship" name="senderBeneficiaryRelationship" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Beneficiary Relationship with sender</option>
								@foreach($relationships as $relationship)
								<option value="{{ $relationship['data'] }}" data-relation-remark="{{ $relationship['value'] }}" {{ isset($edit['senderBeneficiaryRelationship']) && $edit['senderBeneficiaryRelationship'] === $relationship['data'] ? 'selected' : '' }}>{{ $relationship['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Remittance Purpose <span class="text-danger">*</span></label>
							<select id="purposeOfRemittance" name="purposeOfRemittance" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Remittance purpose</option>
								@foreach($purposeRemittances as $purposeRemittance)
								<option value="{{ $purposeRemittance['data'] }}" data-purpose-remittance-remarks="{{ $purposeRemittance['value'] }}" {{ isset($edit['purposeOfRemittance']) && $edit['purposeOfRemittance'] === $purposeRemittance['data'] ? 'selected' : '' }}>{{ $purposeRemittance['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender Source of Fund <span class="text-danger">*</span></label>
							<select id="senderSourceOfFund" name="senderSourceOfFund" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Source of Fund</option>
								@foreach($sourceOfFunds as $sourceOfFund)
								<option value="{{ $sourceOfFund['data'] }}" data-sender-sourceof-fundremarks="{{ $sourceOfFund['value'] }}" {{ isset($edit['senderSourceOfFund']) && $edit['senderSourceOfFund'] === $sourceOfFund['data'] ? 'selected' : '' }}>{{ $sourceOfFund['value'] }}</option>
								@endforeach
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Beneficiary Id Type <span class="text-danger">*</span></label>
							<select id="receiverIdType" name="receiverIdType" class="form-control form-control-lg content-3 select2" >
								<option value="">Select Beneficiary Id Type</option>
								@foreach($documentOfCustomers as $documentOfCustomer)
								<option value="{{ $documentOfCustomer['data'] }}" {{ isset($edit['receiverIdType']) && $edit['receiverIdType'] === $documentOfCustomer['data'] ? 'selected' : '' }}>{{ $documentOfCustomer['value'] }}</option>
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
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="return $('#editTransferBankBeneficiary, .modal-backdrop').remove();">Close</button>
				<button type="submit" form="editTransferBankBeneficiaryForm" class="btn btn-primary">Update</button>
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
	
	// Attach the submit event handler
	var $beneficiaryForm = $('#editTransferBankBeneficiaryForm'); 
	$beneficiaryForm.submit(function(event) 
	{
		event.preventDefault();   
		$beneficiaryForm.find('button').prop('disabled',true);  
		run_waitMe($('#editTransferBankBeneficiaryForm'), 1, 'facebook');
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
		formData['senderBeneficiaryRelationshipRemarks'] = $beneficiaryForm.find('#senderBeneficiaryRelationship').find(':selected').data('relation-remark') ?? '';
		formData['purposeOfRemittanceRemark'] = $beneficiaryForm.find('#purposeOfRemittance').find(':selected').data('purpose-remittance-remarks') ?? '';
		formData['senderSourceOfFundRemarks'] = $beneficiaryForm.find('#senderSourceOfFund').find(':selected').data('sender-sourceof-fundremarks') ?? '';
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
				$beneficiaryForm.find('button').prop('disabled',false);	 
				$('#editTransferBankBeneficiaryForm').waitMe('hide');
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