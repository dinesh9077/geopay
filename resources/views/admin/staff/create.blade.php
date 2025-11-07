<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Add New staff</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="addStaffForm" action="{{ route('admin.staff.store') }}" method="post" enctype="multipart/form-data">
				<div class="modal-body row">
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name" name="name">
					</div>  
					 
					<div class="mb-3 col-md-6">
						<label for="exampleInputEmail1" class="form-label">Date Of Birth</label>
						<div class="input-group flatpickr" id="flatpickr-date">
							<input type="text" class="form-control" id="dob" name="dob" placeholder="Select Date Of Birth" data-input>
							<span class="input-group-text input-group-addon" data-toggle><i data-feather="calendar"></i></span>
						</div>
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Personal Mobile <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="mobile" name="mobile" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Office Mobile</label>
						<input type="text" class="form-control" id="office_mobile" name="office_mobile"  oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Email <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="email" name="email">
					</div>  
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Password <span class="text-danger">*</span></label>
						<input type="password" class="form-control" id="password" name="password">
					</div> 
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Roles <span class="text-danger">*</span></label>
						<select class="form-control" id="role_id" name="role_id">
							<option value=""> Select Roles </option> 
							@foreach($roles as $role)
							<option value="{{ $role->id }}" data-role-name="{{$role->name}}"> {{ $role->name }} </option> 
							@endforeach
						</select>
					</div>
					
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-control" id="status" name="status">
							<option value="1"> Active </option>
							<option value="0"> In-Active </option>
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
	var flatpickrDateEl = document.querySelector('#flatpickr-date');
	if(flatpickrDateEl) {
		flatpickr("#flatpickr-date", {
		  wrap: true,
		  dateFormat: "Y-m-d",
		});
	}	
		
	$('#addStaffForm').submit(function(event) 
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
				$('#addStaffForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.message);  
					$('#addStaffModal').modal('hide');
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
	
	$('#role_id').change(function ()
	{ 
		// Clear previous permissions
		$('.permission_show').html('');

		const role_id = $(this).val();
		const role_name = $(this).find(':selected').data('role-name');

		// Append role name input only if not already appended
		if ($('#role').length === 0) {
			$('#addStaffForm').append(`<input type="hidden" id="role" name="role" value="${role_name}">`);
		} else {
			$('#addStaffForm').find('#role').val(role_name);  // Update the existing input if it exists
		}
		if(role_name != "admin")
		{
			$.get("{{url('admin/roles/groups')}}/"+role_id, function(res)
			{ 
				const result = decryptData(res.response);
				$('.permission_show').html(result.view);  
			},'Json');
		}  
	})
		
	$('.selectAllModule').on('click', function()
	{
		if (this.checked) {
			$('tbody').find('input[type="checkbox"]').prop('checked', true);
		} else {
			$('tbody').find('input[type="checkbox"]').prop('checked', false);
		}
	});
	
	$('.selectAll').on('click',function()
	{ 
		var num = $(this).val();   
		if(this.checked)
		{
			$('.checkbox_'+num).each(function(){
				this.checked = true;
			});
		}
		else
		{
			$('.checkbox_'+num).each(function(){
				this.checked = false;
			});
		}
	});
</script>  