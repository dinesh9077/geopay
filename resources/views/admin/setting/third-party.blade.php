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
		<ul class="nav nav-tabs nav-tabs-line" id="lineTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="metamap-line-tab" data-bs-toggle="tab" href="#line-metamap" role="tab" aria-controls="line-metamap" aria-selected="true">Meta Map</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="smtpmail-line-tab" data-bs-toggle="tab" href="#line-smtpmail" role="tab" aria-controls="line-smtpmail" aria-selected="false">SMTP Mail</a>
			</li> 
			<li class="nav-item">
				<a class="nav-link" id="smsplus-line-tab" data-bs-toggle="tab" href="#line-smsplus" role="tab" aria-controls="line-smsplus" aria-selected="false">SMS Plus</a>
			</li> 
			<li class="nav-item">
				<a class="nav-link" id="dtone-line-tab" data-bs-toggle="tab" href="#line-dtone" role="tab" aria-controls="line-dtone" aria-selected="false">International Airtime (dtone)</a>
			</li> 
			<li class="nav-item">
				<a class="nav-link" onclick="getLightNetView(event)" id="lightnet-line-tab" data-bs-toggle="tab" href="#line-lightnet" role="tab" aria-controls="line-lightnet" aria-selected="false">Lightnet</a>
			</li> 
			<li class="nav-item">
				<a class="nav-link" onclick="getOnafricMobileView(event)" id="onafriq-line-tab" data-bs-toggle="tab" href="#line-onafriq" role="tab" aria-controls="line-onafriq" aria-selected="false">Onafriq (Mobile Money)</a>
			</li> 
		</ul>
		<div class="tab-content mt-3" id="lineTabContent">
			<div class="tab-pane fade show active" id="line-metamap" role="tabpanel" aria-labelledby="metamap-line-tab"> 
				@include('admin.setting.partial.meta-map')
			</div>
			<div class="tab-pane fade" id="line-smtpmail" role="tabpanel" aria-labelledby="smtpmail-line-tab"> 
				@include('admin.setting.partial.smtp-mail')
			</div>
			<div class="tab-pane fade" id="line-smsplus" role="tabpanel" aria-labelledby="smsplus-line-tab">
				@include('admin.setting.partial.sms-plus')
			</div>
			<div class="tab-pane fade" id="line-dtone" role="tabpanel" aria-labelledby="dtone-line-tab">
				@include('admin.setting.partial.international-airtime')
			</div> 
			<div class="tab-pane fade" id="line-lightnet" role="tabpanel" aria-labelledby="lightnet-line-tab">
				@include('admin.setting.partial.lightnet')
			</div> 
			<div class="tab-pane fade" id="line-onafriq" role="tabpanel" aria-labelledby="onafriq-line-tab"> 
				@include('admin.setting.partial.onafric-mobile')
			</div>
		</div>
	</div>
</div> 
@endsection

@push('js')
<script>
	var $forms = $('#metaMapForm, #smsPlusForm, #dtonePlusForm, #lightnetPlusForm, #onafricForm');

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
</script>
@endpush				