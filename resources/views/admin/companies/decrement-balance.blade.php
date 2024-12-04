<div class="modal fade" id="decrementBalanceModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Deduct Balance</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="decrementBalanceForm" method="post" action="{{ route('admin.companies.store-decrement-balance') }}">
				<input type="hidden" name="id" value="{{ $userId }}">
				<div class="modal-body row">
					<div class="mb-3 col-md-12">
						<label class="form-label">Users <span class="text-danger">*</span></label>
						<select id="user_id" name="user_id" class="form-control select2"> 
							<option value="">Select User</option>
							@foreach($users as $user)
								<option value="{{ $user->id }}">{{ $user->first_name. ' ' .$user->last_name }} ({{ $user->formatted_number }})</option>
							@endforeach
						</select>
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Amount <span class="text-danger">*</span></label>
						<input type="number" id="amount" name="amount" class="form-control" placeholder="Enter amount"> 
					</div>
					<div class="mb-3 col-md-12">
						<label class="form-label">Remark <span class="text-danger">*</span></label>
						<textarea name="remark" id="remark" class="form-control" placeholder="Remark"></textarea> 
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
	$('.select2').select2({
		width: "100%",
		dropdownParent: $('#decrementBalanceModal') // Replace with the ID of your modal
	});
	
	$('#decrementBalanceForm').submit(function(event) 
	{
		event.preventDefault();   
		
		$(this).find('button').prop('disabled',true);   
		
		var formDataInput = {}; 
		$(this).find("input, select, textarea").each(function() {
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
				$('#decrementBalanceForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{ 
					toastrMsg(res.status,res.message);  
					Livewire.dispatch('refreshData');
					$('#decrementBalanceModal').modal('hide');
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