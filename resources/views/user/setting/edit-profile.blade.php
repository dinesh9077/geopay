<div class="grid-container mt-md-4">
    <form id="profileForm" action="{{ route('profile-update') }}" method="post" class="animate__animated animate__fadeIn g-2">
		<div class="d-flex d-md-block justify-content-center">
			<div class="mb-3 w-fit position-relative"> 
				@if($user->profile_image) 
					<img class="avatar-2xl rounded-4 shadow-lg" id="profileImage" src="{{ url('storage/profile', $user->profile_image) }}" alt="Profile Image" height="100" width="100">
				@else 
					<img class="avatar-2xl rounded-4 shadow-lg" id="profileImage" src="{{ url('admin/default-profile.png') }}" alt="Profile Image" height="100" width="100">
				@endif
				<input type="file" id="imageUpload" name="profile_image" accept=".jpg, .png" style="display: none;">
				<div class="edit-icon btn btn-light aspect-sq" id="editIcon"><i class="bi bi-pencil-fill small"></i></div>
			</div>
		</div>
        
        <div class="row text-start col-lg-8">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label content-2 fw-semibold mb-1">First Name</label>
                <input id="first_name" name="first_name" type="text" class="form-control form-control-lg default-input" value="{{ $user->first_name }}">
			</div>
			<!-- Last Name -->
			<div class="col-md-6 mb-3">
				<label for="last_name" class="form-label content-2 fw-semibold mb-1">Last Name</label>
				<input id="last_name" name="last_name" type="text" class="form-control form-control-lg default-input" value="{{ $user->last_name }}">
			</div>
			<!-- Email Address -->
			<div class="col-md-6 mb-3">
				<label for="email" class="form-label content-2 fw-semibold mb-1">Email Address</label>
				<div class="position-relative">
					<input type="email" class="form-control form-control-lg default-input" value="{{ $user->email }}" readonly>
					@if($user->is_email_verify == 1)
						<span class="kyc-status kyc-success">verified</span>
					@else
						<span class="kyc-status kyc-failed">verified</span>	
					@endif
				</div>
			</div> 
			<!-- Mobile No. -->
			<div class="col-md-6 mb-3">
				<label for="mobile" class="form-label content-2 fw-semibold mb-1">Mobile No.</label>
				<div class="position-relative">
					<input  type="text" class="form-control form-control-lg default-input" value="{{ $user->formatted_number }}" readonly>
					@if($user->is_mobile_verify == 1)
						<span class="kyc-status kyc-success">verified</span>
					@else
						<span class="kyc-status kyc-failed">verified</span>	
					@endif
				</div>
			</div> 
		</div> 
        <button type="submit" class="btn btn-lg btn-secondary rounded-2 col-12 col-md-2 mt-3 mt-md-0">Save</button>
	</form>
</div>

@push('js')
<script>  
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