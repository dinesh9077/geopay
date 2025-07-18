@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Geopay to Geopay Wallet')
@section('header_title', 'Geopay to Geopay Wallet')
@section('content')
 
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<form id="walletToWalletForm" action="{{ route('wallet-to-wallet.store') }}" method="post" class="animate__animated animate__fadeIn g-2">
				<div class="mb-1 row">
					<div class="col-12 mb-3"> 
						<label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
						<select id="country_id" name="country_id" class="form-control form-control-lg default-input">
							<option></option> <!-- Placeholder option -->
						</select>
					</div>
					<div class="col-12 mb-3">
						<label for="mobile_number" class="form-label">Enter Mobile No (eg.2444765454) <span class="text-danger">*</span></label> 
						<div class="d-flex align-items-center gap-2">
							<input id="mobile_code" type="text" name="mobile_code" class="form-control form-control-lg default-input mobile-number mb-3 px-2" style="max-width: 65px;" placeholder="+91" readonly />
							<input id="mobile_number" type="number" name="mobile_number" class="form-control form-control-lg default-input mobile-number mb-3" placeholder="Enter Mobile No (eg.2444765454)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));"/>
						</div> 
					</div>
					
					<div class="col-12 mb-3">	
						<label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
						<input id="amount" name="amount" type="text" class="form-control form-control-lg default-input" placeholder="Enter Amount in USD (eg : 100 or eg : 0.0)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div>
					<div class="col-12 mb-3">
						<label for="notes" class="form-label">Notes</label>
						<textarea name="notes" id="notes" class="form-control form-control-lg default-input" id="" placeholder="Account Description"></textarea>
					</div> 
				</div>
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2"> 
					<button type="submit" class="btn btn-primary rounded-2 text-nowrap" >Pay Money</button>
				</div>
			</form>
		</div>   
		@include('user.layouts.partial.quick-transfer')
	</div>
</div>
@endsection

@push('js')
	<script>
	
		$walletForm = $('#walletToWalletForm');
		var countries = @json($countriesWithFlags);

		$(document).ready(function() {
			// Initialize Select2 for the individual form
			$walletForm.find('#country_id').select2({
				data: countries.map(country => ({
					id: country.id,
					text: country.name,
					flag: country.country_flag // Add custom data for the flag
				})),
				templateResult: formatCountry,
				templateSelection: formatCountrySelection,
				width: "100%",
				placeholder: "Select Country", 
				allowClear: false,  
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
			
			$walletForm.find('#country_id').change(function()
			{
				const selectedCountryId = parseInt($(this).val()); 
				const country = countries.find(c => c.id === selectedCountryId); 
				$walletForm.find('#mobile_code').val(country.isdcode ? '+' + country.isdcode : '');
			});	 
		});
		
		// Attach the submit event handler
		$walletForm.submit(function(event) 
		{
			event.preventDefault();   
			
			$walletForm.find('[type="submit"]')
			.prop('disabled', true) 
			.addClass('loading-span') 
			.html('<span class="spinner-border"></span>');
			var formData = {};
			$walletForm.find('input, select, textarea, checkbox').each(function() {
				var inputName = $(this).attr('name');

				if ($(this).is(':checkbox')) {
					// For checkboxes, store whether it is checked (true or false)
					formData[inputName] = $(this).is(':checked');
				} else {
					// For other inputs, use the value
					formData[inputName] = $(this).val();
				}
			});

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
					$walletForm.find('[type="submit"]')
					.prop('disabled', false)  
					.removeClass('loading-span') 
					.html('Pay Money'); 
					$('.error_msg').remove(); 
					
					if(res.status === "success")
					{ 
						toastrMsg(res.status, res.message); 
						resetForm($walletForm);  
						Livewire.dispatch('refreshRecentTransactions');
						Livewire.dispatch('refreshNotificationDropdown');
						Livewire.dispatch('updateBalance');
					}
					else if(res.status == "validation")
					{  
						$.each(res.errors, function(key, value) {
							var inputField = $walletForm.find('#' + key);
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
