 
@foreach ($fieldList as $field) 
	@php
		$fieldName = strtolower($field['fieldName']);
	@endphp
	 
	<div class="mb-4 col-md-6">
		 
		<label class="content-3 mb-0">
			{{ $field['fieldLabel'] }}
			@if ($field['required'])
				<span class="text-danger">*</span>
			@endif
		</label>
		 
		
		@if (in_array($fieldName, ["beneficiarytype", "remittertype"]))  
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" required>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				<option value="I" {{ $editData && isset($editData[$fieldName]) ? ("I" == $editData[$fieldName] ? 'selected' : '' ) : '' }}>Individual</option>
				<option value="B" {{ $editData && isset($editData[$fieldName]) ? ("B" == $editData[$fieldName] ? 'selected' : '' ) : '' }}>Business</option> 
			</select>
			 
		@elseif ($fieldName == "paymentmode") 
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif> 
				<option value="B">Account Deposit</option>
			</select>
			
		@elseif (in_array($fieldName, ["sendercountry", "sendernationality", "senderidissuecountry", "receivercountry", "receivernationality"]))  
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@foreach($countries as $country) 
					<option value="{{ $country['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($country['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }}>{{ $country['label'] }}</option>
				@endforeach
			</select>
			 
		{{-- @elseif (in_array($fieldName, ["receiverstate", "senderstate"]))  
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@foreach($states as $row) 
					<option value="{{ $row['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($row['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }}>{{ $row['value'] }}</option>
				@endforeach
			</select>
			  --}}
		@elseif ($fieldName == "senderbeneficiaryrelationship") 
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@if($catalogue->has('REL'))
					@foreach($catalogue->get('REL')->data as $row)
						<option value="{{ $row['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($row['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }}>{{ $row['value'] }}</option>
					@endforeach
				@endif
			</select>
			 
		@elseif (in_array($fieldName, ["receiveroccupation", "senderoccupation"])) 
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@if($catalogue->has('OCC'))
					@foreach($catalogue->get('OCC')->data as $row)
						<option value="{{ $row['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($row['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }}>{{ $row['value'] }}</option>
					@endforeach
				@endif
			</select>
			
		@elseif (in_array($fieldName, ["receiveridtype", "sendersecondaryidtype", "senderidtype"])) 
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@if($catalogue->has('DOC'))
					@foreach($catalogue->get('DOC')->data as $row)
						<option value="{{ $row['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($row['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }}>{{ $row['value'] }}</option>
					@endforeach
				@endif
			</select>
			
		@elseif ($fieldName == "purposeofremittance") 
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@if($catalogue->has('POR'))
					@foreach($catalogue->get('POR')->data as $row)
						<option value="{{ $row['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($row['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }}>{{ $row['value'] }}</option>
					@endforeach
				@endif
			</select>
			
		@elseif ($fieldName == "sendersourceoffund") 
			<select id="{{ $fieldName }}" name="{{ $fieldName }}" class="form-control form-control-lg content-3 select2" @if ($field['required']) required @endif>
				<option value="" disabled selected>Select {{ $field['fieldLabel'] }}</option> 
				@if($catalogue->has('SOF'))
					@foreach($catalogue->get('SOF')->data as $row)
						<option value="{{ $row['data'] }}" {{ $editData && isset($editData[$fieldName]) ? ($row['data'] == $editData[$fieldName] ? 'selected' : '' ) : '' }} >{{ $row['value'] }}</option>
					@endforeach
				@endif
			</select>
			
		@elseif ($fieldName == "receivercontactnumber") 
			<div class="d-flex align-items-center gap-2">
				<input id="mobile_code" type="text" name="mobile_code" class="form-control form-control-lg content-3 mobile-number px-2" style="max-width: 65px;" placeholder="+91" value="{{ $editData && isset($editData['mobile_code']) ? $editData['mobile_code'] : '+'.$isdcode }}" readonly />
				<input
					id="{{ $fieldName }}"
					name="{{ $fieldName }}"
					placeholder="Enter {{ $field['fieldLabel'] }}"
					type="text"
					class="form-control form-control-lg content-3"
					value="{{ $editData && isset($editData[$fieldName]) ? $editData[$fieldName] : '' }}"
					@if ($field['required']) required @endif
					@if (isset($field['minLength'])) minlength="{{ $field['minLength'] }}" @endif
					@if (isset($field['maxLength'])) maxlength="{{ $field['maxLength'] }}" @endif
				/>
			</div> 
	 
		@elseif (in_array($fieldName, ["receiverdateofbirth", "receiveridexpiredate", "receiveridissuedate", "senderidissuedate"])) 
			<input
				id="{{ $fieldName }}"
				name="{{ $fieldName }}"
				placeholder="Enter {{ $field['fieldLabel'] }}"
				type="date"
				class="form-control form-control-lg content-3"
				value="{{ $editData && isset($editData[$fieldName]) ? $editData[$fieldName] : '' }}"
				@if ($field['required']) required @endif
				@if (isset($field['minLength'])) minlength="{{ $field['minLength'] }}" @endif
				@if (isset($field['maxLength'])) maxlength="{{ $field['maxLength'] }}" @endif
				onclick="this.showPicker()" style="cursor: pointer;"
			/>
	 
		@else
			<!-- Generate a text input -->
			<input
				id="{{ $fieldName }}"
				name="{{ $fieldName }}"
				placeholder="Enter {{ $field['fieldLabel'] }}"
				type="text"
				class="form-control form-control-lg content-3"
				value="{{ $editData && isset($editData[$fieldName]) ? $editData[$fieldName] : '' }}"
				@if ($field['required']) required @endif
				@if (isset($field['minLength'])) minlength="{{ $field['minLength'] }}" @endif
				@if (isset($field['maxLength'])) maxlength="{{ $field['maxLength'] }}" @endif
			/>
		@endif
	</div>
@endforeach 

<script>
	// Initialize Select2 for dropdowns
	$('#transferBankBeneficiaryForm .select2').select2({
		dropdownParent: $('#addTransferBankBeneficiary'),
		width: "100%"
	}); 
	
	document.querySelectorAll('input[type="date"]').forEach(input => {
		input.addEventListener('focus', function () {
			this.showPicker?.();
		});
	});
</script>