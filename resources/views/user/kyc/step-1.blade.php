<div class="row"> 
	<div class="col-md-6 mb-3">
		<label for="business_licence" class="required text-black font-md mb-2">Business Type <span class="text-danger">*</span></label>
		<select id="business_type_id" name="business_type_id" class="form-control form-control-lg bg-light border-light select2">
			<option value="">Select Business Type</option>
			@foreach($businessTypes as $businessType)
				<option value="{{ $businessType->id }}" 
						data-is_director="{{ $businessType->is_director }}" 
						{{ isset($companyDetail) && $companyDetail->business_type_id == $businessType->id ? 'selected' : '' }}>
					{{ $businessType->business_type }}
				</option>
			@endforeach 
		</select>	
	</div>
	<div class="col-md-6 mb-3">
		<label for="postcode" class="required text-black font-md mb-2">Number of Directors <span class="text-danger">*</span></label>
		<input 
			type="text" 
			class="form-control bg-light border-light" 
			id="no_of_director" 
			name="no_of_director" 
			value="{{ $companyDetail->no_of_director ?? 1 }}"  
			oninput="this.value = this.value.replace(/\D/g, '')" 
			{{ $user->is_upload_document == 1 ? 'readonly' : '' }}
			{{ $companyDetail && $companyDetail->businessTypes->is_director === 0 ? 'readonly' : '' }} 
			> 
	</div> 
	<div class="col-md-6 mb-3">
		<label for="business_licence" class="required text-black font-md mb-2">Company Registration Number <span class="text-danger">*</span></label>
		<input type="text" class="form-control bg-light border-light" id="business_licence" name="business_licence" value="{{ $companyDetail ? $companyDetail->business_licence : '' }}"> 
	</div>
	<div class="col-md-6 mb-3">
		<label for="postcode" class="required text-black font-md mb-2">Postal Code/Zip Code <span class="text-danger">*</span></label>
		<input type="text" class="form-control bg-light border-light" id="postcode" name="postcode" value="{{ $companyDetail ? $companyDetail->postcode : '' }}"> 
	</div> 
	<div class="col-md-12 mb-3">
		<label for="company_address" class="required text-black font-md mb-2">Legal registered Corporate/Company Address <span class="text-danger">*</span></label>
		<input type="text" class="form-control bg-light border-light" id="company_address" name="company_address" value="{{ $companyDetail ? $companyDetail->company_address : '' }}"> 
	</div> 
</div>
<div class="raw" id="director_html"> 
	@if($companyDetail && $companyDetail->companyDirectors->isNotEmpty()) 
		@foreach($companyDetail->companyDirectors as $key => $companyDirector)
			@if($key == 0)<hr>@endif
			<div class="col-md-12 mb-3"><label for="postcode" class="required text-black font-md mb-2">Director {{ ($key+1) }}  <span class="text-danger">*</span></label><input type="text" class="form-control bg-light border-light" id="director_name_{{$key}}" name="director_name[{{$key}}]" value="{{ $companyDirector->name }}" placeholder="Enter Director Name"></div>
			<input type="hidden" id="director_id_{{$key}}" name="director_id[{{$key}}]" value="{{ $companyDirector->id }}">
		@endforeach
	@endif
</div> 
<div class="d-flex justify-content-end">
	<button type="button" class="btn btn-primary btn-lg next-step">Next <i class="bi bi-arrow-right ms-1"></i></button>
</div>
 