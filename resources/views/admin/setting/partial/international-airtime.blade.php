<div class="col-md-12 grid-margin stretch-card">
	<div class="card">
		<div class="card-body"> 
			<form class="forms-sample row" id="dtonePlusForm" action="{{ route('admin.third-party-key.update') }}" method="post" enctype="multipart/form-data">
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Base Url</label>
					<input type="url" class="form-control" id="dtone_url" name="dtone_url" autocomplete="off" placeholder="Base Url"  value="{{ config('setting.dtone_url') }}">
				</div>  
				
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Api Key</label>
					<input type="text" class="form-control" id="dtone_apikey" name="dtone_apikey" autocomplete="off" placeholder="Api Key"  value="{{ config('setting.dtone_apikey') }}">
				</div>  
				 
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Secret Key</label>
					<input type="text" class="form-control" id="dtone_secretkey" name="dtone_secretkey" autocomplete="off" placeholder="Secret Key"  value="{{ config('setting.dtone_secretkey') }}">
				</div> 
				<div class="mb-3 col-md-3">
					<label for="exampleInputUsername1" class="form-label">Service Id</label>
					<input type="text" class="form-control" id="dtone_serviceid" name="dtone_serviceid" autocomplete="off" placeholder="Service Id"  value="{{ config('setting.dtone_serviceid') }}">
				</div> 
				<div class="mb-3 col-md-3">
					<label for="exampleInputUsername1" class="form-label">Subservice Id</label>
					<input type="text" class="form-control" id="dtone_subserviceid" name="dtone_subserviceid" autocomplete="off" placeholder="Subservice Id"  value="{{ config('setting.dtone_subserviceid') }}">
				</div>  
				<hr>
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Type</label>
					<select class="form-control" id="dtone_commission_type" name="dtone_commission_type" > 
						<option value="flat" {{ config('setting.dtone_commission_type') == "flat" ? 'selected' : '' }}>Flat/Fix</option>
						<option value="percentage" {{ config('setting.dtone_commission_type') == "percentage" ? 'selected' : '' }}>Percentage</option>
					</select>
				</div>  
				<div class="mb-3 col-md-6">
					<label for="exampleInputUsername1" class="form-label">Commission Charge Flat/%</label>
					<input type="text" class="form-control" id="dtone_commission_charge" name="dtone_commission_charge" autocomplete="off" placeholder="Commission Charge Flat/%" value="{{ config('setting.dtone_commission_charge') ?? 0 }}" oninput="$(this).val($(this).val().replace(/[^0-9.]/g, ''));">
				</div>  
				 
				<div class="d-flex justify-content-end">
					<button type="submit" class="btn btn-primary me-2">Submit</button> 
				</div>
			</form> 
		</div>
	</div>
</div>