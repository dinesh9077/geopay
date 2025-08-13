<div class="grid-container mt-md-4">
    <form id="basicInfoForm" action="{{ route('basic-info-update') }}" method="post" class="animate__animated animate__fadeIn g-2">  
        <div class="row text-start col-lg-8">
			<div class="col-md-6 mb-3">
				<label for="first_name" class="form-label content-2 fw-semibold mb-1">Select Id Type <span class="text-danger">*</span></label>
				<select id="id_type" name="id_type" class="form-control form-control-lg default-input select2" required>
					<option value="">Select ID Type</option>
					@foreach(App\Enums\IdType::options() as $option)
						<option value="{{ $option['value'] }}" {{ $user->id_type === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-6 mb-3">
				<label for="last_name" class="form-label content-2 fw-semibold mb-1">ID Number <span class="text-danger">*</span></label>
				<input id="id_number" name="id_number" type="text" class="form-control form-control-lg default-input border-light" value="{{ $user->id_number }}" required /> 
			</div>
		</div> 
		<div class="row text-start col-lg-8"> 
			<div class="col-md-6 mb-3">
				<label for="email" class="form-label content-2 fw-semibold mb-1">ID Issue Date <span class="text-danger">*</span></label>
				<input id="issue_id_date" name="issue_id_date" type="date" class="form-control form-control-lg default-input"  onclick="this.showPicker()" style="cursor: pointer;" value="{{ $user->issue_id_date }}" required /> 
			</div>
			<div class="col-md-6 mb-3">
				<label for="email" class="form-label content-2 fw-semibold mb-1">ID Expiry Date <span class="text-danger">*</span></label>
				<input id="expiry_id_date" name="expiry_id_date" type="date" class="form-control form-control-lg default-input"  onclick="this.showPicker()" style="cursor: pointer;" value="{{ $user->expiry_id_date }}" required /> 
			</div>
		</div>
		<div class="row text-start col-lg-8"> 
			<div class="col-md-12 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Full Residential Address <span class="text-danger">*</span></label>
				<input id="address" name="address" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->address }}" required /> 
			</div> 
		</div>
		
		<div class="row text-start col-lg-8"> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">City <span class="text-danger">*</span></label>
				<input id="city" name="city" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->city }}" required /> 
			</div> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">State <span class="text-danger">*</span></label>
				<input id="state" name="state" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->state }}" required /> 
			</div> 
		</div> 
		<div class="row text-start col-lg-8"> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Zip Code/Postal Code <span class="text-danger">*</span></label>
				<input id="zip_code" name="zip_code" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->zip_code }}" required /> 
			</div> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Date Of Birth <span class="text-danger">*</span></label>
				<input id="date_of_birth" name="date_of_birth" type="date" max="{{ date('Y-m-d') }}" autocomplete="off" class="form-control form-control-lg default-input" onclick="this.showPicker()" style="cursor: pointer;" value="{{ $user->date_of_birth }}"  required /> 
			</div> 
		</div>
		<div class="row text-start col-lg-8"> 
			
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Zip Code/Postal Code <span class="text-danger">*</span></label>
				<select name="gender" class="form-control form-control-lg default-input select2" id="gender" required>
					<option value="">Select Gender</option>
					<option value="Male" {{ $user->gender === "Male" ? 'selected' : '' }}>Male</option>
					<option value="Female" {{ $user->gender === "Female" ? 'selected' : '' }}>Female</option>
					<option value="Other" {{ $user->gender === "Other" ? 'selected' : '' }}>Other</option>
				</select>
			</div> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Business Activity or Occupation <span class="text-danger">*</span></label>
				<select name="business_activity_occupation" class="form-control form-control-lg default-input select2" id="business_activity_occupation" required>
					<option value="">Select Business Activity or Occupation</option>
					@foreach(App\Enums\BusinessOccupation::options() as $option)
						<option value="{{ $option['value'] }}" {{ $user->business_activity_occupation === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
					@endforeach
				</select>
			</div> 
		</div>
		<div class="row text-start col-lg-8">  
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Source of Fund <span class="text-danger">*</span></label>
				<select name="source_of_fund" class="form-control form-control-lg default-input select2" id="source_of_fund" required>
					<option value="">Select Source of Fund</option> 
					@foreach(App\Enums\SourceOfFunds::options() as $option)
						<option value="{{ $option['value'] }}" {{ $user->source_of_fund === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
					@endforeach
				</select>
			</div> 
		</div> 
        <button type="submit" class="btn btn-lg btn-secondary rounded-2 col-12 col-md-2 mt-3 mt-md-0">Save</button>
	</form>
</div>

@push('js')
<script>  
	var $basicInfoForm = $('#basicInfoForm');
	
	$basicInfoForm.find('.select2').select2({ 
		width: "100%"
	});
	
	$basicInfoForm.submit(function(event) 
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
				$basicInfoForm.find('button').prop('disabled',false);	 
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
</script>
@endpush	