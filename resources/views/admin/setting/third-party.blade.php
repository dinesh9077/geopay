@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Third party Key')

@section('content')
 
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Third Party Credential</h4>
	</div> 
</div>

<div class="row">
	<div class="example">
		@php
			$tabs = [
				'metamap_setting',
				'smtp_mail_setting',
				'smsplus_setting',
				'internation_airtime_setting',
				'lightnet_setting',
				'onafric_mobile_setting',
				'onafric_bank_setting',
				'onafric_mobile_collection_setting',
				'guardian_payment_gateway',
			];

			// Filter only tabs that the user has permission to view
			$visibleTabs = collect($tabs)->filter(fn($tab) => config("permission.$tab.view"));
			
			// Get the first tab's ID for setting the active class dynamically
			$firstTab = $visibleTabs->first(); 
		@endphp
		<ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
			@if (config("permission.metamap_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'metamap_setting' ? 'active' : '' }}" id="metamap-line-tab" data-bs-toggle="tab" href="#line-metamap" role="tab" aria-controls="line-metamap" aria-selected="true">Meta Map</a>
				</li>
			@endif
			
			@if (config("permission.smsplus_setting.view"))
				<li class="nav-item {{ $firstTab == 'smsplus_setting' ? 'active' : '' }}">
					<a class="nav-link" id="smsplus-line-tab" data-bs-toggle="tab" href="#line-smsplus" role="tab" aria-controls="line-smsplus" aria-selected="false">SMS Plus</a>
				</li> 
			@endif
			
			@if (config("permission.smtp_mail_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'smtp_mail_setting' ? 'active' : '' }}" id="smtpmail-line-tab" data-bs-toggle="tab" href="#line-smtpmail" role="tab" aria-controls="line-smtpmail" aria-selected="false">SMTP Mail</a>
				</li> 
			@endif 

			@if (config("permission.onafric_mobile_collection_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'onafric_mobile_collection_setting' ? 'active' : '' }}" id="onafriq-line-tab" data-bs-toggle="tab" href="#line-onafriq-mobile-collection" role="tab" aria-controls="line-onafriq" aria-selected="false">Onafric - Mobile Money - Add Service</a>
				</li> 
			@endif

			<li class="nav-item">
				<a class="nav-link {{ $firstTab == 'guardian_payment_gateway' ? 'active' : '' }}" id="guardian-line-tab" data-bs-toggle="tab" href="#line-guardian-gateway" role="tab" aria-controls="line-guardian" aria-selected="false">Guardian PG (Add Service)</a>
			</li> 
			
			@if (config("permission.onafric_mobile_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'onafric_mobile_setting' ? 'active' : '' }}" onclick="getOnafricMobileView(event)" id="onafriq-line-tab" data-bs-toggle="tab" href="#line-onafriq-mobile" role="tab" aria-controls="line-onafriq" aria-selected="false">Onafric - Mobile Money - Pay Service</a>
				</li> 
			@endif
			
			@if (config("permission.internation_airtime_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'internation_airtime_setting' ? 'active' : '' }}" id="dtone-line-tab" data-bs-toggle="tab" href="#line-dtone" role="tab" aria-controls="line-dtone" aria-selected="false">International Airtime (dtone) - Pay Service</a>
				</li> 
			@endif
			
			@if (config("permission.lightnet_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'lightnet_setting' ? 'active' : '' }}" onclick="getLightNetView(event)" id="lightnet-line-tab" data-bs-toggle="tab" href="#line-lightnet" role="tab" aria-controls="line-lightnet" aria-selected="false">Lightnet - Transfer To Bank - Pay Service</a>
				</li> 
			@endif
			
			@if (config("permission.onafric_bank_setting.view"))
				<li class="nav-item">
					<a class="nav-link {{ $firstTab == 'onafric_bank_setting' ? 'active' : '' }}" id="onafriq-line-tab" data-bs-toggle="tab" href="#line-onafriq-bank" role="tab" aria-controls="line-onafriq" aria-selected="false">Onafric - Transfer To Bank - Pay Service</a>
				</li> 
			@endif   
			 
		</ul>
		<div class="tab-content mt-3" id="lineTabContent">
			@if (config("permission.metamap_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'metamap_setting' ? 'show active' : '' }}" id="line-metamap" role="tabpanel" aria-labelledby="metamap-line-tab"> 
					@include('admin.setting.partial.meta-map')
				</div>
			@endif
			@if (config("permission.smtp_mail_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'smtp_mail_setting' ? 'show active' : '' }}" id="line-smtpmail" role="tabpanel" aria-labelledby="smtpmail-line-tab"> 
					@include('admin.setting.partial.smtp-mail')
				</div>
			@endif
			@if (config("permission.smsplus_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'smsplus_setting' ? 'show active' : '' }}" id="line-smsplus" role="tabpanel" aria-labelledby="smsplus-line-tab">
					@include('admin.setting.partial.sms-plus')
				</div>
			@endif
			@if (config("permission.internation_airtime_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'internation_airtime_setting' ? 'show active' : '' }}" id="line-dtone" role="tabpanel" aria-labelledby="dtone-line-tab">
					@include('admin.setting.partial.international-airtime')
				</div> 
			@endif
			@if (config("permission.lightnet_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'lightnet_setting' ? 'show active' : '' }}" id="line-lightnet" role="tabpanel" aria-labelledby="lightnet-line-tab">
					@include('admin.setting.partial.lightnet')
				</div> 
			@endif
			@if (config("permission.onafric_mobile_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'onafric_mobile_setting' ? 'show active' : '' }}" id="line-onafriq-mobile" role="tabpanel" aria-labelledby="onafriq-line-tab"> 
					@include('admin.setting.partial.onafric-mobile')
				</div>
			@endif 
			@if (config("permission.onafric_bank_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'onafric_bank_setting' ? 'show active' : '' }}" id="line-onafriq-bank" role="tabpanel" aria-labelledby="onafriq-line-tab"> 
					@include('admin.setting.partial.onafric-bank')
				</div>
			@endif 
			@if (config("permission.onafric_mobile_collection_setting.view"))
				<div class="tab-pane fade {{ $firstTab == 'onafric_mobile_collection_setting' ? 'show active' : '' }}" id="line-onafriq-mobile-collection" role="tabpanel" aria-labelledby="onafriq-line-tab"> 
					@include('admin.setting.partial.onafric-mobile-collection')
				</div>
			@endif 
			<div class="tab-pane fade {{ $firstTab == 'guardian_payment_gateway' ? 'show active' : '' }}" id="line-guardian-gateway" role="tabpanel" aria-labelledby="guardian-line-tab"> 
				@include('admin.setting.partial.guardian-payment-gateway')
			</div>
		</div>
	</div>
</div> 
@endsection

@push('js')
<script>
	var $forms = $('#metaMapForm, #smsPlusForm, #dtonePlusForm, #lightnetPlusForm, #onafricForm, #onafricBankForm, #guardianPgForm');
	
	$('.select2').select2({ 
		width: "100%"
	})
			
	$forms.submit(function (event) {
		event.preventDefault();
		var $form = $(this); // Reference the current form being submitted
		 
		$form.find('button').prop('disabled', true);

		var formDataInput = {};

		// Gather input and select values
		$form.find("input, select").each(function () {
			var inputName = $(this).attr('name');

			if ($(this).attr('type') !== 'file') {
				formDataInput[inputName] = $(this).val();
			}
		});

		const encrypted_data = encryptData(JSON.stringify(formDataInput));

		var formData = new FormData();
		formData.append('encrypted_data', encrypted_data);
		formData.append('_token', "{{ csrf_token() }}");

		// Add file inputs
		$form.find("input[type='file']").each(function () {
			var inputName = $(this).attr('name');
			var files = $(this)[0].files;

			$.each(files, function (index, file) {
				formData.append(inputName, file);
			});
		});

		// Use the form's specific action and method attributes
		$.ajax({
			type: $form.attr('method'),
			url: $form.attr('action'),
			data: formData,
			processData: false,
			contentType: false,
			cache: false,
			dataType: 'json',
			success: function (res) {
				$form.find('button').prop('disabled', false);
				$form.find('.error_msg').remove();

				if (res.status === "success") 
				{
					toastrMsg(res.status, res.message);
					var formId = $form.attr('id');
					if(formId == "lightnetPlusForm")
					{
						getLightNetView(event)
					} 
				} else if (res.status === "validation") {
					$.each(res.errors, function (key, value) {
						var inputField = $form.find('[name="' + key + '"]');
						var errorSpan = $('<span>')
							.addClass('error_msg text-danger')
							.attr('id', key + 'Error')
							.text(value[0]);
						inputField.parent().append(errorSpan);
					});
				} else {
					toastrMsg(res.status, res.message);
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$form.find('button').prop('disabled', false);
				toastrMsg("error", "An unexpected error occurred. Please try again.");
			}
		});
	}); 
	   
	function getLightNetView(event) {  
		event.preventDefault();
		
		let $lightnetView = $('#lightnetView');
		
		run_waitMe($lightnetView, 1, 'facebook');

		$.get("{{ route('admin.third-party-key.lightnet-view') }}", function(res) {
			$lightnetView.waitMe('hide');

			if (res?.response) {
				let result = decryptData(res.response);
				$lightnetView.html(result?.view || '<p>Error loading data</p>');
			} else {
				$lightnetView.html('<p>Error: No response data</p>');
			}
		}, 'json')
		.fail(function() {
			$('body').waitMe('hide');
			$lightnetView.html('<p>Error fetching data</p>');
		});
	} 

	function getOnafricMobileView(event) {  
		event.preventDefault();
		
		let $onafricMobileView = $('#onafricMobileView');
		
		run_waitMe($onafricMobileView, 1, 'facebook');

		$.get("{{ route('admin.third-party-key.onafric-mobile-view') }}", function(res) {
			$onafricMobileView.waitMe('hide');

			if (res?.response) {
				let result = decryptData(res.response);
				$onafricMobileView.html(result?.view || '<p>Error loading data</p>');
			} else {
				$onafricMobileView.html('<p>Error: No response data</p>');
			}
		}, 'json')
		.fail(function() {
			$('body').waitMe('hide');
			$onafricMobileView.html('<p>Error fetching data</p>');
		});
	} 
	
	//collection country
	var $onafricCollectionCountryform = $('#onafricCollectionCountryform');
	var i = @json($collectionCountryCount);
	
	function addCollectionCountry(obj, event)
	{
		event.preventDefault();
		i++;
		var html =`
			<div class="row mt-3" id="remove_row">
				<div class="col-md-3">
					<label class="form-label">Country Name</label>
					<select name="country_name[]" id="country_name_${i}" class="form-control select2" required>
						<option value="">Select Country</option>
						@foreach($countries as $country)
							<option value="{{ $country->nicename}}">{{ $country->nicename}}</option>
						@endforeach
					</select>
				</div>
				<input type="hidden" name="ids[]" id="id" value="">
				<div class="col-md-5">
					<label class="form-label">Channel Name</label>
					<input type="text" class="form-control" name="channels[]" value="" required>
				</div>
				
				<div class="col-md-4 d-flex align-items-end gap-3"> 
					<button type="button" class="btn btn-danger" onclick="removeCollectionCountry(this)">Remove Country</button>  
				</div>
			</div>
		`;
		
		$onafricCollectionCountryform.find('#collectionCountryAppend').append(html);
		$('.select2').select2({ 
			width: "100%"
		})
	}
	
	function removeCollectionCountry(obj)
	{
		$(obj).closest('#remove_row').remove();
	}
	
	$onafricCollectionCountryform.submit(function (event) {
		event.preventDefault(); 

		let submitButton = $onafricCollectionCountryform.find('[type="submit"]');
		submitButton.prop('disabled', true).addClass('loading-span').html('<span class="spinner-border"></span>');

		let formData = new FormData(this); // Correct way to capture form fields

		$.ajax({
			url: $(this).attr('action'),
			type: $(this).attr('method'),
			data: formData,
			processData: false, // Prevent automatic data processing
			contentType: false, // Prevent setting content-type header
			cache: false,
			dataType: 'json',
			success: function (res) {   
				submitButton.prop('disabled', false).removeClass('loading-span').html('Submit');
				$('.error_msg').remove(); 

				toastrMsg(res.status, res.message);  
			},
			error: function (xhr) {
				submitButton.prop('disabled', false).removeClass('loading-span').html('Submit');
				console.error(xhr.responseText);
				toastrMsg("error", "Something went wrong. Please try again.");
			}
		});
	});
	
	//Onafric Bank country
	var $onafricOnafricBankCountryform = $('#onafricOnafricBankCountryform');
	var i = @json($onafriqBankCountryCount);
	
	function addOnacfricBankCountry(obj, event)
	{
		event.preventDefault();
		i++;
		var html =`
			<div class="row mt-3" id="remove_row">
				<div class="col-md-6">
					<label class="form-label">Country Name</label>
					<select name="country_name[]" id="bank_country_name_${i}" class="form-control select2" required>
						<option value="">Select Country</option>
						@foreach($countries as $country)
							<option value="{{ $country->nicename}}">{{ $country->nicename}}</option>
						@endforeach
					</select>
				</div>
				<input type="hidden" name="ids[]" id="id" value="">
				  
				<div class="col-md-4 d-flex align-items-end gap-3"> 
					<button type="button" class="btn btn-danger" onclick="removeOnacfricBankCountry(this)">Remove Country</button>  
				</div>
			</div>
		`;
		
		$onafricOnafricBankCountryform.find('#onafricBankCountryAppend').append(html);
		$('.select2').select2({ 
			width: "100%"
		})
	}
	
	function removeOnacfricBankCountry(obj)
	{
		$(obj).closest('#remove_row').remove();
	}
	
	$onafricOnafricBankCountryform.submit(function (event) {
		event.preventDefault(); 

		let submitButton = $onafricOnafricBankCountryform.find('[type="submit"]');
		submitButton.prop('disabled', true).addClass('loading-span').html('<span class="spinner-border"></span>');

		let formData = new FormData(this); // Correct way to capture form fields

		$.ajax({
			url: $(this).attr('action'),
			type: $(this).attr('method'),
			data: formData,
			processData: false, // Prevent automatic data processing
			contentType: false, // Prevent setting content-type header
			cache: false,
			dataType: 'json',
			success: function (res) {   
				submitButton.prop('disabled', false).removeClass('loading-span').html('Submit');
				$('.error_msg').remove(); 

				toastrMsg(res.status, res.message);  
			},
			error: function (xhr) {
				submitButton.prop('disabled', false).removeClass('loading-span').html('Submit');
				console.error(xhr.responseText);
				toastrMsg("error", "Something went wrong. Please try again.");
			}
		});
	});
	
	$('#bankOnaficListDatatable').DataTable({
		lengthChange: false,  
		autoWidth: false 
	});
</script>
@endpush				