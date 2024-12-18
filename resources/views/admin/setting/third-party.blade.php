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
				<a class="nav-link" id="lightnet-line-tab" data-bs-toggle="tab" href="#line-lightnet" role="tab" aria-controls="line-lightnet" aria-selected="false">Lightnet (LiquidNet)</a>
			</li> 
		</ul>
		<div class="tab-content mt-3" id="lineTabContent">
			<div class="tab-pane fade show active" id="line-metamap" role="tabpanel" aria-labelledby="metamap-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="metaMapForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Meta Host</label>
									<input type="url" class="form-control" id="meta_host" name="meta_host" autocomplete="off" placeholder="Meta Host"  value="{{ config('setting.meta_host') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Meta Verification Api Key</label>
									<input type="text" class="form-control" id="meta_verification_api_key" name="meta_verification_api_key" autocomplete="off" placeholder="Meta Verification Api Key"  value="{{ config('setting.meta_verification_api_key') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Meta Verification Flow Id</label>
									<input type="text" class="form-control" id="meta_verification_flow_id" name="meta_verification_flow_id" autocomplete="off" placeholder="Meta Verification Flow Id"  value="{{ config('setting.meta_verification_flow_id') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Meta Verification Secret</label>
									<input type="text" class="form-control" id="meta_verification_secret" name="meta_verification_secret" autocomplete="off" placeholder="Meta Verification Secret"  value="{{ config('setting.meta_verification_secret') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Meta Bearer Token</label>
									<input type="text" class="form-control" id="meta_bearer" name="meta_bearer" autocomplete="off" placeholder="Meta Bearer Token" value="{{ config('setting.meta_bearer') }}">
								</div>  
								
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div>
							</form> 
						</div>
					</div>
				</div> 
			</div>
			<div class="tab-pane fade" id="line-smtpmail" role="tabpanel" aria-labelledby="smtpmail-line-tab"> 
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="metaMapForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail Host</label>
									<input type="text" class="form-control" id="mail_host" name="mail_host" autocomplete="off" placeholder="Mail Host"  value="{{ config('setting.mail_host') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail Port</label>
									<input type="text" class="form-control" id="mail_port" name="mail_port" autocomplete="off" placeholder="Mail Port"  value="{{ config('setting.mail_port') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail Username</label>
									<input type="text" class="form-control" id="mail_username" name="mail_username" autocomplete="off" placeholder="Mail Username"  value="{{ config('setting.mail_username') }}">
								</div>  
								  
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail Password</label>
									<input type="text" class="form-control" id="mail_password" name="mail_password" autocomplete="off" placeholder="Mail Password" value="{{ config('setting.mail_password') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail Encryption</label>
									<select class="form-control" id="mail_encryption" name="mail_encryption">
										<option value="tls">TLS</option>
										<option value="ssl">SSL</option>
									</select>
								</div>
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail From Address</label>
									<input type="text" class="form-control" id="mail_from_address" name="mail_from_address" autocomplete="off" placeholder="Mail From Address" value="{{ config('setting.mail_from_address') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Mail From Name</label>
									<input type="text" class="form-control" id="mail_from_name" name="mail_from_name" autocomplete="off" placeholder="Mail From Name" value="{{ config('setting.mail_from_name') }}">
								</div> 
								   
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div>
							</form> 
						</div>
					</div>
				</div> 
			</div>
			<div class="tab-pane fade" id="line-smsplus" role="tabpanel" aria-labelledby="smsplus-line-tab">
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="smsPlusForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">SMS Host</label>
									<input type="url" class="form-control" id="sms_host" name="sms_host" autocomplete="off" placeholder="SMS Host"  value="{{ config('setting.sms_host') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">SMS Username</label>
									<input type="text" class="form-control" id="sms_username" name="sms_username" autocomplete="off" placeholder="SMS Username"  value="{{ config('setting.sms_username') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">SMS Password</label>
									<input type="text" class="form-control" id="sms_password" name="sms_password" autocomplete="off" placeholder="SMS Password"  value="{{ config('setting.sms_password') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">SMS Sender</label>
									<input type="text" class="form-control" id="sms_sender" name="sms_sender" autocomplete="off" placeholder="SMS Sender"  value="{{ config('setting.sms_sender') }}">
								</div>  
								 
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div>
							</form> 
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane fade" id="line-dtone" role="tabpanel" aria-labelledby="dtone-line-tab">
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="dtonePlusForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Base Url</label>
									<input type="url" class="form-control" id="dtone_url" name="dtone_url" autocomplete="off" placeholder="Base Url"  value="{{ config('setting.dtone_url') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Api Key</label>
									<input type="text" class="form-control" id="dtone_apikey" name="dtone_apikey" autocomplete="off" placeholder="Api Key"  value="{{ config('setting.dtone_apikey') }}">
								</div>  
								 
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Secret Key</label>
									<input type="text" class="form-control" id="dtone_secretkey" name="dtone_secretkey" autocomplete="off" placeholder="Secret Key"  value="{{ config('setting.dtone_secretkey') }}">
								</div> 
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Service Id</label>
									<input type="text" class="form-control" id="dtone_serviceid" name="dtone_serviceid" autocomplete="off" placeholder="Service Id"  value="{{ config('setting.dtone_serviceid') }}">
								</div> 
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Subservice Id</label>
									<input type="text" class="form-control" id="dtone_subserviceid" name="dtone_subserviceid" autocomplete="off" placeholder="Subservice Id"  value="{{ config('setting.dtone_subserviceid') }}">
								</div>  
								 
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div>
							</form> 
						</div>
					</div>
				</div>
			</div> 
			<div class="tab-pane fade" id="line-lightnet" role="tabpanel" aria-labelledby="lightnet-line-tab">
				<div class="col-md-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body"> 
							<form class="forms-sample row" id="lightnetPlusForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Base Url</label>
									<input type="url" class="form-control" id="lightnet_url" name="lightnet_url" autocomplete="off" placeholder="Base Url"  value="{{ config('setting.lightnet_url') }}">
								</div>  
								
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Api Key</label>
									<input type="text" class="form-control" id="lightnet_apikey" name="lightnet_apikey" autocomplete="off" placeholder="Api Key"  value="{{ config('setting.lightnet_apikey') }}">
								</div>  
								 
								<div class="mb-3 col-md-6">
									<label for="exampleInputUsername1" class="form-label">Api Secret</label>
									<input type="text" class="form-control" id="lightnet_secretkey" name="lightnet_secretkey" autocomplete="off" placeholder="Secret Key"  value="{{ config('setting.lightnet_secretkey') }}">
								</div>  
								<div class="d-flex justify-content-end">
									<button type="submit" class="btn btn-primary me-2">Submit</button> 
								</div>
							</form> 
						</div>
					</div>
				</div>
			</div> 
		</div>
	</div>
</div>

@endsection

@push('js')
<script>
	var $forms = $('#metaMapForm, #smsPlusForm, #dtonePlusForm, #lightnetPlusForm');
	$forms.submit(function(event) 
	{
		event.preventDefault();   
		
		$(this).find('button').prop('disabled',true);   
		
		var formDataInput = {}; 
		$(this).find("input, select").each(function() {
			var inputName = $(this).attr('name');
			
			if ($(this).attr('type') !== 'file') { 
				formDataInput[inputName] = $(this).val();
			}
		}); 
		const encrypted_data = encryptData(JSON.stringify(formDataInput));
		
		var formData = new FormData(); 
		formData.append('encrypted_data', encrypted_data);  
		formData.append('_token', "{{ csrf_token() }}");
		
		$(this).find("input[type='file']").each(function() {
			var inputName = $(this).attr('name');
			var files = $(this)[0].files;
			
			$.each(files, function(index, file) {
				formData.append(inputName + '', file);  
			});
		});
		
		$.ajax({ 
			type: $(this).attr('method'),
			url: $(this).attr('action'),
			data: formData,
			processData: false, 
			contentType: false,  
			cache: false, 
			dataType: 'Json', 
			success: function (res) 
			{ 
				$forms.find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status,res.message);  
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $('#' + key);
						var errorSpan = $('<span>')
						.addClass('error_msg text-danger') 
						.attr('id', key + 'Error')
						.text(value[0]);  
						inputField.parent().append(errorSpan);
					});
				}
				else
				{ 
					toastrMsg(res.status,res.message);
				}
			} 
		});
	});
</script>
@endpush				