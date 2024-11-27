<div class="row">
	<div class="col-md-6 mb-3">
		<label for="bank_name" class="required text-black font-md mb-2">Bank Name <span class="text-danger">*</span></label>
		<input type="text" class="form-control bg-light border-light" id="bank_name" name="bank_name" value="{{ $companyDetail ? $companyDetail->bank_name : '' }}"> 
	</div>
	<div class="col-md-6 mb-3">
		<label for="bank_code" class="required text-black font-md mb-2">Bank Code <span class="text-danger">*</span></label>
		<input type="text" class="form-control bg-light border-light" id="bank_code" name="bank_code" value="{{ $companyDetail ? $companyDetail->bank_code : '' }}">
	</div>
	<div class="col-md-12 mb-5">
		<label for="account_number" class="required text-black font-md mb-2">Account No <span class="text-danger">*</span></label>
		<input type="text" class="form-control bg-light border-light" id="account_number" name="account_number" value="{{ $companyDetail ? $companyDetail->account_number : '' }}"> 
	</div>
</div> 

<div class="d-flex align-items-center justify-content-between">
	<button type="button" class="btn btn-secondary prev-step"><i class="bi bi-arrow-left me-1"></i>Previous</button>
	<button type="button" class="btn btn-primary next-step">Next <i class="bi bi-arrow-right ms-1"></i></button>
</div>