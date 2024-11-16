@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Setting')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">General Setting</h4>
	</div> 
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<form class="forms-sample row" id="generalForm" action="{{ route('admin.general-setting.update') }}" method="post" enctype="multipart/form-data">
					<div class="mb-3 col-md-6">
						<label for="exampleInputUsername1" class="form-label">Site Name</label>
						<input type="text" class="form-control" id="site_name" name="site_name" autocomplete="off" placeholder="Site Name"  value="{{ config('setting.site_name') }}">
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Default Currency</label>
						<input type="text" class="form-control" id="default_currency" name="default_currency" placeholder="Default Currency" value="{{ config('setting.default_currency') }}"> 
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputPassword1" class="form-label">Site Logo</label> 
						<input type="file" name="site_logo" id="site_logo" class="form-control" accept=".jpg,.jpeg,.png"> 
						<img class="mt-3" src="{{ url('storage/setting', config('setting.site_logo')) }}" style="height:80px; width:80px">
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputPassword1" class="form-label">Login Logo</label> 
						<input type="file" name="login_logo" id="login_logo" class="form-control" accept=".jpg,.jpeg,.png"> 
						<img class="mt-3" src="{{ url('storage/setting', config('setting.login_logo')) }}" style="height:80px; width:80px">
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputPassword1" class="form-label">Fevicon Icon</label> 
						<input type="file" name="fevicon_icon" id="fevicon_icon" class="form-control" accept=".jpg,.jpeg,.png,.ico"> 
						<img class="mt-3" src="{{ url('storage/setting', config('setting.fevicon_icon')) }}" style="height:80px; width:80px">
					</div>
					
					<div class="d-flex justify-content-end">
						<button type="submit" class="btn btn-primary me-2">Submit</button> 
					</div>
				</form> 
			</div>
		</div>
	</div> 
</div>
 
@endsection

@push('js')
<script>
	$('#generalForm').submit(function(event) 
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
				$('#generalForm').find('button').prop('disabled',false);	 
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