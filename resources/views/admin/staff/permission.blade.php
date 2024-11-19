<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update staff Permission</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="editPermissionForm" action="{{ route('admin.staff.permission-update', ['id' => $staff->id]) }}" method="post" enctype="multipart/form-data">
				<div class="modal-body"> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Roles <span class="text-danger">*</span></label>
						<select class="form-control" id="role_id" name="role_id">
							<option value=""> Select Roles </option> 
							@foreach($roles as $role)
							<option value="{{ $role->id }}" data-role-name="{{$role->name}}" {{ $staff->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }} </option> 
							@endforeach
						</select>
					</div> 
					<input type="hidden" id="role" name="role" value="{{ $staff->role }}">
					<div class="roles-table-main permission_show">
						<div class="role-head"> 
							<div class="table-responsive ">
								<table id="datatable" class="table table-bordered  dt-responsive nowrap extra" style="border-collapse: collapse; border-spacing: 0; width: 100%;"> 
									<thead>
										<tr>
											<th>Module Permission</th>
											<th>all</th>
											<th>view</th>
											<th>add</th>
											<th>edit</th>
											<th>delete</th> 
										</tr>
									</thead>
									<tbody>
										<?php
											$length = count($permissions); 
											for($i = 0; $i < $length; $i++) 
											{
												$headkey = strtolower(str_replace(' ','_',$permissions[$i]->name)).$i;
											?>
											<tr>
												<td>{{ucwords(str_replace('_',' ',$permissions[$i]->name))}}</td>
												<td>
													<input id="all_{{$headkey}}" class="selectAll" value="{{$headkey}}" type="checkbox">
												</td>
												<td>
													<input id="view_{{$headkey}}" class="checkbox_{{$headkey}}" type="checkbox" name="{{$permissions[$i]->name}}.view" value="view" <?php if(in_array($permissions[$i]->name.'.view',$roleper)){ echo 'checked'; } ?>>
												</td> 
												<td>
													<input id="add_{{$headkey}}" class="checkbox_{{$headkey}}" type="checkbox" name="{{$permissions[$i]->name}}.add" value="add" <?php if(in_array($permissions[$i]->name.'.add',$roleper)){ echo 'checked'; } ?>>
												</td> 
												<td>
													<input id="edit_{{$headkey}}" class="checkbox_{{$headkey}}" type="checkbox" name="{{$permissions[$i]->name}}.edit" value="edit" <?php if(in_array($permissions[$i]->name.'.edit',$roleper)){ echo 'checked'; } ?>>
												</td> 
												<td>
													<input id="delete_{{$headkey}}" class="checkbox_{{$headkey}}" type="checkbox" name="{{$permissions[$i]->name}}.delete" value="delete" <?php if(in_array($permissions[$i]->name.'.delete',$roleper)){ echo 'checked'; } ?>>
												</td>  
											</tr> 
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>  	
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
	$('#editPermissionForm').submit(function(event) 
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
				$('#editPermissionForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.message);  
					$('#editPermissionModal').modal('hide');
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
	
	$('#role_id').change(function ()
	{ 
		// Clear previous permissions
		$('.permission_show').html('');

		const role_id = $(this).val();
		const role_name = $(this).find(':selected').data('role-name');

		// Append role name input only if not already appended
		if ($('#role').length === 0) {
			$('#editPermissionForm').append(`<input type="hidden" id="role" name="role" value="${role_name}">`);
		} else {
			$('#editPermissionForm').find('#role').val(role_name);  // Update the existing input if it exists
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
	<?php
		$length = count($permissions); 
		for($i = 0; $i < $length; $i++) 
		{
			$headkey = strtolower(str_replace(' ','_',$permissions[$i]->name)).$i;
		?>
		<script>
			$(document).ready(function()
			{ 
				$('.checkbox_{{$headkey}}').on('click',function()
				{
					if($('.checkbox_{{$headkey}}:checked').length == $('.checkbox_{{$headkey}}').length){
						
						$('#all_{{$headkey}}').prop('checked',true);
						}else{
						
						$('#all_{{$headkey}}').prop('checked',false);
					}
				});
				
				if($('.checkbox_{{$headkey}}:checked').length == $('.checkbox_{{$headkey}}').length){
					
					$('#all_{{$headkey}}').prop('checked',true);
					}else{
					
					$('#all_{{$headkey}}').prop('checked',false);
				}
			});
		</script>
		<?php
		}
	?> 