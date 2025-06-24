<form id="mobileCollectionForm" action="{{ route('mobile-collection.store') }}" method="post" class="animate__animated animate__fadeIn g-2">
    <div class="mb-1 row">
		<div class="col-12 mb-3"> 
			<label for="country_code" class="form-label">Country <span class="text-danger">*</span></label>
			<select id="country_code" name="country_code" class="form-control form-control-lg content-3 default-input" >
				<option value="">Select Country</option> 
			</select>
		</div>
		<div class="col-12 mb-3"> 
			<label for="country_code" class="form-label">Channel <span class="text-danger">*</span></label>
			<select id="channel" name="channel" class="form-control form-control-lg default-input mb-3 select2" >
				<option value="">Select Channel</option> 
			</select>
		</div>
		<div class="col-12 mb-3"> 
			<label for="country_code" class="form-label">Enter Mobile No (eg.2444765454) <span class="text-danger">*</span></label> 
			<div class="d-flex align-items-center gap-2">
				<input id="mobile_code" type="text" name="mobile_code" class="form-control form-control-lg default-input mobile-number mb-3 px-2" style="max-width: 65px;" placeholder="+91" readonly />
				<input id="mobile_no" type="number" name="mobile_no" class="form-control form-control-lg default-input mobile-number mb-3" placeholder="Enter Mobile No (eg.2444765454)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));"/>
			</div>
        </div>
		<div class="col-12 mb-3 request_currency" style="display:none;"> 
			<label for="country_code" class="form-label">Request Currency <span class="text-danger">*</span></label>
			<select id="request_currency" name="request_currency" class="form-control form-control-lg default-input mb-3 select2" >
				<option value="">Request Currency</option> 
				<option value="USD">USD</option> 
				<option value="CDF">CDF</option> 
			</select>
		</div>
		
		<div class="col-12 mb-3"> 
			<label for="txnAmount" class="form-label">Amount <span class="text-danger">*</span></label>
			<input id="txnAmount" name="txnAmount" class="form-control form-control-lg content-3 default-input"  placeholder="Enter Amount in {{config('setting.default_currency')}} (eg : 100 or eg : 0.0)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));"> 
		</div>
		<div class="col-12" id="commissionHtml"></div>
		<div class="col-12 mb-3"> 
			<label for="txnAmount" class="form-label">Beneficiary Name </label>
			<input id="beneficiary_name" name="beneficiary_name" class="form-control form-control-lg content-3 default-input" placeholder="Beneficiary Name"> 
		</div> 
		 
		<div class="col-12 mb-3 beneficiary_email" style="display:none;"> 
			<label for="txnAmount" class="form-label">Beneficiary Last Name </label>
			<input id="beneficiary_last_name" name="beneficiary_last_name" class="form-control form-control-lg content-3 default-input" placeholder="Beneficiary Last Name"> 
		</div> 

		<div class="col-12 mb-3 beneficiary_email" style="display:none;"> 
			<label for="txnAmount" class="form-label">Beneficiary Email </label>
			<input id="beneficiary_email" name="beneficiary_email" class="form-control form-control-lg content-3 default-input" placeholder="Beneficiary Email"> 
		</div> 
		
		<div class="col-12 mb-3 beneficiary_email" style="display:none;"> 
			<label for="txnAmount" class="form-label">Expired Date </label>
			<input id="expired_date" type="date" name="expired_date" class="form-control form-control-lg content-3 default-input" placeholder="Expired Date"> 
		</div> 
        <div class="col-12 mb-3">
			<label for="notes" class="form-label">Notes </label>
			<textarea name="notes" id="notes" class="form-control form-control-lg default-input" placeholder="Account Description"></textarea>
		</div>  
    </div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2"> 
        <button type="submit" class="btn btn-lg btn-primary rounded-2 text-nowrap" id="addMoney">Add Money</button>
    </div>
</form>

@push('js')
	<script>
		var $mobileCollectionForm = $('#mobileCollectionForm');  
		var collectionCountries = @json($collectionCountries);
		
		flatpickr("#expired_date", {
			dateFormat: "Y-m-d"
		});

		$(document).ready(function() {
			$mobileCollectionForm.find('.select2').select2();
			// Initialize Select2 for the individual form
			$mobileCollectionForm.find('#country_code').select2({
				data: collectionCountries.map(country => ({
					id: country.id,
					iso: country.iso,
					text: country.name,
					flag: country.country_flag // Add custom data for the flag
				})),
				templateResult: formatCountry,
				templateSelection: formatCountrySelection,
				width: "100%"
			}).on('select2:select', function (e) {
				let selectedData = e.params.data; 
				
				$('.beneficiary_email').hide(); 
				$('#beneficiary_name').closest('div').find('label').text('Beneficiary Name');
				$('#beneficiary_name').attr('placeholder', 'Beneficiary Name');
				$('#request_currency').prop('required', false);
				$('.request_currency').hide();
				 
				if(selectedData.iso == "NG")
				{
					$('#beneficiary_name').closest('div').find('label').text('Beneficiary First Name');	
					$('#beneficiary_name').attr('placeholder', 'Beneficiary First Name');
					$('.beneficiary_email').show(); 
				}
				else if(selectedData.iso == "CD" && selectedData.id == "240")
				{
					$('#request_currency').prop('required', true);
					$('.request_currency').show();
				}
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
			
			$mobileCollectionForm.find('#country_code').change(function()
			{
				const selectedCountryId = parseInt($(this).val());
				 
				const country = collectionCountries.find(c => c.id === selectedCountryId);

				let options = '<option value="">Select Channel</option>';

				if (country && country.available_channels) {
					country.available_channels.forEach(channel => {
						options += `<option value="${channel}">${channel}</option>`;
					});
				}

				$mobileCollectionForm.find('#channel').html(options);   
				$mobileCollectionForm.find('#mobile_code').val('+' + (country.isdcode || '')); 
			});	
		});	 
		
		$mobileCollectionForm.submit(function(event) 
		{
			event.preventDefault();   
			$mobileCollectionForm.find('[type="submit"]')
			.prop('disabled', true) 
			.addClass('loading-span') 
			.html('<span class="spinner-border"></span>');

			var formData = {};
			$(this).find('input, select, textarea').each(function() {
				var inputName = $(this).attr('name'); 
				formData[inputName] = $(this).val();
			});
			
			formData['category_name'] = 'add money'; 
			formData['service_name'] = 'onafric mobile collection';  
			
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
					$mobileCollectionForm.find('[type="submit"]')
					.prop('disabled', false)  
					.removeClass('loading-span') 
					.html('Submit'); 
					
					$('.error_msg').remove(); 
					if(res.status === "success")
					{ 
						toastrMsg(res.status, res.message);  
						resetForm($mobileCollectionForm);  
						Livewire.dispatch('refreshRecentTransactions'); 
						Livewire.dispatch('refreshNotificationDropdown');
						Livewire.dispatch('updateBalance'); 
						
						const decodeRes = decryptData(res.response);
						
						// Update balance dynamically
						const balanceHtml = `${decodeRes.userBalance} <span class="currency">${decodeRes.currencyCode}</span>`;
						$("#updateBalance").html(balanceHtml);
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) 
						{
							var inputField = $mobileCollectionForm.find('#' + key);
							var errorSpan = $('<span>')
							.addClass('error_msg text-danger content-4') 
							.attr('id', key + 'Error')
							.text(value[0]); 
						 
							if(key === "mobile_no")
							{
								inputField.parent().parent().append(errorSpan);
							}
							else
							{
								inputField.parent().append(errorSpan);
							} 
						});
					}
					else
					{ 
						toastrMsg(res.status, res.message);
					}
				} 
			});
		});
		
		let debounceTimer;
		$('#mobileCollectionForm #txnAmount').on('input', function() 
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
			const recipient_country = $mobileCollectionForm.find('#country_code').val();
			$('.error_msg').remove(); 
			if (!recipient_country) { 
				var errorSpan = $('<span>')
				.addClass('error_msg text-danger content-4')  
				.text('Country selection is required..'); 
				$mobileCollectionForm.find('#country_code').parent().append(errorSpan);
				return; 
			}

			formData['txnAmount'] =  txnAmount; 
			formData['recipient_country'] =  $mobileCollectionForm.find('#country_code').val(); 
			formData['service_name'] =  'onafric mobile collection'; 
			const encryptedData = encryptData(JSON.stringify(formData));

			// Show Loading Indicator
			run_waitMe($('body'), 1, 'facebook');
	 
			$.ajax({
				type: 'POST', 
				url: "{{ route('mobile-collection.commission') }}",
				data: { 
					encrypted_data: encryptedData,
					_token: "{{ csrf_token() }}" 
				},
				dataType: 'json',
				success: function(response) 
				{
					// Hide Loading Indicator
					$('body').waitMe('hide'); 
					
					$('#mobileCollectionForm .removeCommission').remove();
					$('#mobileCollectionForm').find('#netAmount, #totalCharges, #platformCharge, #serviceCharge, #payoutCurrencyAmount, #aggregatorCurrencyAmount, #exchangeRate, #aggregatorRate').remove();

					if (response.status === "success") {
						const result = decryptData(response.response);

						// Safely parse values
						const platformCharge = parseFloat(result.platformCharge) || 0;
						const serviceCharge = parseFloat(result.serviceCharge) || 0;
						const payoutCurrencyAmount = parseFloat(result.payoutCurrencyAmount) || 0;
						const aggregatorCurrencyAmount = parseFloat(result.aggregatorCurrencyAmount) || 0;
						const txnAmountFloat = parseFloat(txnAmount) || 0;
						const sendFee = parseFloat(result.sendFee) || 0;

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
							<input type="hidden" id="sendFee" name="sendFee" value="${sendFee}">
							<input type="hidden" id="netAmount" name="netAmount" value="${netAmount}">
							<input type="hidden" id="exchangeRate" name="exchangeRate" value="${exchangeRate}">
							<input type="hidden" id="aggregatorRate" name="aggregatorRate" value="${aggregatorRate}">
							<input type="hidden" id="totalCharges" name="totalCharges" value="${totalCharges}">
							<input type="hidden" id="platformCharge" name="platformCharge" value="${platformCharge}">
							<input type="hidden" id="serviceCharge" name="serviceCharge" value="${serviceCharge}">
							<input type="hidden" id="payoutCurrencyAmount" name="payoutCurrencyAmount" value="${payoutCurrencyAmount}">
							<input type="hidden" id="aggregatorCurrencyAmount" name="aggregatorCurrencyAmount" value="${aggregatorCurrencyAmount}">
							<input type="hidden" id="payoutCurrency" name="payoutCurrency" value="${payoutCurrency}">
						`;

						// Append all hidden fields at once
						$('#mobileCollectionForm').append(hiddenFields);

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
						$('#mobileCollectionForm #commissionHtml').html(commissionDetails);
					} 
					else
					{
						toastrMsg(response.status, response.message); 
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