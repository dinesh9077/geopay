<div class="modal fade" id="editMerchantModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update Merchant</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="editMerchantForm" action="{{ route('admin.merchant.update', ['id' => $merchant->id]) }}" method="post" enctype="multipart/form-data">
				<div class="modal-body row">
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Company Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="company_name" name="company_name" value="{{ $merchant->company_name }}">
					</div>  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">First Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="first_name" name="first_name" value="{{ $merchant->first_name }}">
					</div>  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Last Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="last_name" name="last_name" value="{{ $merchant->last_name }}">
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Email <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="email" name="email" value="{{ $merchant->email }}">
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Password <span class="text-danger">*</span></label>
						<input type="password" class="form-control" id="password" name="password" value="">
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Country <span class="text-danger">*</span></label>
						<select class="form-control select2" id="country_id" name="country_id">
							<option value=""> Select Country </option>
							@foreach($countries as $country)
								<option value="{{ $country->id }}" {{ $country->id == $merchant->country_id ? 'selected' : '' }}> {{ $country->nicename }} </option>
							@endforeach
						</select>
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Mobile <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="mobile_number" name="mobile_number" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));" value="{{ $merchant->mobile_number }}">
					</div>  
					  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Address </label>
						<input type="text" class="form-control" id="address" name="address" value="{{ $merchant->address }}">
					</div>  
					 
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Date Of Birth</label>
						<div class="input-group flatpickr" id="flatpickr-date">
							<input type="date" class="form-control" id="date_of_birth" name="date_of_birth" placeholder="Select Date Of Birth" value="{{ $merchant->date_of_birth }}"> 
						</div>
					</div>  
					 
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-control select2" id="status" name="status">
							<option value="1" {{ 1 == $merchant->status ? 'selected' : '' }}> Active </option>
							<option value="0" {{ 0 == $merchant->status ? 'selected' : '' }}> In-Active </option>
						</select>
					</div>  
					
					<div class="roles-table-main permission_show"> </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script> 
	$('.select2').select2({
		width: "100%",
		dropdownParent: $('#editMerchantModal') // 👈 set the modal or popup container
	});
 
	$editMerchantForm = $('#editMerchantForm');
	
	$editMerchantForm.submit(function(event) 
	{
		event.preventDefault();    
		$(this).find('button').prop('disabled',true);   
		
		var formDataInput = {}; 
		$(this).find("input, select, checkbox").each(function() {
			var inputName = $(this).attr('name');
			
			 // Ensure the input has a name attribute before processing
			if (!inputName) return;
			 
			if ($(this).attr('type') === 'checkbox') 
			{  
				if ($(this).is(':checked')) 
				{
					// Initialize 'permission' object if not already set
					if (!formDataInput['permission']) {
						formDataInput['permission'] = {};
					}
					// Assign the checkbox value
					formDataInput['permission'][inputName] = $(this).val();
				} 
			} else if ($(this).attr('type') !== 'file') {
				// Handle other inputs
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
				$editMerchantForm.find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.message);  
					$('#editMerchantModal').modal('hide');
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