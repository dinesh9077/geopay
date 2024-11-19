<div class="modal fade" id="editCompanyModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update Company Details</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="editCompanyForm" action="{{ route('admin.companies.update', ['id' => $company->id]) }}" method="post" enctype="multipart/form-data">
				 <div class="modal-body row">
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
						<label for="recipient-name" class="form-label">Password <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="password" name="password" value="">
					</div>  
					   
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Company Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="company_name" name="company_name" value="{{ $company->company_name }}">
					</div>  
					  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-control" id="status" name="status">
							<option value="1" {{ $company->status == 1 ? 'selected' : '' }}> Active </option>
							<option value="0" {{ $company->status == 0 ? 'selected' : '' }}> In-Active </option>
						</select>
					</div>   
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>  
	$('#editCompanyForm').submit(function(event) 
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
				$('#editCompanyForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.message);  
					$('#editCompanyModal').modal('hide');
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