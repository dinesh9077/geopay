<div class="grid-container mt-md-4">
    <form id="passwordResetForm" action="{{ route('password-change') }}" method="post" class="animate__animated animate__fadeIn g-2">
        <div class="row text-start col-lg-8 mb-3">
            <div class="col-md-9 mb-3">
                <label for="old-password" class="form-label content-2 fw-semibold mb-1">Old Password</label>
                <input id="old_password" name="old_password" type="password" class="form-control form-control-lg default-input">
			</div>
            <div class="col-md-9 mb-3">
                <label for="new-password" class="form-label content-2 fw-semibold mb-1">New Password</label>
                <input id="password" name="password" type="password" class="form-control form-control-lg default-input">
			</div>
            <div class="col-md-9 mb-3">
                <label for="re-enter-password" class="form-label content-2 fw-semibold mb-1">Re Enter Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control form-control-lg default-input">
			</div>
		</div> 
        <button type="submit" class="btn btn-lg btn-secondary rounded-2 col-12 col-md-2 mt-3 mt-md-0">Save</button>
	</form>
</div>
 
@push('js')
<script>  
	$('#passwordResetForm').submit(function(event) 
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
				$('#passwordResetForm').find('button').prop('disabled',false);	 
				$('.error_msg').remove(); 
				if(res.status === "success")
				{ 
					toastrMsg(res.status,res.message);  
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
@endpush	