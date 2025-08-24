<div class="modal fade" id="editManualRateModal" tabindex="-1" aria-labelledby="varyingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="varyingModalLabel">Edit Exchange Rate</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
			</div>
			<form id="editManualRateForm" action="{{ route('admin.manual.exchange-rate.update', ['id' => $manualRate->id]) }}" method="post" enctype="multipart/form-data">
				<input type="hidden" value="{{ $manualRate->type}}" id="type"> 
				<div class="modal-body">
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Aggregator Charge <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="aggregator_rate" name="aggregator_rate" value="{{ $manualRate->aggregator_rate }}" placeholder="Aggregator Charge"> 
					</div> 
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Markdown Type <span class="text-danger">*</span></label>
						<select class="form-control" id="markdown_type" name="markdown_type"> 
							<option value="flat" {{ $manualRate->markdown_type == "flat" ? 'selected' : '' }}> Flat/Fixed </option>
							<option value="percentage" {{ $manualRate->markdown_type == "percentage" ? 'selected' : '' }}> Percentage </option>
						</select>
					</div>  
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Markdown Charge <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="markdown_charge" name="markdown_charge" autocomplete="off" placeholder="Markdown Charge Flat/%" value="{{ $manualRate->markdown_charge }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div> 
					<!--<div class="mb-3">
						<label for="recipient-name" class="form-label">Api Markdown Type <span class="text-danger">*</span></label>
						<select class="form-control" id="api_markdown_type" name="api_markdown_type"> 
							<option value="flat" {{ $manualRate->api_markdown_type == "flat" ? 'selected' : '' }}> Flat/Fixed </option>
							<option value="percentage" {{ $manualRate->api_markdown_type == "percentage" ? 'selected' : '' }}> Percentage </option>
						</select>
					</div>  
					<div class="mb-3">
						<label for="recipient-name" class="form-label">Api Markdown Charge <span class="text-danger">*</span></label>
						<input type="text" class="form-control" id="api_markdown_charge" name="api_markdown_charge" autocomplete="off" placeholder="Api Markdown Charge Flat/%" value="{{ $manualRate->api_markdown_charge }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
					</div> -->
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('#editManualRateForm').submit(function(event) 
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
				$('#editManualRateForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				
				if(res.status === "success")
				{   
					toastrMsg(res.status,res.message);  
					$('#editManualRateForm').find('#type').val() == 1 ? addServiceTable.draw() : payServiceTable.draw();
					$('#editManualRateModal').modal('hide');
				}
				else if(res.status == "validation")
				{  
					$.each(res.errors, function(key, value) {
						var inputField = $('#editManualRateForm').find('#' + key);
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