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
				<a class="nav-link" id="smsplus-line-tab" data-bs-toggle="tab" href="#line-smsplus" role="tab" aria-controls="line-smsplus" aria-selected="false">SMS Plus</a>
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
		</div>
	</div>
</div>

@endsection

@push('js')
<script>
	var $forms = $('#metaMapForm, #smsPlusForm');
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