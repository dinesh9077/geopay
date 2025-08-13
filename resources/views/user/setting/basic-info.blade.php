<div class="grid-container mt-md-4">
    <form id="basicInfoForm" action="{{ route('basic-info-update') }}" method="post" class="animate__animated animate__fadeIn g-2">  
        <div class="row text-start col-lg-8">
			<div class="col-md-6 mb-3">
				<label for="first_name" class="form-label content-2 fw-semibold mb-1">Select Id Type <span class="text-danger">*</span></label>
				<select id="id_type" name="id_type" class="form-control form-control-lg default-input select2" required>
					<option value="">Select ID Type</option>
					<option value="Passport" {{ $user->id_type === "Passport" ? 'selected' : '' }}>Passport</option>
					<option value="National ID" {{ $user->id_type === "National ID" ? 'selected' : '' }}>National ID</option>
					<option value="Driving License" {{ $user->id_type === "Driving License" ? 'selected' : '' }}>Driving License</option>
					<option value="Voter ID" {{ $user->id_type === "Voter ID" ? 'selected' : '' }}>Voter ID</option>
					<option value="Residence Permit" {{ $user->id_type === "Residence Permit" ? 'selected' : '' }}>Residence Permit</option>
				</select>
			</div>
			<div class="col-md-6 mb-3">
				<label for="last_name" class="form-label content-2 fw-semibold mb-1">ID Number <span class="text-danger">*</span></label>
				<input id="id_number" name="id_number" type="text" class="form-control form-control-lg default-input border-light" value="{{ $user->id_number }}" required /> 
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
				<label for="email" class="form-label content-2 fw-semibold mb-1">ID Expiry Date <span class="text-danger">*</span></label>
				<input id="expiry_id_date" name="expiry_id_date" type="date" class="form-control form-control-lg default-input"  onclick="this.showPicker()" style="cursor: pointer;" value="{{ $user->expiry_id_date }}" required /> 
			</div>
			
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">City <span class="text-danger">*</span></label>
				<input id="city" name="city" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->city }}" required /> 
			</div> 
		</div> 
		<div class="row text-start col-lg-8"> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">State <span class="text-danger">*</span></label>
				<input id="state" name="state" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->state }}" required /> 
			</div> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Zip Code/Postal Code <span class="text-danger">*</span></label>
				<input id="zip_code" name="zip_code" type="text" autocomplete="off" class="form-control form-control-lg default-input" value="{{ $user->zip_code }}" required /> 
			</div> 
		</div>
		<div class="row text-start col-lg-8"> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Date Of Birth <span class="text-danger">*</span></label>
				<input id="date_of_birth" name="date_of_birth" type="date" max="{{ date('Y-m-d') }}" autocomplete="off" class="form-control form-control-lg default-input" onclick="this.showPicker()" style="cursor: pointer;" value="{{ $user->date_of_birth }}"  required /> 
			</div> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Zip Code/Postal Code <span class="text-danger">*</span></label>
				<select name="gender" class="form-control form-control-lg default-input select2" id="gender" required>
					<option value="">Select Gender</option>
					<option value="Male" {{ $user->gender === "Male" ? 'selected' : '' }}>Male</option>
					<option value="Female" {{ $user->gender === "Female" ? 'selected' : '' }}>Female</option>
					<option value="Other" {{ $user->gender === "Other" ? 'selected' : '' }}>Other</option>
				</select>
			</div> 
		</div>
		<div class="row text-start col-lg-8"> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Business Activity or Occupation <span class="text-danger">*</span></label>
				<select name="business_activity_occupation" class="form-control form-control-lg default-input select2" id="business_activity_occupation" required>
					<option value="">Select Business Activity or Occupation</option>
					<option value="Agriculture forestry fisheries" {{ $user->business_activity_occupation === "Agriculture forestry fisheries" ? 'selected' : '' }}>Agriculture forestry fisheries</option>
					<option value="Construction/manufacturing/marine" {{ $user->business_activity_occupation === "Construction/manufacturing/marine" ? 'selected' : '' }}>Construction/manufacturing/marine</option>
					<option value="Government officials and Special Interest Organizations" {{ $user->business_activity_occupation === "Government officials and Special Interest Organizations" ? 'selected' : '' }}>Government officials and Special Interest Organizations</option>
					<option value="Professional and related workers" {{ $user->business_activity_occupation === "Professional and related workers" ? 'selected' : '' }}>Professional and related workers</option>
					<option value="Retired" {{ $user->business_activity_occupation === "Retired" ? 'selected' : '' }}>Retired</option>
					<option value="Self-employed" {{ $user->business_activity_occupation === "Self-employed" ? 'selected' : '' }}>Self-employed</option>
					<option value="Student" {{ $user->business_activity_occupation === "Student" ? 'selected' : '' }}>Student</option>
					<option value="Unemployed" {{ $user->business_activity_occupation === "Unemployed" ? 'selected' : '' }}>Unemployed</option>
				</select>
			</div> 
			<div class="col-md-6 mb-3">
				<label for="password" class="form-label content-2 fw-semibold mb-1">Source of Fund <span class="text-danger">*</span></label>
				<select name="source_of_fund" class="form-control form-control-lg default-input select2" id="source_of_fund" required>
					<option value="">Select Source of Fund</option>
					<option value="Business profit/dividend" {{ $user->source_of_fund === "Business profit/dividend" ? 'selected' : '' }}>Business profit/dividend</option>
					<option value="Income from employment (normal and/or bonus)" {{ $user->source_of_fund === "Income from employment (normal and/or bonus)" ? 'selected' : '' }}>Income from employment (normal and/or bonus)</option>
					<option value="Investments" {{ $user->source_of_fund === "Investments" ? 'selected' : '' }}>Investments</option>
					<option value="Savings" {{ $user->source_of_fund === "Savings" ? 'selected' : '' }}>Savings</option>
					<option value="Inheritance" {{ $user->source_of_fund === "Inheritance" ? 'selected' : '' }}>Inheritance</option>
					<option value="Loan" {{ $user->source_of_fund === "Loan" ? 'selected' : '' }}>Loan</option>
					<option value="Gift" {{ $user->source_of_fund === "Gift" ? 'selected' : '' }}>Gift</option>
					<option value="Real Estate" {{ $user->source_of_fund === "Real Estate" ? 'selected' : '' }}>Real Estate</option>
					<option value="Lottery/betting/casino winnings" {{ $user->source_of_fund === "Lottery/betting/casino winnings" ? 'selected' : '' }}>Lottery/betting/casino winnings</option>
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