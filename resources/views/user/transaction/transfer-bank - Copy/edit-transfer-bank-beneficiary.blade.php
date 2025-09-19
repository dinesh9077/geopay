<div class="modal fade" id="editTransferBankBeneficiary" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Recipient Details</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="return $('#editTransferBankBeneficiary, .modal-backdrop').remove();" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form id="editTransferBankBeneficiaryForm" method="post" action="{{ route('transfer-to-bank.beneficiary-update', ['id' => $beneficiary->id]) }}">
					<div class="row">  
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Country <span class="text-danger">*</span></label>
							<select id="payoutCurrency" name="payoutCurrency" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Country</option>
								@foreach($countries as $country) 
									<option value="{{ $country['value'] }}" data-service-name="{{ $country['service_name'] }}" data-payout-country="{{ $country['data'] }}" data-country-name="{{ $country['label'] }}" data-iso="{{ $country['iso'] }}" data-isdcode="{{ $country['isdcode'] }}" {{ isset($edit['payoutCurrency']) && $edit['payoutCurrency'] === $country['value'] ? 'selected' : '' }}>{{ $country['label'] }}</option>
								@endforeach
							</select>
						</div> 
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Bank Name <span class="text-danger">*</span></label>
							<select id="bankId" name="bankId" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Bank Name</option>
								@if($beneficiary->service_name == "lightnet")
									@foreach($banks as $bank) 
										<option value="{{ $bank['locationId'] }}" data-bank-name="{{ $bank['locationName'] }}" {{ isset($edit['bankId']) && $edit['bankId'] === $bank['locationId'] ? 'selected' : '' }}>{{ $bank['locationName'] }}</option>
									@endforeach
								@else
									{!! $banks !!}
								@endif
							</select>
						</div>
					</div>
					<div class="row" id="dynamicFields">  
						{!! $fieldView !!} 
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
		var payoutIso = $(this).find(':selected').data('iso'); 
		var serviceName = $(this).find(':selected').data('service-name');
		var formData = { payoutCountry: payoutCountry, serviceName: serviceName, payoutIso: payoutIso };
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
		var isdcode = $beneficiaryForm.find('#payoutCurrency :selected').data('isdcode');
		 
		if (!locationId || !payoutCurrency || !payoutCountry || !serviceName || !isdcode) { 
			toastrMsg('error', 'Missing required data for form submission.'); 
			return;
		}

		var formData = {
			payoutCountry: payoutCountry,
			payoutCurrency: payoutCurrency,
			serviceName: serviceName,
			locationId: locationId,
			isdcode: isdcode
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