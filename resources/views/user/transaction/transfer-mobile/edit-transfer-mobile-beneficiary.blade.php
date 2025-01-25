<div class="modal fade" id="editTransferMobileBeneficiary" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Beneficiary</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="return $('#editTransferMobileBeneficiary').remove();" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form id="editTransferMobileBeneficiaryForm" method="post" action="{{ route('transfer-to-mobile.beneficiary-update', ['id' => $beneficiary->id]) }}">
					 
					<div class="row">
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Sender Country <span class="text-danger">*</span></label>
							<select id="sender_country" name="sender_country" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Sender Country</option> 
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender mobile number e.g. 250700800900.<span class="text-danger">*</span></label>
							<input id="sender_mobile" name="sender_mobile" placeholder="Enter Sender mobile number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')" value="{{ $edit['sender_mobile'] }}" required />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender Name <span class="text-danger">*</span></label>
							<input id="sender_name" name="sender_name" placeholder="Enter Sender Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['sender_name'] }}" required />
						</div>
						  
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender Surname <span class="text-danger">*</span></label>
							<input id="sender_surname" name="sender_surname" placeholder="Enter Sender Surname" type="text" class="form-control form-control-lg content-3" value="{{ $edit['sender_surname'] }}" required />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender Address</label>
							<input id="sender_address" name="sender_address" placeholder="Enter Sender Address" type="text" class="form-control form-control-lg content-3" value="{{ $edit['sender_address'] }}" />
						</div>
						
						<div class="mb-4 col-md-3">
							<label class="content-3 mb-0">Sender City</label>
							<input id="sender_city" name="sender_city" placeholder="Enter Sender City" type="text" class="form-control form-control-lg content-3" value="{{ $edit['sender_city'] }}" />
						</div> 
						<div class="mb-4 col-md-3">
							<label class="content-3 mb-0">Sender State</label>
							<input id="sender_state" name="sender_state" placeholder="Enter Sender state" type="text" class="form-control form-control-lg content-3" value="{{ $edit['sender_state'] }}" />
						</div> 
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Sender Postal Code</label>
							<input id="sender_postalcode" name="sender_postalcode" placeholder="Enter Postal Code" type="text" class="form-control form-control-lg content-3" value="{{ $edit['sender_postalcode'] }}" />
						</div> 
						<div class="col-sm-6">
							<label class="content-3 mb-0">Sender Place Of Birth </label>
							<input id="sender_placeofbirth" name="sender_placeofbirth" placeholder="Sender Place Of Birth" type="text" class="form-control form-control-lg" value="{{ $edit['sender_placeofbirth'] }}" />
						</div>  
					</div>
					
					<div class="row">
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Recipient Country <span class="text-danger">*</span></label>
							<select id="recipient_country" name="recipient_country" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Recipient Country</option> 
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient mobile number e.g. 250700800900.<span class="text-danger">*</span></label>
							<input id="recipient_mobile" name="recipient_mobile" placeholder="Enter Recipient mobile number" type="text" class="form-control form-control-lg content-3" oninput="this.value = this.value.replace(/\D/g, '')" value="{{ $edit['recipient_mobile'] }}" required />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Name <span class="text-danger">*</span></label>
							<input id="recipient_name" name="recipient_name" placeholder="Enter Recipient Name" type="text" class="form-control form-control-lg content-3" value="{{ $edit['recipient_name'] }}" required />
						</div>
						  
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Surname <span class="text-danger">*</span></label>
							<input id="recipient_surname" name="recipient_surname" placeholder="Enter Recipient Surname" type="text" class="form-control form-control-lg content-3" value="{{ $edit['recipient_surname'] }}" required />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Address</label>
							<input id="recipient_address" name="recipient_address" placeholder="Enter Recipient Address" type="text" class="form-control form-control-lg content-3" value="{{ $edit['recipient_address'] }}" />
						</div>
						
						<div class="mb-4 col-md-3">
							<label class="content-3 mb-0">Recipient City</label>
							<input id="recipient_city" name="recipient_city" placeholder="Enter Recipient City" type="text" class="form-control form-control-lg content-3" value="{{ $edit['recipient_city'] }}" />
						</div> 
						<div class="mb-4 col-md-3">
							<label class="content-3 mb-0">Recipient State</label>
							<input id="recipient_state" name="recipient_state" placeholder="Enter Recipient state" type="text" class="form-control form-control-lg content-3" value="{{ $edit['recipient_state'] }}" />
						</div> 
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Postal Code</label>
							<input id="recipient_postalcode" name="recipient_postalcode" placeholder="Enter Recipient Code" type="text" class="form-control form-control-lg content-3" value="{{ $edit['recipient_postalcode'] }}" />
						</div> 
						<div class="col-sm-6">
							<label class="content-3 mb-0">Recipient Place Of Birth </label>
							<input id="recipient_placeofbirth" name="recipient_placeofbirth" placeholder="Recipient Place Of Birth" type="text" class="form-control form-control-lg" value="{{ $edit['recipient_placeofbirth'] }}" />
						</div>  
					</div>
					<div class="row"> 
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Purpose Of Transfer</label>
							<input id="purposeOfTransfer" name="purposeOfTransfer" placeholder="Enter Purpose Of Transfer such as Health/Medical Expense or Education." type="text" class="form-control form-control-lg content-3" value="{{ $edit['purposeOfTransfer'] }}" />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Source Of Funds</label>
							<input id="sourceOfFunds" name="sourceOfFunds" placeholder="Enter Source Of Funds Common sources include Salary/Wages, Investment Income or Savings." type="text" class="form-control form-control-lg content-3" value="{{ $edit['sourceOfFunds'] }}" />
						</div> 
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="return $('#editTransferMobileBeneficiary').remove();">Close</button>
				<button type="submit" form="editTransferMobileBeneficiaryForm" id="beneficiaryStore" class="btn btn-primary">Submit</button>
			</div>
		</div>
	</div>
</div> 
<script> 
	// Initialize Select2 for dropdowns
	$('#editTransferMobileBeneficiaryForm .select2').select2({
		dropdownParent: $('#editTransferMobileBeneficiary'),
		width: "100%"
	});
	
	var $beneficiaryForm = $('#editTransferMobileBeneficiaryForm'); 
	
	var countries = @json($countries);
	
	var recipientCountry = @json($edit['recipient_country']);
	var senderCountry = @json($edit['sender_country']);
	
	$(document).ready(function() {
		// Initialize Select2 for the individual form
		$beneficiaryForm.find('#sender_country').select2({
			data: countries.map(country => ({
				id: country.id,
				iso: country.iso,
				text: country.name,
				flag: country.country_flag // Add custom data for the flag
			})),
			templateResult: formatCountry,
			templateSelection: formatCountrySelection,
			dropdownParent: $('#editTransferMobileBeneficiary'),
			width: "100%"
		}).on('select2:select', function (e) {
			let selectedData = e.params.data; 
			$(this).attr('data-iso', selectedData.iso).attr('data-name', selectedData.text); 
		});
		
		// Initialize Select2 for the individual form
		$beneficiaryForm.find('#recipient_country').select2({
			data: countries.map(country => ({
				id: country.id,
				iso: country.iso,
				text: country.name,
				flag: country.country_flag // Add custom data for the flag
			})),
			templateResult: formatCountry,
			templateSelection: formatCountrySelection,
			dropdownParent: $('#editTransferMobileBeneficiary'),
			width: "100%"
		}).on('select2:select', function (e) {
			let selectedData = e.params.data; 
			$(this).attr('data-iso', selectedData.iso).attr('data-name', selectedData.text); 
		});
		 
		// Template for the dropdown items
		function formatCountry(country) {
			if (!country.id) {
				return country.text; // Default text if no id (for the placeholder option)
			}
			const flagImg = '<img src="'+country.flag+'" style="width: 20px; height: 20px; margin-right: 4px; margin-bottom: 4px;" />';
			return $('<span>'+flagImg+' '+country.text+'</span>');
		}

		// Template for the selected item
		function formatCountrySelection(country) {
			if (!country.id) {
				return country.text;
			}
			const flagImg = '<img src="'+country.flag+'" style="width: 20px; height: 20px; margin-right: 4px; margin-bottom: 4px;" />';
			return $('<span>'+flagImg+' '+country.text+'</span>');
		}
		
		// **Set the selected values and manually trigger select2:select**
		function setSelect2Value(selector, countryId) {
			let countryData = countries.find(c => c.id == countryId);
			if (countryData) {
				$(selector)
					.val(countryId)
					.trigger('change')
					.trigger({
						type: 'select2:select',
						params: { data: { id: countryData.id, iso: countryData.iso, text: countryData.name } }
					});
			}
		}

		setSelect2Value($beneficiaryForm.find('#recipient_country'), recipientCountry);
		setSelect2Value($beneficiaryForm.find('#sender_country'), senderCountry);
	});
	
	// Initialize Flatpickr for date inputs
	flatpickr("#sender_placeofbirth, #recipient_placeofbirth", {
		dateFormat: "Y-m-d"
	});
	 
	// Attach the submit event handler 
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
		
		formData['category_name'] = 'transfer to mobile';  
		formData['service_name'] = 'onafric';  
		formData['sender_country_code'] = $beneficiaryForm.find('#sender_country').attr('data-iso') ?? '';   
		formData['sender_country_name'] = $beneficiaryForm.find('#sender_country').attr('data-name') ?? '';   
		formData['recipient_country_code'] = $beneficiaryForm.find('#recipient_country').attr('data-iso') ?? '';   
		formData['recipient_country_name'] = $beneficiaryForm.find('#recipient_country').attr('data-name') ?? '';   
		  
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
					$('#editTransferMobileBeneficiary').modal('hide');
					$('#editTransferMobileBeneficiary, .modal-backdrop').remove();
					$('#transferToMobileForm #beneficiaryId').trigger('change');
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