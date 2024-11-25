@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Wallet to Wallet')
@section('header_title', 'Wallet to Wallet')
@section('content')
 
<div class="container-fluid p-0">
	<div class="row g-4">
		<!-- Left Column -->
		<div class="col-lg-9 mb-3 add-money-section">
			<form id="walletToWalletForm" action="{{ route('wallet-to-wallet.store') }}" method="post" class="animate__animated animate__fadeIn g-2">
				<div class="mb-1 row">
					<div class="col-12 mb-3"> 
						<select id="country_id" name="country_id" class="form-control form-control-lg default-input">
							<option></option> <!-- Placeholder option -->
						</select>
					</div>
					<div class="col-12 mb-3">
						<input type="number" id="mobile_number" name="mobile_number" class="form-control form-control-lg default-input mobile-number" placeholder="Enter your mobile number without code" oninput="this.value = this.value.replace(/\D/g, '')"/>
					</div>
					<div class="col-12 mb-3">
						<input id="amount" name="amount" type="text" class="form-control form-control-lg default-input" placeholder="Enter Amount in USD (eg : 100 or eg : 0.0)" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div>
					<div class="col-12 mb-3">
						<textarea name="notes" id="notes" class="form-control form-control-lg default-input" id="" placeholder="Account Description"></textarea>
					</div> 
				</div>
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2"> 
					<button type="submit" class="btn btn-lg btn-primary rounded-2 text-nowrap" >Pay Money</button>
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
		const countries = @json($countriesWithFlags);

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
		 
		});
		
		// Attach the submit event handler
		$walletForm.submit(function(event) 
		{
			event.preventDefault();   
			
			$walletForm.find('button').prop('disabled',true);   
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
					$walletForm.find('button').prop('disabled',false);	 
					$('.error_msg').remove(); 
					if(res.status === "success")
					{ 
						toastrMsg(res.status, res.message); 
						resetForm($walletForm); 
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
