<div>
    <label for="company_director_id" class="required text-black content-3 fw-normal mb-2">Director <span class="text-danger">*</span></label> 
    <select 
        id="company_director_id" 
        name="company_director_id" 
        class="form-control form-control-lg bg-light border-light select2">
		@if(count($companyDirectors) != 1) 
			<option value="">Select Director</option>
		@endif 
		@if(count($companyDirectors) > 0) 
			@foreach($companyDirectors as $companyDirector) 
				<option value="{{ $companyDirector['id'] }}">{{ $companyDirector['name'] }}</option>
			@endforeach
		@endif 
    </select>
</div>   