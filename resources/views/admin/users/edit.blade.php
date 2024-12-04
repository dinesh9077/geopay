@extends('admin.layouts.app')
@section('title', config('setting.site_name') . ' - Account Detail')

@section('content') 
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
	<div>
		<h4 class="mb-3 mb-md-0">Account Detail</h4>
	</div>  
</div> 

<div class="row"> 
	<livewire:admin.user-detail-card :company="$company" />
</div>

<div class="row">
	<div class="col-md-4 col-lg-3 mb-2">
		<a href="{{ route('admin.user.login-history', ['id' => $company->id]) }}" class="btn btn-info w-100">
			<i data-feather="align-justify" style="height: 16px;"></i>
			Login History
		</a>
	</div>	
	<div class="col-md-4 col-lg-3 mb-2">
		<a href="{{ route('admin.companies.increment-balance', ['id' => $company->id]) }}" onclick="addBalance(this, event)" class="btn btn-success w-100">
			<i data-feather="plus-circle" style="height: 16px;"></i>
			Balance
		</a>
	</div>
	
	<div class="col-md-4 col-lg-3 mb-2">
		<a href="{{ route('admin.companies.decrement-balance', ['id' => $company->id]) }}" onclick="minusBalance(this, event)" class="btn btn-danger w-100">
			<i data-feather="minus-circle" style="height: 16px;"></i>
			Balance
		</a>
	</div> 
	<div class="col-md-4 col-lg-3 mb-2" id="blockUnblockAccount">
		<button type="button" class="btn btn-warning w-100"  data-block-text="{{ $company->status == 1 ? 'Block' : 'Unblock' }} Account"data-block-msg="{{ $company->status == 1 ? 'If you block this account he/she want able to access his/her dashboard.' : 'If you unblock this account he/she able to access his/her dashboard.' }} Account" data-status="{{ $company->status == 1 ? 0 : 1 }}" onclick="banAccount(this, event)">
			<i data-feather="{{ $company->status == 1 ? 'slash' : 'key' }}" style="height: 16px;"></i> {{ $company->status == 1 ? 'Block' : 'Unblock' }} Account
		</button>
	</div>
</div>

<div class="row gy-4 mt-0">
	<div class="col-xl-3 col-lg-5 col-md-5">
		<div class="row gy-4">
			<div class="">
				<div class="card">
					<div class="card-header">
						<div class="card-title d-flex justify-content-center gap-3 mb-0">
							<h6>
								<i class="text-{{ $company->is_email_verify == 1 ? 'success' : 'danger' }}" data-feather="{{ $company->is_email_verify == 1 ? 'check-circle' : 'x-circle' }}" style="height: 16px;"></i>
								Email
							</h6>
							<h6>
								<i class="text-{{ $company->is_mobile_verify == 1 ? 'success' : 'danger' }}" data-feather="{{ $company->is_mobile_verify == 1 ? 'check-circle' : 'x-circle' }}" style="height: 16px;"></i>
								Mobile
							</h6>
							<h6>
								<i class="text-{{ $company->is_kyc_verify == 1 ? 'success' : 'danger' }}" data-feather="{{ $company->is_kyc_verify == 1 ? 'check-circle' : 'x-circle' }}" style="height: 16px;"></i>
								KYC
							</h6>
						</div>
					</div>
					<div class="card-body text-center"> 
						@if($company->profile_image) 
							<img src="{{ url('storage/profile', $company->profile_image) }}" id="profileImagePreview" alt="{{ $company->first_name }}" class="account-holder-image" style="max-width: 100%; width:90%; height:200px;">
						@else
							<img src="{{ url('admin/default-profile.png') }}" id="profileImagePreview" alt="{{ $company->first_name }}" class="account-holder-image"style="max-width: 100%; width:90%; height:200px;">
						@endif 
					</div>
				</div>
			</div>
			
			@if(!empty($company->userKyc))
			<div class="">
				<div class="card">
					<div class="card-header">
						<h5 class="card-title text-center mb-0">View KYC Details</h5>
					</div>
					<div class="card-body">
						<div class="list-group list-group-flush">
							<div class="list-group-item d-flex justify-content-between flex-column flex-wrap border-0">
								<small class="text-muted">Verification Status</small>
								<h6>{{ $company->userKyc->verification_status ?? 'N/A' }}</h6>
							</div>
							
							<div class="list-group-item d-flex justify-content-between flex-column flex-wrap border-0">
								<small class="text-muted">Identification ID</small>
								<h6>{{ $company->userKyc->identification_id ?? 'N/A' }}</h6>
							</div>
							
							<div class="list-group-item d-flex justify-content-between flex-column flex-wrap border-0">
								<small class="text-muted">Verification ID</small>
								<h6>{{ $company->userKyc->verification_id ?? 'N/A' }}</h6>
							</div>
							
							<!-- Documents (Handle both images and files) -->
							<div class="col-md-12 mb-3">
								<h6>Documents</h6>
								@if ($company->userKyc && $company->userKyc->document)
									@php
										$files = json_decode($company->userKyc->document, true); // Assuming the document column stores a JSON array
									@endphp
									<ul class="list-group">
										@foreach ($files as $file)
											@php
												$fileUrl = url($file); 
												$fileExtension = pathinfo($file, PATHINFO_EXTENSION);
											@endphp
											<li class="list-group-item">
												@if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif', 'bmp']))
													<!-- Display Image -->
													<img src="{{ $fileUrl }}" alt="{{ $fileUrl }}" class="img-fluid" />
												@elseif (in_array(strtolower($fileExtension), ['pdf', 'doc', 'docx', 'txt']))
													<!-- Display Document Link -->
													<a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a>
												@else
													<!-- Fallback for other file types -->
													<a href="{{ $fileUrl }}" target="_blank">{{ $fileUrl }}</a>
												@endif
											</li>
										@endforeach
									</ul>
								@else
									<p>No documents uploaded.</p>
								@endif
							</div>

							<!-- Videos -->
							<div class="col-md-12">
								<h6>Videos</h6>
								@if ($company->userKyc && $company->userKyc->video) 
									<ul class="list-group"> 
										<li class="list-group-item">
											<video controls width="100%">
												<source src="{{ url($company->userKyc->video) }}" type="video/mp4">
												Your browser does not support the video tag.
											</video>
										</li> 
									</ul>
								@else
									<p>No videos uploaded.</p>
								@endif
							</div> 
						</div>
					</div>
				</div>
			</div>
			@endif
		</div>
	</div>
	<div class="col-xl-9 col-lg-7 col-md-7">
		<div class="card">
			<div class="card-header">
				<h5 class="card-title mb-0">Information of {{ $company->first_name. ' ' . $company->last_name }}</h5>
			</div>
			<div class="card-body">
				<form id="informationForm" action="{{ route('admin.user.update', ['id' => $company->id])}}" method="POST" enctype="multipart/form-data"> 
					<div class="row">
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">Profile </label>
							<input type="file" class="form-control" id="profile_image" name="profile_image" >
						</div>  
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">First Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="first_name" name="first_name" value="{{ $company->first_name }}">
						</div>  
						
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">Last Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="last_name" name="last_name" value="{{ $company->last_name }}">
						</div>  
						
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">Email <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="email" value="{{ $company->email }}" readonly>
						</div>  
						
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">Mobile <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="mobile" value="{{ $company->formatted_number }}" readonly>
						</div>  
						
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">Password </label>
							<input type="text" class="form-control" id="password" name="password" value="">
						</div>  
						
						<div class="mb-3 col-md-6">
							<label for="recipient-name" class="form-label">Confirm Password </label>
							<input type="text" class="form-control" id="password_confirmation" name="password_confirmation" value="">
						</div>  
						 
						<div class="col-md-12 mt-3 text-end">
							<button type="submit" class="btn btn-primary ">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div> 
</div> 

<div class="modal fade" id="banConfirmModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> 
			<div class="modal-header">
				<h5 class="modal-title" id="block_text"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="banFormModal" method="post" action="{{ route('admin.companies.update-status') }}">
				<div class="modal-body">
					<p id="block_msg"></p>
				  
					<div class="mb-3"> 
						<input type="hidden" class="form-control" id="id" name="id" value="{{ $company->id }}"> 
						<input type="hidden" class="form-control" id="status" name="status" >  
					</div> 
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger" >Ok</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection 
@push('js')    
<script>
	// JavaScript to handle image preview
	var input = document.getElementById('profile_image');
	var preview = document.getElementById('profileImagePreview');

	input.addEventListener('change', function (event) {
		const file = event.target.files[0];
		if (file) {
			const reader = new FileReader();

			reader.onload = function (e) {
				preview.src = e.target.result; // Set the preview image src
				preview.style.display = 'block'; // Display the preview image
			};

			reader.readAsDataURL(file); // Convert the file to a data URL
		} else {
			preview.style.display = 'none'; // Hide the preview if no file is selected
		}
	});
		
	$('#informationForm').submit(function(event) 
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
				$('#informationForm').find('button').prop('disabled',false);	 
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
	 
	function addBalance(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get(obj, function(res)
			{
				const result = decryptData(res.response); 
				$('body').find('#modal-view-render').html(result.view);
				$('#incrementBalanceModal').modal('show');  
			});
		} 
	}
	
	function minusBalance(obj, event)
	{
		event.preventDefault();
		if (!modalOpen)
		{
			modalOpen = true;
			closemodal(); 
			$.get(obj, function(res)
			{
				const result = decryptData(res.response); 
				$('body').find('#modal-view-render').html(result.view);
				$('#decrementBalanceModal').modal('show');  
			});
		} 
	}
	
	function banAccount(obj, event)
	{
		event.preventDefault();
		var status = $(obj).data('status');
		var blockText = $(obj).data('block-text');
		var blockMsg = $(obj).data('block-msg');
		
		$('#block_text').text(blockText);
		$('#block_msg').text(blockMsg);
		$('#status').val(status);
		$('#banConfirmModal').modal('show');  
	}
	
	$('#banFormModal').submit(function(event) 
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
				$('#banFormModal').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status,res.message); 
					const result = decryptData(res.response);
					$('#blockUnblockAccount').html(result.output);
					$('#banConfirmModal').modal('hide');  
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