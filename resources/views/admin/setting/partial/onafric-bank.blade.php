<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="onafricBankForm" action="{{ route('admin.third-party-key.update') }}?module_type=onafric_bank_setting" method="post" enctype="multipart/form-data"> 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Type</label>
					<select class="form-control" id="onafric_bank_commission_type" name="onafric_bank_commission_type" > 
						<option value="flat" {{ config('setting.onafric_bank_commission_type') == "flat" ? 'selected' : '' }}>Flat/Fix</option>
						<option value="percentage" {{ config('setting.onafric_bank_commission_type') == "percentage" ? 'selected' : '' }}>Percentage</option>
					</select>
				</div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Charge Flat/%</label>
					<input type="text" class="form-control" id="onafric_bank_commission_charge" name="onafric_bank_commission_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ config('setting.onafric_bank_commission_charge') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>
				@if (config("permission.onafric_bank_setting.edit")) 
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
				@endif
			</form> 
		</div>
	</div>
</div> 