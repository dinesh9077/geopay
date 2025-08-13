<div class="modal fade" id="addTransferMobileBeneficiary" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="staticBackdropLabel">Add Beneficiary</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="return $('#addTransferMobileBeneficiary').remove();" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form id="transferMobileBeneficiaryForm" method="post" action="{{ route('transfer-to-mobile.beneficiary-store') }}">
					  
					<div class="row">
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Recipient Country <span class="text-danger">*</span></label>
							<select id="recipient_country" name="recipient_country" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Recipient Country</option> 
							</select>
						</div>
						
						<div class="mb-4 col-lg-6">
							<label class="content-3 mb-0">Channel <span class="text-danger">*</span></label>
							<select id="channel_id" name="channel_id" class="form-control form-control-lg content-3 select2" required>
								<option value="">Select Channel</option> 
							</select>
						</div>
						
						<div class="mb-4 col-md-6">
							<label for="country_code" class="content-3 mb-0">Recipient mobile number (eg.700800900) <span class="text-danger">*</span></label> 
							<div class="d-flex align-items-center gap-2">
								<input id="mobile_code" type="text" name="mobile_code" class="form-control form-control-lg content-3 mobile-number px-2" style="max-width: 65px;" placeholder="+91" readonly />
								<input id="recipient_mobile" type="number" name="recipient_mobile" class="form-control form-control-lg content-3" placeholder="Enter Recipient mobile number" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" required />
							</div> 
						</div>
						 
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Name <span class="text-danger">*</span></label>
							<input id="recipient_name" name="recipient_name" placeholder="Enter Recipient Name" type="text" class="form-control form-control-lg content-3" required />
						</div>
						  
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Surname <span class="text-danger">*</span></label>
							<input id="recipient_surname" name="recipient_surname" placeholder="Enter Recipient Surname" type="text" class="form-control form-control-lg content-3" required />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Address</label>
							<input id="recipient_address" name="recipient_address" placeholder="Enter Recipient Address" type="text" class="form-control form-control-lg content-3" />
						</div>
						
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient City</label>
							<input id="recipient_city" name="recipient_city" placeholder="Enter Recipient City" type="text" class="form-control form-control-lg content-3" />
						</div> 
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient State</label>
							<input id="recipient_state" name="recipient_state" placeholder="Enter Recipient state" type="text" class="form-control form-control-lg content-3" />
						</div> 
						<div class="mb-4 col-md-6">
							<label class="content-3 mb-0">Recipient Postal Code</label>
							<input id="recipient_postalcode" name="recipient_postalcode" placeholder="Enter Recipient Code" type="text" class="form-control form-control-lg content-3" />
						</div> 
						<div class="col-sm-6">
							<label class="content-3 mb-0">Recipient Date Of Birth </label>
							<input id="recipient_dateofbirth" name="recipient_dateofbirth" placeholder="Recipient Date Of Birth" type="date" class="form-control form-control-lg" onclick="this.showPicker()" style="cursor: pointer;" />
						</div>  
					</div> 
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="return $('#addTransferMobileBeneficiary').remove();">Close</button>
				<button type="submit" form="transferMobileBeneficiaryForm" id="beneficiaryStore" class="btn btn-primary">Submit</button>
			</div>
		</div>
	</div>
	<script>  
		var $beneficiaryForm = $('#transferMobileBeneficiaryForm');  
		var countries = @json($countries);

		$(document).ready(function() 
		{  
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
				dropdownParent: $('#addTransferMobileBeneficiary'),
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
		});
		
		$beneficiaryForm.find('#recipient_country').change(function() {
			const countryId = $(this).val();

			// Filter the selected country using the countryId
			var selectedCountry = countries.find(country => country.id == countryId);

			// Check if the country has channels and populate the channel dropdown
			if (selectedCountry && selectedCountry.channels && selectedCountry.channels.length > 0) {
				const channelDropdown = $beneficiaryForm.find('#channel_id'); // Assuming the channel select dropdown ID is #channel
				channelDropdown.empty(); // Clear any existing options

				// Loop through each channel of the selected country and append to the dropdown
				channelDropdown.append('<option value="">Select channels</option>');
				selectedCountry.channels.forEach(function(channel) {
					channelDropdown.append('<option value="' + channel.id + '" data-channel-name="' + channel.channel + '">' + channel.channel + '</option>');
				});
				
				$beneficiaryForm.find('#mobile_code').val('+' + (selectedCountry.isdcode || '')); 
			} else {
				// If no channels available for the country, disable the dropdown or set a default message
				const channelDropdown = $beneficiaryForm.find('#channel');
				channelDropdown.empty();
				channelDropdown.append('<option disabled>No channels available</option>');
				$beneficiaryForm.find('#mobile_code').val(''); 
			}
		});

		
		// Initialize Select2 for dropdowns
		$('#transferMobileBeneficiaryForm .select2').select2({
			dropdownParent: $('#addTransferMobileBeneficiary'),
			width: "100%"
		});
		  
		// Attach the submit event handler 
		$beneficiaryForm.submit(function(event) 
		{
			event.preventDefault();    
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
			
			formData['category_name'] = 'transfer to mobile';  
			formData['service_name'] = 'onafric';   
			formData['recipient_country_code'] = $beneficiaryForm.find('#recipient_country').attr('data-iso') ?? '';   
			formData['recipient_country_name'] = $beneficiaryForm.find('#recipient_country').attr('data-name') ?? '';   
			formData['channel_name'] = $beneficiaryForm.find('#channel_id option:selected').data('channel-name') || '';   
		 
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
						$('#transferToMobileForm #country_code').trigger('change');
						$('#addTransferMobileBeneficiary').modal('hide');
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
		
		document.querySelectorAll('input[type="date"]').forEach(input => {
			input.addEventListener('focus', function () {
				this.showPicker?.();
			});
		}); 
	</script>
</div> 