<div class="modal fade" id="importRateModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Import Exchange Rate</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="importRateForm" action="{{ route('admin.manual.exchange-rate.store') }}" method="post" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Exchange Type <span class="text-danger">*</span></label>
						<select class="form-control" id="type" name="type">
							<option value=""> Select Service </option>
							<option value="1"> Add Service </option>
							<option value="2"> Pay Service </option>
						</select>
					</div> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">File Import <span class="text-danger">*</span></label>
						<input type="file" class="form-control" id="file_import" name="file_import" accept=".xlsx, .csv" aria-required="true" aria-describedby="fileHelp">
						<p id="fileHelp" class="form-text text-muted">Accepted file types: .xlsx, .csv</p>
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
	$('#importRateForm').submit(function(event) 
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
				$('#importRateForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{  
					$('#importRateForm').find('#type').val() == 1 ? addServiceTable.draw() : payServiceTable.draw();
					 
					toastrMsg(res.status,res.message);  
					$('#importRateModal').modal('hide');
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
					toastrMsg(res.status, res.message); 
				}
			} 
		});
	});	
</script>