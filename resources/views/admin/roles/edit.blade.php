<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Update Role</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="editRoleForm" action="{{ route('admin.roles.update', ['id' => $role->id]) }}" method="post" enctype="multipart/form-data">
				<div class="modal-body row">
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Role Name <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}">
					</div>  
					<div class="mb-3 col-md-6">
						<label for="recipient-name" class="form-label">Status <span class="text-danger">*</span></label>
						<select class="form-control" id="status" name="status">
							<option value="1" {{ $role->status == 1 ? 'selected' : '' }}> Active </option>
							<option value="0" {{ $role->status == 0 ? 'selected' : '' }}> In-Active </option>
						</select>
					</div>  
				</div>
				<div class="roles-table-main"> 
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
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('#editRoleForm').submit(function(event) 
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
				$('#editRoleForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					dataTable.draw();
					toastrMsg(res.status,res.message);  
					$('#editRoleModal').modal('hide');
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