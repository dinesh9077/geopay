@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Profile</h4>
	</div> 
</div>

<div class="row">
	<div class="col-md-12 grid-margin stretch-card">
		<div class="card">
			<div class="card-body"> 
				<form class="forms-sample row" id="profileForm" action="{{ route('admin.profile-update') }}" method="post" enctype="multipart/form-data">
					<div class="mb-3 col-md-12">
						@if($admin->profile)
							<img class="mt-3" src="{{ url('storage/admin_profile', $admin->profile) }}" style="height:80px; width:80px">
						@else
							<img class="mt-3" src="{{ url('admin/default-profile.png') }}" style="height:80px; width:80px">
						@endif
					</div>
					<div class="mb-3 col-md-6">
						<label for="exampleInputPassword1" class="form-label">Profile</label> 
						<input type="file" name="profile" id="profile" class="form-control" accept=".jpg,.jpeg,.png">  
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputUsername1" class="form-label">Name</label>
						<input type="text" class="form-control" id="name" name="name" autocomplete="off" placeholder="Name"  value="{{ $admin->name }}">
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Email</label>
						<input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{ $admin->email }}"> 
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Password</label>
						<input type="text" class="form-control" id="password" name="password" placeholder="Password" value=""> 
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Mobile</label>
						<input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="{{ $admin->mobile }}"> 
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Office Mobile</label>
						<input type="text" class="form-control" id="office_mobile" name="office_mobile" placeholder="Office Mobile" value="{{ $admin->office_mobile }}"> 
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Date Of Birth</label>
						<div class="input-group flatpickr" id="flatpickr-date">
							<input type="text" class="form-control" id="dob" name="dob" placeholder="Select Date Of Birth" data-input  value="{{ $admin->dob }}">
							<span class="input-group-text input-group-addon" data-toggle><i data-feather="calendar"></i></span>
						</div>
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
	
	const flatpickrDateEl = document.querySelector('#flatpickr-date');
	if(flatpickrDateEl) {
		flatpickr("#flatpickr-date", {
		  wrap: true,
		  dateFormat: "Y-m-d",
		});
	}
	  
	$('#profileForm').submit(function(event) 
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
				$('#profileForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status,res.message);  
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $('#' + key);
						var existingList = $('#' + key + 'ErrorList');

						// Remove previous error list
						if (existingList.length) {
							existingList.remove();
						}

						// Create a new <ul> list to hold error <li>s
						var errorList = $('<ul style="padding-left: 1rem;">')
							.addClass('error_msg text-danger')
							.attr('id', key + 'ErrorList');

						// Add each error as <li>
						$.each(value, function(i, msg) {
							errorList.append($('<li style="list-style: disc;">').text(msg));
						});

						// Append the list after the input field
						inputField.parent().append(errorList);
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